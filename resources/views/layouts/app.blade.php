<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- Link to your CSS files here -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Optional: Add FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Flexbox Layout for Sidebar and Content */
        .wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #3066be;
            color: white;
            box-shadow: 5px 4px 8px rgba(0, 0, 0, 0.42);
            border-top-right-radius: 3%;
            border-bottom-right-radius: 3%;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-header {
            background-color: #f6f6f6;
            color: #000000;
            padding: 10px;
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 10;
        }

        .sidebar ul li a {
            color: #808080;
            text-decoration: none;
            padding: 10px;
            display: block;
        }

        .sidebar .user-info {
            margin: 15px 0;
        }

        .navbar {
            background-color: #ffffff;
            padding: 15px;
        }
        .sidebar ul li.active {
        background: #feb2bf;
        }

        .sidebar ul li:hover {
            transition: background-color 1s ease;
            background: #feb2bf;
        }
        .sidebar ul li {
            border-radius: 10px; /* Agar tidak terlihat patah saat hover */
            transition: background-color 1s ease, border-radius 0.5s ease; /* Transisi untuk border-radius */
        }
    </style>
</head>
<body>
    @php
    $current_route = Route::currentRouteName(); // Mendapatkan nama route yang sedang aktif
    @endphp
<div class="wrapper">
@include('layouts.header')
<nav id="sidebar" class="sidebar" style="border-top-right-radius: 3%; border-bottom-right-radius: 3%; box-shadow: 5px 4px 8px rgba(0, 0, 0, 0.329); background-color: #f6f6f6;">
    <div class="sidebar-header">
        <h3 class="text-2xl">PT Harry Tridarma</h3>
        <div class="user-info mb-3">
            <small>Welcome, {{ Auth::user()->username }}</small>
        </div>
    </div>
    
    <ul class="list-unstyled components">
        <li class="{{ $current_route == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        @if(Auth::user()->role == 'admin')
        <li class="{{ $current_route == 'dataTruk' ? 'active' : '' }}">
            <a href="{{ route('dataTruk') }}">
                <i class="fas fa-truck"></i> Data Truk
            </a>
        </li>
        
        <li class="{{ $current_route == 'catatanAngkutan' ? 'active' : '' }}">
            <a href="{{ route('catatanAngkutan') }}">
                <i class="fas fa-shipping-fast"></i> Catatan Angkutan
            </a>
        </li>
        
        <li class="{{ $current_route == 'catatanOperasional' ? 'active' : '' }}">
            <a href="{{ route('catatanOperasional') }}">
                <i class="fas fa-tools"></i> Catatan Operasional
            </a>
        </li>
        
        <li class="{{ $current_route == 'pengecekanBulanan' ? 'active' : '' }}">
            <a href="{{ route('pengecekanBulanan') }}">
                <i class="fas fa-clipboard-check"></i> Pengecekan Bulanan
            </a>
        </li>
        
        <li class="">
            <a href="#reportsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
            <ul class="">
                <li>
                    <a href="{{ route('incomeReport') }}">
                        <i class="fas fa-angle-right"></i> Laporan Pendapatan
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-angle-right"></i> Laporan Operasional
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-angle-right"></i> Laporan Pengecekan
                    </a>
                </li>
                <li>
                    <a href="">
                        <i class="fas fa-angle-right"></i> Laporan Keuangan
                    </a>
                </li>
            </ul>
        </li>
        @endif
        
        <li class="">
            <a href="">
                <i class="fas fa-chart-bar"></i> Grafik dan Analisis
            </a>
        </li>
        
        {{-- <li class="mt-auto">
            <a href="" >
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </li> --}}
    </ul>
</nav>
<div class="content">
    @yield('content')
</div>
</div>
</body>
</html>
