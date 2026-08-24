<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS System')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            align-items: stretch;
            min-height: 100vh;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #212529;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1d20;
            border-bottom: 1px solid #2c3136;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.1em;
            display: block;
            color: #c2c7d0;
            text-decoration: none;
            transition: 0.3s;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: #343a40;
        }
        #sidebar ul li.active > a {
            color: #fff;
            background: #0d6efd;
        }
        #sidebar ul li a i {
            margin-right: 10px;
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 25px;
            border-radius: 8px;
            padding: 10px 20px;
        }
        .card-stat {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }
        .card-stat:hover {
            transform: translateY(-3px);
        }
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
            #sidebar {
                min-width: 100%;
                max-width: 100%;
                min-height: auto;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header text-center">
            <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-cart4 me-2"></i>POS Shop</h4>
            <span class="badge bg-secondary mt-2 text-capitalize">{{ Auth::user()->role }} Mode</span>
        </div>

        <ul class="list-unstyled components">
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="{{ Request::is('pos*') ? 'active' : '' }}">
                <a href="{{ route('pos.index') }}"><i class="bi bi-pc-display-horizontal"></i> POS Screen</a>
            </li>
            <li class="{{ Request::is('categories*') ? 'active' : '' }}">
                <a href="{{ route('categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
            </li>
            <li class="{{ Request::is('products*') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
            </li>
            <li class="{{ Request::is('sales*') ? 'active' : '' }}">
                <a href="{{ route('sales.index') }}"><i class="bi bi-receipt"></i> Sales History</a>
            </li>
            @if(Auth::user()->role === 'admin')
                <li class="{{ Request::is('reports*') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}"><i class="bi bi-graph-up-arrow"></i> Reports</a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid p-0">
                <span class="navbar-brand mb-0 h1 text-secondary d-none d-md-block">
                    @yield('page_title', 'Dashboard')
                </span>
                
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown me-3">
                        <a class="btn btn-outline-secondary dropdown-toggle border-0" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text text-muted text-capitalize"><small>Role: {{ Auth::user()->role }}</small></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Body -->
        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
