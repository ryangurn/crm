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
            [
                new DownloadGeo('regions.csv'),
                new HydrateGeo('regions.csv', GeoType::REGION),
            ],
            [
                new DownloadGeo('subregions.csv'),
                new HydrateGeo('subregions.csv', GeoType::SUBREGION),
            ],
            [
                new DownloadGeo('countries.csv'),
                new HydrateGeo('countries.csv', GeoType::COUNTRY),
            ],
            [
                new DownloadGeo('states.csv'),
                new HydrateGeo('states.csv', GeoType::STATE),
            ],
            [
                new DownloadGeo('cities.csv'),
                new HydrateGeo('cities.csv', GeoType::CITY),
            ],
        ])
            ->onQueue('low');
        $batch->dispatch();
        $this->info('[Dispatched]');
    }
}
