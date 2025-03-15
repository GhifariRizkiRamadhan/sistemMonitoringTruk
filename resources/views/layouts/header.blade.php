<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Harry - Sistem Manajemen Truk</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('pt_harry/assets/css/style.css') }}" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
    /* Tambahan CSS untuk sidebar fixed */
    .wrapper {
        display: flex;
        width: 100%;
    }
    
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background-color: #343a40;
        color: #fff;
        z-index: 1000;
        overflow-y: auto;
    }
    
    #content {
        width: calc(100% - 250px);
        margin-left: -68%;
        min-height: 100vh;
        transition: all 0.3s;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
            transition: all 0.3s;
        }
        
        .sidebar.active {
            margin-left: 0;
        }
        
        #content {
            width: 100%;
            margin-left: 0;
        }
        
        #content.active {
            width: calc(100% - 250px);
            margin-left: 250px;
        }
    }
    </style>
</head>
<body>     
        <!-- Page Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4" style="position: fixed; z-index: 99; width: 168%; box-shadow: 3px 4px 8px rgba(0, 0, 0, 0.26);">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="ms-auto">
                        <span class="me-3">
                            <i class="fas fa-user"></i> {{ Auth::user()->username }} <!-- Menampilkan nama pengguna -->
                        </span>
                        <a href="" class="btn btn-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </a>
                    </div>
                </div>
            </nav>
        </div>
            <script>
            $(document).ready(function() {
                // Toggle sidebar
                $('#sidebarCollapse').on('click', function() {
                    $('#sidebar, #content').toggleClass('active');
                });

                // Activate current submenu item
                const currentPath = window.location.pathname;
                $('ul.components a, ul.list-unstyled a').each(function() {
                    if ($(this).attr('href') && currentPath.includes($(this).attr('href'))) {
                        $(this).addClass('active');
                        $(this).closest('.collapse').addClass('show');
                    }
                });
            });
            </script>
        
</body>
</html>