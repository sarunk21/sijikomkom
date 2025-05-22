<!-- Sidebar -->
<ul class="navbar-nav bg-upnvj sidebar sidebar-dark accordion" id="accordionSidebar">
    <li class="nav-item mb-3">
        <a class="nav-link d-flex align-items-center justify-content-center" href="">
            <img src="{{ asset('img/logo.png') }}" alt="logo" width="120">
        </a>
    </li>

    @each('components.molecules.list', $lists, 'item')

</ul>
<!-- End of Sidebar -->
