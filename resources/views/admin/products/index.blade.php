@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="home-tab">
            <div class="mb-4 d-sm-flex align-items-center justify-content-between border-bottom">
                <h3 class="mb-0">Product List</h3>
                <a href="{{ route('admin.products.create') }}" class="btn btn-info">
                    Add Product
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card card-rounded">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>RAM</th>
                                    <th>Storage</th>
                                    <th>Processor</th>
                                    <th>Screen Size</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                            <td>
                                               @if($product->image)
                                                    <img src="{{ asset($product->image) }}"
                                                        alt="{{ $product->name }}"
                                                        width="70"
                                                        height="70"
                                                        style="object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <span class="text-muted">No Image</span>
                                                @endif
                                            </td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->brand }}</td>
                                        <td>{{ $product->model }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ $product->ram }}</td>
                                        <td>{{ $product->storage }}</td>
                                        <td>{{ $product->processor }}</td>
                                        <td>{{ $product->screen_size }}</td>
                                        <td>{{ $product->description }}</td>
                                        <td>{{ $product->created_at ? $product->created_at->format('d-m-Y') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="btn btn-sm btn-info">
                                            Edit
                                            </a>

                                            <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this product?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection