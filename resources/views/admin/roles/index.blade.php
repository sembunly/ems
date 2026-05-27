@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded bg-gradient-primary text-white shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1">Role-Based Access Control (RBAC)</h3>
                        <p class="mb-0 text-white-50">Manage application roles, permissions, and security mappings from a unified dashboard.</p>
                    </div>
                    <div>
                        <i class="mdi mdi-shield-key-outline text-white-50" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Statistics Cards -->
<div class="row mb-4">
    <!-- Admin Card -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #dc3545 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-2 fw-bold">Admin</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $userCounts['admin'] ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="mdi mdi-security text-danger" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Full system administration & control
                </div>
            </div>
        </div>
    </div>

    <!-- Seller Card -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #fd7e14 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-2 fw-bold">Seller</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $userCounts['seller'] ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="mdi mdi-store text-warning" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Manage products and categories
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Card -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #198754 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-2 fw-bold">Customer</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $userCounts['customer'] ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="mdi mdi-account-group text-success" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Standard shopping and ordering
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Card -->
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 5px solid #6c757d !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-2 fw-bold">Visitor</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $userCounts['visitor'] ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                        <i class="mdi mdi-eye-outline text-secondary" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    View only public screens
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role-Permission Authorization Matrix -->
<div class="shadow-sm card mb-4">
    <div class="bg-white card-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-dark fs-5">Authorization Matrix</strong>
            <div class="text-muted small mt-1">Cross-reference active roles with system-level permission capabilities</div>
        </div>
        <span class="badge bg-primary text-uppercase px-3 py-2">Spatie Engine Active</span>
    </div>

    <div class="p-0 card-body">
        <div class="table-responsive">
            <table class="table m-0 align-middle table-hover table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start py-3" style="width: 300px;">Permission Name</th>
                        <th class="py-3">Admin</th>
                        <th class="py-3">Seller</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Visitor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td class="text-start fw-semibold py-3 text-dark">
                                <i class="mdi mdi-key-variant text-primary me-2"></i>
                                {{ ucwords($permission->name) }}
                            </td>
                            
                            <!-- Admin column -->
                            <td>
                                @php
                                    $adminRole = $roles->firstWhere('name', 'admin');
                                    $hasPerm = $adminRole && $adminRole->hasPermissionTo($permission->name);
                                @endphp
                                @if($hasPerm)
                                    <span class="badge bg-success bg-opacity-10 text-success border-success border px-3 py-2 fw-semibold">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Granted
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="mdi mdi-minus-circle-outline me-1"></i> Denied
                                    </span>
                                @endif
                            </td>

                            <!-- Seller column -->
                            <td>
                                @php
                                    $sellerRole = $roles->firstWhere('name', 'seller');
                                    $hasPerm = $sellerRole && $sellerRole->hasPermissionTo($permission->name);
                                @endphp
                                @if($hasPerm)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border-warning border px-3 py-2 fw-semibold" style="color: #fd7e14 !important; border-color: #fd7e14 !important; background-color: rgba(253, 126, 20, 0.1) !important;">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Granted
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="mdi mdi-minus-circle-outline me-1"></i> Denied
                                    </span>
                                @endif
                            </td>

                            <!-- Customer column -->
                            <td>
                                @php
                                    $customerRole = $roles->firstWhere('name', 'customer');
                                    $hasPerm = $customerRole && $customerRole->hasPermissionTo($permission->name);
                                @endphp
                                @if($hasPerm)
                                    <span class="badge bg-success bg-opacity-10 text-success border-success border px-3 py-2 fw-semibold">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Granted
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="mdi mdi-minus-circle-outline me-1"></i> Denied
                                    </span>
                                @endif
                            </td>

                            <!-- Visitor column -->
                            <td>
                                @php
                                    $visitorRole = $roles->firstWhere('name', 'visitor');
                                    $hasPerm = $visitorRole && $visitorRole->hasPermissionTo($permission->name);
                                @endphp
                                @if($hasPerm)
                                    <span class="badge bg-success bg-opacity-10 text-success border-success border px-3 py-2 fw-semibold">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Granted
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2">
                                        <i class="mdi mdi-minus-circle-outline me-1"></i> Denied
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-muted">
                                No permissions registered in the Spatie database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
