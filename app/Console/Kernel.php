<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Import products from all enabled APIs daily at 2:00 AM
        $schedule->command('products:import-api --all')->dailyAt('02:00');
    }

    /**
     * Get the commands that should be removed from the default Artisan command list.
     *
     * @return array
     */
    protected function getExcludedCommands(): array
    {
        return [];
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
