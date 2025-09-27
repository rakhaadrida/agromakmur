<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GoodsReceiptExport implements WithMultipleSheets
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        return [
            new GoodsReceiptSheet($this->request),
            new GoodsReceiptItemSheet($this->request),
        ];
    }
}
