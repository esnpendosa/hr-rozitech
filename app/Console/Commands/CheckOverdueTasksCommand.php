<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueTasksCommand extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Mark tasks past their due date as overdue and send notifications';

    public function handle(): void
    {
        // Full implementation in Task 36
        $this->info('Checking overdue tasks...');
    }
}
