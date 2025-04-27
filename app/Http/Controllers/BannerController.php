<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * Get all active banners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveBanners(Request $request)
    {
        try {
            $query = Banner::where('is_active', true)
                          ->with(['images' => function($query) {
                               $query->orderBy('display_order', 'asc');
                           }])
                          ->orderBy('order', 'asc');
                          
            // Filter by page_id if provided
            if ($request->has('page_id') && $request->page_id) {
                $query->where('page_id', $request->page_id);
            }
            
            // Limit results if requested
            if ($request->has('limit') && is_numeric($request->limit)) {
                $query->limit($request->limit);
            }
            
            $banners = $query->get();
            
            // Transform the response to include image URLs
            $banners = $banners->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'image_url' => $banner->image_url, // Uses accessor from model
                    'link' => $banner->link,
                    'images' => $banner->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'is_primary' => $image->is_primary,
                        ];
                    }),
                ];
            });
            
            return response()->json([
                'success' => true,
                'banners' => $banners
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific banner by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanner($id)
    {
        try {
            $banner = Banner::with('images')->findOrFail($id);
            
            if (!$banner->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Banner is not active'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'banner' => [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'image_url' => $banner->image_url,
                    'link' => $banner->link,
                    'images' => $banner->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'is_primary' => $image->is_primary,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Get banners for a specific page by slug.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBannersByPage($slug)
    {
        try {
            $page = \App\Models\Page::where('slug', $slug)->first();
            
            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found'
                ], 404);
            }
            
            $banners = Banner::where('page_id', $page->id)
                            ->where('is_active', true)
                            ->with('images')
                            ->orderBy('order', 'asc')
                            ->get();
            
            return response()->json([
                'success' => true,
                'banners' => $banners->map(function($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'image_url' => $banner->image_url,
                        'link' => $banner->link,
                        'images' => $banner->images->map(function($image) {
                            return [
                                'id' => $image->id,
                                'image_url' => $image->image_url,
                                'is_primary' => $image->is_primary,
                            ];
                        }),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banners',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 