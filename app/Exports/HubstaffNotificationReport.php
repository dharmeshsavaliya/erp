<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HubstaffNotificationReport implements FromArray, ShouldAutoSize, WithHeadings
{
    protected $lists;

    public function __construct(array $lists)
    {
        $this->lists = $lists;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->lists;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Start Date',
            'Daily Working_hour',
            'Total Working_hour',
            'Different',
            'Min Percentage',
            'Actual Percentage',
            'Reason',
            'Status',
        ];
    }
}
