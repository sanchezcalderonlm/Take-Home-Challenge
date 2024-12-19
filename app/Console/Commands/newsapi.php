<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\APIs\NewsapiApi;

class newsapi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:newsapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(NewsapiApi $newsApi)
    {
        try {
            $endDate = $today = Carbon::now();
            $beginDate = $today->clone()->subDay();
            $newsApi->setBeginDate($beginDate)
                ->setEndDate($endDate)
                ->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
