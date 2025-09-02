@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session()->has('message'))
                <div class="alert alert-success text-sm text-dark font-weight-bold" role="alert" id="alertMessage">
                    {{ session()->get('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Change Password') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('update-password') }}" id="form">
                        @csrf
                        <div class="form-group row">
                            <label for="currentPassword" class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control @error('current_password') is-invalid user-invalid-no-icon @enderror" name="current_password" id="currentPassword" required autofocus>
                                <i class="far fa-eye password-eye-icon" id="toggleCurrentPassword"></i>
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="newPassword" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control @error('new_password') is-invalid user-invalid-no-icon @enderror" name="new_password" id="newPassword" required>
                                <i class="far fa-eye password-eye-icon" id="toggleNewPassword"></i>
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="confirmPassword" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="new_password_confirmation" id="confirmPassword" data-toogle="tooltip" data-placement="bottom" title="Password confirmation does not match" required>
                                <i class="far fa-eye password-eye-icon" id="toggleConfirmPassword"></i>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    {{ __('Change Password') }}
                                </button>
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
            const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
            const currentPassword = document.getElementById('currentPassword');
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPassword = document.getElementById('newPassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPassword = document.getElementById('confirmPassword');

            toggleCurrentPassword.addEventListener('click', function (e) {
                const type = currentPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                currentPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            toggleNewPassword.addEventListener('click', function (e) {
                const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                newPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            toggleConfirmPassword.addEventListener('click', function (e) {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });

            $('#btnSubmit').on('click', function (event) {
                if (newPassword.value !== confirmPassword.value) {
                    $(confirmPassword).tooltip('show');
                    newPassword.style.borderColor = "red";
                    newPassword.style.borderWidth = "2px";
                    confirmPassword.style.borderColor = "red";
                    confirmPassword.style.borderWidth = "2px";

                    return false;
                } else {
                    $('#form').submit();
                }
            });

            setTimeout(function(){
                $('#alertMessage').remove();
            }, 3000 );
        });
    </script>
@endpush
