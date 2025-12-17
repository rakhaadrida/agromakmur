<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Print Receipt</title>
        <style>
            @page {
                size: 76mm;
                margin: 0;
                height: 100%
            }

            body {
                width: 76mm;
                height: 100%;
                margin: 0;
                padding: 4px;
                font-family: "Courier New", Courier, monospace;
                font-size: 11px;
                line-height: 1.2;
            }

            .center { text-align: center; }
            .right { text-align: right; }
            .bold { font-weight: bold; }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            td {
                padding: 1px 0;
                vertical-align: top;
            }

            .line {
                text-align: center;
                margin: 4px 0;
            }

            .store-name {
                font-size: 18px;
            }

            .order-number,
            .item-name,
            .table-item,
            .total-quantity {
                font-size: 12px !important;
            }

            .item-price {
                padding-left: 15px;
            }

            .total-quantity {
                margin-bottom: 10px;
            }

            .grand-total {
                font-size: 13px;
            }
        </style>
    </head>

    <body>
        <div class="center bold store-name">
            AGRO MAKMUR
        </div>
        <div class="center">
            {{ $salesOrder->branch->address }}<br>
            No. Telp: {{ $salesOrder->branch->phone_number }}
        </div>
        <div class="line">
            -------------------------------------------
        </div>
        <table>
            <tr>
                <td>
                    {{ $printDate }}<br>
                    {{ $printTime }}
                </td>
                <td class="right">
                    {{ $salesOrder->user->username }}<br>
                    {{ $salesOrder->customer->name }}<br>
                    {{ $salesOrder->customer->address }}
                </td>
            </tr>
        </table>
        <div class="order-number">{{ $salesOrder->number }}</div>
        <div class="line">
            -------------------------------------------
        </div>
        @foreach($salesOrder->salesOrderItems as $index => $salesOrderItem)
            <div class="bold item-name">
                {{ $index + 1 }}. {{ $salesOrderItem->product->name }}
            </div>
            <table class="table-item">
                <tr>
                    <td class="item-price">
                        {{ $salesOrderItem->quantity }} {{ $salesOrderItem->unit->name }} x {{ formatPrice($salesOrderItem->price) }}
                    </td>
                    <td class="right">
                        Rp {{ formatPrice($salesOrderItem->total) }}
                    </td>
                </tr>
            </table>
        @endforeach
        <div class="line">
            -------------------------------------------
        </div>
        <div class="total-quantity">Total QTY : {{ $salesOrder->total_quantity }}</div>
        <table>
            <tr>
                <td>Sub Total</td>
                <td class="right">Rp {{ formatPrice($salesOrder->subtotal) }}</td>
            </tr>
            @if($salesOrder->is_taxable)
                <tr>
                    <td>PPN</td>
                    <td class="right">Rp {{ formatPrice($salesOrder->tax_amount) }}</td>
                </tr>
            @endif
            <tr class="bold grand-total">
                <td>Total</td>
                <td class="right">Rp {{ formatPrice($salesOrder->grand_total) }}</td>
            </tr>
            <tr>
                <td>Pembayaran</td>
                <td class="right">Rp {{ formatPrice($salesOrder->payment_amount) }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="right">Rp {{ number_format($salesOrder->change_amount) }}</td>
            </tr>
        </table>
        <br>
        <div class="center">
            Terimakasih Telah Berbelanja
        </div>
        <br>

        <script type="text/javascript">
            window.onafterprint = function() {
                window.location = '{{ route('sales-orders.after-print-bill', $id) }}';
            }

            window.print();
        </script>
    </body>
</html>
