<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        $tickets = DB::table('support_tickets')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        DB::table('support_tickets')->insert([
            'tenant_id'  => session('tenant_id'),
            'user_id'    => Auth::id(),
            'subject'    => $request->subject,
            'message'    => $request->message,
            'status'     => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('support.index')->with('success', __('support.sent_success'));
    }

    public function show(int $id)
    {
        $tenantId = session('tenant_id');

        $ticket = DB::table('support_tickets')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ticket) {
            abort(404);
        }

        $user = DB::table('users')->find($ticket->user_id);

        return view('support.show', compact('ticket', 'user'));
    }
}
