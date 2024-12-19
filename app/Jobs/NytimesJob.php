<?php

namespace App\Jobs;

use App\APIs\NytimesApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NytimesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public Carbon $beginDate;
    public Carbon $endDate;
    public int $fetchPage;
    public function __construct(Carbon $endDate, int $fetchPage)
    {
        $this->beginDate = $endDate->clone()->subDay();
        $this->endDate = $endDate;
        $this->fetchPage = $fetchPage;
    }

    /**
     * Execute the job.
     */
    public function handle(NytimesApi $nytimesApi): void
    {
        $nytimesApi
            ->setBeginDate($this->beginDate)
            ->setEndDate($this->endDate)
            ->fetchSourceAndUpdateNews($this->fetchPage);
    }
}
