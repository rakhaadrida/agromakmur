@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Tambah Customer Baru</h1>
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
                            <form action="{{ route('customers.store') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Nama</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ old('name') }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-2 col-form-label text-bold text-right">Alamat</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-8">
                                        <textarea class="form-control col-form-label-sm" name="address" id="address" required>{{ old('address') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contactNumber" class="col-2 col-form-label text-bold text-right">Nomor Telepon</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="contact_number" id="contactNumber" value="{{ old('contact_number') }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="taxNumber" class="col-2 col-form-label text-bold text-right">NPWP</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="tax_number" id="taxNumber" value="{{ old('tax_number') }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="creditLimit" class="col-2 col-form-label text-bold text-right">Limit Kredit</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="number" min="0" class="form-control col-form-label-sm" name="credit_limit" id="creditLimit" value="{{ old('credit_limit') ?? 0 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tempo" class="col-2 col-form-label text-bold text-right">Tempo</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="number" min="0" class="form-control col-form-label-sm" name="tempo" id="tempo" value="{{ old('tempo') ?? 0 }}" data-toogle="tooltip" data-placement="bottom" title="Hanya masukkan angka saja" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="marketing" class="col-2 col-form-label text-bold text-right">Sales</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <select class="selectpicker custom-select-picker" name="marketing_id" id="marketing" data-live-search="true">
                                            @foreach($marketings as $marketing)
                                                <option value="{{ $marketing->id }}" data-tokens="{{ $marketing->name }}">{{ $marketing->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('marketing')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-success btn-block text-bold">Simpan</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="reset" class="btn btn-outline-danger btn-block text-bold">Reset</button>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Batal</a>
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

            $('#creditLimit').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#creditLimit').tooltip('show');
                    event.preventDefault();
                }
            });

            $('#tempo').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#tempo').tooltip('show');
                    event.preventDefault();
                }
            });

            $('#marketing').selectpicker();
        });
    </script>
@endpush
