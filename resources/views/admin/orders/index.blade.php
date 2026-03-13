@extends('layouts.admin')

@section('title','Order List')

@section('content')

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4 class="m-0">Order List</h4>
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
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Note</th>
                        <th style="width:140px;">Total Amount</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:180px;">Order At</th>
                        <!--<th style="width:180px;">Updated At</th>-->
                        <th style="width:200px;" class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user_id }}</td>
                            <td class="fw-semibold">{{ $order->full_name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ $order->address }}</td>
                            <td>{{ $order->note }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at }}</td>
                            <!--<td>{{ $order->updated_at }}</td>-->
                            <td class="text-center">
                                <a href="{{ route('admin.orders.edit', $order->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                      method="POST"
                                      style="display:inline-block">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this order?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-4 text-center text-muted">
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