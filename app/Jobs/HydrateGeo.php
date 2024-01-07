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
        // Opening the file for reading
        $fileStream = fopen(storage_path('app/tmp/' . $this->file), 'r');
        $csvContents = [];

        // Reading the file line by line into an array
        while (($line = fgetcsv($fileStream)) !== false) {
            $csvContents[] = $line;
        }

        // Closing the file stream
        fclose($fileStream);

        $skipHeader = true;
        // Attempt to import the CSV
        foreach ($csvContents as $content) {
            if ($skipHeader) {
                // Skipping the header column (first row)
                $skipHeader = false;
                continue;
            }

            $arr = [];
            foreach ($csvContents[0] as $key => $index) {
                $arr[$index] = $content[$key];
            }

            $this->batch()->add(
                new ProcessGeo($arr, $this->type)
            );
        }
    }
}
