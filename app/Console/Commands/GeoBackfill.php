<?php

namespace App\Console\Commands;

use App\Enum\GeoType;
use App\Jobs\HydrateGeo;
use App\Jobs\DownloadGeo;
use App\Jobs\ProcessGeo;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Bus;

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
        $this->info('[Start] Batching Started');
        // setup batch
        $batch = Bus::batch([
            new DownloadGeo('regions.json'),
            new DownloadGeo('subregions.json'),
            new DownloadGeo('countries.json'),
            new DownloadGeo('states.json'),
            new DownloadGeo('cities.json'),
            new HydrateGeo('regions.json', GeoType::REGION),
            new HydrateGeo('subregions.json', GeoType::SUBREGION),
            new HydrateGeo('countries.json', GeoType::COUNTRY),
            new HydrateGeo('states.json', GeoType::STATE),
            new HydrateGeo('cities.json', GeoType::CITY),
        ])
            ->onQueue('low');
        $batch->dispatch();
        $this->info('[Dispatched]');
    }
}
