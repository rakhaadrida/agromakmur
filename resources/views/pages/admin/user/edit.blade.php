@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

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
                            <div class="form-group row">
                                <label for="branchIds" class="col-md-4 col-form-label text-md-right">Branch</label>
                                <div class="col-md-6">
                                    <input type="hidden" name="branch_id_values[]" id="branchIdValues" value="{{ $branchIds }}">
                                    <select class="selectpicker custom-select-picker" name="branch_ids[]" id="branchIds" data-live-search="true" data-selected-text-format="count > 3" multiple @if(isUserDetailSuperAdmin($user->role)) disabled @endif>
                                        @foreach($branches as $key => $branch)
                                            <option value="{{ $branch->id }}" data-tokens="{{ $branch->name }}">{{ $branch->name }}</option>
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
    <script src="{{ url('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#username').on('keydown', function(event) {
                if (event.which === 32) {
                    $('#username').tooltip('show');
                    event.preventDefault();
                }
            });

            let branchIdValues = $('#branchIdValues').val();
            $('#branchIds').selectpicker('val', branchIdValues.split(','));

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
                    branchIds.selectpicker('val', branchIdValues.split(','));
                }

                branchIds.selectpicker('refresh');
            });
        });
    </script>
@endpush
