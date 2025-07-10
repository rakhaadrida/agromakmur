<ul class="navbar-nav bg-gradient-primary sidebar toggled sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">{{ env('APP_INITIAL') }}</div>
        <div class="sidebar-brand-text mx-3">{{ env('APP_NAME') }}</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active sidebar-first-icon">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    @if(isUserSuperAdmin())
        <li class="nav-item sidebar-menu-icon" >
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApproval" aria-expanded="true" aria-controls="collapseApproval">
                <i class="fas fa-fw fa-check"></i>
                <span>Approval</span>
            </a>
            <div id="collapseApproval" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#">Butuh Approval</a>
                    <a class="collapse-item" href="#">Histori Approval</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserAdminOnly())
        <li class="nav-item sidebar-menu-icon" >
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserWarehouse())
        <li class="nav-item sidebar-menu-icon" >
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-warehouse"></i>
                <span>Stok</span>
            </a>
        </li>
    @endif

    @if(isUserAdmin())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Sales and Purchases
        </div>
        <li class="nav-item sidebar-first-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-folder"></i>
                <span>Master</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    @if(isUserSuperAdmin())
                        <a class="collapse-item" href="{{ route('users.index') }}">User</a>
                    @endif
                    <a class="collapse-item" href="{{ route('marketings.index') }}">Marketing</a>
                    <a class="collapse-item" href="{{ route('suppliers.index') }}">Supplier</a>
                    <a class="collapse-item" href="#">Customer</a>
                    <a class="collapse-item" href="#">Warehouse</a>
                    <a class="collapse-item" href="#">Price</a>
                    <a class="collapse-item" href="#">Category</a>
                    <a class="collapse-item" href="#">Sub Category</a>
                    <a class="collapse-item" href="#">Item</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePembelian" aria-expanded="true" aria-controls="collapsePembelian">
                <i class="fas fa-fw fa-shopping-cart"></i>
                <span>Purchase</span>
            </a>
            <div id="collapsePembelian" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#">Penerimaan Barang</a>
                    <a class="collapse-item" href="#">Cetak Barang Masuk</a>
                    <a class="collapse-item" href="#">Ubah Barang Masuk</a>
                    <a class="collapse-item" href="#">Barang Masuk Harian</a>
                    <a class="collapse-item" href="#">Transfer Barang</a>
                    <a class="collapse-item" href="#">Cetak Transfer Barang</a>
                    <a class="collapse-item" href="#">Data Transfer Barang</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenjualan" aria-expanded="true" aria-controls="collapsePenjualan">
                <i class="fas fa-fw fa-shipping-fast"></i>
                <span>Sales</span>
            </a>
            <div id="collapsePenjualan" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#">Input Faktur</a>
                    <a class="collapse-item" href="#">Cetak Faktur</a>
                    <a class="collapse-item" href="#">Ubah Faktur</a>
                    <a class="collapse-item" href="#">Cetak Tanda Terima</a>
                    <a class="collapse-item" href="#">Data Tanda Terima</a>
                    <a class="collapse-item" href="#">Transaksi Harian</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserFinance())
        <li class="nav-item sidebar-menu-icon" >
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-warehouse"></i>
                <span>Stock</span>
            </a>
        </li>
    @endif

    <li class="nav-item sidebar-menu-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRetur" aria-expanded="true" aria-controls="collapseRetur">
          <i class="fas fa-fw fa-recycle"></i>
          <span>Return</span>
        </a>
        <div id="collapseRetur" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                @if(isUserAdmin() || isUserWarehouse())
                    <a class="collapse-item" href="#">Stok Retur</a>
                @endif
                <a class="collapse-item" href="#">Retur Customer</a>
                <a class="collapse-item" href="#">Retur Supplier</a>
            </div>
        </div>
    </li>

    @if(isUserAdmin())
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Report</span>
            </a>
            <div id="collapseLaporan" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#">Price List</a>
                    <a class="collapse-item" href="#">Penjualan Extrana</a>
                    <a class="collapse-item" href="#">Barang Masuk</a>
                    <a class="collapse-item" href="#">Barang Keluar</a>
                    <a class="collapse-item" href="#">Kartu Stok</a>
                    <a class="collapse-item" href="#">Rekap Stok</a>
                    <a class="collapse-item" href="#">Rekap Value</a>
                    <a class="collapse-item" href="#">Rekap Penjualan</a>
                    @if(isUserSuperAdmin())
                        <a class="collapse-item" href="#">Rekap Qty Sales</a>
                    @endif
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserSuperAdmin() || isUserFinance())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Finance
        </div>
        <li class="nav-item sidebar-first-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i>
                <span>Account Receivable</span>
            </a>
            <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="#">Data AR</a>
                    <a class="collapse-item" href="#">Cek Faktur</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Account Payable</span>
            </a>
        </li>
    @endif

    @if(isUserSuperAdmin())
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporanFinance" aria-expanded="true" aria-controls="collapseLaporanFinance">
                <i class="fas fa-fw fa-table"></i>
                <span>Report</span>
            </a>
            <div id="collapseLaporanFinance" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                  <a class="collapse-item" href="#">Laporan Keuangan</a>
                  <a class="collapse-item" href="#">Komisi Sales</a>
                  <a class="collapse-item" href="#">Program Prime</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserSuperAdmin() || isUserFinance())
        <hr class="sidebar-divider d-none d-md-block">
    @endif

    <div class="text-center d-none d-md-inline sidebar-arrow-icon">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
