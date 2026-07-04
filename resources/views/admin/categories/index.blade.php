@extends('layouts.admin')

@section('title', 'Category List')

@section('content')
    @php
        $canCreateCategories = $canAccessAdmin('categories', 'create');
        $canEditCategories = $canAccessAdmin('categories', 'edit');
        $canDeleteCategories = $canAccessAdmin('categories', 'destroy');
        $canUseCategoryActions = $canEditCategories || $canDeleteCategories;
    @endphp

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="m-0">Category List</h4>

        @if($canCreateCategories)
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                + Add Category
            </a>
        @endif
    </div>

    <div class="shadow-sm card">
        <div class="p-0 card-body">

            <table class="table m-0 align-middle table-hover table-bordered">

                <thead class="table-dark">
                    <tr>
                        <th style="width:80px;">ID</th>
                        <th>Category Name</th>
                        <th style="width:140px;" class="text-center">Image</th>
                        @if($canUseCategoryActions)
                            <th style="width:200px;" class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @forelse($categories as $c)

                        <tr>

                            <td>{{ $c->id }}</td>

                            <td class="fw-semibold">
                                {{ $c->name }}
                            </td>

                            <td class="text-center">

                                @if($c->image)
                                    <img src="{{ asset($c->image) }}" width="70" class="border rounded">
                                @else
                                    <span class="badge text-bg-secondary">No Image</span>
                                @endif

                            </td>

                            @if($canUseCategoryActions)
                                <td class="text-center">

                                    @if($canEditCategories)
                                        <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                    @endif

                                    @if($canDeleteCategories)
                                        <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST"
                                            style="display:inline">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">
                                                Delete
                                            </button>

                                        </form>
                                    @endif

                                </td>
                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $canUseCategoryActions ? 4 : 3 }}" class="p-4 text-center text-muted">
                                No categories found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

@endsection