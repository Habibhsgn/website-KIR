<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="/assets/plugin-admin/vendors/feather/feather.css">
    <link rel="stylesheet" href="/assets/plugin-admin/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/plugin-admin/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="/assets/plugin-admin/js/select.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="/assets/plugin-admin/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="/assets/plugin-admin/images/favicon.png" />

    @stack('styles')
</head>

<body>
    <div class="container-scroller">
        <!-- Navbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo me-5" href="/admin/dashboard">
                    <h3>ADMIN KIR RSUD</h3>
                </a>
                <a class="navbar-brand brand-logo-mini" href="/admin/dashboard">
                    <img src="/assets/plugin-admin/images/logo-mini.svg" alt="logo" />
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>
                <ul class="navbar-nav mr-lg-2">
                </ul>
                <ul class="navbar-nav navbar-nav-right">

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link p-0">
                                <i class="ti-power-off text-primary"></i> Logout
                            </button>
                        </form>
                    </li>

                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/data-pengajuan">
                            <i class="icon-layout menu-icon"></i>
                            <span class="menu-title">Data Pengajuan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/resume">
                            <i class="icon-head menu-icon"></i>
                            <span class="menu-title">Resume Pengajuan</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>

                <!-- footer -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            © {{ date('Y') }}. All rights reserved.
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- plugins:js -->
    <script src="/assets/plugin-admin/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->

    <!-- Plugin js for this page -->
    <script src="/assets/plugin-admin/vendors/chart.js/Chart.min.js"></script>
    <script src="/assets/plugin-admin/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="/assets/plugin-admin/js/dataTables.select.min.js"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="/assets/plugin-admin/js/off-canvas.js"></script>
    <script src="/assets/plugin-admin/js/hoverable-collapse.js"></script>
    <script src="/assets/plugin-admin/js/template.js"></script>
    <script src="/assets/plugin-admin/js/settings.js"></script>
    <script src="/assets/plugin-admin/js/todolist.js"></script>
    <!-- endinject -->

    <!-- Custom js for this page-->
    <script src="/assets/plugin-admin/js/dashboard.js"></script>
    <script src="/assets/plugin-admin/js/Chart.roundedBarCharts.js"></script>
    <!-- End custom js for this page-->

    @stack('scripts')
</body>

</html>
