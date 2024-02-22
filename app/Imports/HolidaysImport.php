<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Holiday as ModelsHoliday;

class HolidaysImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $dateToCheck = \Carbon\Carbon::createFromFormat('j/n/Y', sprintf('%d/%d/%d', $row[1], $row[2], $row[3]))->format('Y-m-d');

            $holiday = ModelsHoliday::where('date', $dateToCheck)->first();

            if ($holiday) {
                $holiday->update([
                    'name' => $row[0],
                    'description' => $row[4] ?? null,
                    'updated_by' => auth()->id(),
                ]);
            } else {
                ModelsHoliday::create([
                    'name' => $row[0],
                    'date' => $dateToCheck,
                    'description' => $row[4] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }
}
