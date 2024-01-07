<?php

namespace App\Jobs;

use App\Enum\GeoType;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HydrateGeo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $file, public GeoType $type)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        JsonParser::parse(storage_path('app/tmp/') . $this->file)->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            $this->batch()->add(
                new ProcessGeo($value, $this->type),
            );
        });
    }
}
