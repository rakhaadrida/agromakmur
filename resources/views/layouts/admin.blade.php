<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>{{ env('APP_NAME') }}</title>

        @stack('prepend-style')
        @include('includes.style')
        @stack('addon-style')
    </head>

    <body id="page-top" class="sidebar-toggled">
        <div id="wrapper">
            @include('includes.sidebar')
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    @include('includes.navbar')
                    @yield('content')
                </div>
                @include('includes.footer')
            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Selesai Sesi?</h5>
                    </div>
                    <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                    <div class="modal-footer">
                        <form action="{{ url('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-primary" type="submit">Logout</button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modalPassword" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Masukkan Password Anda</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row input-password-modal-body">
                            <label for="password" class="col-3 col-form-label text-bold text-right">Password</label>
                            <span class="col-form-label text-bold">:</span>
                            <div class="col-6">
                                <input type="password" class="form-control col-form-label-sm" name="password" id="password" required autofocus>
                                <i class="far fa-eye password-eye-icon validate-password-eye-icon" id="togglePassword"></i>
                            </div>
                        </div>
                        <div class="form-group row input-password-modal-body">
                            <label class="col-3 col-form-label text-bold text-right"></label>
                            <span class="col-form-label text-bold"></span>
                            <div class="col-6">
                                <input type="hidden" id="targetRoute">
                                <strong><span class="invalid-feedback ml-2" role="alert" id="passwordErrorMessage"></span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="btnSubmitPassword">Simpan</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPasswordEdit" tabindex="-1" role="dialog" aria-labelledby="modalPassword" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Masukkan Password Anda</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row input-password-modal-body">
                            <label for="password" class="col-3 col-form-label text-bold text-right">Password</label>
                            <span class="col-form-label text-bold">:</span>
                            <div class="col-6">
                                <input type="password" class="form-control col-form-label-sm" name="password" id="passwordEdit" required autofocus>
                                <i class="far fa-eye password-eye-icon validate-password-eye-icon" id="togglePasswordEdit"></i>
                            </div>
                        </div>
                        <div class="form-group row input-password-modal-body">
                            <label class="col-3 col-form-label text-bold text-right"></label>
                            <span class="col-form-label text-bold"></span>
                            <div class="col-6">
                                <input type="hidden" id="subjectIndex">
                                <strong><span class="invalid-feedback ml-2" role="alert" id="passwordErrorMessageEdit"></span></strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="btnSubmitPasswordEdit">Simpan</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        @stack('prepend-script')
        @include('includes.script')
        @stack('addon-script')
    </body>
</html>
