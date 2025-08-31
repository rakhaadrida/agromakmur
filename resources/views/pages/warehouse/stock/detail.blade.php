@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Detail Product - {{ $product->name }}</h1>
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
                            <form action="" method="POST" id="form">
                                @csrf
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold text-right">Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-6">
                                        <input type="text" class="form-control col-form-label-sm" name="name" id="name" value="{{ $product->name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="category" class="col-2 col-form-label text-bold text-right">Category</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="category" id="category" value="{{ $product->category->name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="subcategory" class="col-2 col-form-label text-bold text-right">Subcategory</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="subcategory" id="subcategory" value="{{ $product->subcategory->name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="unit" class="col-2 col-form-label text-bold text-right">Unit</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-3">
                                        <input type="text" class="form-control col-form-label-sm" name="unit" id="unit" value="{{ $product->unit->name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="conversion" class="col-2 col-form-label text-bold text-right"></label>
                                    <span class="col-form-label text-bold"></span>
                                    <div class="col-6 ml-1">
                                        <input class="form-check-input product-check-input" type="checkbox" name="conversion" id="conversion" {{ $product->productConversions->count() ? 'checked' : '' }} disabled>
                                        <label class="col-form-label product-check-label ml-4" for="remember">Does this product have unit conversion?</label>
                                    </div>
                                </div>
                                <div id="conversionSection" @if(empty($product->productConversions)) hidden @endif>
                                    @foreach($product->productConversions as $conversion)
                                        <div class="form-group row">
                                            <label for="unitConversion" class="col-2 col-form-label text-bold text-right">Unit Conversion</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-3">
                                                <input type="text" class="form-control col-form-label-sm" name="unitConversion" id="unitConversion" value="{{ $conversion->unit->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="quantity" class="col-2 col-form-label text-bold text-right">Quantity</label>
                                            <span class="col-form-label text-bold">:</span>
                                            <div class="col-2">
                                                <input type="number" min="1" class="form-control col-form-label-sm" name="quantity" id="quantity" value="{{ formatQuantity($conversion->quantity) }}" readonly>
                                            </div>
                                            <span class="col-form-label text-bold" id="primaryUnit">{{ $product->unit->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                                <h5 class="h5 mb-3 text-gray-800 menu-title">Stock List</h5>
                                @foreach($warehouses as $key => $warehouse)
                                    <div class="form-group row">
                                        <label for="stock-{{ $key }}" class="col-2 col-form-label text-bold text-right">{{ $warehouse->name }}</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-1 col-product-stock">
                                            <input type="text" class="form-control col-form-label-sm text-right form-product-stock" name="stock[]" id="stock-{{ $key }}" value="{{ formatQuantity($productStocks[$warehouse->id]['stock'] ?? 0) }}" readonly>
                                        </div>
                                        <span class="col-form-label text-bold">{{ $product->unit->name }}</span>
                                    </div>
                                @endforeach
                                <hr>
                                <div class="form-row justify-content-center">
                                    <div class="col-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn-block text-bold">Back to list</a>
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
    </script>
@endpush
