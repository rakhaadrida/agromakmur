@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Value Recap</h1>
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
                            <form action="" method="">
                                @csrf
                                <div class="row justify-content-center" style="margin-bottom: 15px">
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="" formmethod="POST" formtarget="_blank" class="btn btn-primary btn-block text-bold">Export PDF</>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="" formmethod="POST"  class="btn btn-danger btn-block text-bold">Export Excel</>
                                    </div>
                                </div>
                                <hr>
                                <div id="priceListCarousel" class="carousel slide price-list-carousel" data-interval="false" wrap="false">
                                    <div class="carousel-inner">
                                        @foreach($categories as $key => $category)
                                            <div class="carousel-item @if(!$key) active @endif">
                                                <div class="container" style="margin-bottom: 0">
                                                    <div class="row justify-content-center">
                                                        <h4 class="text-bold text-dark">Value Recap {{ $category->name }}</h4>
                                                    </div>
                                                    <div class="row justify-content-center" style="margin-top: -5px">
                                                        <h6 class="text-dark">Time : {{ $reportDate }}</h6>
                                                    </div>
                                                </div>
                                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover">
                                                    <thead class="text-center text-dark text-bold">
                                                        <tr>
                                                            <td class="align-middle th-price-list-number">No</td>
                                                            <td class="align-middle th-price-list-product-sku">SKU</td>
                                                            <td class="align-middle">Product Name</td>
                                                            <td class="align-middle th-price-list-price">Price</td>
                                                            <td class="align-middle th-price-list-price">Total Stock</td>
                                                            <td class="align-middle th-price-list-total-value">Total Value</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($mapSubcategoryByCategory[$category->id] ?? [] as $subcategory)
                                                            <tr class="text-dark text-bold" style="background-color: rgb(255, 221, 181)">
                                                                <td colspan="6" class="text-center">
                                                                    <button type="button" class="btn btn-link btn-sm text-dark text-bold" data-toggle="collapse" data-target="#collapseSubcategory-{{ $subcategory->id }}" aria-expanded="false" aria-controls="collapseSubcategory-{{ $subcategory->id }}" style="padding: 0; font-size: 15px; width: 100%">{{ $subcategory->name }}</button>
                                                                </td>
                                                            </tr>
                                                            @forelse($mapProductBySubcategory[$subcategory->id] ?? [] as $index => $product)
                                                                <tr class="text-dark text-bold collapse show" id="collapseSubcategory-{{ $subcategory->id }}">
                                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                                    <td class="text-center">{{ $product->sku }}</td>
                                                                    <td>{{ $product->name }}</td>
                                                                    <td class="text-right">{{ formatPrice($product->price) }}</td>
                                                                    <td class="text-right">{{ formatQuantity($mapStockByProduct[$product->id] ?? 0) }}</td>
                                                                    <td class="text-right">{{ formatPrice($product->total_value) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-dark text-bold h4 p-2">No Data Available</td>
                                                                </tr>
                                                            @endforelse
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-dark text-bold h4 p-2">No Data Available</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(($categories->count() > 0) && ($categories->count() != 1))
                                        <a class="carousel-control-prev" href="#priceListCarousel" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next " href="#priceListCarousel" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    @endif
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
    </script>
@endpush
