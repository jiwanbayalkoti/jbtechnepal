<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $menuItem = new \App\Models\MenuItem([
        'name' => 'Products',
        'url' => '/products',
        'order' => 1
    ]);
    $menuItem->save();
    
    echo "Menu item created with ID: " . $menuItem->id . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 