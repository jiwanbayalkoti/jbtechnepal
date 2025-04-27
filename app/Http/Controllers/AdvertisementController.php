<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * Get advertisements for a specific position.
     *
     * @param string $position
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByPosition($position)
    {
        $ads = Advertisement::where('position', $position)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->orderBy('display_order')
            ->get();
            
        // Record views for these ads
        foreach ($ads as $ad) {
            $ad->recordView();
        }
        
        return response()->json($ads);
    }
    
    /**
     * Record a click on an advertisement and return its URL for JavaScript to handle the redirect.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordClick($id)
    {
        try {
            $ad = Advertisement::findOrFail($id);
            $ad->recordClick();
            
            return response()->json([
                'success' => true,
                'url' => $ad->url,
                'id' => $ad->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Advertisement not found or error recording click',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
