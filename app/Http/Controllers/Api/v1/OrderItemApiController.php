<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderItemApiController extends Controller
{
    public function index()
    {
        return response()->json(OrderItem::with(['order', 'product'])->get());
    }

    public function show($id)
    {
        $item = OrderItem::with(['order', 'product'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        return response()->json($item);
    }

    public function store(Request $request)
    {
        $item = OrderItem::create($request->all());
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = OrderItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = OrderItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $item->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}