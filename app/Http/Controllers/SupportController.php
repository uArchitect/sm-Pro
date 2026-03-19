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

        $tenantId = session('tenant_id');
        if (!$tenantId) {
            abort(403);
        }

        DB::table('support_tickets')->insert([
            'tenant_id'  => $tenantId,
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
        $messages = $this->buildConversation($ticket, $user?->name ?? __('support.user'));

        return view('support.show', compact('ticket', 'user', 'messages'));
    }

    public function reply(Request $request, int $id)
    {
        $tenantId = session('tenant_id');

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket = DB::table('support_tickets')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$ticket) {
            abort(404);
        }

        $thread = $this->decodeThread((string) ($ticket->admin_reply ?? ''));
        $thread[] = [
            'sender'   => 'user',
            'user_id'  => Auth::id(),
            'name'     => Auth::user()?->name ?? __('support.user'),
            'message'  => trim((string) $request->message),
            'datetime' => now()->toIso8601String(),
        ];

        DB::table('support_tickets')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update([
                'admin_reply' => json_encode($thread, JSON_UNESCAPED_UNICODE),
                'status'      => 'open',
                'updated_at'  => now(),
            ]);

        return back()->with('success', __('support.message_sent'));
    }

    private function buildConversation(object $ticket, string $requesterName): array
    {
        $messages = [[
            'sender'   => 'user',
            'name'     => $requesterName,
            'message'  => (string) $ticket->message,
            'datetime' => $ticket->created_at,
        ]];

        $thread = $this->decodeThread((string) ($ticket->admin_reply ?? ''));
        foreach ($thread as $item) {
            if (!is_array($item) || empty($item['message'])) {
                continue;
            }
            $messages[] = [
                'sender'   => $item['sender'] ?? 'developer',
                'name'     => $item['name'] ?? (($item['sender'] ?? '') === 'user' ? $requesterName : 'Developer'),
                'message'  => (string) $item['message'],
                'datetime' => $item['datetime'] ?? $ticket->updated_at,
            ];
        }

        // Backward compatibility for old single reply data
        if (empty($thread) && !empty($ticket->admin_reply)) {
            $messages[] = [
                'sender'   => 'developer',
                'name'     => 'Developer',
                'message'  => (string) $ticket->admin_reply,
                'datetime' => $ticket->replied_at ?? $ticket->updated_at,
            ];
        }

        return $messages;
    }

    private function decodeThread(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
