<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menus:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix menu items by activating them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing menu items...');
        
        // Get all menu items
        $menuItems = MenuItem::all();
        
        // Count of inactive menu items
        $inactiveCount = $menuItems->where('active', false)->count();
        
        $this->info("Found {$menuItems->count()} menu items, {$inactiveCount} are inactive.");
        
        if ($inactiveCount === 0) {
            $this->info('All menu items are already active.');
            return;
        }
        
        // Set all menu items to active
        MenuItem::query()->update(['active' => true]);
        
        // Clear cache to ensure changes take effect
        Cache::forget('main_menu');
        Cache::forget('footer_menu');
        Cache::put('menu_updated', true, now()->addMinutes(5));
        
        $this->info('Successfully activated all menu items and cleared menu cache.');
    }
} 