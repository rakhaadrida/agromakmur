<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AccountReceivableExport implements WithMultipleSheets
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
            new AccountReceivableSheet($this->request),
            new AccountReceivableItemSheet($this->request),
        ];
    }
}
