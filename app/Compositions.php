<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compositions extends Model
{
    //
    protected $fillable = [
        'name',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $parts = preg_split('/\s+/', $name);
        
        $mc = self::query();
        if(!empty($parts))  {
            foreach($parts as $p){
                $mc->orWhere("name","like","%".trim($p)."%");
            }
        }
        $mc = $mc->distinct('name')->get(['name', 'replace_with']);

        $isReplacementFound = false;
        if (!$mc->isEmpty() && !empty($name)) {
            foreach ($mc as $key => $c) {
                // check if the full replacement found then assign from there
                if (strtolower($name) == strtolower($c->name)) {
                    $name = $c->replace_with;
                    $isReplacementFound = true;
                    break;
                }

                foreach($parts as $p) {
                    if (strtolower($p) == strtolower($c->name)) {
                        $name = str_replace($p, $c->replace_with, $name);
                        $isReplacementFound = true;
                    }
                }
            }
        }

        // check if replacement found then assing that to the composition otherwise add new one and start next process
        if($isReplacementFound) {
            return $name;
        }

        // in this case color refenrece we don't found so we need to add that one
        if(!empty($name)) {
            self::create([
                'name'         => $name,
                'replace_with' => '',
            ]);
        }

        // Return an empty string by default
        return '';
    }
}
