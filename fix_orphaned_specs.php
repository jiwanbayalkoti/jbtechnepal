<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProductSpecification;

echo "Starting orphaned specification check...\n";

// Find specifications with missing specification types
$orphanedSpecs = ProductSpecification::whereNotExists(function ($query) {
    $query->select(\DB::raw(1))
          ->from('specification_types')
          ->whereRaw('specification_types.id = product_specifications.specification_type_id');
})->get();

echo "Found " . $orphanedSpecs->count() . " orphaned specifications.\n";

// Option 1: Delete the orphaned specifications
if ($orphanedSpecs->count() > 0) {
    echo "Do you want to delete these orphaned specifications? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) === 'yes') {
        foreach ($orphanedSpecs as $spec) {
            echo "Deleting specification ID: " . $spec->id . " for product ID: " . $spec->product_id . "\n";
            $spec->delete();
        }
        echo "Orphaned specifications deleted successfully.\n";
    } else {
        echo "Skipping deletion.\n";
        
        // Display the orphaned specs for manual inspection
        echo "\nOrphaned specification details:\n";
        echo "------------------------------\n";
        foreach ($orphanedSpecs as $spec) {
            echo "ID: " . $spec->id . 
                 ", Product ID: " . $spec->product_id . 
                 ", Spec Type ID: " . $spec->specification_type_id . 
                 ", Value: " . $spec->value . "\n";
        }
    }
}

echo "Script completed.\n"; 