<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title></title>
        <style>
            body {
                width: 914.7px;
                height: 520px;
                font-family: "Calibri", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-size: 1.2rem;
                font-weight: 700;
                line-height: 1.5;
                color: black;
                text-align: left;
                background-color: #fff;
            }

            @font-face {
                font-family: "epson1";
                font-weight: 900;
                color: black;
                src: url('{{ public_path('backend/fonts/epson1.woff') }}');
            }

            @font-face {
                font-family: "Dotrice";
                font-weight: 800;
                color: black;
                src: url('{{ public_path('backend/fonts/Dotrice.ttf') }}');
            }

            @font-face {
                font-family: "Dotrice Bold";
                font-weight: 800;
                color: black;
                src: url('{{ public_path('backend/fonts/Dotrice-Bold.otf') }}');
            }

            @font-face {
                font-family: "buenard";
                font-weight: 800;
                color: black;
                src: url('{{ public_path('backend/fonts/Buenard-Regular.ttf') }}');
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
                font-size: 1.15rem;
            }

            .container-fluid {
                width: 87.29%;
                padding-right: 0.75rem;
                padding-left: 0.75rem;
                margin-left: auto;
                color: black;
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

            .text-wrap {
                white-space: normal !important;
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
                width: 90.1%;
                margin-bottom: 1rem;
                color: black;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid black;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 1px solid black;
            }

            .table tbody+tbody {
                border-top: 1px solid black;
            }

            .table-sm th,
            .table-sm td {
                padding: 0.4rem 0.15rem;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #afbbc5;
            }

            .table-bordered thead th,
            .table-bordered thead td {
                border-bottom-width: 2px;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0, 0, 0, 0.05);
            }

            .table-hover tbody tr:hover {
                color: #858796;
                background-color: rgba(59, 57, 57, 0.075);
            }

            .print-container {
                margin-bottom: -3.438rem;
                page-break-after: always
            }

            .header-section {
                display: inline-block;
                color: black;
                border: 1.4px dotted;
                border-bottom: none;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                border-left: 1px solid black;
                border-right: 1px solid black;
                padding-top: 5px;
                padding-bottom: 10px;
                margin-left: 0;
                margin-right: 0;
            }

            .title-header {
                font-weight: bold;
                font-family: Arial, Helvetica, sans-serif;
            }

            .order-type {
                margin-top: -0.625rem;
            }

            .order-number-info {
                font-family: 'Courier New', Courier, monospace;
                color: black;
                margin-top: -5px;
                font-size: 15px;
                font-weight: normal;
            }

            .order-date-info {
                font-family: 'Courier New', Courier, monospace;
                color: black;
                margin-left: -9px;
                margin-top: -2px;
                font-size: 15px;
                font-weight: normal;
                letter-spacing: 1px;
            }

            .sub-title {
                font-family: 'Calibri', Helvetica, sans-serif;
            }

            .logo-section {
                margin-top: -99px;
            }

            .logo-section img {
                width: 148px;
                height: 45px;
            }

            .logo-section-phone-info {
                font-family: "Rockwell", Helvetica, sans-serif;
                font-size: 10.5px;
                line-height: 15px;
                margin-left: 3px;
            }

            .customer-section {
                color: black;
                font-weight: 500;
                font-size: 16px;
                width: 260px;
                margin-top: -110px;
                margin-right: 90px;
            }

            .customer-section-greetings {
                font-size: 12px;
            }

            .customer-name-info {
                color: black;
                font-size: 16px;
                line-height: 15px;
            }

            .customer-address-info {
                color: black;
                font-size: 15px;
                margin-bottom: -10px;
                line-height: 20px;
            }

            .table-general-info {
                margin-top: -30px;
                font-size: 15px;
                border-spacing: 0px;
                margin-right: 30px;
            }

            .table-general-info td {
                border: 1px dotted;
            }

            .table-general-info thead td {
                padding-bottom: 0.25rem !important;
                font-weight: 600;
            }

            .table-general-info-head {
                font-family: 'Courier New', Courier, sans-serif;
                line-height: 6px;
            }

            .table-general-info-head-number {
                width: 120px;
            }

            .table-general-info-head-date {
                width: 130px;
            }

            .table-general-info-head-tempo {
                width: 110px;
            }

            .table-general-info-head-marketing {
                width: 180px;
            }

            .table-general-info-body td {
                line-height: 9px;
            }

            .table-order-item {
                page-break-inside: auto;
                font-size: 16px;
                width: 90.1% !important;
                height: 52.5% !important;
                margin-right: 34.5px;
                margin-top: -13px;
            }

            .table-order-item thead td {
                padding-top: 0.3rem !important;
                padding-bottom: 0.4rem !important;
            }

            .table-order-item tbody td:empty {
                border-left: 0;
                border-right: 0;
                border-top: 0;
            }

            .table-order-item-head td {
                line-height: 6px;
                border-top: 1px dotted;
                border-bottom: 1px dotted;
            }

            .table-order-item-head-number {
                width: 10px;
                border-left: 1px dotted;
            }

            .table-order-item-head-product {
                width: 340px;
            }

            .table-order-item-head-quantity {
                width: 75px;
            }

            .table-order-item-head-price {
                width: 50px;
            }

            .table-order-item-head-total {
                width: 90px;
            }

            .table-order-item-head-subtotal {
                width: 80px;
                border-right: 1px dotted;
            }

            .table-order-item-body {
                line-height: 7px;
            }

            .table-order-item-body td {
                border-bottom: none;
                border-top: none;
            }

            .table-order-item-body-row {
                height: 21px !important;
            }

            .footer-section {
                border: 1px dotted;
                border-radius: 10px;
                border-left: 1px solid black;
                border-right: 1px solid black;
                margin: -15px 30px -40px 0;
            }

            .table-footer {
                margin-left: -15px;
                width: 920px;
                margin-right: -50px;
            }

            .table-footer-head-recipient {
                width: 90px;
                border-right: 1px dotted;
            }

            .recipient-signature {
                font-size: 15px;
                padding-left: 5px;
                margin-bottom: 12px;
                margin-top: 5px;
            }

            .recipient-signature-table {
                font-size: 15px !important;
            }

            .recipient-signature-table-blank {
                height: 38px;
            }

            .table-footer-head-account-info {
                width: 273px;
                border-right: 1px dotted;
            }

            .payment-info {
                color: black;
                margin-top: -7px;
                margin-left: 5px;
                margin-right: 30px;
                font-size: 15px;
                line-height: 18px;
            }

            .table-footer-head-warehouse {
                width: 90px;
            }

            .warehouse-signature {
                font-size: 15px;
                margin-top: -5px;
                margin-left: 1px;
                margin-bottom: -4px;
                line-height: 14px;
            }

            .warehouse-signature-table {
                font-size: 15px !important;
            }

            .warehouse-signature-table-blank {
                height: 30px;
            }

            .ttd-mengetahui {
                font-size: 15px;
                margin-top: -10px;
                margin-bottom: -5px;
                line-height: 10px;
            }

            .tgl-ttd {
                font-size: 13px;
                line-height: 3px;
                padding-left: 0.4rem;
                padding-bottom: 0.01rem;
            }

            .total-faktur {
                margin-top: 0px;
                margin-left: 5px;
                font-size: 14px;
            }

            .tabel-total-faktur {
                line-height: 15px;
                margin-bottom: 3px;
            }

            .title-total {
                font-size: 14px;
            }

            .angka-total {
                width: 180px;
                font-size: 16px;
                padding-right: 0.01rem !important;
            }

            .angka-total-akhir {
                width: 145px;
                font-size: 17px;
            }

            .waktu-cetak-so {
                font-weight: 700;
                margin-left: 30px;
                margin-right: 100px;
                margin-top: 33px;
            }

            .waktu-cetak {
                font-size: 12px !important;
                margin-left: -15px;
                margin-right: 10px;
            }

            .cetak-ke {
                font-size: 12px !important;
            }

            @media print {
                @page {
                    width: 21.8cm;
                    height: 13.8cm;
                    margin: 0.4002cm 1.27cm 0.144cm 0.281cm;
                }

                body {
                    margin: 0;
                    zoom: 1.37;
                }
            }
        </style>
    </head>
    <body>
        @php $number = 1 @endphp
        @foreach($salesOrders as $key => $salesOrder)
            <div class="print-container">
                <div class="container-fluid header-section">
                    <div class="title-header text-center">
                        <h5 class="text-bold">SALES INVOICE</h5>
                        <h5 class="text-bold order-type">{{ $salesOrder->tempo > 0 ? '(TEMPO)' : '(CASH)' }}</h5>
                    </div>
                    <div class="order-number-info text-center">
                        <span class="text-right sub-title">Number</span>
                        <span>:</span>
                        <span class="text-bold">{{ $salesOrder->number }}</span>
                    </div>
                    <div class="order-date-info text-center">
                        <span class="text-right sub-title">Date</span>
                        <span>:</span>
                        <span class="text-bold">{{ formatDate($salesOrder->date, 'd-M-y') }}</span>
                    </div>
                </div>
                <div class="float-left logo-section">
                    <img src="{{ url('assets/img/logo.png') }}" alt="">
                    <br>
                    <span class="logo-section-phone-info">Phone : +62 822-8239-3930</span>
                </div>
                <div class="float-right customer-section">
                    <span class="customer-section-greetings">Dear :</span>
                    <br>
                    <span class="customer-name-info">{{ $salesOrder->customer_name }}</span>
                    <br>
                    <span class="customer-address-info text-wrap">{{ $salesOrder->customer_address }}</span>
                    <br>
                </div>
                <br>
                <br>
                <table class="table table-sm table-responsive-sm table-hover table-general-info">
                    <thead class="text-center">
                        <tr class="table-general-info-head">
                            <td class="table-general-info-head-number">Order Number</td>
                            <td class="table-general-info-head-date">Order Date</td>
                            <td class="table-general-info-head-tempo">Tempo</td>
                            <td class="table-general-info-head-date">Due Date</td>
                            <td class="table-general-info-head-marketing">Marketing</td>
                            <td>Admin</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-general-info-body">
                            <td class="text-center">{{ $salesOrder->number }}</td>
                            <td class="text-center">{{ formatDate($salesOrder->date, 'd-M-y') }}</td>
                            <td class="text-center">{{ $salesOrder->tempo }} Day</td>
                            <td class="text-center">{{ getDueDate($salesOrder->date, $salesOrder->tempo, 'd-m-Y') }}</td>
                            <td class="text-center">{{ $salesOrder->marketing_name }}</td>
                            <td class="text-center">{{ $salesOrder->user_name }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-sm table-responsive-sm table-order-item">
                    <thead class="text-center table-order-item-head">
                        <tr>
                            <td class="table-order-item-head-number">No</td>
                            <td class="table-order-item-head-product">Product Name</td>
                            <td class="table-order-item-head-quantity">Qty</td>
                            <td class="table-order-item-head-price">Price</td>
                            <td class="table-order-item-head-total">Total</td>
                            <td colspan="2">Discount</td>
                            <td class="table-order-item-head-subtotal">Subtotal</td>
                        </tr>
                    </thead>
                    <tbody class="table-order-item-body">
                        @foreach($salesOrder->salesOrderItems as $index => $salesOrderItem)
                            <tr class="table-order-item-body-row">
                                <td class="text-center">{{ ++$index }}</td>
                                <td>{{ $salesOrderItem->product_name }}</td>
                                <td class="text-center">{{ $salesOrderItem->quantity }}</td>
                                <td class="text-right">{{ formatPrice($salesOrderItem->price) }}</td>
                                <td class="text-right">{{ formatPrice($salesOrderItem->total) }}</td>
                                <td class="text-right" style="width: 55px; font-size: 14.5px">{{ $salesOrderItem->discount }}</td>
                                <td class="text-right" style="width: 65px">{{ formatPrice($salesOrderItem->discount_amount) }}</td>
                                <td class="text-right">{{ formatPrice($salesOrderItem->final_amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="container-fluid footer-section">
                    <table class="table-footer">
                        <thead>
                            <tr>
                                <td class="table-footer-head-recipient">
                                    <div class="recipient-signature">
                                        <table class="recipient-signature-table">
                                            <tr>
                                                <td class="text-center">Recipient,</td>
                                            </tr>
                                            <tr>
                                                <td class="recipient-signature-table-blank"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">(__________)</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td class="table-footer-head-account-info">
                                    <div class="payment-info">
                                        <span>Giro / Transfer Payment</span>
                                        <br>
                                        <span>BCA Bank Account</span>
                                        <br>
                                        <span>p.p Indah Ramadhon 5790416491</span>
                                    </div>
                                </td>
                                <td class="table-footer-head-warehouse">
                                    <div class="warehouse-signature">
                                        <table class="warehouse-signature-table">
                                            <tr>
                                                <td class="text-center">Warehouse,</td>
                                            </tr>
                                            <tr>
                                                <td class="warehouse-signature-table-blank"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">(___________)</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td style="border-right: 1px dotted; width: 88px">
                                    <div class="ttd-mengetahui">
                                        <table style="font-size: 15px !important">
                                            <tr>
                                                <td class="tgl-ttd">{{ formatDate($salesOrder->date, 'd-M-y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">Mengetahui,</td>
                                            </tr>
                                            <tr>
                                                <td style="height: 30px"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">(__________)</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td>
                                    <div class="total-faktur">
                                        <table class="tabel-total-faktur">
                                            <tr>
                                                <td class="title-total text-bold">Total</td>
                                                <td class="text-right angka-total">{{ formatPrice($salesOrder->subtotal) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="title-total text-bold">Invoice Discount</td>
                                                <td class="text-right angka-total">{{ formatPrice($salesOrder->discount_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="title-total text-bold">Subtotal</td>
                                                <td class="text-right angka-total">{{ formatPrice($salesOrder->subtotal - $salesOrder->discount_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="title-total text-bold">Tax Amount</td>
                                                <td class="text-right angka-total">{{ formatPrice($salesOrder->tax_amount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="title-total"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="height: 3px"></td>
                                            </tr>
                                            <tr>
                                                <td class="title-total text-bold">Grand Total</td>
                                                <td class="text-right angka-total-akhir">{{ formatPrice($salesOrder->grand_total) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="float-right waktu-cetak-so">
                    <span class="waktu-cetak">Print Time : {{ $printDate }} {{ $printTime }}</span>
                    <span class="cetak-ke">Print Count: {{ $salesOrder->print_count }}</span>
                </div>
            </div>
        @endforeach

        <script type="text/javascript">
            window.onafterprint = function() {
                {{--const url = '{{ route('sales-orders.after-print', $id) }}';--}}
                {{--window.location = url + '?start_number=' + encodeURIComponent('{{ $startNumber }}') + '&final_number=' + encodeURIComponent('{{ $finalNumber }}');--}}
            }

            // window.print();
        </script>
    </body>
</html>
