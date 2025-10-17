<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DeliveryOrderExport implements WithMultipleSheets
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
            new DeliveryOrderSheet($this->request),
            new DeliveryOrderItemSheet($this->request),
        ];
    }
}
