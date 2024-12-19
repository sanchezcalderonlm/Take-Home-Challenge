<?php

namespace App\Console\Commands;

use App\APIs\NytimesApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class nytimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:nytimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(NytimesApi $nytimesApi)
    {
        try {
            $endDate = $today = Carbon::now();
            $beginDate = $today->clone()->subDay();
            $nytimesApi->setBeginDate($beginDate)
                ->setEndDate($endDate)
                ->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
