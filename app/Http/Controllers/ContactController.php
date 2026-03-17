<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if (Schema::hasTable('contact_messages')) {
            DB::table('contact_messages')->insert([
                'name'       => $request->input('name'),
                'email'      => $request->input('email'),
                'phone'      => $request->input('phone'),
                'message'    => $request->input('message'),
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('contact')->with('contact_success', true);
    }

    /**
     * Developer panel — list messages.
     */
    public function index()
    {
        try {
            $messages    = DB::table('contact_messages')->orderByDesc('created_at')->paginate(25);
            $unreadCount = DB::table('contact_messages')->where('is_read', false)->count();
        } catch (\Throwable $e) {
            $messages    = null;
            $unreadCount = 0;
        }

        return view('developer.contact-messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Developer panel — single message.
     */
    public function show(int $id)
    {
        $msg = DB::table('contact_messages')->where('id', $id)->first();

        if (!$msg) {
            abort(404);
        }

        if (!$msg->is_read) {
            DB::table('contact_messages')->where('id', $id)->update(['is_read' => true]);
            $msg->is_read = true;
        }

        return view('developer.contact-messages.show', compact('msg'));
    }

    /**
     * Toggle read status.
     */
    public function toggleRead(int $id)
    {
        $msg = DB::table('contact_messages')->where('id', $id)->first();

        if (!$msg) {
            abort(404);
        }

        $newStatus = !$msg->is_read;
        DB::table('contact_messages')->where('id', $id)->update([
            'is_read'    => $newStatus,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', $newStatus ? 'Okundu olarak işaretlendi.' : 'Okunmadı olarak işaretlendi.');
    }

    /**
     * Delete message.
     */
    public function destroy(int $id)
    {
        DB::table('contact_messages')->where('id', $id)->delete();

        return redirect()->route('developer.contact-messages')->with('success', 'Mesaj silindi.');
    }
}
