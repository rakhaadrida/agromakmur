<ul class="navbar-nav bg-gradient-primary sidebar toggled sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">{{ env('APP_INITIAL') }}</div>
        <div class="sidebar-brand-text mx-3">{{ env('APP_NAME') }}</div>
    </a>
    <hr class="sidebar-divider my-0">
    @if(isUserSuperAdmin() || isUserSuperAdminBranch())
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getDashboardRoute()) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard-super-admin') }}">
                <i class="fas fa-fw fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @endif

    @if(isUserAdminOnly())
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getDashboardRoute()) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard-admin') }}">
                <i class="fas fa-fw fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @endif

    @if(isUserSuperAdmin() || isUserSuperAdminBranch())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getApprovalRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApproval" aria-expanded="true" aria-controls="collapseApproval">
                <i class="fas fa-fw fa-check"></i>
                <span>Approval</span>
            </a>
            <div id="collapseApproval" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('approvals.index') }}">Butuh Approval</a>
                    <a class="collapse-item" href="{{ route('approvals.index-history') }}">Histori Approval</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserAdminOnly())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserWarehouse())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('stocks.index') }}">
                <i class="fas fa-fw fa-warehouse"></i>
                <span>Stok</span>
            </a>
        </li>
    @endif

    @if(isUserAdmin())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Transaksi
        </div>
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getMasterRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-folder"></i>
                <span>Master</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    @if(isUserSuperAdmin() || isUserSuperAdminBranch())
                        <a class="collapse-item" href="{{ route('users.index') }}">User</a>
                    @endif
                    @if(isUserSuperAdmin())
                        <a class="collapse-item" href="{{ route('branches.index') }}">Cabang</a>
                    @endif
                    <a class="collapse-item" href="{{ route('marketings.index') }}">Sales</a>
                    <a class="collapse-item" href="{{ route('suppliers.index') }}">Supplier</a>
                    <a class="collapse-item" href="{{ route('customers.index') }}">Customer</a>
                    <a class="collapse-item" href="{{ route('warehouses.index') }}">Gudang</a>
                    <a class="collapse-item" href="{{ route('prices.index') }}">Harga</a>
                    <a class="collapse-item" href="{{ route('categories.index') }}">Kategori</a>
                    <a class="collapse-item" href="{{ route('units.index') }}">Unit</a>
                    <button class="collapse-item product-menu-button" id="menuProduct" href="{{ route('products.index') }}">Produk</button>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getPurchaseRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePurchase" aria-expanded="true" aria-controls="collapsePurchase">
                <i class="fas fa-fw fa-shopping-cart"></i>
                <span>Pembelian</span>
            </a>
            <div id="collapsePurchase" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item d-flex justify-content-between align-items-center" href="#" data-toggle="collapse" data-target="#collapsePlanOrder">
                        <span>Plan Order</span>
                        <i class="fas fa-angle-down"></i>
                    </a>
                    <div id="collapsePlanOrder" class="collapse collapse-inner rounded ml-3 mt-2">
                        <a class="collapse-item" href="{{ route('plan-orders.create') }}">Input Plan Order</a>
                        <a class="collapse-item" href="{{ route('plan-orders.index-print') }}">Cetak Plan Order</a>
                        <a class="collapse-item" href="{{ route('plan-orders.index') }}">Plan Order Harian</a>
                    </div>
                    <a class="collapse-item d-flex justify-content-between align-items-center border-bottom-0" href="#" data-toggle="collapse" data-target="#collapseGoodsReceipt">
                        <span>Barang Masuk</span>
                        <i class="fas fa-angle-down"></i>
                    </a>
                    <div id="collapseGoodsReceipt" class="collapse collapse-inner rounded ml-3 mt-5">
                        <a class="collapse-item" href="{{ route('goods-receipts.create') }}">Input Barang Masuk</a>
                        <a class="collapse-item" href="{{ route('goods-receipts.index-print') }}">Cetak Barang Masuk</a>
                        <a class="collapse-item" href="{{ route('goods-receipts.index') }}">Barang Masuk Harian</a>
                    </div>
                </div>
            </div>
        </li>
    @endif
    @if(isUserAdmin() || isUserSales())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getSalesRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSales" aria-expanded="true" aria-controls="collapseSales">
                <i class="fas fa-fw fa-shipping-fast"></i>
                <span>Penjualan</span>
            </a>
            <div id="collapseSales" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item d-flex justify-content-between align-items-center" href="#" data-toggle="collapse" data-target="#collapseSalesOrder">
                        <span>Sales Order</span>
                        <i class="fas fa-angle-down"></i>
                    </a>
                    <div id="collapseSalesOrder" class="collapse collapse-inner rounded ml-3 mt-2">
                        <a class="collapse-item" href="{{ route('sales-orders.create') }}">Input Sales Order</a>
                        <a class="collapse-item" href="{{ route('sales-orders.index-print') }}">Cetak Sales Order</a>
                        <a class="collapse-item" href="{{ route('sales-orders.index') }}">Sales Order Harian</a>
                    </div>
                    @if(!isUserSales())
                        <a class="collapse-item d-flex justify-content-between align-items-center border-bottom-0" href="#" data-toggle="collapse" data-target="#collapseDeliveryOrder">
                            <span>Surat Jalan</span>
                            <i class="fas fa-angle-down"></i>
                        </a>
                        <div id="collapseDeliveryOrder" class="collapse collapse-inner rounded ml-3 mt-5">
                            <a class="collapse-item" href="{{ route('delivery-orders.create') }}">Input Surat Jalan</a>
                            <a class="collapse-item" href="{{ route('delivery-orders.index-print') }}">Cetak Surat Jalan</a>
                            <a class="collapse-item" href="{{ route('delivery-orders.index') }}">Surat Jalan Harian</a>
                        </div>
                    @endif
                </div>
            </div>
        </li>
    @endif

    @if(!isUserSales())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getReturnRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReturn" aria-expanded="true" aria-controls="collapseReturn">
              <i class="fas fa-fw fa-recycle"></i>
              <span>Retur</span>
            </a>
            <div id="collapseReturn" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('sales-returns.index') }}">Retur Penjualan</a>
                    <a class="collapse-item" href="{{ route('purchase-returns.index') }}">Retur Pembelian</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserAdmin())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getProductReportRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProductReport" aria-expanded="true" aria-controls="collapseProductReport">
                <i class="fas fa-fw fa-boxes"></i>
                <span>Laporan Produk</span>
            </a>
            <div id="collapseProductReport" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('report.product-histories.index') }}">Histori Produk</a>
                    <a class="collapse-item" href="{{ route('report.low-stocks.index') }}">Stok Rendah</a>
                    <a class="collapse-item" href="{{ route('report.stock-cards.index') }}">Kartu Stok</a>
                    <a class="collapse-item" href="{{ route('report.incoming-items.index') }}">Barang Masuk</a>
                    <a class="collapse-item" href="{{ route('report.outgoing-items.index') }}">Barang Keluar</a>
                    <button class="collapse-item price-list-menu-button" id="menuReportPriceList" href="{{ route('report.price-lists.index') }}">Daftar Harga</button>
                    <button class="collapse-item price-list-menu-button" id="menuReportStockRecap" href="{{ route('report.stock-recap.index') }}">Rekap Stok</button>
                    <a class="collapse-item" href="{{ route('report.value-recap.index') }}">Rekap Value</a>
                    @if(isUserSuperAdmin() || isUserSuperAdminBranch())
                        <a class="collapse-item" href="{{ route('report.marketing-recap.index') }}">Rekap Qty Sales</a>
                    @endif
                </div>
            </div>
        </li>

        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getTransactionReportRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransactionReport" aria-expanded="true" aria-controls="collapseTransactionReport">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Laporan Transaksi</span>
            </a>
            <div id="collapseTransactionReport" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('report.sales-recap.index') }}">Rekap Penjualan</a>
                    <a class="collapse-item" href="{{ route('report.purchase-recap.index') }}">Rekap Pembelian</a>
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserSuperAdmin() || isUserSuperAdminBranch() || isUserFinance())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Keuangan
        </div>
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getReceivableRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReceivables" aria-expanded="true" aria-controls="collapseReceivables">
                <i class="fas fa-fw fa-folder"></i>
                <span>Piutang</span>
            </a>
            <div id="collapseReceivables" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('account-receivables.index') }}">Daftar Piutang</a>
                    <a class="collapse-item" href="{{ route('account-receivables.check-invoice') }}">Cek Faktur</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('account-payables.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('account-payables.index') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Hutang</span>
            </a>
        </li>
    @endif

    @if(isUserSuperAdmin() || isUserSuperAdminBranch() || isUserFinance())
        <hr class="sidebar-divider d-none d-md-block">
    @endif

    <div class="text-center d-none d-md-inline sidebar-arrow-icon">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
