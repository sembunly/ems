@extends('layouts.frontend')

@section('title', 'QR Payment')
@section('hero_title', 'Scan to Pay')
@section('hero_subtitle', 'Scan the QR code below to complete your payment')
@section('hero_action')
    <a class="px-4 btn btn-outline-dark pill" href="{{ route('home') }}">
        <i class="bi bi-house me-1"></i> Back to Home
    </a>
@endsection

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="mb-0 breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('checkout.index') }}" class="text-decoration-none">Checkout</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">QR Payment</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row g-4 justify-content-center">
        {{-- QR Code Card --}}
        <div class="col-lg-5">
            <div class="p-3 text-center card soft-card p-md-4">
                <h5 class="mb-1 fw-bold">Order #{{ $order->id }}</h5>
                <div class="mb-3 text-muted small">
                    Amount:
                    <span class="fw-bold text-dark fs-5">${{ number_format($order->total_amount, 2) }}</span>
                </div>

                @if($qr)
                    <div class="p-3 mx-auto mb-3 bg-white border rounded-4" style="display:inline-block;">
                        <div id="qrcode"></div>
                    </div>
                @else
                    <div class="alert alert-danger rounded-4">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Unable to generate QR code.
                    </div>
                @endif

                <div class="border alert alert-light rounded-4">
                    <div class="fw-bold">
                        <i class="bi bi-phone me-1"></i> How to Pay
                    </div>
                    <ol class="mt-2 mb-0 text-start text-muted small">
                        <li>Scan the QR code above</li>
                        <li>Complete the payment in your app</li>
                        <li>Wait a moment for payment verification</li>
                    </ol>
                </div>

                <!-- <div class="gap-2 mt-3 d-grid">
                    <button id="btnCheckPayment" class="btn btn-dark rounded-pill" @disabled(!$qr || !$md5)>
                        <i class="bi bi-check-circle me-1"></i> I Have Paid
                    </button>
                </div> -->

                <div id="statusBox" class="mt-3">
                    <div class="gap-2 d-flex align-items-center justify-content-center text-muted">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span>Waiting for payment...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-5">
            <div class="p-3 card soft-card p-md-4">
                <h5 class="mb-3 fw-bold">Order Summary</h5>

                <div class="gap-2 d-grid">
                    @foreach($order->orderItems as $item)
                        <div class="d-flex justify-content-between">
                            <div class="me-2">
                                <div class="fw-semibold">{{ $item->product->name ?? 'Product' }}</div>
                                <div class="text-muted small">
                                    ${{ number_format($item->price, 2) }} &times; {{ $item->quantity }}
                                </div>
                            </div>
                            <div class="fw-bold">
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold fs-5">${{ number_format($order->total_amount, 2) }}</span>
                </div>

                <hr>

                <div class="mb-2 fw-bold">Customer</div>
                <div class="text-muted small">
                    <div>{{ $order->full_name }}</div>
                    <div>{{ $order->phone }}</div>
                    <div>{{ $order->address }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        const qrString = @json($qr ?? null);
        const md5 = @json($md5 ?? null);
        const orderId = @json($order->id);
        const verifyUrl = @json(route('checkout.verify.transaction'));
        const successUrl = @json(route('checkout.success', $order->id));
        const csrfToken = @json(csrf_token());

        if (qrString) {
            new QRCode(document.getElementById('qrcode'), {
                text: qrString,
                width: 220,
                height: 220,
                colorDark: '#111827',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        }

        let checking = false;

        function setWaitingStatus(message = 'Waiting for payment...') {
            document.getElementById('statusBox').innerHTML = `
    <div class="gap-2 d-flex align-items-center justify-content-center text-muted">
    <div class="spinner-border spinner-border-sm" role="status"></div>
    <span>${message}</span>
    </div>
    `;
        }

        function setSuccessStatus(message = 'Payment received! Redirecting...') {
            document.getElementById('statusBox').innerHTML = `
    <div class="mb-0 alert alert-success rounded-4 fw-bold">
    <i class="bi bi-check-circle me-1"></i> ${message}
    </div>
    `;
        }

        function setErrorStatus(message = 'Payment not completed yet.') {
            document.getElementById('statusBox').innerHTML = `
    <div class="mb-0 alert alert-warning rounded-4 fw-bold">
    <i class="bi bi-exclamation-circle me-1"></i> ${message}
    </div>
    `;
        }

        async function verifyPayment(manual = false) {
            if (checking || !md5) return;

            checking = true;

            if (manual) {
                setWaitingStatus('Checking payment...');
            }

            try {
                const response = await fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        md5: md5,
                        order_id: orderId
                    })
                });

                const data = await response.json();

                if (data.paid) {
                    setSuccessStatus(data.message || 'Payment received! Redirecting...');
                    setTimeout(() => {
                        window.location.href = data.redirect || successUrl;
                    }, 1500);
                } else {
                    if (manual) {
                        setErrorStatus(data.message || 'Payment not completed yet.');
                    }
                }
            } catch (error) {
                if (manual) {
                    setErrorStatus('Unable to verify payment right now.');
                }
            } finally {
                checking = false;
            }
        }

        document.getElementById('btnCheckPayment')?.addEventListener('click', function () {
            verifyPayment(true);
        });

        function autoCheckPayment() {
            verifyPayment(false).finally(() => {
                setTimeout(autoCheckPayment, 5000);
            });
        }

        @if($qr && $md5)
            autoCheckPayment();
        @endif
    </script>
@endpush