@extends('layouts.admin')

@section('title','Edit Product')

@section('content')

<div class="card">
<div class="card-body">

<h3 class="mb-4">Edit Product</h3>

<form action="{{ route('admin.products.update',$product->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="row">

<div class="mb-3 col-md-6">
<label>Name</label>
<input type="text" name="name" value="{{ $product->name }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
<label>Brand</label>
<input type="text" name="brand" value="{{ $product->brand }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
<label>Model</label>
<input type="text" name="model" value="{{ $product->model }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
<label>Category</label>
<select name="category_id" class="form-control">
@foreach($categories as $cat)
<option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
{{ $cat->name }}
</option>
@endforeach
</select>
</div>

<div class="mb-3 col-md-6">
<label>Price</label>
<input type="number" name="price" value="{{ $product->price }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
<label>Stock</label>
<input type="number" name="stock" value="{{ $product->stock }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
    <label>RAM</label>
    <input type="text" name="ram" value="{{ old('ram', $product->ram) }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
    <label>Storage</label>
    <input type="text" name="storage" value="{{ old('storage', $product->storage) }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
    <label>Processor</label>
    <input type="text" name="processor" value="{{ old('processor', $product->processor) }}" class="form-control">
</div>

<div class="mb-3 col-md-6">
    <label>Screen Size</label>
    <input type="text" name="screen_size" value="{{ old('screen_size', $product->screen_size) }}" class="form-control">
</div>

<div class="mb-3 col-12">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
</div>

<div class="mb-3 col-12">
<label>Image</label><br>

@if($product->image)
<img src="{{ asset($product->image) }}" width="120" class="mb-2">
@endif

<input type="file" name="image" class="form-control">
</div>

</div>

<button class="btn btn-primary">Update Product</button>
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

@endsection