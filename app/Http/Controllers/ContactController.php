<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('contact.index');
    }
    
    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $contactMessage = new ContactMessage([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);
        
        // Associate with user if logged in
        if (Auth::check()) {
            $contactMessage->user_id = Auth::id();
        }
        
        $contactMessage->save();
        
        // TODO: Send notification to admin (can be implemented with Laravel Notifications)
        
        return redirect()->route('contact.index')
            ->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
    
    /**
     * Show user's messages with admin replies
     */
    public function myMessages()
    {
        // Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to view your messages');
        }
        
        $messages = ContactMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('contact.my-messages', compact('messages'));
    }
}
