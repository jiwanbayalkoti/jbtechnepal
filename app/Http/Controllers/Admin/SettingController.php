<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $generalSettings = Setting::getByGroup('general');
        $seoSettings = Setting::getByGroup('seo');
        $contactSettings = Setting::getByGroup('contact');
        $socialSettings = Setting::getByGroup('social');
        
        return view('admin.settings.index', compact(
            'generalSettings', 
            'seoSettings', 
            'contactSettings',
            'socialSettings'
        ));
    }
    
    /**
     * Update the specified settings.
     */
    public function update(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                
                if (!$setting) {
                    continue;
                }
                
                // Handle image uploads
                if ($setting->type == 'image' && $request->hasFile($key)) {
                    $request->validate([
                        $key => 'image|mimes:jpg,jpeg,png,gif,ico|max:2048',
                    ]);
                    
                    // Delete old image if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    try {
                        // Store new image
                        $path = $request->file($key)->store('settings', 'public');
                        $value = $path;
                        
                        // Log success
                        \Log::info("File {$key} uploaded successfully to {$path}");
                    } catch (\Exception $e) {
                        \Log::error("Error uploading file {$key}: " . $e->getMessage());
                        return redirect()->back()->with('error', "Error uploading {$setting->label}: " . $e->getMessage());
                    }
                }
                
                // Special handling for checkboxes (they don't get submitted when unchecked)
                if ($setting->type == 'checkbox' && !$request->has($key)) {
                    $value = 0;
                }
                
                Setting::set($key, $value);
            }
            
            return redirect()->back()->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Update SEO settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSeoSettings(Request $request)
    {
        try {
            foreach ($request->except('_token', '_method') as $key => $value) {
                // Handle the og_image file upload if present
                if ($key == 'og_image' && $request->hasFile('og_image')) {
                    // Find the setting
                    $setting = Setting::where('key', 'og_image')->where('group', 'seo')->first();
                    
                    if ($setting) {
                        // Delete old image if exists
                        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                            Storage::disk('public')->delete($setting->value);
                        }
                        
                        // Store new image
                        $path = $request->file('og_image')->store('settings/seo', 'public');
                        Setting::set('og_image', $path);
                    }
                } else {
                    // For regular settings
                    $setting = Setting::where('key', $key)->where('group', 'seo')->first();
                    
                    if ($setting) {
                        Setting::set($key, $value);
                    }
                }
            }
            
            // Special handling for checkbox values that aren't submitted when unchecked
            if (!$request->has('robots_index')) {
                $setting = Setting::where('key', 'robots_index')->where('group', 'seo')->first();
                if ($setting) {
                    Setting::set('robots_index', false);
                }
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'SEO settings updated successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'SEO settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating SEO settings: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating SEO settings: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating SEO settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Update Contact settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateContactSettings(Request $request)
    {
        try {
            foreach ($request->except('_token', '_method') as $key => $value) {
                $setting = Setting::where('key', $key)->where('group', 'contact')->first();
                
                if ($setting) {
                    Setting::set($key, $value);
                }
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact settings updated successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Contact settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating contact settings: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating contact settings: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating contact settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Update Social Media settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSocialSettings(Request $request)
    {
        try {
            foreach ($request->except('_token', '_method') as $key => $value) {
                $setting = Setting::where('key', $key)->where('group', 'social')->first();
                
                if ($setting) {
                    Setting::set($key, $value);
                }
            }
            
            // Special handling for checkbox values that aren't submitted when unchecked
            $checkboxSettings = ['show_social_share', 'show_social_follow'];
            foreach ($checkboxSettings as $key) {
                if (!$request->has($key)) {
                    $setting = Setting::where('key', $key)->where('group', 'social')->first();
                    if ($setting) {
                        Setting::set($key, false);
                    }
                }
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Social media settings updated successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Social media settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating social media settings: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating social media settings: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error updating social media settings: ' . $e->getMessage());
        }
    }
}
