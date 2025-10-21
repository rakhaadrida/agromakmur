<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ValueRecapExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        $categories = Category::All();

        foreach($categories as $category) {
            $sheets[] = new ValueRecapItemSheet($category);
        }

        return $sheets;
    }
}
