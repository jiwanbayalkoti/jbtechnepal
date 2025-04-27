<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$menuItems = \App\Models\MenuItem::all();
foreach ($menuItems as $item) {
    echo "ID: {$item->id}, Name: {$item->name}, URL: {$item->url}, Order: {$item->order}\n";
} 