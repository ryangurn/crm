<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadGeo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    
    /**
     * Create a new job instance.
     */
    public function __construct(public string $file)
    {
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (file_put_contents(
                storage_path('app/tmp/') . $this->file,
                file_get_contents(
                    'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/csv/' . $this->file
                )
            ) == false) {
            $this->fail("Unable to save file: " . $this->file);
        }
    }
}
