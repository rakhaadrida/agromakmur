@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-dark text-bold">Add New User</div>
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
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control @error('password') is-invalid user-invalid-no-icon @enderror" name="password" id="password" required>
                                    <i class="far fa-eye password-eye-icon" id="togglePassword"></i>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                         </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="passwordConfirmation" class="col-md-4 col-form-label text-md-right">Password Confirmation</label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control @error('password') is-invalid user-invalid-no-icon @enderror" name="password_confirmation" id="passwordConfirmation" required>
                                    <i class="far fa-eye password-eye-icon" id="togglePasswordConfirmation"></i>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                         </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">Role</label>
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
                            <div class="form-group row mb-0">
                                <div class="col-md-auto offset-md-4">
                                    <button type="submit" class="btn btn-primary text-bold">Submit</button>
                                </div>
                                <div class="col-md-2 user-button-cancel">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-danger text-bold">
                                        Cancel
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#username').on('keydown', function(event) {
            if (event.which === 32) {
                $('#username').tooltip('show');
                event.preventDefault();
            }
        });
    });

    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
    const passwordConfirmation = document.getElementById('passwordConfirmation');

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
