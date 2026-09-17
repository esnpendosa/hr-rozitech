<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckContractExpiryCommand extends Command
{
    protected $signature = 'employees:check-contract-expiry';
    protected $description = 'Check employee contracts expiring in 30/7/1 days and send notifications';

    public function handle(): void
    {
        // Full implementation in Task 19
        $this->info('Checking employee contract expiry...');
    }
}
