@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Stock Data - {{ $product->name }}</h1>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
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
                            <form action="{{ route('products.update-stock', $product->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label for="name" class="col-2 col-form-label text-bold">Product Name</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-2">
                                        <input type="text" class="form-control col-form-label-sm text-bold" name="name" id="name" value="{{ $product->name }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="category" class="col-2 col-form-label text-bold">Category</label>
                                    <span class="col-form-label text-bold">:</span>
                                    <div class="col-4">
                                        <input type="text" class="form-control col-form-label-sm text-bold" name="category" id="category" value="{{ $product->category_name }}" readonly>
                                    </div>
                                </div>
                                <hr>
                                @foreach($warehouses as $key => $warehouse)
                                    <div class="form-group row">
                                        <label for="stock-{{ $key }}" class="col-2 col-form-label text-bold">{{ $warehouse->name }}</label>
                                        <span class="col-form-label text-bold">:</span>
                                        <div class="col-1 col-product-stock">
                                            <input type="number" min="0" step="any" class="form-control col-form-label-sm text-right form-product-stock" name="stock[]" id="stock-{{ $key }}" value="{{ $productStocks[$warehouse->id]['stock'] ?? 0 }}" required>
                                            <input type="hidden" name="warehouse_id[]" value="{{ $warehouse->id }}">
                                        </div>
                                        <span class="col-form-label text-bold">{{ $product->unit_name }}</span>
                                    </div>
                                @endforeach
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
