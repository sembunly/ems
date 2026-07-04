<nav class="sidebar sidebar-offcanvas" id="sidebar">
    @php
        $canViewProducts = $canAccessAdmin('products', 'index');
        $canCreateProducts = $canAccessAdmin('products', 'create');
        $canViewCategories = $canAccessAdmin('categories', 'index');
        $canCreateCategories = $canAccessAdmin('categories', 'create');
        $canViewReports = $canAccessAdmin('reports', 'index');
        $canViewUsers = $canAccessAdmin('users', 'index');
        $canManagePermissions = $canAccessAdmin('permissions', 'index');
    @endphp

    <ul class="nav">

        @if($canAccessAdmin('dashboard', 'index'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        @endif

        <li class="nav-item nav-category">Management</li>

        <!-- Products -->
        @if($canViewProducts || $canCreateProducts)
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#products" aria-expanded="false" aria-controls="products">
                <i class="menu-icon mdi mdi-laptop"></i>
                <span class="menu-title">Products</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="products">
                <ul class="nav flex-column sub-menu">
                    @if($canViewProducts)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.products.index') }}">All Products</a>
                    </li>
                    @endif
                    @if($canCreateProducts)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.products.create') }}">Add Product</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

        <!-- Categories -->
        @if($canViewCategories || $canCreateCategories)
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#categories" aria-expanded="false" aria-controls="categories">
                <i class="menu-icon mdi mdi-shape"></i>
                <span class="menu-title">Categories</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="categories">
                <ul class="nav flex-column sub-menu">
                    @if($canViewCategories)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}">All Categories</a>
                    </li>
                    @endif
                    @if($canCreateCategories)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.categories.create') }}">Add Category</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

        <!-- Orders -->
        @if($canViewReports)
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#orders" aria-expanded="false" aria-controls="orders">
                <i class="menu-icon mdi mdi-cart"></i>
                <span class="menu-title">Report</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="orders">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">Sale Report</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        <!-- Users -->
        @if($canViewUsers || $canManagePermissions)
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#users" aria-expanded="false" aria-controls="users">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Users</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="users">
                <ul class="nav flex-column sub-menu">
                    @if($canViewUsers)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">All Users</a>
                    </li>
                    @endif
                    @if($canManagePermissions)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.permissions.index') }}">Permissions</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif

    </ul>
</nav>
