<!DOCTYPE html>
<html>
<head>
@include('partials.head')
<!-- Custom CSS -->
    @yield('header-resource')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Topbar -->
@include('navigation.topbar')
<!-- Topbar -->

    <!-- Main Sidebar Container -->
@include('navigation.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Breadcrumb -->
    @yield('breadcrumb')
    <!-- Breadcrumb -->

        <!-- Main content -->
    @yield('content')
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- footer -->
    @include('partials.footer')
</div>
<!-- ./wrapper -->
@include('partials.footer-script')

@yield('script')
</body>
</html>
