@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="row">
        <!-- Stats Widgets -->
        <div class="col-md-3 grid-margin stretch-card">
            <div class="text-white card bg-primary">
                <div class="card-body">
                    <h4 class="text-white card-title">Total Products</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-package-variant mdi-36px"></i>
                        <h2 class="mb-0">{{ $totalProducts }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="text-white card bg-success">
                <div class="card-body">
                    <h4 class="text-white card-title">Total Users</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-account-multiple mdi-36px"></i>
                        <h2 class="mb-0">{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="text-white card bg-info">
                <div class="card-body">
                    <h4 class="text-white card-title">Total Orders</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-receipt mdi-36px"></i>
                        <h2 class="mb-0">{{ $totalOrders }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="text-white card bg-warning">
                <div class="card-body">
                    <h4 class="text-white card-title">Total Revenue</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-cash mdi-36px"></i>
                        <h2 class="mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 grid-margin stretch-card">
            <div class="text-white card bg-info">
                <div class="card-body">
                    <h4 class="text-white card-title">Total Categories</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="mdi mdi-receipt mdi-36px"></i>
                        <h2 class="mb-0">{{ $totalCategories }}</h2>
                    </div>
                </div>
            </div>
        </div>
</div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Selling Overview</h4>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 small">From:</label>
                            <input type="date" id="startDate" class="form-control form-control-sm" style="width: auto;">
                            <label class="mb-0 small">To:</label>
                            <input type="date" id="endDate" class="form-control form-control-sm" style="width: auto;">
                            <button id="filterBtn" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                    </div>
                    <canvas id="myChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById("myChart");
        const chart = new Chart(ctx, {
            type: "line",
            data: {
                labels: [],
                datasets: [{
                    label: "Sales ($)",
                    borderColor: "#6366f1",
                    backgroundColor: "rgba(99, 102, 241, 0.1)",
                    data: [],
                    fill: true,
                    pointBackgroundColor: "#6366f1",
                    borderWidth: 3,
                    pointBorderWidth: 4,
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 8,
                    pointHoverBorderColor: "rgba(99, 102, 241, 0.2)",
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    }
                }
            }
        });

        // Set default dates: 12 months ago to today
        const today = new Date();
        const yearAgo = new Date();
        yearAgo.setMonth(yearAgo.getMonth() - 11);
        yearAgo.setDate(1);

        document.getElementById('startDate').value = yearAgo.toISOString().split('T')[0];
        document.getElementById('endDate').value = today.toISOString().split('T')[0];

        function loadSalesData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            fetch(`/api/sales-data?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.data;
                    chart.update();
                });
        }

        document.getElementById('filterBtn').addEventListener('click', loadSalesData);
        loadSalesData();
    </script>
    @endpush

 

    
@endsection
