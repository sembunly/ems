<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalRevenue = Order::sum('total_amount');
        $totalCategories = Category::count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalUsers',
            'totalRevenue',
            'totalCategories'
        ));
    }

    public function salesData()
    {
        $startDate = request('start_date', now()->subMonths(11)->startOfMonth()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $monthlySales = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total', 'month_key');

        $labels = [];
        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $current = \Carbon\Carbon::parse($startDate)->startOfMonth();
        $end = \Carbon\Carbon::parse($endDate)->endOfMonth();

        while ($current <= $end) {
            $key = $current->format('Y-m');
            $labels[] = $months[$current->month - 1] . ' ' . $current->format('Y');
            $data[] = round((float) ($monthlySales[$key] ?? 0), 2);
            $current->addMonth();
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}