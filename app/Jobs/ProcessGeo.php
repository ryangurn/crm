<?php

namespace App\Jobs;

use App\Enum\GeoType;
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
        dd($this->data, $this->type);

        switch ($this->type) {
            case GeoType::REGION:
                break;
            case GeoType::SUBREGION:
                break;
            case GeoType::COUNTRY:
                break;
            case GeoType::STATE:
                break;
            case GeoType::CITY:
                break;
            default:
                $this->fail("Unknown type provided: " . $this->type);
                break;
        }
    }
}
