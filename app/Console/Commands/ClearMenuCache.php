<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearMenuCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the menu cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('main_menu');
        Cache::forget('footer_menu');
        Cache::forget('footer_admin_menu');

        $this->info('Menu cache cleared successfully!');
    }
} 