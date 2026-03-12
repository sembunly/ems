@extends('layouts.admin')

@section('title','Edit Category')

@section('content')

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4 class="m-0">Edit Category</h4>

```
<a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark btn-sm">
    Back to List
</a>
```

</div>

<div class="shadow-sm card">

<div class="bg-white card-header">
<strong>Update Category</strong>
<div class="text-muted small">
Change category information and optionally replace the image
</div>
</div>

<div class="card-body">

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="mb-3">
<label class="form-label">Category Name</label>

<input type="text"
name="name"
value="{{ old('name', $category->name) }}"
class="form-control">

</div>

<div class="mb-3">
<label class="form-label">Slug</label>

<input type="text"
name="slug"
value="{{ old('slug', $category->slug) }}"
class="form-control">

</div>

<div class="mb-3">
<label class="form-label">Description</label>

<textarea name="description"
class="form-control"
rows="3">{{ old('description', $category->description) }}</textarea>

</div>

<div class="mb-3">
<label class="form-label">Current Image</label>

<div>
@if($category->image)
<img src="{{ asset($category->image) }}"
class="border rounded"
width="140">
@else
<span class="badge text-bg-secondary">No Image</span>
@endif
</div>
</div>

<div class="mb-3">
<label class="form-label">Change Image (optional)</label>

<input type="file"
name="image"
class="form-control"
accept="image/*"
onchange="previewImg(event,'catEditPreview')">

<div class="mt-2">
<img id="catEditPreview"
src=""
class="border rounded d-none"
style="width:140px;height:auto;">
</div>
</div>

<button class="btn btn-primary">
Save Changes
</button>

</form>

</div>
</div>

@endsection

@push('scripts')

<script>
function previewImg(event, id){

const img = document.getElementById(id);
const file = event.target.files?.[0];

if(!file){
img.classList.add('d-none');
img.src="";
return;
}

img.src = URL.createObjectURL(file);
img.classList.remove('d-none');

}
</script>

@endpush
