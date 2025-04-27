<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ContactMessage;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $recentCustomers = Customer::latest()->take(5)->get();
        
        // Order & Sales Statistics
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $totalReturns = ReturnRequest::count();
        $recentOrders = Order::with('customer')->latest()->take(5)->get();
        
        // Low stock products
        $lowStockProducts = Product::whereHas('inventory', function($query) {
            $query->whereRaw('quantity <= reorder_level')->where('quantity', '>', 0);
        })->count();
        
        // Sales data
        $salesData = $this->getSalesData();
        $dailySales = $this->getDailySales();
        $monthlySales = $this->getMonthlySales();
        $topSellingProducts = $this->getTopSellingProducts();
        
        // Customer growth over the past 6 months
        $customerGrowth = $this->getCustomerGrowthData();
        
        // Categories with product counts for donut chart
        $categories = Category::withCount('products')->get();
        
        // Price range data for horizontal bar chart
        $priceRanges = $this->getPriceRangeData();
        
        // Monthly product additions per category
        $monthlyData = $this->getMonthlyProductData();
        
        // Customer status distribution
        $customerStatusData = $this->getCustomerStatusData();
        
        // Parent menu items for the menu creation modal
        $parentMenuItems = MenuItem::whereNull('parent_id')->get();
        
        // Get SEO and Contact settings
        $seoSettings = Setting::where('group', 'seo')->first();
        $contactSettings = Setting::where('group', 'contact')->first();
        
        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCategories', 
            'totalCustomers',
            'activeCustomers',
            'recentProducts',
            'recentCustomers',
            'customerGrowth',
            'categories',
            'priceRanges',
            'monthlyData',
            'customerStatusData',
            'parentMenuItems',
            'seoSettings',
            'contactSettings',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'totalReturns',
            'recentOrders',
            'lowStockProducts',
            'salesData',
            'dailySales',
            'monthlySales',
            'topSellingProducts'
        ));
    }
    
    // ... existing methods ...
    
    /**
     * Get sales data for today, this week, and this month
     */
    private function getSalesData()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        return [
            'today' => [
                'orders' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)->sum('total')
            ],
            'week' => [
                'orders' => Order::where('created_at', '>=', $startOfWeek)->count(),
                'revenue' => Order::where('created_at', '>=', $startOfWeek)->sum('total')
            ],
            'month' => [
                'orders' => Order::where('created_at', '>=', $startOfMonth)->count(),
                'revenue' => Order::where('created_at', '>=', $startOfMonth)->sum('total')
            ]
        ];
    }
    
    /**
     * Get daily sales for the past 15 days
     */
    private function getDailySales()
    {
        $days = 15;
        $period = Carbon::now()->subDays($days);
        
        $sales = Order::where('created_at', '>=', $period)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as order_count, SUM(total) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $dates = [];
        $orderCounts = [];
        $salesAmounts = [];
        
        // Fill with zeros for days with no orders
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - $i - 1)->format('Y-m-d');
            $dates[] = Carbon::parse($date)->format('M d');
            
            $dayData = $sales->firstWhere('date', $date);
            $orderCounts[] = $dayData ? $dayData->order_count : 0;
            $salesAmounts[] = $dayData ? $dayData->total_sales : 0;
        }
        
        return [
            'labels' => $dates,
            'orders' => $orderCounts,
            'sales' => $salesAmounts
        ];
    }
    
    /**
     * Get monthly sales for the past 12 months
     */
    private function getMonthlySales()
    {
        $months = 12;
        $period = Carbon::now()->subMonths($months);
        
        $sales = Order::where('created_at', '>=', $period)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as order_count, SUM(total) as total_sales')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        $labels = [];
        $orderCounts = [];
        $salesAmounts = [];
        
        // Fill with zeros for months with no orders
        for ($i = 0; $i < $months; $i++) {
            $date = Carbon::now()->subMonths($months - $i - 1);
            $year = $date->year;
            $month = $date->month;
            $labels[] = $date->format('M Y');
            
            $monthData = $sales->first(function($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });
            
            $orderCounts[] = $monthData ? $monthData->order_count : 0;
            $salesAmounts[] = $monthData ? $monthData->total_sales : 0;
        }
        
        return [
            'labels' => $labels,
            'orders' => $orderCounts,
            'sales' => $salesAmounts
        ];
    }
    
    /**
     * Get top selling products
     */
    private function getTopSellingProducts($limit = 5)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_sales')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get customer growth data for the past 6 months
     */
    private function getCustomerGrowthData()
    {
        $months = 6;
        $labels = [];
        $data = [];
        
        // Generate data for the past 6 months
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = $count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * Get customer status distribution data
     */
    private function getCustomerStatusData()
    {
        $active = Customer::where('status', 'active')->count();
        $inactive = Customer::where('status', 'inactive')->count();
        $blocked = Customer::where('status', 'blocked')->count();
        
        return [
            'labels' => ['Active', 'Inactive', 'Blocked'],
            'data' => [$active, $inactive, $blocked]
        ];
    }
    
    /**
     * Get price range data for products
     */
    private function getPriceRangeData()
    {
        $ranges = [
            ['< $500', Product::where('price', '<', 500)->count()],
            ['$500 - $999', Product::whereBetween('price', [500, 999.99])->count()],
            ['$1000 - $1499', Product::whereBetween('price', [1000, 1499.99])->count()],
            ['$1500 - $1999', Product::whereBetween('price', [1500, 1999.99])->count()],
            ['$2000+', Product::where('price', '>=', 2000)->count()]
        ];
        
        return $ranges;
    }
    
    /**
     * Get monthly product data by category
     */
    private function getMonthlyProductData()
    {
        $categories = Category::all();
        $monthlyData = [];
        
        foreach ($categories as $category) {
            $monthlyCounts = [];
            
            // Get products count for each month of the current year
            for ($month = 1; $month <= 12; $month++) {
                $count = Product::where('category_id', $category->id)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', $month)
                    ->count();
                
                $monthlyCounts[] = $count;
            }
            
            $monthlyData[] = [$category->name, $monthlyCounts];
        }
        
        return $monthlyData;
    }
}