<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApiImport\SamsungApiService;
use App\Services\ApiImport\AppleApiService;
use App\Services\ApiImport\SonyApiService;
use Illuminate\Support\Facades\Log;

class ApiImportController extends Controller
{
    /**
     * Display the API import dashboard
     */
    public function index()
    {
        // Get available API integrations
        $availableApis = $this->getAvailableApis();
        
        // Get recent import logs - this would need a database table and model
        $recentImports = []; // Import::latest()->take(10)->get();
        
        return view('admin.imports.index', compact('availableApis', 'recentImports'));
    }
    
    /**
     * Run an import from a specific API
     */
    public function runImport(Request $request)
    {
        $request->validate([
            'api' => 'required|string',
            'options' => 'nullable|array',
        ]);
        
        $apiName = $request->input('api');
        $options = $request->input('options', []);
        
        try {
            // Get the appropriate API service
            $apiService = $this->getApiService($apiName);
            
            if (!$apiService) {
                return $this->handleImportError("API service for '{$apiName}' not found or not enabled.");
            }
            
            // Run the import
            $importStats = $apiService->importProducts($options);
            
            // Log the import stats
            Log::info("API Import completed for {$apiName}", $importStats);
            
            // Future: Save import log to database
            // $importLog = new Import([
            //     'api' => $apiName,
            //     'stats' => json_encode($importStats),
            //     'options' => json_encode($options),
            //     'status' => $importStats['errors'] ? 'partial' : 'success',
            // ]);
            // $importLog->save();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully imported products from {$apiName}",
                    'stats' => $importStats
                ]);
            }
            
            return redirect()->route('admin.imports.index')
                ->with('success', "Successfully imported products from {$apiName}")
                ->with('stats', $importStats);
                
        } catch (\Exception $e) {
            return $this->handleImportError("Error importing from {$apiName}: " . $e->getMessage(), $e);
        }
    }
    
    /**
     * Get a list of available API integrations
     */
    private function getAvailableApis()
    {
        return [
            'samsung' => [
                'name' => 'Samsung',
                'description' => 'Import Samsung products (phones, TVs, refrigerators, etc.)',
                'enabled' => config('api_imports.samsung.enabled', false),
                'endpoint' => config('api_imports.samsung.endpoint'),
            ],
            'apple' => [
                'name' => 'Apple',
                'description' => 'Import Apple products (iPhones, iPads, MacBooks, etc.)',
                'enabled' => config('api_imports.apple.enabled', false),
                'endpoint' => config('api_imports.apple.endpoint'),
            ],
            'sony' => [
                'name' => 'Sony',
                'description' => 'Import Sony products (TVs, cameras, audio equipment, etc.)',
                'enabled' => config('api_imports.sony.enabled', false),
                'endpoint' => config('api_imports.sony.endpoint'),
            ],
            // Add more APIs as needed
        ];
    }
    
    /**
     * Get the appropriate API service based on the name
     */
    private function getApiService($apiName)
    {
        // Check if the API is enabled in config
        if (!config("api_imports.{$apiName}.enabled", false)) {
            return null;
        }
        
        // Return the appropriate service based on the API name
        switch ($apiName) {
            case 'samsung':
                return new SamsungApiService();
            case 'apple':
                return new AppleApiService();
            case 'sony':
                return new SonyApiService();
            default:
                return null;
        }
    }
    
    /**
     * Handle import errors
     */
    private function handleImportError($message, \Exception $exception = null)
    {
        if ($exception) {
            Log::error($message, [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
        } else {
            Log::error($message);
        }
        
        // Future: Save error log to database
        // $importLog = new Import([
        //     'api' => $apiName ?? 'unknown',
        //     'stats' => json_encode(['errors' => [$message]]),
        //     'status' => 'error',
        // ]);
        // $importLog->save();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }
        
        return redirect()->route('admin.imports.index')
            ->with('error', $message);
    }
} 