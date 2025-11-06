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

            .td-invoice-count {
                width: 50px;
            }

            .td-status {
                width: 60px;
            }

            .td-grand-total {
                width: 75px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-section">
            <div class="header-section text-center">
                <h5 class="text-bold text-dark">Account Receivable Data</h5>
                <h6 class="text-dark report-date">Report Date : {{ formatDate($startDate, 'd M Y') }} - {{ formatDate($finalDate, 'd M Y') }}</h6>
                <h6 class="text-dark report-date">Export Date : {{ $exportDate }}</h6>
            </div>

            <table class="table table-sm table-bordered table-items">
                <thead class="text-center text-dark text-bold" >
                    <tr>
                        <th class="align-middle td-number">No</th>
                        <th class="align-middle">Customer</th>
                        <th class="align-middle td-invoice-count">Invoice Count</th>
                        <th class="align-middle td-grand-total">Total Amount</th>
                        <th class="align-middle td-grand-total">Payment</th>
                        <th class="align-middle td-grand-total">Return Amount</th>
                        <th class="align-middle td-grand-total">Outstanding Amount</th>
                        <th class="align-middle td-status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accountReceivables as $index => $accountReceivable)
                        <tr class="text-dark">
                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                            <td class="align-middle">{{ $accountReceivable->customer_name }}</td>
                            <td class="align-middle text-center">{{ $accountReceivable->invoice_count }}</td>
                            <td class="align-middle text-right">{{ formatPrice($accountReceivable->grand_total) }}</td>
                            <td class="align-middle text-right">{{ formatPrice($accountReceivable->payment_amount) }}</td>
                            <td class="align-middle text-right">{{ formatPrice($accountReceivable->return_amount) }}</td>
                            <td class="align-middle text-right">{{ formatPrice($accountReceivable->outstanding_amount) }}</td>
                            <td class="align-middle text-center">{{ getAccountReceivableStatusLabel($accountReceivable->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-bold text-dark text-center">GRAND TOTAL</th>
                        <th class="text-bold text-dark text-center">{{ getGrandTotal($accountReceivables, 'invoice_count') }}</th>
                        <th class="text-bold text-dark text-right">{{ getGrandTotal($accountReceivables, 'grand_total') }}</th>
                        <th class="text-bold text-dark text-right">{{ getGrandTotal($accountReceivables, 'payment_amount') }}</th>
                        <th class="text-bold text-dark text-right">{{ getGrandTotal($accountReceivables, 'return_amount') }}</th>
                        <th class="text-bold text-dark text-right">{{ getGrandTotal($accountReceivables, 'outstanding_amount') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>
