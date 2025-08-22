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
                src: url('{{ public_path('assets/fonts/epson1.woff') }}');
            }

            @font-face {
                font-family: "Dotrice";
                font-weight: 800;
                src: url('{{ public_path('assets/fonts/Dotrice.ttf') }}');
            }

            @font-face {
                font-family: "Dotrice Bold";
                font-weight: 800;
                src: url('{{ public_path('assets/fonts/Dotrice-Bold.otf') }}');
            }

            @font-face {
                font-family: "buenard";
                font-weight: 800;
                src: url('{{ public_path('assets/fonts/Buenard-Regular.ttf') }}');
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

            .align-middle {
                vertical-align: middle !important;
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
                margin-bottom: -1.438rem;
                page-break-after: always;
            }

            .table-order-item {
                font-size: 16px;
                width: 90.1% !important;
                height: 52.5% !important;
                margin-right: 34px;
            }

            .table-order-item thead td {
                padding-top: 0.3rem !important;
                padding-left: 0 !important;
            }

            .table-order-item tbody td:empty {
                border-left: 0;
                border-right: 0;
                border-top: 0;
            }

            .print-header {
                width: 90.3% !important;
            }

            .header-section {
                display: inline-block;
                color: black;
                border: 1px dotted;
                border-bottom: none;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                border-left: 1px solid black;
                border-right: 1px solid black;
                padding-top: 5px;
                padding-bottom: 10px;
                margin-left: -1px;
                margin-right: 0;
                margin-bottom: -6px !important;
                margin-top: -5px;
                width: 97.3% !important;
            }

            .title-header {
                font-weight: bold;
                font-family: Arial, Helvetica, sans-serif;
            }

            .order-info-section {
                font-family: 'Courier New', Courier, monospace;
                color: black;
                font-size: 15px;
                text-align: left;
                margin-top: 1.2rem;
                margin-bottom: 0.5rem;
            }

            .order-info-row {
                width: 600px;
                display: flex;
                margin-bottom: 2px;
            }

            .order-info-label {
                font-family: 'Calibri', Helvetica, sans-serif;
                width: 380px;
                text-align: right;
                padding-right: 0.4rem;
                margin-top: -0.1rem !important;
            }

            .order-info-separator {
                text-align: center;
            }

            .order-info-value {
                flex: 1;
                padding-left: 0.4rem;
                text-align: left;
                width: 120px;
                font-weight: bold;
            }

            .logo-section {
                margin-top: -95px;
                margin-left: -7px;
            }

            .logo-section img {
                width: 148px;
                height: 45px;
            }

            .logo-section-phone-info {
                font-family: "Rockwell", Helvetica, sans-serif;
                font-size: 10.5px;
                line-height: 15px;
                margin-left: 10px;
            }

            .customer-section {
                color: black;
                font-weight: 500;
                font-size: 16px;
                width: 260px;
                margin-top: -95px;
                margin-right: 0;
            }

            .customer-section-greetings {
                font-size: 12px;
            }

            .customer-name-info {
                color: black;
                font-size: 16px;
                line-height: 22px;
            }

            .customer-address-info {
                color: black;
                font-size: 15px;
                margin-bottom: -10px;
                line-height: 14px;
            }

            .table-order-item-head {
                margin-top: -1rem !important;
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
                width: 95px;
            }

            .table-order-item-head-unit {
                width: 70px;
            }

            .table-order-item-head-price {
                width: 70px;
            }

            .table-order-item-head-total {
                width: 110px;
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
                margin: -5px 0 -5px -3px;
                width: 97.5% !important;
            }

            .table-footer {
                margin-left: -15px;
                width: 860px;
                margin-right: -50px;
            }

            .table-footer-head-recipient {
                width: 363px;
                border-right: 1px dotted;
            }

            .recipient-signature {
                font-size: 15px;
                padding-left: 5px;
                margin-bottom: 0;
                margin-top: 0;
            }

            .payment-info {
                color: black;
                margin-top: 0;
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
                margin-top: 5px;
                margin-left: 1px;
                line-height: 14px;
            }

            .warehouse-signature-table {
                font-size: 15px !important;
            }

            .warehouse-signature-table-blank {
                height: 30px;
            }

            .table-footer-head-admin {
                width: 88px;
                border-right: 1px dotted;
            }

            .admin-signature {
                font-size: 15px;
                margin-top: -2px;
                margin-bottom: 0;
                line-height: 10px;
            }

            .admin-signature-table {
                font-size: 15px !important;
            }

            .admin-signature-table-date {
                font-size: 13px;
                line-height: 3px;
                padding-bottom: 0.25rem !important;
            }

            .admin-signature-table .admin-signature-label {
                padding-top: 0 !important;
            }

            .admin-signature-table-blank {
                height: 30px;
            }

            .invoice-amount-section {
                margin-top: 0;
                margin-left: 5px;
                font-size: 14px;
            }

            .invoice-amount-table {
                line-height: 6px;
            }

            .invoice-amount-label {
                font-size: 14px;
                width: 100px;
            }

            .invoice-amount-number {
                width: 157px;
                font-size: 16px;
                padding-right: 0.01rem !important;
            }

            .print-time-section {
                font-weight: 700;
                margin-top: 5px;
            }

            .print-time-section-time {
                font-size: 12px !important;
            }

            @media print {
                @page {
                    width: 21.8cm;
                    height: 13.8cm;
                    margin: 0.4002cm 1.27cm 0.144cm 0.281cm;

                    @bottom-right {
                        /*content: "Page " counter(page);*/
                        font-family: "Calibri", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                        font-size: 12px;
                        font-weight: bold;
                        margin-top: -24.235rem !important;
                        margin-right: 17.75rem !important;
                    }
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
        @foreach($planOrders as $key => $planOrder)
            <div class="print-container" id="printContainer">
                <table class="table table-sm table-responsive-sm table-order-item">
                    <thead class="print-header">
                        <tr>
                            <td colspan="9">
                                <div class="container-fluid header-section">
                                    <div class="title-header text-center">
                                        <h5 class="text-bold">PLAN ORDER</h5>
                                    </div>
                                    <div class="order-info-section">
                                        <div class="order-info-row">
                                            <span class="order-info-label">Number</span>
                                            <span class="order-info-separator">:</span>
                                            <span class="order-info-value">{{ $planOrder->number }}</span>
                                        </div>
                                        <div class="order-info-row">
                                            <span class="order-info-label">Date</span>
                                            <span class="order-info-separator">:</span>
                                            <span class="order-info-value">{{ formatDate($planOrder->date, 'd-M-y') }}</span>
                                        </div>
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
                                    <span class="customer-name-info">{{ $planOrder->supplier_name }}</span>
                                    <br>
                                    <span class="customer-address-info text-wrap">{{ $planOrder->supplier_address }}</span>
                                    <br>
                                </div>
                            </td>
                        </tr>
                        <tr class="text-center table-order-item-head">
                            <td class="table-order-item-head-number">No</td>
                            <td class="table-order-item-head-product">Product Name</td>
                            <td class="table-order-item-head-quantity">Qty</td>
                            <td class="table-order-item-head-unit">Unit</td>
                            <td class="table-order-item-head-price">Price</td>
                            <td class="table-order-item-head-total">Total</td>
                        </tr>
                    </thead>
                    <tbody class="table-order-item-body">
                        @foreach($planOrder->planOrderItems as $index => $planOrderItem)
                            <tr class="table-order-item-body-row">
                                <td class="text-center">{{ ++$index }}</td>
                                <td>{{ $planOrderItem->product->name }}</td>
                                <td class="text-right">{{ $planOrderItem->quantity }}</td>
                                <td class="text-center">{{ $planOrderItem->unit->name }}</td>
                                <td class="text-right">{{ formatPrice($planOrderItem->price) }}</td>
                                <td class="text-right">{{ formatPrice($planOrderItem->total) }}</td>
                            </tr>
                        @endforeach
                        @for($i = $planOrder->total_rows; $i < ($planOrder->total_page * 15); $i++)
                            <tr class="table-order-item-body-row">
                                <td colspan="6"></td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <div class="container-fluid footer-section">
                                    <table class="table-footer">
                                        <thead>
                                            <tr>
                                                <td class="table-footer-head-recipient">
                                                    <div class="recipient-signature">
                                                        <div class="payment-info">
                                                            <span>Note:</span>
                                                            <br>
                                                            <span>1. Apabila ada perubahan harga, harap konfirmasi terlebih dahulu</span>
                                                            <br>
                                                            <span>2. Harap kirimkan barang hanya sesuai pesanan</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="table-footer-head-warehouse">
                                                    <div class="warehouse-signature">
                                                        <table class="warehouse-signature-table">
                                                            <tr>
                                                                <td class="text-center">Warehouse</td>
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
                                                <td class="table-footer-head-admin">
                                                    <div class="admin-signature">
                                                        <table class="admin-signature-table">
                                                            <tr>
                                                                <td class="text-center admin-signature-table-date">{{ formatDate($planOrder->date, 'd-M-y') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center admin-signature-label">Admin</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="admin-signature-table-blank"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">(__________)</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="invoice-amount-section">
                                                        <table class="invoice-amount-table">
                                                            <tr>
                                                                <td class="text-bold invoice-amount-label">Total</td>
                                                                <td class="text-right invoice-amount-number">{{ formatPrice($planOrder->subtotal) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="float-left print-time-section">
                                    <span class="print-time-section-time">Print Time : {{ $printDate }} {{ $printTime }}</span>
                                </div>
                                <div class="float-right print-time-section">
                                    <span class="print-time-section-time"><span class="page-number"></span> of {{ $planOrder->total_page }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach

        <script type="text/javascript">
            function paginateTable(table, pageHeight = 1122) {
                let rows = table.querySelectorAll("tbody tr");
                let footer = table.querySelector("tfoot td .page-number");

                let totalHeight = 0;
                let page = 1;
                let pages = 1;
                let rowHeights = [];

                rows.forEach(row => {
                    rowHeights.push(row.offsetHeight);
                });

                totalHeight = 0;
                pages = 1;
                for (let h of rowHeights) {
                    totalHeight += h;
                    if (totalHeight > pageHeight) {
                        pages++;
                        totalHeight = h;
                    }
                }

                footer.innerText = `Page ${page}`;
            }

            window.onbeforeprint = () => {
                document.querySelectorAll("table.table-order-item").forEach(table => {
                    paginateTable(table, 842);
                });
            };

            window.onafterprint = function() {
                const url = '{{ route('plan-orders.after-print', $id) }}';
                window.location = url + '?start_number=' + encodeURIComponent('{{ $startNumber }}') + '&final_number=' + encodeURIComponent('{{ $finalNumber }}');
            }

            window.print();
        </script>
    </body>
</html>
