@extends('layouts.admin')

@section('title','User List')

@section('content')
@php
    $canEditUsers = $canAccessAdmin('users', 'edit');
    $canDeleteUsers = $canAccessAdmin('users', 'destroy');
    $canUseUserActions = $canEditUsers || $canDeleteUsers;
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4 class="m-0">User List</h4>
</div>

<div class="shadow-sm card">
<div class="p-0 card-body">

<table class="table m-0 align-middle table-hover table-bordered">

<thead class="table-dark">
<tr>
<th style="width:80px;">ID</th>
<th>Name</th>
<th>Email</th>
<th style="width:120px;">Role</th>
@if($canUseUserActions)
<th style="width:200px;" class="text-center">Actions</th>
@endif
</tr>
</thead>

<tbody>

@forelse($users as $user)

<tr>

<td>{{ $user->id }}</td>

<td class="fw-semibold">
{{ $user->name }}
</td>

<td>
{{ $user->email }}
</td>

<td>
@if($user->role == 'admin')
<span class="badge bg-success">Admin</span>
@else
<span class="badge bg-secondary">Customer</span>
@endif
</td>

@if($canUseUserActions)
<td class="text-center">

@if($canEditUsers)
<a href="{{ route('admin.users.edit',$user->id) }}"
class="btn btn-warning btn-sm">
Edit </a>
@endif

@if($canDeleteUsers)
<form action="{{ route('admin.users.destroy',$user->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')">
Delete </button>

</form>
@endif

</td>
@endif

</tr>

@empty

<tr>
<td colspan="{{ $canUseUserActions ? 5 : 4 }}" class="p-4 text-center text-muted">
No users found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>
</div>

@endsection
