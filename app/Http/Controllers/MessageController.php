<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Message;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $companies = Company::query()
            ->with(['subscription', 'modules'])
            ->withCount(['users', 'messages'])
            ->orderBy('name')
            ->get();

        $currentCompany = $companies->firstWhere('id', $request->integer('company_id')) ?? $companies->first();
        $users = $currentCompany
            ? User::query()
                ->where('company_id', $currentCompany->id)
                ->orderBy('name')
                ->get()
            : collect();
        $currentUser = $users->firstWhere('id', $request->integer('user_id')) ?? $users->first();
        $recipients = $currentUser
            ? $users->where('id', '!=', $currentUser->id)->values()
            : collect();

        $inbox = $currentUser
            ? Message::query()
                ->with('sender')
                ->where('company_id', $currentCompany->id)
                ->where('recipient_id', $currentUser->id)
                ->latest()
                ->get()
            : collect();

        $sent = $currentUser
            ? Message::query()
                ->with('recipient')
                ->where('company_id', $currentCompany->id)
                ->where('sender_id', $currentUser->id)
                ->latest()
                ->get()
            : collect();

        $availableModules = Module::query()->orderBy('name')->get();
        $enabledModuleIds = $currentCompany
            ? $currentCompany->modules->pluck('id')->all()
            : [];
        $subscriptionActive = in_array($currentCompany?->subscription?->status, ['active', 'trialing'], true);
        $companyActive = in_array($currentCompany?->status, ['active', 'trialing'], true);
        $messagingModuleEnabled = $currentCompany?->hasModule('messaging') ?? false;
        $canSendMessages = $companyActive && $subscriptionActive && $messagingModuleEnabled;
        $unreadCount = $inbox->whereNull('read_at')->count();

        return view('messages.index', [
            'companies' => $companies,
            'currentCompany' => $currentCompany,
            'users' => $users,
            'currentUser' => $currentUser,
            'recipients' => $recipients,
            'inbox' => $inbox,
            'sent' => $sent,
            'availableModules' => $availableModules,
            'enabledModuleIds' => $enabledModuleIds,
            'subscriptionActive' => $subscriptionActive,
            'companyActive' => $companyActive,
            'messagingModuleEnabled' => $messagingModuleEnabled,
            'canSendMessages' => $canSendMessages,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sender_id' => ['required', 'integer', 'exists:users,id'],
            'recipient_id' => ['required', 'integer', 'exists:users,id', 'different:sender_id'],
            'subject' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $sender = User::query()
            ->with(['company.subscription', 'company.modules'])
            ->findOrFail($validated['sender_id']);
        $recipient = User::query()
            ->where('company_id', $sender->company_id)
            ->findOrFail($validated['recipient_id']);
        $company = $sender->company;

        $canSendMessages = $company
            && in_array($company->status, ['active', 'trialing'], true)
            && in_array($company->subscription?->status, ['active', 'trialing'], true)
            && $company->hasModule('messaging');

        if (! $canSendMessages) {
            return redirect()
                ->route('messages.index', ['company_id' => $sender->company_id, 'user_id' => $sender->id])
                ->withErrors('This company cannot send messages because its subscription, status, or Messaging module is inactive.');
        }

        Message::create([
            'company_id' => $sender->company_id,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);

        return redirect()
            ->route('messages.index', ['company_id' => $sender->company_id, 'user_id' => $sender->id])
            ->with('status', 'Message sent inside this company tenant.');
    }

    public function markAsRead(Request $request, Message $message): RedirectResponse
    {
        $userId = $request->integer('user_id');
        $user = User::query()->findOrFail($userId);

        abort_unless($message->company_id === $user->company_id && $message->recipient_id === $userId, 403);

        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return redirect()
            ->route('messages.index', ['company_id' => $message->company_id, 'user_id' => $userId])
            ->with('status', 'Message marked as read.');
    }
}
