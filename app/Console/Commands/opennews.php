<?php

namespace App\Console\Commands;

use App\APIs\OpennewsRSS;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class opennews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:opennews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(OpennewsRSS $opennewsRSS)
    {
        try {
            $opennewsRSS->fetchSourceAndUpdateNews();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
