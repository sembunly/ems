<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.product')->latest()->get();
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
}