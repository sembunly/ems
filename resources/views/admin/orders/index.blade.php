@extends('layouts.admin')

@section('title', 'Order List')

@section('content')
    @php
        $canExportReports = $canAccessAdmin('reports', 'export');
        $canEditOrders = $canAccessAdmin('orders', 'edit');
        $canDeleteOrders = $canAccessAdmin('orders', 'destroy');
        $canUseOrderActions = $canEditOrders || $canDeleteOrders;
    @endphp

    <form method="GET" class="gap-2 mb-3 d-flex align-items-end">
        <div class="d-flex flex-column">
            <label class="form-label small text-muted">Start date</label>
            <input type="date" name="from" value="{{ request('from') }}" placeholder="Start date" class="form-control">
        </div>

        <div class="d-flex flex-column">
            <label class="form-label small text-muted">End date</label>
            <input type="date" name="to" value="{{ request('to') }}" placeholder="End date" class="form-control">
        </div>

        <button class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            Clear
        </a>

        @if($canExportReports)
            <a href="{{ route('admin.orders.export', [
                'from' => request('from'),
                'to' => request('to')
            ]) }}" class="btn btn-success">
                Export Excel
            </a>
        @endif
    </form>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="m-0">Sale Report</h4>
    </div>

    <div class="shadow-sm card">
        <div class="p-0 card-body">

            <div class="table-responsive">
                <table class="table m-0 align-middle table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:80px;">Order ID</th>
                            <th style="width:100px;">User ID</th>
                            <th>Full Name</th>
                            <th>Items</th>
                            <th style="width:120px;">Total QTY</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Note</th>
                            <th style="width:140px;">Total Amount</th>
                            <th style="width:120px;">Status</th>
                            <th style="width:180px;">Order At</th>
                            @if($canUseOrderActions)
                                <th style="width:200px;" class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user_id }}</td>
                                <td class="fw-semibold">{{ $order->full_name }}</td>

                                <td>
                                    {{ $order->orderItems->pluck('product.name')->join(', ') }}
                                </td>

                                <td>
                                    {{ $order->orderItems->sum('quantity') }}
                                </td>

                                <td>{{ $order->phone }}</td>
                                <td>{{ $order->address }}</td>
                                <td>{{ $order->note }}</td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>

                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($order->status == 'awaiting_payment')
                                        <span class="badge bg-info text-dark">Awaiting Payment</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>

                                <td>{{ $order->created_at }}</td>

                                @if($canUseOrderActions)
                                <td class="text-center">
                                    @if($canEditOrders)
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                    @endif

                                    @if($canDeleteOrders)
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this order?')">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canUseOrderActions ? 12 : 11 }}" class="p-4 text-center text-muted">
                                    No orders found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
