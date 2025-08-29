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
                    <span class="badge badge-danger badge-counter">{{ \Illuminate\Support\Facades\Auth::user()->totalUnreadNotification }}</span>
                </a>

                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Notification
                    </h6>
                    @forelse($notificationLists as $notification)
                        <a href="{{ route('approvals.detail', $notification->data['approval_id']) }}" class="dropdown-item d-flex align-items-center">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary" style="margin-left: -10px">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-dark-600">{{ formatDate($notification->created_at, 'd M y h:i:s') }}</div>
                                <span class="small font-weight-bold">{{ $notification->data['message'] }}</span>
                            </div>
                        </a>
                    @empty
                        <a class="dropdown-item text-center text-dark-600" href="#">No Notifications</a>
                    @endforelse
                    @if($notificationLists->count() > 0)
                        @if(isUserSuperAdmin())
                            <a class="dropdown-item text-center small text-dark-600" href="{{ route('approvals.index') }}">See All Notifications</a>
                        @elseif(isUserAdminOnly())
                            <a class="dropdown-item text-center small text-dark-600" href="{{ route('notifications.index') }}">See All Notifications</a>
                        @endif
                    @endif
                </div>
            </li>
        @endif

        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->username }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
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
