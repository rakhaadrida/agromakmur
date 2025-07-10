@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Subcategory - {{ $subcategory->name }}</h1>
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
                            <form action="{{ route('subcategories.update', $subcategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ $subcategory->name }}" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="category" class="col-2 col-form-label text-bold text-right">Category</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <select class="selectpicker custom-select-picker" name="category_id" id="category" data-live-search="true">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" data-tokens="{{ $category->name }}" @if($subcategory->category_id == $category->id) selected @endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('$category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="reminderLimit" class="col-2 col-form-label text-bold text-right">Reminder Limit</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="number" min="0" class="form-control col-form-label-sm" name="reminder_limit" id="reminderLimit" value="{{ $subcategory->reminder_limit }}" data-toogle="tooltip" data-placement="bottom" title="Only allowed to input numbers" required>
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
            $('#reminderLimit').on('keypress', function(event) {
                if (event.which > 31 && (event.which < 48 || event.which > 57)) {
                    $('#creditLimit').tooltip('show');
                    event.preventDefault();
                }
            });

            $('#marketing').selectpicker();
        });
    </script>
@endpush
