@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-dark text-bold">Tambah User Baru</div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST" role="form">
                            @csrf
                            <div class="form-group row">
                                <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" value="{{ old('username') }}" data-toogle="tooltip" data-placement="bottom" title="No spaces allowed" required autofocus>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="passwordUser" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control @error('password') is-invalid user-invalid-no-icon @enderror" name="password" id="passwordUser" required>
                                    <i class="far fa-eye password-eye-icon" id="togglePasswordUser"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="passwordConfirmationUser" class="col-md-4 col-form-label text-md-right">Konfirmasi Password</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control @error('password') is-invalid user-invalid-no-icon @enderror" name="password_confirmation" id="passwordConfirmationUser" required>
                                    <i class="far fa-eye password-eye-icon" id="togglePasswordConfirmationUser"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">Peran</label>
                                <div class="col-md-6">
                                    <select class="custom-select mr-sm-2" name="role" id="role">
                                        @foreach($userRoles as $key => $userRole)
                                            <option value="{{ $key }}">{{ $userRole }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="branchIds" class="col-md-4 col-form-label text-md-right">Cabang</label>
                                <div class="col-md-6">
                                    <select class="selectpicker custom-select-picker" name="branch_ids[]" id="branchIds" data-live-search="true" data-selected-text-format="count > 3" multiple @if(isUserSuperAdmin()) disabled @endif>
                                        @foreach($branches as $key => $branch)
                                            <option value="{{ $branch->id }}" data-tokens="{{ $branch->name }}" @if(isUserSuperAdminBranch() && !$key) selected @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_ids[]')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-auto offset-md-4">
                                    <button type="submit" class="btn btn-primary text-bold">Simpan</button>
                                </div>
                                <div class="col-md-2 user-button-cancel">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-danger text-bold">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#username').on('keydown', function(event) {
                if (event.which === 32) {
                    $('#username').tooltip('show');
                    event.preventDefault();
                }
            });

            $('#branchIds').selectpicker();

            $('#role').on('change', function(event) {
                event.preventDefault();

                const selected = $(this).find(':selected');
                let branchIds = $('#branchIds');

                if(selected.val() === 'SUPER_ADMIN' ) {
                    branchIds.prop('disabled', true);
                    branchIds.prop('required', false);
                    branchIds.selectpicker('deselectAll');
                } else {
                    branchIds.prop('disabled', false);
                    branchIds.prop('required', true);
                }

                branchIds.selectpicker('refresh');
            });
        });

        const togglePassword = document.getElementById('togglePasswordUser');
        const password = document.getElementById('passwordUser');
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmationUser');
        const passwordConfirmation = document.getElementById('passwordConfirmationUser');

        togglePassword.addEventListener('click', function (e) {
            togglePasswordVisibility(this, password);
        });

        togglePasswordConfirmation.addEventListener('click', function (e) {
            togglePasswordVisibility(this, passwordConfirmation);
        });

        function togglePasswordVisibility(toggle, item) {
            const type = item.getAttribute('type') === 'password' ? 'text' : 'password';
            item.setAttribute('type', type);
            toggle.classList.toggle('fa-eye-slash');
        }
    </script>
@endpush
