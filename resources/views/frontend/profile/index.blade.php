@extends('layouts.frontend')

@section('title','My Profile')
@section('hero_title','My Profile')
@section('hero_subtitle','Manage your account information')

@section('content')

<div class="mb-5 row justify-content-center">

    <div class="col-lg-8">

        <div class="border card rounded-3">
            <div class="p-3 card-body">

                <h4 class="mb-3">Account Information</h4>

                <div class="mb-2 row">
                    <div class="col-md-4 text-secondary">Name</div>
                    <div class="col-md-8">{{ auth()->user()->name }}</div>
                </div>

                <div class="mb-2 row">
                    <div class="col-md-4 text-secondary">Email</div>
                    <div class="col-md-8">{{ auth()->user()->email }}</div>
                </div>

                <div class="mb-2 row">
                    <div class="col-md-4 text-secondary">Account Created</div>
                    <div class="col-md-8">{{ auth()->user()->created_at->format('d M Y') }}</div>
                </div>

                <hr>

                <div class="gap-2 d-flex flex-column flex-sm-row">
                    <a href="{{ route('profile.edit') }}" class="btn btn-dark flex-fill">
                        Edit Profile
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary flex-fill">
                        Back to Home
                    </a>
                </div>

            </div>
        </div>

    </div>

</div>

<h4 class="mb-3">My Orders</h4>

@if($orders->isEmpty())
    <p class="text-muted">You haven’t placed any orders yet.</p>
@else
    <div class="row g-3">
        @foreach($orders as $order)
            <div class="col-12">
                <div class="p-3 border card rounded-3">

                    <div class="mb-2">
                        <strong>Order #{{ $order->id }}</strong> - 
                        <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                        <span class="badge bg-secondary ms-2">{{ ucfirst($order->status) }}</span>
                    </div>

                    <ul class="mb-2 list-unstyled">
                        @foreach($order->orderItems as $item)
                            <li class="mb-1 d-flex justify-content-between align-items-center">
                                <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                <a href="{{ route('products.show', $item->product->id) }}" class="btn btn-sm btn-outline-success">
                                    Buy Again
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mb-2 small text-muted">
                        <div>Name: {{ $order->full_name }}</div>
                        <div>Phone: {{ $order->phone }}</div>
                        <div>Address: {{ $order->address }}</div>
                        @if($order->note)
                            <div>Note: {{ $order->note }}</div>
                        @endif
                    </div>

                    <div><strong>Total: ${{ number_format($order->total_amount, 2) }}</strong></div>

                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection