<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CsvTranslator extends Model
{
    protected $table = 'csv_translators';

    protected $fillable = ['key', 'en', 'es', 'ru', 'ko', 'ja', 'it', 'de', 'fr', 'nl', 'zh', 'ar', 'ur'];
}
