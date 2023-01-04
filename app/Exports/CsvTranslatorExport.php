<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\CsvTranslator;

class CsvTranslatorExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      return CsvTranslator::select('*')->get();
    }
}
