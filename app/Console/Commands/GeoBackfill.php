<?php

namespace App\Console\Commands;

use App\Models\Reference\City;
use App\Models\Reference\Country;
use App\Models\Reference\Region;
use App\Models\Reference\State;
use App\Models\Reference\SubRegion;
use App\Models\Reference\Timezone;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Console\Command;

use function GuzzleHttp\json_encode;

class GeoBackfill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geo:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill countries, states and cities into reference tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // download all files
        $this->info('[Starting] Downloading JSON');
        $baseUrl = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/';
        $storagePath = storage_path('app/tmp/');
        $files = [
            'regions.json',
            'subregions.json',
            'countries.json',
            'states.json',
            'cities.json',
        ];
        foreach ($files as $file) {
            $this->info('Downloading ' . $file);
            file_put_contents($storagePath . $file, file_get_contents($baseUrl . $file),);
        }
        $this->info('[Completed]');

        JsonParser::parse($storagePath . 'regions.json')->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            $region = Region::firstOrCreate([
                'name' => $value['name'],
            ]);
            $region->id = $value['id'];
            $region->translations = json_encode($value['translations']);
            if ($region->save()) {
                $this->info('Region: ' . $value['name']);
            } else {
                $this->warn('Region: ' . $value['name'] . ' | Unable to save');
            }
        });


        JsonParser::parse($storagePath . 'subregions.json')->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            $subRegion = SubRegion::firstOrCreate([
                'name' => $value['name'],
                'region_id' => $value['region_id'],
            ]);
            $subRegion->id = $value['id'];
            $subRegion->translations = $value['translations'];
            if ($subRegion->save()) {
                $this->info('Sub Region: ' . $value['name']);
            } else {
                $this->warn('Sub Region: ' . $value['name'] . ' | Unable to save');
            }
        });

        JsonParser::parse($storagePath . 'countries.json')->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            // country
            $country = Country::firstOrNew([
                'name' => $value['name'],
            ]);
            $country->id = $value['id'];
            $country->iso3 = $value['iso3'];
            $country->iso2 = $value['iso2'];
            $country->numeric_code = $value['numeric_code'];
            $country->phone_code = $value['phone_code'];
            $country->capital = $value['capital'];
            $country->tld = $value['tld'];
            $country->native = $value['native'];
            $country->region_id = $value['region_id'];
            $country->sub_region_id = $value['subregion_id'];
            $country->nationality = $value['nationality'];
            $country->translations = $value['translations'];
            $country->latitude = $value['latitude'];
            $country->longitude = $value['longitude'];
            $country->emoji = $value['emoji'];
            $country->emojiU = $value['emojiU'];
            if ($country->save()) {
                $this->info('Country: ' . $value['name']);
            } else {
                $this->warn('Country: ' . $value['name'] . ' | Unable to save');
            }

            // timezones
            if ($value['timezones'] == null) {
                return;
            }

            foreach ($value['timezones'] as $tz) {
                $timezone = Timezone::firstOrNew([
                    'zone_name' => $tz['zoneName'],
                ]);
                $timezone->name = $tz['tzName'];
                $timezone->gmt_offset = $tz['gmtOffset'];
                $timezone->gmt_offset_name = $tz['gmtOffsetName'];
                $timezone->abbreviation = $tz['abbreviation'];
                if ($timezone->save()) {
                    $this->info('Timezone: ' . $tz['tzName']);
                } else {
                    $this->warn('Timezone: ' . $tz['tzName'] . ' | Unable to save');
                }

                $timezone->relCountry()->attach($country);
            }
        });

        JsonParser::parse($storagePath . 'states.json')->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            $state = State::firstOrNew([
                'name' => $value['name'],
            ]);
            $state->id = $value['id'];
            $state->country_id = $value['country_id'];
            $state->state_code = $value['state_code'];
            $state->type = $value['type'];
            $state->latitude = $value['latitude'];
            $state->longitude = $value['longitude'];
            if ($state->save()) {
                $this->info('State: ' . $value['name']);
            } else {
                $this->warn('State: ' . $value['name'] . ' | Unable to save');
            }
        });

        JsonParser::parse($storagePath . 'cities.json')->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            $city = City::firstOrNew([
                'name' => $value['name'],
            ]);

            $city->country_id = Country::where('name', $value['country_name'])->first()->id;
            $city->state_id = State::where('name', $value['state_name'])->first()->id;
            $city->latitude = $value['latitude'];
            $city->longitude = $value['longitude'];
            if ($city->save()) {
                $this->info('City: ' . $value['name']);
            } else {
                $this->warn('City: ' . $value['name'] . ' | Unable to save');
            }
        });
    }
}
