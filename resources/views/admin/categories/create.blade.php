@extends('layouts.admin')
@section('title', 'Create Category')

@section('content')

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="m-0">Create Category</h4>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark btn-sm">
            Back to List
        </a>
    </div>

    <div class="shadow-sm card">

        <div class="bg-white card-header">
            <strong>Category Form</strong>
            <div class="text-muted small">
                Create new category with optional image
            </div>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label">Category Name</label>

                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                        placeholder="Enter category name">

                </div>

                <div class="mb-3">
                    <label class="form-label">Slug</label>

                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}"
                        placeholder="category-slug">

                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>

                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Category description">{{ old('description') }}</textarea>

                </div>

                <div class="mb-3">
                    <label class="form-label">Category Image (optional)</label>

                    <input type="file" name="image" class="form-control" accept="image/*"
                        onchange="previewImg(event,'catPreview')">

                    <div class="mt-2">
                        <img id="catPreview" src="" class="border rounded d-none" style="width:120px; height:auto;">
                    </div>

                </div>

                <button class="btn btn-primary">
                    Save Category
                </button>

            </form>

        </div>

    </div>

@endsection


@push('scripts')

    <script>

        function previewImg(event, id) {

            const img = document.getElementById(id);
            const file = event.target.files?.[0];

            if (!file) {
                img.classList.add('d-none');
                img.src = "";
                return;
            }

            img.src = URL.createObjectURL(file);
            img.classList.remove('d-none');

        }

    </script>

@endpush