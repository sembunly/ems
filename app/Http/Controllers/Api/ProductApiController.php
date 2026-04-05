<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{
    // GET all products
    public function index()
    {
        return response()->json(Product::all());
    }

    // GET single product
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }

    // CREATE product
    public function store(Request $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
            'description' => $request->description,
            'ram' => $request->ram,
            'storage' => $request->storage,
            'processor' => $request->processor,
            'screen_size' => $request->screen_size,
        ]);

        return response()->json($product, 201);
    }

    // UPDATE product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->update($request->all());

        return response()->json($product);
    }

    // DELETE product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}