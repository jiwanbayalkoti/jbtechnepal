<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        $pendingCount = ContactMessage::where('status', 'pending')->count();
        
        return view('admin.contact.index', compact('messages', 'pendingCount'));
    }
    
    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read if it's pending
        if ($message->status === 'pending') {
            $message->status = 'read';
            $message->save();
        }
        
        return view('admin.contact.show', compact('message'));
    }
    
    /**
     * Update the specified contact message with a reply.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string',
        ]);
        
        $message = ContactMessage::findOrFail($id);
        $message->admin_reply = $request->admin_reply;
        $message->status = 'replied';
        $message->save();
        
        // TODO: Send email notification to user about the reply
        
        return redirect()->route('admin.contact.show', $message->id)
            ->with('success', 'Reply sent successfully.');
    }
    
    /**
     * Remove the specified contact message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Message deleted successfully.');
    }
}
