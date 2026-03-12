<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required',
            'slug'=>'required',
            'description'=>'nullable',
            'image'=>'nullable|image'
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/categories'),$filename);
            $data['image'] = 'images/categories/' . $filename;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name'=>'required',
            'slug'=>'required',
            'description'=>'nullable',
            'image'=>'nullable|image'
        ]);

        if($request->hasFile('image')){
            if($category->image && file_exists(public_path($category->image))){
                unlink(public_path($category->image));
            }

            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/categories'),$filename);
            $data['image'] = 'images/categories/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if($category->image && file_exists(public_path('images/categories/'.$category->image))){
            unlink(public_path('images/categories/'.$category->image));
        }

        $category->delete();

        return redirect()->route('admin.categories.index');
    }
}