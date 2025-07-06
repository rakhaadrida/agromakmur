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

  @if(Auth::user()->roles == 'SUPER')
  <li class="nav-item sidebar-menu-icon" >
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseApproval" aria-expanded="true" aria-controls="collapseApproval">
      <i class="fas fa-fw fa-check"></i>
      <span>Approval</span>
    </a>
    <div id="collapseApproval" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
      <div class="py-2 collapse-inner rounded">
        <a class="collapse-item" href="{{ route('approval') }}">Butuh Approval</a>
        <a class="collapse-item" href="{{ route('app-histori') }}">Histori Approval</a>
      </div>
    </div>
  </li>
  @endif

  @if((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'KENARI'))
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" @if(Auth::user()->roles == 'ADMIN') href="{{ route('notif') }}" @else href="{{ route('notif-kenari') }}" @endif>
        <i class="fas fa-fw fa-bell"></i>
        <span>Notifikasi</span></a>
    </li>
  @endif

  @if(Auth::user()->roles == 'OFFICE02')
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" href="{{ route('stok-office') }}">
        <i class="fas fa-fw fa-warehouse"></i>
        <span>Stok</span></a>
    </li>
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" href="{{ route('ar') }}">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Penjualan</span></a>
    </li>
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" href="{{ route('lap-keu') }}">
        <i class="fas fa-fw fa-table"></i>
        <span>Laporan</span></a>
    </li>
  @endif

  @if(Auth::user()->roles == 'KENARI')
    <hr class="sidebar-divider">
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" href="{{ route('stok-kenari') }}">
        <i class="fas fa-fw fa-warehouse"></i>
        <span>Stok</span></a>
    </li>
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTbKenari" aria-expanded="true" aria-controls="collapseTbKenari">
        <i class="fas fa-fw fa-shopping-cart"></i>
        <span>Transfer</span>
      </a>
      <div id="collapseTbKenari" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('tb', 'false') }}">Input Transfer</a>
            <a class="collapse-item" href="{{ route('tb-index') }}">Data Transfer</a>
          </div>
        </div>
    </li>
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKenari" aria-expanded="true" aria-controls="collapseKenari">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Penjualan</span>
      </a>
      <div id="collapseKenari" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('so-kenari', 'false') }}">Input Faktur</a>
          <a class="collapse-item" href="{{ route('cetak-faktur-kenari', ['status' => 'false', 'awal' => '0', 'akhir' => '0']) }}">Cetak Faktur</a>
          <a class="collapse-item" href="{{ route('so-change-kenari') }}">Ubah Faktur</a>
          <a class="collapse-item" href="{{ route('trans-kenari') }}">Transaksi Harian</a>
        </div>
      </div>
    </li>
  @endif

  <!-- Divider -->
  <hr class="sidebar-divider">

  @if(Auth::user()->roles == 'GUDANG')
    <li class="nav-item sidebar-menu-icon" >
      <a class="nav-link" href="{{ route('stok-office') }}">
        <i class="fas fa-fw fa-warehouse"></i>
        <span>Stok</span></a>
    </li>
  @endif

  @if((Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'GUDANG') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'AP'))
    @if((Auth::user()->roles != 'GUDANG') && (Auth::user()->roles != 'AR') && (Auth::user()->roles != 'AP'))
      <!-- Heading Sales and Purchases -->
      <div class="sidebar-heading sidebar-heading-title text-white">
        Sales and Purchases
      </div>

      <!-- Nav Item - Master Menu -->
      <li class="nav-item sidebar-first-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
          <i class="fas fa-fw fa-folder"></i>
          <span>Master</span>
        </a>
        <div id="collapseMaster" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            @if(Auth::user()->roles == 'SUPER')
              <a class="collapse-item" href="{{ route('user.index') }}">User</a>
            @endif
            <a class="collapse-item" href="{{ route('supplier.index') }}">Supplier</a>
            <a class="collapse-item" href="{{ route('sales.index') }}">Sales</a>
            <a class="collapse-item" href="{{ route('customer.index') }}">Customer</a>
            <a class="collapse-item" href="{{ route('jenis.index') }}">Jenis Barang</a>
            <a class="collapse-item" href="{{ route('subjenis.index') }}">Sub Jenis Barang</a>
            <a class="collapse-item" href="{{ route('barang.index') }}">Barang</a>
            <a class="collapse-item" href="{{ route('harga.index') }}">Harga</a>
            <a class="collapse-item" href="{{ route('gudang.index') }}">Gudang</a>
          </div>
        </div>
      </li>

      <!-- Nav Item - Pembelian Menu -->
      <li class="nav-item sidebar-menu-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePembelian" aria-expanded="true" aria-controls="collapsePembelian">
          <i class="fas fa-fw fa-shopping-cart"></i>
          <span>Pembelian</span>
        </a>
        <div id="collapsePembelian" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            {{-- <a class="collapse-item" href="{{ route('po') }}">Purchase Order</a> --}}
            <a class="collapse-item" href="{{ route('barangMasuk', 'false')}}">Penerimaan Barang</a>
            <a class="collapse-item" href="{{ route('cetak-bm', ['status' => 'false', 'awal' => '0', 'akhir' => '0']) }}">Cetak Barang Masuk</a>
            <a class="collapse-item" href="{{ route('bm-change')}}">Ubah Barang Masuk</a>
            <a class="collapse-item" href="{{ route('bm-harian')}}">Barang Masuk Harian</a>
            <a class="collapse-item" href="{{ route('tb', 'false') }}">Transfer Barang</a>
            <a class="collapse-item" href="{{ route('cetak-tb', ['status' => 'false', 'awal' => '0', 'akhir' => '0']) }}">Cetak Transfer Barang</a>
            <a class="collapse-item" href="{{ route('tb-index') }}">Data Transfer Barang</a>
          </div>
        </div>
      </li>

      <!-- Nav Item - Penjualan Menu -->
      <li class="nav-item sidebar-menu-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenjualan" aria-expanded="true" aria-controls="collapsePenjualan">
          <i class="fas fa-fw fa-shipping-fast"></i>
          <span>Penjualan</span>
        </a>
        <div id="collapsePenjualan" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('so', 'false') }}">Input Faktur</a>
            <a class="collapse-item" href="{{ route('cetak-faktur', ['status' => 'false', 'awal' => '0', 'akhir' => '0']) }}">Cetak Faktur</a>
            <a class="collapse-item" href="{{ route('so-change') }}">Ubah Faktur</a>
            <a class="collapse-item" href="{{route('ttr-index-cetak', ['status' => 'false', 'awal' => '0', 'akhir' => '0'])}}">Cetak Tanda Terima</a>
            <a class="collapse-item" href="{{ route('ttr') }}">Data Tanda Terima</a>
            <a class="collapse-item" href="{{ route('trans') }}">Transaksi Harian</a>
            {{-- <a class="collapse-item" data-toggle="collapse" data-target="#collapseRetur" aria-expanded="true" aria-controls="collapseRetur">Retur Barang</a>
              <div id="collapseRetur" class="collapse" aria-labelledby="headingUtilities" data-parent="#collapsePenjualan">
                <div class="py-2 collapse-sub-inner rounded">
                  <a class="collapse-item" href="{{ route('retur-stok') }}">Input Retur</a>
                  <a class="collapse-item" href="{{ route('retur-jual') }}">Retur Penjualan</a>
                  <a class="collapse-item" href="{{ route('retur-beli') }}">Retur Pembelian</a>
                </div>
              </div> --}}
            {{-- <a class="collapse-item" href="{{ route('sj') }}">Surat Jalan</a> --}}
          </div>
        </div>
      </li>
    @endif

    @if(Auth::user()->roles == 'AR')
      <li class="nav-item sidebar-menu-icon" >
        <a class="nav-link" href="{{ route('stok-office') }}">
          <i class="fas fa-fw fa-warehouse"></i>
          <span>Stok</span></a>
      </li>
    @endif

      <!-- Nav Item - Retur Menu -->
      <li class="nav-item sidebar-menu-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRetur" aria-expanded="true" aria-controls="collapseRetur">
          <i class="fas fa-fw fa-recycle"></i>
          <span>Retur</span>
        </a>
        <div id="collapseRetur" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            @if((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'GUDANG'))
              <a class="collapse-item" href="{{ route('retur-stok') }}">Stok Retur</a>
            @endif
            @if((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'GUDANG'))
              <a class="collapse-item" href="{{ route('retur-jual', ['status' => 'false', 'id'=> '0']) }}">Retur Customer</a>
            @endif
            @if((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AP') || (Auth::user()->roles == 'GUDANG'))
              <a class="collapse-item" href="{{ route('retur-beli', ['status' => 'false', 'id'=> '0']) }}">Retur Supplier</a>
            @endif
          </div>
        </div>
      </li>

    @if((Auth::user()->roles != 'GUDANG') && (Auth::user()->roles != 'AR') && (Auth::user()->roles != 'AP'))
      <!-- Nav Item - Laporan Menu -->
      <li class="nav-item sidebar-menu-icon">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Laporan</span>
        </a>
        <div id="collapseLaporan" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('price') }}">Price List</a>
            <a class="collapse-item" href="{{ route('extrana') }}">Penjualan Extrana</a>
            <a class="collapse-item" href="{{ route('bmk') }}">Barang Masuk</a>
            <a class="collapse-item" href="{{ route('bk') }}">Barang Keluar</a>
            <a class="collapse-item" href="{{ route('kartu') }}">Kartu Stok</a>
            <a class="collapse-item" href="{{ route('rekap') }}">Rekap Stok</a>
            <a class="collapse-item" href="{{ route('value') }}">Rekap Value</a>
            <a class="collapse-item" href="{{ route('lap-keu') }}">Rekap Penjualan</a>
            @if(Auth::user()->roles == 'SUPER')
              <a class="collapse-item" href="{{ route('qty-sales') }}">Rekap Qty Sales</a>
            @endif
          </div>
        </div>
      </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

  @endif

  @if((Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'AP'))
  <!-- Heading -->
  <div class="sidebar-heading sidebar-heading-title text-white">
    Finance
  </div>
  @endif

  @if((Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AR'))
    <!-- Nav Item - Account Receivable -->
    <li class="nav-item sidebar-first-icon">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-folder"></i>
        <span>Account Receivable</span>
      </a>
      <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('ar') }}">Data AR</a>
          <a class="collapse-item" href="{{ route('so-change') }}">Cek Faktur</a>
        </div>
      </div>
    </li>
  @endif

  @if((Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AP'))
    <!-- Nav Item - Account Payable -->
    <li class="nav-item @if(Auth::user()->roles == 'SUPER') sidebar-menu-icon @else sidebar-first-icon @endif">
      <a class="nav-link" href="{{ route('ap') }}">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Account Payable</span></a>
    </li>
  @endif

  @if(Auth::user()->roles == 'SUPER')
    <!-- Nav Item - Laporan Keuangan -->
    <li class="nav-item sidebar-menu-icon">
      {{-- <a class="nav-link" href="{{ route('lap-keu') }}">
        <i class="fas fa-fw fa-table"></i>
        <span>Laporan</span>
      </a> --}}
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporanFinance" aria-expanded="true" aria-controls="collapseLaporanFinance">
        <i class="fas fa-fw fa-table"></i>
        <span>Laporan</span>
      </a>
      <div id="collapseLaporanFinance" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('lap-keu') }}">Laporan Keuangan</a>
          <a class="collapse-item" href="{{ route('komisi') }}">Komisi Sales</a>
          <a class="collapse-item" href="{{ route('prime') }}">Program Prime</a>
        </div>
      </div>
    </li>
  @endif

  @if((Auth::user()->roles == 'SUPER') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'AP'))
  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">
  @endif

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline sidebar-arrow-icon">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
<!-- End of Sidebar -->
