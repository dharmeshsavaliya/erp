<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Creatomate\Client;

class CreatomateController extends Controller
{

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createVideo(Request $request)
    {
       
    $client = new Client(env('CREATOMATE_API'));

    $renders = $client->render([
        'template_id' => 'ae5fc0d1-0b57-48fa-930e-3644c9e8a1f7',
        'modifications' => [
            'cf27818e-07e9-40bf-b115-762957b98986' => 'Hi! 👋 Thanks for trying out Creatomate!'
        ],
    ]);

    return response()->json($renders, 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

}