<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiImport\SamsungApiService;
use Illuminate\Support\Facades\Log;

class ImportProductsFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-api {api?} {--all : Import from all enabled APIs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from brand APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->importFromAllApis();
        } else {
            $apiName = $this->argument('api');
            
            if (!$apiName) {
                $this->error('Please specify an API name or use --all to import from all enabled APIs.');
                return 1;
            }
            
            $this->importFromApi($apiName);
        }
        
        return 0;
    }
    
    /**
     * Import products from all enabled APIs
     */
    protected function importFromAllApis()
    {
        $allApis = ['samsung', 'apple', 'sony']; // Add more as needed
        $successCount = 0;
        
        foreach ($allApis as $apiName) {
            if (config("api_imports.{$apiName}.enabled", false)) {
                $result = $this->importFromApi($apiName);
                if ($result === 0) {
                    $successCount++;
                }
            } else {
                $this->info("Skipping {$apiName} (disabled in config)");
            }
        }
        
        $this->info("Import completed. {$successCount} of " . count($allApis) . " APIs processed successfully.");
    }
    
    /**
     * Import products from a specific API
     */
    protected function importFromApi(string $apiName)
    {
        $this->info("Starting import from {$apiName}...");
        
        try {
            // Check if API is enabled
            if (!config("api_imports.{$apiName}.enabled", false)) {
                $this->warn("The {$apiName} API is disabled in configuration.");
                return 1;
            }
            
            // Get the appropriate service
            $apiService = $this->getApiService($apiName);
            
            if (!$apiService) {
                $this->error("No service found for {$apiName} API.");
                return 1;
            }
            
            // Run the import
            $startTime = now();
            $stats = $apiService->importProducts();
            $endTime = now();
            $duration = $endTime->diffInSeconds($startTime);
            
            // Display results
            $this->info("Import completed in {$duration} seconds.");
            $this->table(
                ['Total', 'Created', 'Updated', 'Failed'],
                [[
                    $stats['total'], 
                    $stats['created'], 
                    $stats['updated'], 
                    $stats['failed']
                ]]
            );
            
            // Show errors if any
            if (!empty($stats['errors'])) {
                $this->warn("Encountered " . count($stats['errors']) . " errors:");
                foreach ($stats['errors'] as $index => $error) {
                    $this->error("[{$index}] {$error}");
                }
            }
            
            // Log the results
            Log::info("API Import from {$apiName} completed", [
                'stats' => $stats,
                'duration' => $duration
            ]);
            
            return count($stats['errors']) > 0 ? 1 : 0;
            
        } catch (\Exception $e) {
            $this->error("Error importing from {$apiName}: " . $e->getMessage());
            Log::error("Error in API import command", [
                'api' => $apiName,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
    
    /**
     * Get the appropriate API service
     */
    protected function getApiService(string $apiName)
    {
        switch ($apiName) {
            case 'samsung':
                return new SamsungApiService();
            // Add cases for other APIs
            default:
                return null;
        }
    }
} 