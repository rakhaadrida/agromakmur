@extends('layouts.admin')

@push('addon-style')
    <link href="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-0">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Daftar Harga</h1>
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
                            <form>
                                <div class="row justify-content-center" style="margin-bottom: 15px">
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="{{ route('report.price-lists.pdf') }}" formmethod="GET" formtarget="_blank" class="btn btn-primary btn-block text-bold">Export PDF</button>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <button type="submit" formaction="{{ route('report.price-lists.export') }}" formmethod="GET"  class="btn btn-danger btn-block text-bold">Export Excel</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="container" style="margin-bottom: 0">
                                    <div class="row justify-content-center">
                                        <h4 class="text-bold text-dark">Daftar Harga Produk</h4>
                                    </div>
                                    <div class="row justify-content-center" style="margin-top: -5px">
                                        <h6 class="text-dark">Tanggal Laporan : {{ $reportDate }}</h6>
                                    </div>
                                </div>
                                <table class="table table-sm table-bordered table-striped table-responsive-sm table-hover" id="dataTable">
                                    <thead class="text-center text-dark text-bold">
                                        <tr>
                                            <td class="align-middle th-price-list-number">No</td>
                                            <td class="align-middle th-price-list-product-sku">SKU</td>
                                            <td class="align-middle">Nama Produk</td>
                                            <td class="align-middle th-price-list-product-category">Kategori</td>
                                            @foreach($prices as $price)
                                                <td class="align-middle th-price-list-price">{{ $price->name }}</td>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products ?? [] as $index => $product)
                                            <tr class="text-dark text-bold">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-center">{{ $product->sku }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td class="text-center">{{ $product->category_name }}</td>
                                                @foreach($prices as $price)
                                                    <td class="text-right" data-sort="{{ $mapPriceByProduct[$product->id][$price->id] ?? 0 }}">{{ formatPrice($mapPriceByProduct[$product->id][$price->id] ?? 0) }}</td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $prices->count() + 4 }}" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        let datatable = $('#dataTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 25,
        });
    </script>
@endpush
