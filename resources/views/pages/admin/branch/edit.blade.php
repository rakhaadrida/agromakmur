@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Branch - {{ $branch->name }}</h1>
        </div>
        @if($errors->any())
            <div class="alert alert-danger alert-input-section">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card show">
                        <div class="card-body">
                            <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ $branch->name }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-2 col-form-label text-bold text-right">Address</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-8">
                                        <textarea class="form-control col-form-label-sm" name="address" id="address" required>{{ $branch->address }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phoneNumber" class="col-2 col-form-label text-bold text-right">Phone Number</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="phone_number" id="phoneNumber" value="{{ $branch->phone_number }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="userIds" class="col-2 col-form-label text-right">Users</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="hidden" name="user_id_values[]" id="userIdValues" value="{{ $userIds }}">
                                        <select class="selectpicker custom-select-picker" name="user_ids[]" id="userIds" data-live-search="true" data-selected-text-format="count > 3" multiple>
                                            @foreach($users as $key => $user)
                                                <option value="{{ $user->id }}" data-tokens="{{ $user->username }}">{{ $user->username }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_ids[]')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="warehouseIds" class="col-2 col-form-label text-right">Warehouses</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="hidden" name="warehouse_id_values[]" id="warehouseIdValues" value="{{ $warehouseIds }}">
                                        <select class="selectpicker custom-select-picker" name="warehouse_ids[]" id="warehouseIds" data-live-search="true" data-selected-text-format="count > 3" multiple>
                                            @foreach($warehouses as $key => $warehouse)
                                                <option value="{{ $warehouse->id }}" data-tokens="{{ $warehouse->name }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('warehouse_ids[]')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
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
            $('#phoneNumber').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#phoneNumber').tooltip('show');
                    event.preventDefault();
                }
            });

            let userIdValues = $('#userIdValues').val();
            let warehouseIdValues = $('#warehouseIdValues').val();

            $('#userIds').selectpicker('val', userIdValues.split(','));
            $('#warehouseIds').selectpicker('val', warehouseIdValues.split(','));
        });
    </script>
@endpush
