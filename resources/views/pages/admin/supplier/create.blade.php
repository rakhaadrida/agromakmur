@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Add New Supplier</h1>
        </div>
        @if ($errors->any())
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
                            <form action="{{ route('suppliers.store') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ old('name') }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-2 col-form-label text-bold text-right">Address</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-8">
                                        <textarea class="form-control col-form-label-sm" name="address" id="address" required>{{ old('address') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contactNumber" class="col-2 col-form-label text-bold text-right">Contact Number</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="contact_number" id="contactNumber" value="{{ old('contact_number') }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="taxNumber" class="col-2 col-form-label text-bold text-right">Tax Number / NPWP</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="tax_number" id="taxNumber" value="{{ old('tax_number') }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers">
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold">Submit</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" class="btn btn-outline-danger btn-block text-bold">Reset</button>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#contactNumber').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#contactNumber').tooltip('show');
                    event.preventDefault();
                }
            });

            $('#taxNumber').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#taxNumber').tooltip('show');
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush
