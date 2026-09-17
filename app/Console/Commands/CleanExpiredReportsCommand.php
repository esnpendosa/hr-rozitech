<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanExpiredReportsCommand extends Command
{
    protected $signature = 'reports:clean-expired';
    protected $description = 'Delete generated report files older than 7 days';

    public function handle(): void
    {
        // Full implementation in Task 53
        $this->info('Cleaning expired report files...');
    }
}
