<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    /**
     * Public form submission — store message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        if (!Schema::hasTable('contact_messages')) {
            return redirect()->route('contact')->with('contact_success', true);
        }

        ContactMessage::create($request->only('name', 'email', 'phone', 'message'));

        return redirect()->route('contact')->with('contact_success', true);
    }

    /**
     * Developer panel — list messages.
     */
    public function index()
    {
        $messages = $this->safeQuery(fn () =>
            ContactMessage::orderByDesc('created_at')->paginate(25)
        );
        $unreadCount = $this->safeQuery(fn () =>
            ContactMessage::where('is_read', false)->count(), 0
        );

        return view('developer.contact-messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Developer panel — single message.
     */
    public function show(int $id)
    {
        $msg = ContactMessage::findOrFail($id);

        if (!$msg->is_read) {
            $msg->update(['is_read' => true]);
        }

        return view('developer.contact-messages.show', compact('msg'));
    }

    /**
     * Toggle read status.
     */
    public function toggleRead(int $id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->update(['is_read' => !$msg->is_read]);

        return redirect()->back()->with('success', $msg->is_read ? 'Okundu olarak işaretlendi.' : 'Okunmadı olarak işaretlendi.');
    }

    /**
     * Delete message.
     */
    public function destroy(int $id)
    {
        ContactMessage::findOrFail($id)->delete();

        return redirect()->route('developer.contact-messages')->with('success', 'Mesaj silindi.');
    }

    private function safeQuery(callable $fn, $default = null)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
