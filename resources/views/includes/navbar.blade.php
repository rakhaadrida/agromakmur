<nav class="navbar navbar-expand navbar-light bg-white topbar mb-3 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        @if(isUserAdmin())
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
        @if(Auth::user()->roles == 'SUPER')
          @php
            $items = \App\Models\NeedApproval::groupBy('id_dokumen')->get();
          @endphp
        @elseif((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'AP'))
          @php
            $so = \App\Models\SalesOrder::with(['customer'])
                ->select('id', 'status', 'id_customer')
                ->whereIn('status', ['UPDATE', 'BATAL', 'APPROVE_LIMIT'])
                ->whereHas('approval', function($q) {
                    $q->where('baca', 'F');
                })->get();
          $bm = \App\Models\BarangMasuk::with(['supplier'])
                  ->select('id', 'id_supplier', 'status')
                  ->whereIn('status', ['UPDATE', 'BATAL'])
                  ->whereHas('approval', function($q) {
                      $q->where('baca', 'F');
                  })->get();

          $items = $so->merge($bm);
          $items = $items->sortBy(function($sort) {
              return $sort->approval[0]->created_at;
          });
          @endphp
        @endif
        <span class="badge badge-danger badge-counter">{{ $items->count() }}</span>
      </a>
      <!-- Dropdown - Alerts -->
      <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
          Notifikasi
        </h6>
        @php $row = 1; @endphp
        @if($items->count() != 0)
          @foreach($items as $item)
            @if($row <= 5)
              <a class="dropdown-item d-flex align-items-center"
              @if(Auth::user()->roles == 'SUPER')
                href="{{ route('app-show', $item->id_dokumen) }}"
              @elseif((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'KENARI'))
                href="{{ route('notif-show', $item->id) }}"
              @endif
              >
                <div class="mr-3">
                  <div class="icon-circle bg-primary" style="margin-left: -10px">
                    <i class="fas fa-file-alt text-white"></i>
                  </div>
                </div>
                <div>
                  @if(Auth::user()->roles == 'SUPER')
                    <div class="small text-dark-500 text-bold">
                      {{ \Carbon\Carbon::parse($item->tgl_so)->format('d-M-y') }}
                    </div>
                    @if($item->status != "PENDING_LIMIT")
                      <span class="font-weight-bold">
                        Perubahan @if($item->status == "PENDING_UPDATE") isi detail @elseif($item->status == "PENDING_BATAL") status @endif pada {{ $item->tipe }} {{ $item->id_dokumen }}
                      </span>
                    @else
                      <span class="font-weight-bold">
                        Customer {{ $item->so->customer->nama }} melebihi limit pada faktur {{ $item->id_dokumen }}
                      </span>
                    @endif
                  @elseif((Auth::user()->roles == 'ADMIN') || (Auth::user()->roles == 'AR') || (Auth::user()->roles == 'KENARI'))
                    <div class="small text-dark-600">
                      {{ \Carbon\Carbon::parse($item->approval[0]->tanggal)->format('d-M-y') }}
                    </div>
                    @if($item->status != "APPROVE_LIMIT")
                      <span class="font-weight-bold">
                        Perubahan @if($item->status == "UPDATE") <b>detail</b> @elseif($item->status == "BATAL") <b>status BATAL</b> pada @endif {{ $item->approval[0]->tipe }} <b>{{ $item->id }}</b> telah di disetujui.@if(($item->approval[0]->tipe == 'Faktur') && ($item->status != 'BATAL')) Silahkan cetak faktur. @endif
                      </span>
                    @else
                      <span class="font-weight-bold">
                        Kelebihan limit pada Faktur <b>{{ $item->id }}</b> telah disetujui. Silahkan cetak faktur.
                      </span>
                    @endif
                  @endif
                </div>
              </a>
              @php $row++; @endphp
            @endif
          @endforeach
          <a class="dropdown-item text-center medium text-dark-600" href="@if(Auth::user()->roles == 'SUPER') {{ route('approval') }} @else {{ route('notif') }} @endif">Tampilkan Semua  Notifikasi</a>
        @else
          <a class="dropdown-item text-center medium text-dark-600" href="#">Tidak Ada Notifikasi</a>
        @endif
      </div>
    </li>
    @endif

        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('user-change') }}">
                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                    Ganti Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
