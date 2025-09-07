<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-text mx-3">Pengaduan Masyarakat</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item {{ request()->is('admin/resident*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.resident.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Masyarakat</span></a>
    </li>
    <li class="nav-item {{ request()->is('admin/report-category*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report-category.index') }}">
            <i class="fas fa-fw fa-folder"></i>
            <span>Data Kategori</span></a>
    </li>


    <li
        class="nav-item {{ request()->is('admin/report*') && !request()->is('admin/report-category*') && !request()->is('admin/report-export*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Data Laporan</span></a>
    </li>

    <li class="nav-item {{ request()->is('admin/report-export*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.export') }}">
            <i class="fas fa-fw fa-file-export"></i>
            <span>Export Laporan</span></a>
    </li>



</ul>