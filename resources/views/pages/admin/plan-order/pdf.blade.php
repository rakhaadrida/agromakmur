<html lang="en">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            body {
                margin: 0;
                font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-size: 1rem;
                font-weight: 400;
                line-height: 1.5;
                color: #858796;
                text-align: left;
                background-color: #fff;
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

            .td-number {
                width: 10px;
            }

            .td-receipt-number {
                width: 90px;
            }

            .td-admin {
                width: 60px;
            }

            .td-receipt-date  {
                width: 70px;
            }

            .td-branch {
                width: 75px;
            }

            .td-receipt-status {
                width: 65px;
            }

            .td-total-items {
                width: 50px;
            }

            .td-grand-total {
                width: 70px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-section">
            <div class="header-section text-center">
                <h5 class="text-bold text-dark">Daftar Plan Order</h5>
                <h6 class="text-dark report-date">Tanggal Laporan : {{ formatDateIso($startDate, 'DD MMM Y') }} - {{ formatDateIso($finalDate, 'D MMM Y') }}</h6>
                <h6 class="text-dark report-date">Tanggal Export : {{ $exportDate }}</h6>
            </div>

            <table class="table table-sm table-bordered table-items">
                <thead class="text-center text-dark text-bold" >
                    <tr>
                        <th class="align-middle td-number">No</th>
                        <th class="align-middle td-receipt-number">Nomor PO</th>
                        <th class="align-middle td-receipt-date">Tanggal</th>
                        <th class="align-middle td-branch">Cabang</th>
                        <th class="align-middle">Supplier</th>
                        <th class="align-middle td-total-items">Total Barang</th>
                        <th class="align-middle td-receipt-status">Status</th>
                        <th class="align-middle td-admin">Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($planOrders as $index => $planOrder)
                        <tr class="text-dark">
                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                            <td class="align-middle text-center">{{ $planOrder->number }}</td>
                            <td class="align-middle text-center">{{ formatDate($planOrder->date, 'd-M-y')  }}</td>
                            <td class="align-middle">{{ $planOrder->branch_name }}</td>
                            <td class="align-middle">{{ $planOrder->supplier_name }}</td>
                            <td class="align-middle text-center">{{ $planOrder->total_items }}</td>
                            <td class="align-middle text-center">{{ getPlanOrderStatusLabel($planOrder->status) }}</td>
                            <td class="align-middle text-center">{{ $planOrder->user_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-bold text-dark text-center">GRAND TOTAL BARANG</th>
                        <th class="text-bold text-dark text-center">{{ formatPrice($grandTotalItems) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>
