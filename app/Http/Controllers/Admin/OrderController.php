<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $orders = Order::with('orderItems.product')
            ->when($from && $to, function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->when($from && !$to, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when(!$from && $to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->latest()
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function edit(string $id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully');
    }

    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        $order->orderItems()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully');
    }

    public function export(Request $request)
{
    return Excel::download(
        new OrdersExport($request->from, $request->to),
        'orders.xlsx'
    );
}
}