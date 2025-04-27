<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'product.category', 'product.images']);
        
        // Handle search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('sku', 'like', "%{$search}%");
        }
        
        // Handle status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Handle stock status filter
        if ($request->has('stock_status')) {
            $status = $request->stock_status;
            
            if ($status === 'out_of_stock') {
                $query->where('quantity', 0);
            } elseif ($status === 'low_stock') {
                $query->whereRaw('quantity <= reorder_level')->where('quantity', '>', 0);
            } elseif ($status === 'in_stock') {
                $query->whereRaw('quantity > reorder_level');
            }
        }
        
        // Handle category filter
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Get alert counts for dashboard
        $outOfStockCount = Inventory::where('quantity', 0)->count();
        $lowStockCount = Inventory::whereRaw('quantity <= reorder_level')
            ->where('quantity', '>', 0)
            ->count();
        $totalValue = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(inventories.quantity * products.price) as total_value'))
            ->first()->total_value ?? 0;
        $totalItems = Inventory::count();
            
        // Get all categories for filtering
        $categories = Category::orderBy('name')->get();
        
        // Get inventory with pagination
        $inventoryItems = $query->latest('updated_at')->paginate(15);
        
        return view('admin.inventory.index', compact(
            'inventoryItems', 
            'categories', 
            'outOfStockCount', 
            'lowStockCount', 
            'totalValue',
            'totalItems'
        ));
    }
    
    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $products = Product::whereDoesntHave('inventory')
            ->orderBy('name')
            ->get();
            
        return view('admin.inventory.create', compact('products'));
    }
    
    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:inventories,product_id',
            'sku' => 'required|string|max:50|unique:inventories,sku',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'location' => 'nullable|string|max:100',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create inventory item
            $inventory = Inventory::create([
                'product_id' => $request->product_id,
                'sku' => $request->sku,
                'quantity' => $request->quantity,
                'reorder_level' => $request->reorder_level, 
                'status' => $request->status,
                'location' => $request->location,
            ]);
            
            // Create initial inventory adjustment record
            if ($request->quantity > 0) {
                InventoryAdjustment::create([
                    'inventory_id' => $inventory->id,
                    'type' => 'add',
                    'quantity' => $request->quantity,
                    'reason' => 'initial',
                    'notes' => 'Initial inventory setup',
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show the form for editing an inventory item.
     */
    public function edit($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        
        return view('admin.inventory.edit', compact('inventory'));
    }
    
    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        
        $request->validate([
            'sku' => 'required|string|max:50|unique:inventories,sku,' . $id,
            'reorder_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'location' => 'nullable|string|max:100',
        ]);
        
        $inventory->update([
            'sku' => $request->sku,
            'reorder_level' => $request->reorder_level,
            'status' => $request->status,
            'location' => $request->location,
        ]);
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }
    
    /**
     * Show the form for adjusting inventory stock.
     */
    public function showAdjust($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        $recentAdjustments = InventoryAdjustment::where('inventory_id', $id)
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.inventory.adjust', compact('inventory', 'recentAdjustments'));
    }
    
    /**
     * Update the stock quantity for an inventory item.
     */
    public function updateStock(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        
        $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:purchase,sale,return,damaged,correction,other',
            'other_reason' => 'required_if:reason,other',
            'reference' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            $oldQuantity = $inventory->quantity;
            $newQuantity = $oldQuantity;
            $adjustmentType = $request->adjustment_type;
            $adjustmentQuantity = $request->quantity;
            
            // Calculate new quantity based on adjustment type
            if ($adjustmentType === 'add') {
                $newQuantity = $oldQuantity + $adjustmentQuantity;
            } elseif ($adjustmentType === 'remove') {
                $newQuantity = max(0, $oldQuantity - $adjustmentQuantity);
                // Adjust the actual quantity removed if it would go below zero
                $adjustmentQuantity = min($adjustmentQuantity, $oldQuantity);
            } else { // set
                $newQuantity = $adjustmentQuantity;
                $adjustmentQuantity = $newQuantity - $oldQuantity;
            }
            
            // Update inventory quantity
            $inventory->quantity = $newQuantity;
            $inventory->save();
            
            // Create adjustment record
            $reason = $request->reason === 'other' ? $request->other_reason : $request->reason;
            
            InventoryAdjustment::create([
                'inventory_id' => $inventory->id,
                'type' => $adjustmentQuantity >= 0 ? 'add' : 'remove',
                'quantity' => $adjustmentQuantity,
                'reason' => $reason,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory quantity updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update inventory: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * View inventory history.
     */
    public function history($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        $adjustments = InventoryAdjustment::where('inventory_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.inventory.history', compact('inventory', 'adjustments'));
    }
    
    /**
     * Remove an inventory item.
     */
    public function destroy($id)
    {
        try {
            $inventory = Inventory::findOrFail($id);
            
            // Check if there are any adjustments
            if ($inventory->adjustments()->count() > 0) {
                return back()->with('error', 'Cannot delete inventory with recorded adjustments.');
            }
            
            $inventory->delete();
            
            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete inventory: ' . $e->getMessage());
        }
    }
    
    /**
     * Export inventory data to CSV.
     */
    public function export(Request $request)
    {
        $query = Inventory::with(['product', 'product.category']);
        
        // Apply filters from the request if they exist
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('sku', 'like', "%{$search}%");
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('stock_status')) {
            $status = $request->stock_status;
            
            if ($status === 'out_of_stock') {
                $query->where('quantity', 0);
            } elseif ($status === 'low_stock') {
                $query->whereRaw('quantity <= reorder_level')->where('quantity', '>', 0);
            } elseif ($status === 'in_stock') {
                $query->whereRaw('quantity > reorder_level');
            }
        }
        
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        $inventory = $query->get();
        
        // Create CSV file
        $filename = 'inventory_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $columns = [
            'ID', 'Product Name', 'SKU', 'Category', 'Quantity', 
            'Reorder Level', 'Status', 'Location', 'Last Updated'
        ];
        
        $callback = function() use ($inventory, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($inventory as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->product->name,
                    $item->sku,
                    $item->product->category->name ?? 'Uncategorized',
                    $item->quantity,
                    $item->reorder_level,
                    ucfirst($item->status),
                    $item->location ?? 'N/A',
                    $item->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
