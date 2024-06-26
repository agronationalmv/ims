<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    /**
     * Retrieve all the reports from the database.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Report::all();
    }

    /**
     * Define the column headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Created At',
            'Updated At'
        ];
    }
}
