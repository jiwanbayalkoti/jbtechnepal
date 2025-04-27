<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;

class AddSearchMenuItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:add-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add the search menu item to the navigation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the search menu item already exists
        $exists = MenuItem::where('route_name', 'search')
                         ->where('location', 'main')
                         ->exists();
        
        if ($exists) {
            $this->info('Search menu item already exists.');
            return;
        }
        
        // Get the highest order for main menu
        $maxOrder = MenuItem::where('location', 'main')->max('order') ?? 0;
        
        // Create the search menu item
        MenuItem::create([
            'name' => 'Search',
            'url' => null,
            'route_name' => 'search',
            'icon' => 'fas fa-search',
            'location' => 'main',
            'active' => true,
            'order' => $maxOrder + 1,
            'parent_id' => null,
        ]);
        
        $this->info('Search menu item added successfully!');
    }
} 