<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

use Http;

class StoreCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for store cities of the state of countries.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $states = State::has('country')->with('country')->get();

        foreach($states as $state){

            $country = $state->country;

            $url = 'https://api.countrystatecity.in/v1/countries/'.$country->code.'/states/'.$state->code.'/cities';

            // API Reference: https://countrystatecity.in/docs/api/cities-by-state-country/
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => 'WUZWeG9GbFpXMnhEcmRBNUZzN0JIYXpuN1FlMTd3eG1YR2duRnlwRA==',
            ])->get($url)->json();

            if(!@$response['error']){
                
                foreach($response as $value){

                    $input = array(
                        "name" => $value['name'],
                        "state_id" => $state->id,
                        "country_id" => $country->id,
                    );

                    City::updateOrCreate($input);

                    $this->info("Stored city: ". $value['name']);
                }

            }
        }

    }
}
