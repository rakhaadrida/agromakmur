<html lang="en">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            body {
                font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: #858796;
                text-align: left;
                background-color: #fff;
                width: 85%;
                margin: auto;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin-top: 0;
                margin-bottom: 0.5rem;
                font-weight: 400;
                line-height: 1.2;
            }

            h5 {
                font-size: 1.25rem;
            }

            .text-center {
                text-align: center !important;
            }

            .text-bold {
                font-weight: bold
            }

            .text-dark {
                color: #292a2b !important;
            }

            .text-right {
                text-align: right !important;
            }

            .align-middle {
                vertical-align: middle !important;
            }

            table {
                border-collapse: collapse;
                font-size: 8px;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #858796;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid #000000;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 1px solid #000000;
            }

            .table tbody+tbody {
                border-top: 1px solid #000000;
            }

            .table-sm th,
            .table-sm td {
                padding: 0.3rem;
            }

            .table-bordered {
                border: 1px solid #000000;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000000;
            }

            .table-bordered thead th,
            .table-bordered thead td {
                border-bottom-width: 2px;
            }

            .pdf-section {
                margin-bottom: -25px;
                page-break-after: always;
            }

            .pdf-section:last-child {
                page-break-after: avoid;
            }

            .header-section {
                margin-top: -30px;
            }

            .report-date {
                font-size: 12px !important;
                margin-top: -10px !important;
            }

            .table-items {
                font-size: 11px;
            }

            .table-items th,
            .table-items td {
                padding-top: 0 !important;
                padding-bottom: 0.1rem !important;
                border-width: thin !important;
            }

            .th-value-recap {
                background-color: lightgreen;
            }

            .td-number {
                width: 20px;
            }

            .td-receipt-number {
                width: 80px;
            }

            .td-price {
                width: 70px;
            }
        </style>
    </head>
    <body>
        @foreach($categories as $key => $category)
            <div class="pdf-section">
                <div class="header-section text-center">
                    <h5 class="text-bold text-dark">Daftar Harga {{ $category->name }}</h5>
                    <h6 class="text-dark report-date">Tanggal Export : {{ $exportDate }}</h6>
                </div>

                <table class="table table-sm table-bordered table-items">
                    <thead class="text-center text-dark text-bold">
                        <tr class="th-value-recap">
                            <th class="align-middle td-number">No</th>
                            <th class="align-middle td-receipt-number">SKU</th>
                            <th class="align-middle">Nama Produk</th>
                            @foreach($prices as $price)
                                <td class="align-middle td-price">{{ $price->name }}</td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapProductByCategory[$category->id] ?? [] as $index => $product)
                            <tr class="text-dark text-bold">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                @foreach($prices as $price)
                                    <td class="text-right">{{ formatPrice($mapPriceByProduct[$product->id][$price->id] ?? 0) }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $prices->count() + 3 }}" class="text-center text-dark text-bold h4 p-2">Tidak Ada Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </body>
</html>
