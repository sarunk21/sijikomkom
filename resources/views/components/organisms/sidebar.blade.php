<!-- Sidebar -->
<ul class="navbar-nav bg-upnvj sidebar sidebar-dark accordion" id="accordionSidebar">
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center justify-content-center" href="">
            <img src="{{ asset('img/logo.png') }}" alt="logo" width="120">
        </a>
    </li>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    @yield('sidebar-menu')

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
