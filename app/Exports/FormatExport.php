<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class FormatExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([
            ['name', 'date', 'month', 'year', 'description'],
        ]);
    }

    public function headings(): array
    {
        // Define column headings
        return ['name', 'date', 'month', 'year', 'description'];
    }
}
