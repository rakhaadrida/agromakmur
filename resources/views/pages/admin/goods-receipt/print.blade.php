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

            .print-header {
                display: inline-block;
                padding-top: 5px;
                padding-bottom: 10px;
                margin-top: -5px;
                margin-left: 13px;
                margin-right: 30px;
            }

            .title-header {
                font-size: 31px;
                font-family: Arial, Helvetica, sans-serif;
                margin-top: 65px;
                margin-left: 35px;
            }

            .supplier-info {
                font-family: Arial, Helvetica, sans-serif;
                margin-top: 17px;
                margin-left: -50px;
                font-size: 17px;
            }

            .supplier-info-label {
                margin-top: -3px;
            }

            .print-logo img {
                width: 170px;
                height: 65px;
                margin-top: -177px;
                margin-left: -30px;
            }

            .address-info {
                margin-top: -109px;
                margin-left: -22.5px !important;
                font-size: 14px;
                font-family: 'Courier New', Courier, monospace;
            }

            .address-info-region {
                margin-top: -8px !important;
            }

            .print-time-info {
                font-family: 'Courier New', Courier, monospace;
                font-size: 15px;
                margin-top: -10.940rem;
                margin-right: -2.815rem;
                line-height: 16px;
            }

            .print-time-info-label {
                margin-left: 0;
            }

            .print-time-info-admin {
                margin-left: 2.815rem;
            }

            .print-receipt-info {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 17px;
                width: 258px;
                margin-top: -95px;
                margin-left: 41.45rem;
                line-height: 20px;
            }

            .print-receipt-info-number {
                margin-left: -1.53rem;
            }

            .print-receipt-info-warehouse {
                margin-left: 0.76rem;
            }

            .page-number {
                float: right;
                margin-top: -23px;
                margin-right: -105px;
                font-family: 'Courier New', Courier, monospace;
                font-size: 16px;
            }

            .print-table {
                font-size: 18px;
                margin-left: -25px;
                margin-right: 21px;
                margin-top: -0px;
            }

            .print-table-head {
                line-height: 20px;
                border: 1px solid;
                border-right-style: none;
                border-left-style: none;
                color: black !important;
                font-family: 'Courier New', Courier, monospace;
                font-size: 21px;
            }

            .print-table-row {
                line-height: 13px;
                color: black !important;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 18px;
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

            .table-content-sku {
                padding-left: 0.5rem !important;
            }

            .table-content-product {
                padding-left: 0.5rem !important;
            }

            .print-footer {
                margin: -13px 30px -50px 90px;
            }

            .print-table-signature {
                margin-left: -88px;
                font-size: 18px;
            }

            .table-signature-blank-row {
                height: 3.5rem;
            }

            .table-signature-head-warehouse {
                width: 12.5rem;
            }

            .table-signature-admin {
                width: 17.5rem;
            }

            .table-signature-staff-warehouse {
                width: 10rem;
            }

            .signature-name {
                margin-left: 4.35rem;
                margin-right: 4.35rem;
            }

            @media print {
                @page {
                    margin: 0.4302cm 1.27cm 0.254cm 0.381cm;
                }
            }
        </style>
    </head>
    <body>
        @php $i = 1; $no = 1; $kode = []; @endphp
        @foreach($goodsReceipts as $key => $goodsReceipt)
            <div class="print-container">
                <div class="container-fluid print-header">
                    <div class="title-header text-center">
                        <h3 class="text-bold">Goods Receipt Note</h3>
                    </div>
                    <div class="supplier-info">
                        <span class="text-right">Supplier</span>
                        <span>:</span>
                        <span>{{ $goodsReceipt->supplier_name }}</span>
                    </div>
                    <div class="supplier-info supplier-info-label">
                        <span class="text-right">We had accepted these following item(s) :</span>
                    </div>
                </div>
                <div class="float-left print-logo">
                    <img src="{{ url('assets/img/logo.png') }}" alt="">
                    <h6 class="address-info">JL KRAMAT PULO GUNDUL</h6>
                    <h6 class="address-info address-info-region">KRAMAT SENTIONG - JAKPUS</h6>
                </div>
                <div class="float-right print-time-info">
                    <span class="text-right text-bold">Print Date</span>
                    <span>:</span>
                    <span>{{ $printDate }}</span>
                    <br>
                    <span class="print-time-info-label text-right text-bold">Print Time</span>
                    <span>:</span>
                    <span>{{ $printTime }}</span>
                    <br>
                    <span class="print-time-info-admin text-right text-bold">Admin</span>
                    <span>:</span>
                    <span>{{ $goodsReceipt->user_name }}</span>
                </div>
                <div class="print-receipt-info">
                    <span class="text-right">Receipt Date</span>
                    <span>:</span>
                    <span>{{ formatDate($goodsReceipt->date, 'd-M-y') }}</span>
                    <br>
                    <span class="print-receipt-info-number text-right">Receipt Number</span>
                    <span>:</span>
                    <span>{{ $goodsReceipt->number }}</span>
                    <span class="print-receipt-info-warehouse text-right">Warehouse</span>
                    <span>:</span>
                    <span>{{ $goodsReceipt->warehouse_name }}</span>
                    <br>
                </div>
                <br>

                <span class="page-number text-right">Page  :   {{ $i }}</span>
                <table class="table table-sm table-responsive-sm print-table">
                    <thead class="text-center text-bold print-table-head">
                        <tr>
                            <th class="table-head-number">No</th>
                            <th class="table-head-sku">SKU</th>
                            <th class="table-head-product">Product Name</th>
                            <th class="table-head-quantity">Quantity</th>
                            <th class="table-head-unit">Unit</th>
                        </tr>
                    </thead>
                    <tbody class="print-table-row">
                        @php $cek = 0; @endphp
                        @foreach($goodsReceiptItems as $index => $goodsReceiptItem)
                            <tr>
                                <td class="text-center">{{ ++$index }}</td>
                                <td class="table-content-sku">{{ $goodsReceiptItem->product->sku }}</td>
                                <td class="table-content-product">{{ $goodsReceiptItem->product->name }}</td>
                                <td class="text-center">{{ formatQuantity($goodsReceiptItem->quantity) }}</td>
                                <td class="text-center">{{ $goodsReceiptItem->unit->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php $i++; @endphp

                <div class="container-fluid print-footer">
                    <table class="print-table-signature">
                        <thead>
                            <tr>
                                <td class="text-center table-signature-head-warehouse">HEAD OF WAREHOUSE</td>
                                <td class="text-center table-signature-admin">ADMIN</td>
                                <td class="text-center table-signature-staff-warehouse">WAREHOUSE STAFF</td>
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
            </div>
        @endforeach

        <script type="text/javascript">
            window.onafterprint = function() {
                {{--window.location = "{{ route('bm-after-print', $id) }}";--}}
            }

            window.print();
        </script>
    </body>
</html>
