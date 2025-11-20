<ul class="navbar-nav bg-gradient-primary sidebar toggled sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">{{ env('APP_INITIAL') }}</div>
        <div class="sidebar-brand-text mx-3">{{ env('APP_NAME') }}</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item sidebar-first-icon {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    @if(isUserSuperAdmin())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getApprovalRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApproval" aria-expanded="true" aria-controls="collapseApproval">
                <i class="fas fa-fw fa-check"></i>
                <span>Approval</span>
            </a>
            <div id="collapseApproval" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('approvals.index') }}">Need Approval</a>
                    <a class="collapse-item" href="{{ route('approvals.index-history') }}">Approval History</a>
                </div>
            </div>
        </li>
    @endif

    @if(isUserAdminOnly())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notification</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserWarehouse())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('stocks.index') }}">
                <i class="fas fa-fw fa-warehouse"></i>
                <span>Stock</span>
            </a>
        </li>
    @endif

    @if(isUserAdmin())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Sales and Purchases
        </div>
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getMasterRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-folder"></i>
                <span>Master</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    @if(isUserSuperAdmin())
                        <a class="collapse-item" href="{{ route('users.index') }}">User</a>
                        <a class="collapse-item" href="{{ route('branches.index') }}">Branch</a>
                    @endif
                    <a class="collapse-item" href="{{ route('marketings.index') }}">Marketing</a>
                    <a class="collapse-item" href="{{ route('suppliers.index') }}">Supplier</a>
                    <a class="collapse-item" href="{{ route('customers.index') }}">Customer</a>
                    <a class="collapse-item" href="{{ route('warehouses.index') }}">Warehouse</a>
                    <a class="collapse-item" href="{{ route('prices.index') }}">Price</a>
                    <a class="collapse-item" href="{{ route('categories.index') }}">Category</a>
                    <a class="collapse-item" href="{{ route('subcategories.index') }}">Sub Category</a>
                    <a class="collapse-item" href="{{ route('units.index') }}">Unit</a>
                    <button class="collapse-item product-menu-button" id="menuProduct" href="{{ route('products.index') }}">Product</button>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getPurchaseRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePurchase" aria-expanded="true" aria-controls="collapsePurchase">
                <i class="fas fa-fw fa-shopping-cart"></i>
                <span>Purchase</span>
            </a>
            <div id="collapsePurchase" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('plan-orders.create') }}">Plan Order</a>
                    <a class="collapse-item" href="{{ route('plan-orders.index-print') }}">Print Plan Order</a>
                    <a class="collapse-item" href="{{ route('plan-orders.index') }}">Daily Plan Order</a>
                    <a class="collapse-item" href="{{ route('goods-receipts.create') }}">Goods Receipt</a>
                    <a class="collapse-item" href="{{ route('goods-receipts.index-print') }}">Print Goods Receipt</a>
                    <a class="collapse-item" href="{{ route('goods-receipts.index-edit') }}">Edit Goods Receipt</a>
                    <a class="collapse-item" href="{{ route('goods-receipts.index') }}">Daily Goods Receipt</a>
                    <a class="collapse-item" href="{{ route('product-transfers.create') }}">Product Transfer</a>
                    <a class="collapse-item" href="{{ route('product-transfers.index-print') }}">Print Product Transfer</a>
                    <a class="collapse-item" href="{{ route('product-transfers.index') }}">List Product Transfer</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getSalesRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSales" aria-expanded="true" aria-controls="collapseSales">
                <i class="fas fa-fw fa-shipping-fast"></i>
                <span>Sales</span>
            </a>
            <div id="collapseSales" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('sales-orders.create') }}">Sales Order</a>
                    <a class="collapse-item" href="{{ route('sales-orders.index-print') }}">Print Sales Order</a>
                    <a class="collapse-item" href="{{ route('sales-orders.index-edit') }}">Edit Sales Order</a>
                    <a class="collapse-item" href="{{ route('sales-orders.index') }}">Daily Sales Order</a>
                    <a class="collapse-item" href="{{ route('delivery-orders.create') }}">Delivery Order</a>
                    <a class="collapse-item" href="{{ route('delivery-orders.index-print') }}">Print Delivery Order</a>
                    <a class="collapse-item" href="{{ route('delivery-orders.index-edit') }}">Edit Delivery Order</a>
                    <a class="collapse-item" href="{{ route('delivery-orders.index') }}">Daily Delivery Order</a>
                </div>
            </div>
        </li>
    @endif

    <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getReturnRoute()) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReturn" aria-expanded="true" aria-controls="collapseReturn">
          <i class="fas fa-fw fa-recycle"></i>
          <span>Return</span>
        </a>
        <div id="collapseReturn" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                @if(isUserAdmin() || isUserWarehouse())
                    <a class="collapse-item" href="{{ route('returns.index') }}">Return Stock</a>
                @endif
                <a class="collapse-item" href="{{ route('sales-returns.index') }}">Sales Return</a>
                <a class="collapse-item" href="{{ route('purchase-returns.index') }}">Purchase Return</a>
            </div>
        </div>
    </li>

    @if(isUserAdmin())
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getProductReportRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProductReport" aria-expanded="true" aria-controls="collapseProductReport">
                <i class="fas fa-fw fa-boxes"></i>
                <span>Product Report</span>
            </a>
            <div id="collapseProductReport" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('report.product-histories.index') }}">Product History</a>
                    <a class="collapse-item" href="{{ route('report.low-stocks.index') }}">Low Stock</a>
                    <a class="collapse-item" href="{{ route('report.stock-cards.index') }}">Stock Card</a>
                    <a class="collapse-item" href="{{ route('report.incoming-items.index') }}">Incoming Items</a>
                    <a class="collapse-item" href="{{ route('report.outgoing-items.index') }}">Outgoing Items</a>
                    <button class="collapse-item price-list-menu-button" id="menuReportPriceList" href="{{ route('report.price-lists.index') }}">Price List</button>
                    <button class="collapse-item price-list-menu-button" id="menuReportStockRecap" href="{{ route('report.stock-recap.index') }}">Stock Recap</button>
                    <a class="collapse-item" href="{{ route('report.value-recap.index') }}">Value Recap</a>
                    @if(isUserSuperAdmin())
                        <a class="collapse-item" href="{{ route('report.marketing-recap.index') }}">Marketing Qty Recap</a>
                    @endif
                </div>
            </div>
        </li>

        <li class="nav-item sidebar-menu-icon {{ request()->routeIs(getTransactionReportRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransactionReport" aria-expanded="true" aria-controls="collapseTransactionReport">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Transaction Report</span>
            </a>
            <div id="collapseTransactionReport" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('report.sales-recap.index') }}">Sales Recap</a>
                    <a class="collapse-item" href="{{ route('report.purchase-recap.index') }}">Purchase Recap</a>
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider">

    @if(isUserSuperAdmin() || isUserFinance())
        <div class="sidebar-heading sidebar-heading-title text-white">
            Finance
        </div>
        <li class="nav-item sidebar-first-icon {{ request()->routeIs(getReceivableRoute()) ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReceivables" aria-expanded="true" aria-controls="collapseReceivables">
                <i class="fas fa-fw fa-folder"></i>
                <span>Account Receivable</span>
            </a>
            <div id="collapseReceivables" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('account-receivables.index') }}">Receivable List</a>
                    <a class="collapse-item" href="{{ route('account-receivables.check-invoice') }}">Check Invoice</a>
                </div>
            </div>
        </li>
        <li class="nav-item sidebar-menu-icon {{ request()->routeIs('account-payables.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('account-payables.index') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Account Payable</span>
            </a>
        </li>
    @endif

    @if(isUserSuperAdmin())
        <li class="nav-item sidebar-menu-icon">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinancialReport" aria-expanded="true" aria-controls="collapseFinancialReport">
                <i class="fas fa-fw fa-table"></i>
                <span>Report</span>
            </a>
            <div id="collapseFinancialReport" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="py-2 collapse-inner rounded">
                  <a class="collapse-item" href="#">Financial Statements</a>
                  <a class="collapse-item" href="#">Marketing Commission</a>
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
