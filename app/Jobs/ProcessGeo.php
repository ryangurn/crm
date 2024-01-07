<?php

namespace App\Jobs;

use App\Enum\GeoType;
use App\Models\Reference\Timezone;
use App\Models\Reference\Region;
use App\Models\Reference\SubRegion;
use App\Models\Reference\Country;
use App\Models\Reference\State;
use App\Models\Reference\City;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGeo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data, public ?GeoType $type)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // check if foreign key data exists
        // if not fail and set retry policy.

        switch ($this->type) {
            case GeoType::REGION:
                $this->processRegion();
                break;
            case GeoType::SUBREGION:
                $this->processSubRegion();
                break;
            case GeoType::COUNTRY:
                $this->processCountry();
                break;
            case GeoType::STATE:
                $this->processState();
                break;
            case GeoType::CITY:
                $this->processCity();
                break;
            default:
                $this->fail("Unknown type provided: " . $this->type);
                break;
        }
    }

    private function processRegion()
    {
        if (Region::where('name', $this->data['name'])->count() != 0)
            return;

        $region = Region::firstOrCreate([
            'name' => $this->data['name']
        ]);
        $region->id = $this->data['id'];
        $region->translations = $this->data['translations'] ?? [];
        $region->save();
    }

    private function processSubRegion()
    {
        if (SubRegion::where('name', $this->data['name'])->where('region_id', $this->data['region_id'])->count() != 0)
            return;

        $subRegion = SubRegion::firstOrCreate([
            'name' => $this->data['name'],
            'region_id' => $this->data['region_id']
        ]);
        $subRegion->id = $this->data['id'];
        $subRegion->translations = $this->data['translations'] ?? [];
        $subRegion->save();
    }

    private function processCountry()
    {
        if (Country::where('name', $this->data['name'])->count() != 0)
            return;

        $country = Country::firstOrNew([
            'name' => $this->data['name'],
        ]);
        $country->id = $this->data['id'];
        $country->iso3 = $this->data['iso3'];
        $country->iso2 = $this->data['iso2'];
        $country->numeric_code = $this->data['numeric_code'];
        $country->phone_code = $this->data['phone_code'];
        $country->capital = $this->data['capital'];
        $country->tld = $this->data['tld'];
        $country->native = $this->data['native'];
        $country->region_id = $this->data['region_id'] ?? null;
        $country->sub_region_id = $this->data['subregion_id'] ?? null;
        $country->nationality = $this->data['nationality'];
        $country->translations = $this->data['translations'] ?? [];
        $country->latitude = $this->data['latitude'];
        $country->longitude = $this->data['longitude'];
        $country->emoji = $this->data['emoji'];
        $country->emojiU = $this->data['emojiU'];
        $country->save();

        if ($this->data['timezones'] == null) {
            return;
        }

        foreach ($this->data['timezones'] as $tz) {
            $timezone = Timezone::firstOrNew([
                'zone_name' => $tz['zoneName'],
            ]);

            if (Timezone::where('zone_name', $tz['zoneName'])->count() == 0) {
                $timezone->name = $tz['tzName'];
                $timezone->gmt_offset = $tz['gmtOffset'];
                $timezone->gmt_offset_name = $tz['gmtOffsetName'];
                $timezone->abbreviation = $tz['abbreviation'];
                $timezone->save();
            }

            $timezone->relCountry()->attach($country);
        }
    }

    private function processState()
    {
        if (State::where('name', $this->data['name'])->count() != 0)
            return;

        $state = State::firstOrNew([
            'name' => $this->data['name'],
        ]);
        $state->id = $this->data['id'];
        $state->country_id = $this->data['country_id'];
        $state->state_code = $this->data['state_code'];
        $state->type = $this->data['type'];
        $state->latitude = $this->data['latitude'];
        $state->longitude = $this->data['longitude'];
        $state->save();
    }

    private function processCity()
    {
        if (City::where('name', $this->data['name'])->count() != 0)
            return;

        $city = City::firstOrNew([
            'name' => $this->data['name'],
        ]);
        $city->country_id = Country::where('name', $this->data['country_name'])->first()->id;
        $city->state_id = State::where('name', $this->data['state_name'])->first()->id;
        $city->latitude = $this->data['latitude'];
        $city->longitude = $this->data['longitude'];
        $city->save();
    }
}
