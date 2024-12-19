<?php

namespace App\Jobs;

use App\APIs\NewsapiApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NewsapiJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public Carbon $beginDate;
    public Carbon $endDate;
    public int $fetchPage;
    public int $sourceId;
    public string $source;
    public function __construct(Carbon $endDate, int $fetchPage, int $sourceId, string $source)
    {
        $this->beginDate = $endDate->clone()->subDay();
        $this->endDate = $endDate;
        $this->fetchPage = $fetchPage;
        $this->sourceId = $sourceId;
        $this->source = $source;
    }


    /**
     * Execute the job.
     */
    public function handle(NewsapiApi $newsapiApi): void
    {
        $newsapiApi
            ->setBeginDate($this->beginDate)
            ->setEndDate($this->endDate)
            ->updateNews($this->sourceId, $this->source, $this->fetchPage);
    }
}
