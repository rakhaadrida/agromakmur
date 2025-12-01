<html lang="en">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            body {
                width: 816px;
                height: 520px;
                font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: black;
                text-align: left;
                background-color: #fff;
                margin: 0 0 0 35px;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                margin-bottom: 0.5rem;
                font-weight: 400;
                line-height: 1.2;
            }

            h5 {
                font-size: 1.25rem;
            }

            .container-fluid {
                width: 100%;
                padding-right: 0.75rem;
                padding-left: 0.75rem;
                margin-right: auto;
                margin-left: auto;
            }

            .text-center {
                text-align: center !important;
            }

            .text-bold {
                font-weight: bold
            }

            .text-right {
                text-align: right !important;
            }

            .float-right {
                float: right !important;
            }

            .float-left {
                float: left !important;
            }

            table {
                border-collapse: collapse;
            }

            .table {
                width: 116%;
                margin-bottom: 1rem;
                color: black;
            }

            .table th,
            .table td {
                padding: 2rem;
                vertical-align: top;
            }

            .table tbody+tbody {
                border-top: 1px solid black;
            }

            .table-sm th {
                padding: 0.4rem 0.3rem;
            }

            .table-sm td {
                padding: 0.4rem 0.1rem 0.8rem;
            }

            .table tbody tr:last-child td {
                border-bottom: solid black;
                border-width: 1px;
                padding-bottom: 0.3rem;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0, 0, 0, 0.05);
            }

            .table-hover tbody tr:hover {
                color: #858796;
                background-color: rgba(59, 57, 57, 0.075);
            }

            .print-container {
                page-break-after: always;
            }

            .print-table {
                font-size: 18px;
                margin-left: -25px;
                margin-right: 21px;
                margin-top: -0px;
            }

            .print-table-head {
                line-height: 20px;
                border-right-style: none;
                border-left-style: none;
                color: black !important;
                font-family: 'Courier New', Courier, monospace;
                font-size: 21px;
            }

            .print-logo img {
                width: 170px;
                height: 70px;
                margin-left: -135px;
            }

            .address-info {
                margin-top: 5px;
                margin-left: 0;
                font-size: 16px;
                font-family: 'Courier New', Courier, monospace;
                text-align: left;
                width: 290px;
                height: 39px;
            }

            .print-time-info {
                font-family: 'Courier New', Courier, monospace;
                font-size: 16px;
                line-height: 16px;
                margin-right: 1.5rem;
            }

            .info-row {
                display: flex;
                margin-bottom: 2px;
            }

            .info-label {
                width: 130px;
                text-align: right;
                font-weight: bold;
                padding-right: 0.4rem;
            }

            .info-separator {
                text-align: center;
            }

            .info-value {
                flex: 1;
                padding-left: 0.4rem;
                text-align: left;
                font-weight: normal;
            }

            .print-time-info-clear {
                clear: both;
            }

            .header-receipt-row {
                padding-bottom: 0 !important;
            }

            .print-header {
                display: inline-block;
                padding-bottom: 10px;
                margin-top: -70px;
            }

            .title-header {
                font-size: 36px !important;
                font-family: Arial, Helvetica, sans-serif;
                margin-left: -3rem;
                margin-top: -1px;
            }

            .supplier-info {
                text-align: left;
                font-family: Arial, Helvetica, sans-serif;
                margin-top: 18px;
                margin-left: -12px;
                font-size: 20px;
                font-weight: normal;
            }

            .supplier-info-label {
                margin-top: 5px;
            }

            .print-receipt-info {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 20px;
                font-weight: normal;
                margin-top: -65px;
                margin-left: 42rem;
                line-height: 20px;
            }

            .receipt-info-row {
                width: 400px;
                display: flex;
                margin-bottom: 2px;
            }

            .receipt-info-label {
                width: 220px;
                text-align: right;
                padding-right: 0.4rem;
            }

            .receipt-info-separator {
                text-align: center;
            }

            .receipt-info-value {
                flex: 1;
                padding-left: 0.4rem;
                text-align: left;
                width: 120px;
            }

            .page-number {
                float: right;
                font-family: 'Courier New', Courier, monospace;
                font-size: 18px;
                font-weight: normal;
                margin-top: -6px;
                margin-right: 1rem;
                margin-bottom: 0.05rem;
                height: 20px;
            }

            .table-head-title {
                border-top: 1px solid black;
                border-bottom: 1px solid black;
                font-size: 26px;
            }

            .table-head-title th {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .table-head-number {
                width: 0.3rem;
            }

            .table-head-sku {
                width: 4.5rem;
            }

            .table-head-product {
                width: 20.6rem;
            }

            .table-head-quantity {
                width: 3.7rem;
            }

            .table-head-unit {
                width: 6rem;
            }

            .print-table-row {
                line-height: 14px;
                color: black !important;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 22px;
            }

            .print-table-row td {
                padding-top: 0.5rem !important;
                padding-bottom: 0.5rem !important;
            }

            .table-content-sku {
                padding-left: 0.5rem !important;
            }

            .table-content-product {
                padding-left: 0.5rem !important;
            }

            .print-footer {
                margin: -5px 30px 0 150px;
            }

            .print-table-signature {
                margin-left: -88px;
                font-size: 22px;
            }

            .table-signature-blank-row {
                height: 3.5rem;
            }

            .table-signature-head-warehouse {
                width: 14.5rem;
            }

            .table-signature-admin {
                width: 17.5rem;
            }

            .table-signature-staff-warehouse {
                width: 12rem;
            }

            .signature-name {
                margin-left: 4.35rem;
                margin-right: 4.35rem;
            }

            @media print {
                @page {
                    margin: 0.4302cm 1.27cm 0.254cm 0.381cm;

                    @top-right {
                        content: "Page " counter(page) " of " counter(pages);
                        font-family: 'Courier New', Courier, monospace;
                        font-size: 12px;
                        font-weight: normal;
                        margin-bottom: -24.1rem !important;
                        margin-right: 0.44rem !important;
                    }
                }
            }
        </style>
    </head>
    <body>
        @php $number = 1; @endphp
        @foreach($goodsReceipts as $key => $goodsReceipt)
            <div class="print-container">
                <table class="table table-sm table-responsive-sm print-table">
                    <thead class="text-center text-bold print-table-head">
                        <tr class="print-header-logo">
                            <td colspan="5">
                                <div class="float-left print-logo">
                                    <img src="{{ url('assets/img/logo.png') }}" alt="">
                                    <h6 class="address-info">{{ formatUppercase($goodsReceipt->branch_address) }}</h6>
                                </div>
                                <div class="float-right print-time-info">
                                    <div class="info-row">
                                        <span class="info-label">Tanggal Cetak</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $printDate }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Waktu Cetak</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $printTime }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Admin</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $goodsReceipt->user_name }}</span>
                                    </div>
                                </div>
                                <div class="print-time-info-clear"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="header-receipt-row">
                                <div class="container-fluid print-header">
                                    <div class="title-header text-center">
                                        <h3 class="text-bold">Nota Terima Barang</h3>
                                    </div>
                                    <div class="supplier-info">
                                        <span class="text-right">Supplier</span>
                                        <span>:</span>
                                        <span>{{ $goodsReceipt->supplier_name }}</span>
                                    </div>
                                    <div class="supplier-info supplier-info-label">
                                        <span class="text-right">Kami telah menerima barang-barang berikut ini:</span>
                                    </div>
                                    <div class="print-receipt-info">
                                        <div class="receipt-info-row">
                                            <span class="receipt-info-label">Tanggal Terima</span>
                                            <span class="receipt-info-separator">:</span>
                                            <span class="receipt-info-value">{{ formatDate($goodsReceipt->date, 'd-M-y') }}</span>
                                        </div>
                                        <div class="receipt-info-row">
                                            <span class="receipt-info-label">Nomor Nota</span>
                                            <span class="receipt-info-separator">:</span>
                                            <span class="receipt-info-value">{{ $goodsReceipt->number }}</span>
                                        </div>
                                        <div class="receipt-info-row">
                                            <span class="receipt-info-label">Gudang</span>
                                            <span class="receipt-info-separator">:</span>
                                            <span class="receipt-info-value">{{ $goodsReceipt->warehouse_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="page-number text-right"></span>
                            </td>
                        </tr>
                        <tr class="table-head-title">
                            <th class="table-head-number">No</th>
                            <th class="table-head-sku">SKU</th>
                            <th class="table-head-product">Nama Produk</th>
                            <th class="table-head-quantity">Quantity</th>
                            <th class="table-head-unit">Unit</th>
                        </tr>
                    </thead>
                    <tbody class="print-table-row">
                        @foreach($goodsReceipt->goodsReceiptItems as $index => $goodsReceiptItem)
                            <tr>
                                <td class="text-center">{{ ++$index }}</td>
                                <td class="table-content-sku">{{ $goodsReceiptItem->product->sku }}</td>
                                <td class="table-content-product">{{ $goodsReceiptItem->product->name }}</td>
                                <td class="text-center">{{ formatQuantity($goodsReceiptItem->quantity) }}</td>
                                <td class="text-center">{{ $goodsReceiptItem->unit->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <div class="container-fluid print-footer">
                                    <table class="print-table-signature">
                                        <thead>
                                            <tr>
                                                <td class="text-center table-signature-head-warehouse">KEPALA GUDANG</td>
                                                <td class="text-center table-signature-admin">ADMIN</td>
                                                <td class="text-center table-signature-staff-warehouse">STAFF GUDANG</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="table-signature-blank-row"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">(<span class="signature-name"></span>)</td>
                                                <td class="text-center">(<span class="signature-name"></span>)</td>
                                                <td class="text-center">(<span class="signature-name"></span>)</td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach

        <script type="text/javascript">
            window.onafterprint = function() {
                const url = '{{ route('goods-receipts.after-print', $id) }}';
                window.location = url + '?start_number=' + encodeURIComponent('{{ $startNumber }}') + '&final_number=' + encodeURIComponent('{{ $finalNumber }}');
            }

            window.print();
        </script>
    </body>
</html>
