<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReturn;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReturnController extends Controller
{
    /**
     * Display a listing of the returns.
     */
    public function index(Request $request)
    {
        $query = ProductReturn::with(['customer', 'order']); 

        // Filter by return number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('return_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($q) use ($search) {
                      $q->where('order_number', 'LIKE', "%{$search}%");
                  });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by latest first
        $returns = $query->latest()->paginate(15);

        // Get return status counts for statistics
        $statusCounts = [
            'total' => ProductReturn::count(),
            'requested' => ProductReturn::where('status', 'requested')->count(),
            'approved' => ProductReturn::where('status', 'approved')->count(),
            'received' => ProductReturn::where('status', 'received')->count(),
            'processed' => ProductReturn::where('status', 'processed')->count(),
            'completed' => ProductReturn::where('status', 'completed')->count(),
            'rejected' => ProductReturn::where('status', 'rejected')->count(),
        ];

        return view('admin.returns.index', compact('returns', 'statusCounts'));
    }

    /**
     * Show the form for creating a new return.
     */
    public function create(Request $request)
    {
        $orders = Order::with('customer')
                ->whereIn('status', ['processing', 'shipped', 'delivered', 'completed'])
                ->latest()
                ->get();
                
        return view('admin.returns.create', compact('orders'));
    }

    /**
     * Get returnable items for an order.
     */
    public function getOrderItems(Order $order)
    {
        try {
            // Log the order ID for debugging
            \Log::info('getOrderItems called with order ID: ' . $order->id);
            
            $order->load(['items.returns']);
            
            $items = $order->items->map(function ($item) {
                $returnedQuantity = $item->returnedQuantity();
                $availableQuantity = $item->quantity - $returnedQuantity;
                dd($availableQuantity);
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'returned_quantity' => $returnedQuantity,
                    'available_quantity' => $availableQuantity,
                    'can_return' => $availableQuantity > 0,
                ];
            });
            
            $response = [
                'order_id' => $order->id,
                'items' => $items
            ];
            
            // Log the response for debugging
            \Log::info('getOrderItems response: ' . json_encode($response));
            
            return response()->json($response);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in getOrderItems: ' . $e->getMessage());
            
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Store a newly created return in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|string|in:new,used,damaged',
            'items.*.reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create the return request
            $return = new ProductReturn();
            $return->order_id = $request->order_id;
            $return->customer_id = $request->customer_id;
            $return->return_number = 'RET-' . strtoupper(uniqid());
            $return->status = 'requested';
            $return->reason = $request->reason;
            $return->admin_notes = $request->admin_notes;
            $return->save();

            // Add return items
            foreach ($request->items as $itemId => $itemData) {
                $orderItem = OrderItem::findOrFail($itemData['order_item_id']);
                
                // Check if quantity is valid
                $returnedQuantity = $orderItem->returnedQuantity();
                $availableQuantity = $orderItem->quantity - $returnedQuantity;
                
                if ($itemData['quantity'] > $availableQuantity) {
                    throw new \Exception("Cannot return more items than available. Item: {$orderItem->product_name}");
                }
                
                // Create return item
                $return->items()->create([
                    'order_item_id' => $itemData['order_item_id'],
                    'quantity' => $itemData['quantity'],
                    'condition' => $itemData['condition'],
                    'reason' => $itemData['reason'],
                    'approved' => false,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.returns.index')->with('success', 'Return request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating return request: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified return.
     */
    public function show(ProductReturn $return)
    {
        $return->load(['customer', 'order', 'items.orderItem.product']);
        return view('admin.returns.show', compact('return'));
    }

    /**
     * Show the form for editing the specified return.
     */
    public function edit(ProductReturn $return)
    {
        $return->load(['customer', 'order', 'items.orderItem.product']);
        return view('admin.returns.edit', compact('return'));
    }

    /**
     * Update the specified return in storage.
     */
    public function update(Request $request, ProductReturn $return)
    {
        $request->validate([
            'status' => 'required|string|in:requested,approved,received,processed,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $return->status;
            $return->status = $request->status;
            $return->admin_notes = $request->admin_notes;
            
            // If status changed to processed or completed, handle refund details
            if (($request->status == 'processed' || $request->status == 'completed') && 
                ($oldStatus != 'processed' && $oldStatus != 'completed')) {
                
                $request->validate([
                    'refund_method' => 'required|string|in:credit,original_payment,exchange',
                    'refund_amount' => 'required|numeric|min:0',
                ]);
                
                $return->refund_method = $request->refund_method;
                $return->refund_amount = $request->refund_amount;
                $return->processed_at = Carbon::now();
            }
            
            // Update tracking number if provided
            if ($request->filled('return_tracking_number')) {
                $return->return_tracking_number = $request->return_tracking_number;
            }
            
            $return->save();
            
            // If status is approved, approve all return items
            if ($request->status == 'approved' && $oldStatus != 'approved') {
                foreach ($return->items as $item) {
                    $item->approved = true;
                    $item->save();
                }
            }
            
            // If status is completed, update inventory
            if ($request->status == 'completed' && $oldStatus != 'completed') {
                foreach ($return->items as $item) {
                    if ($item->approved && $item->orderItem && $item->orderItem->product) {
                        // If product tracks inventory, add the returned quantity back
                        if ($item->orderItem->product->track_inventory) {
                            $item->orderItem->product->increment('quantity', $item->quantity);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.returns.show', $return)->with('success', 'Return request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating return request: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update return status via AJAX.
     */
    public function updateStatus(Request $request, ProductReturn $return)
    {
        $request->validate([
            'status' => 'required|string|in:requested,approved,received,processed,completed,rejected',
        ]);

        $return->status = $request->status;
        $return->save();

        return response()->json([
            'success' => true,
            'message' => 'Return status updated successfully.'
        ]);
    }

    /**
     * Remove the specified return from storage.
     */
    public function destroy(ProductReturn $return)
    {
        // Only allow deletion of returns in "requested" status
        if ($return->status != 'requested') {
            return back()->with('error', 'Cannot delete a return that has been processed.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete related return items first
            $return->items()->delete();
            
            // Then delete the return
            $return->delete();
            
            DB::commit();
            return redirect()->route('admin.returns.index')->with('success', 'Return request deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting return request: ' . $e->getMessage());
        }
    }
} 