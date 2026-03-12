<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.create', compact('categories'));
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'ram' => 'nullable|string|max:255',
        'storage' => 'nullable|string|max:255',
        'processor' => 'nullable|string|max:255',
        'screen_size' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/products'), $filename);

        $data['image'] = 'images/products/' . $filename;
    }

    Product::create($data);

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Product $product)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'ram' => 'nullable|string|max:255',
        'storage' => 'nullable|string|max:255',
        'processor' => 'nullable|string|max:255',
        'screen_size' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/products'), $filename);
        $data['image'] = 'images/products/' . $filename;
    }

    $product->update($data);

    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Product updated successfully');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success','Product deleted successfully');
    }
}
