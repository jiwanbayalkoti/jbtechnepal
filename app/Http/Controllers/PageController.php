<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the specified page by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();
        
        return view('pages.show', compact('page'));
    }

    /**
     * Display the about us page.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        $page = Page::where('slug', 'about-us')
                    ->where('is_active', true)
                    ->firstOrFail();
        
        return view('pages.about', compact('page'));
    }

    /**
     * Display the contact us page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        $page = Page::where('slug', 'contact-us')
                    ->where('is_active', true)
                    ->firstOrFail();
        
        return view('pages.contact', compact('page'));
    }

    /**
     * Store a contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Store the contact message
        $message = \App\Models\ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);
        
        return redirect()->route('contact')
            ->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }
} 