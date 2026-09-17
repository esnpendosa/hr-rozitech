<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTargetRiskCommand extends Command
{
    protected $signature = 'targets:check-risk';
    protected $description = 'Assess target risk levels and send AT_RISK/CRITICAL notifications';

    public function handle(): void
    {
        // Full implementation in Task 39
        $this->info('Checking target risk levels...');
    }
}
