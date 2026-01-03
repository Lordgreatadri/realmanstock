<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::with('assignedTo')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);
        $newCount = ContactMessage::new()->count();

        return view('admin.contact.index', compact('messages', 'newCount'));
    }

    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();
        $admins = User::role('admin')->get();
        
        return view('admin.contact.show', compact('contactMessage', 'admins'));
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,responded,archived',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $contactMessage->update($validated);

        return redirect()->back()->with('success', 'Contact message updated successfully.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact.index')->with('success', 'Contact message deleted successfully.');
    }
}
