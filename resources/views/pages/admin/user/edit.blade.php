@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-dark text-bold">Detail User - {{ $user->username }}</div>
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST" role="form">
                            @method('PUT')
                            @csrf
                            <div class="form-group row">
                                <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" value="{{ $user->username }}" data-toogle="tooltip" data-placement="bottom" title="No spaces allowed" required autofocus>
                                    @error('username')
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
                                            <option value="{{ $key }}" @if($user->role == $key) selected @endif>{{ $userRole }}</option>
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
</script>
@endpush
