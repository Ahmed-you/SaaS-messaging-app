<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cloud SaaS Messaging') }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --panel: #ffffff;
            --ink: #172033;
            --muted: #647084;
            --line: #d8e0ea;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --amber: #b45309;
            --red: #b42318;
            --green: #167647;
            --soft-green: #e4f6ee;
            --soft-amber: #fff4dd;
            --soft-red: #fff0ed;
            --soft-blue: #e9f2ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Inter, Segoe UI, Arial, sans-serif;
            line-height: 1.5;
        }

        .shell {
            width: min(1240px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 36px;
        }

        .topbar,
        .panel,
        .message,
        .stat {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 20px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        h1 {
            font-size: clamp(25px, 3vw, 34px);
            line-height: 1.1;
            font-weight: 800;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 14px;
        }

        h3 {
            font-size: 15px;
        }

        .muted {
            color: var(--muted);
            font-size: 14px;
        }

        .selectors {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 10px;
            min-width: min(460px, 100%);
        }

        label {
            display: block;
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 6px;
        }

        input,
        select,
        textarea {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #fff;
            color: var(--ink);
            padding: 9px 11px;
            font: inherit;
        }

        textarea {
            min-height: 132px;
            resize: vertical;
        }

        input:disabled,
        select:disabled,
        textarea:disabled {
            background: #f1f4f8;
            color: #8a94a6;
        }

        .notice,
        .errors {
            border-radius: 8px;
            margin-top: 16px;
            padding: 12px 14px;
        }

        .notice {
            background: var(--soft-green);
            border: 1px solid #b7dfcf;
            color: #0b5138;
        }

        .errors {
            background: var(--soft-red);
            border: 1px solid #f2b8b3;
            color: var(--red);
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .layout {
            display: grid;
            grid-template-columns: 390px minmax(0, 1fr);
            gap: 18px;
            margin-top: 18px;
            align-items: start;
        }

        .panel {
            padding: 18px;
        }

        .section-gap {
            margin-top: 18px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat {
            padding: 14px;
        }

        .stat strong {
            display: block;
            font-size: 25px;
            line-height: 1.15;
            margin-top: 4px;
        }

        .stack {
            display: grid;
            gap: 12px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            border: 0;
            border-radius: 6px;
            background: var(--brand);
            color: #fff;
            padding: 9px 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background: var(--brand-dark);
        }

        .button.secondary {
            background: var(--soft-blue);
            color: #164782;
            border: 1px solid #bdd4f3;
        }

        .button:disabled {
            cursor: not-allowed;
            background: #a7b0bf;
        }

        .field {
            margin-bottom: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            border-radius: 999px;
            padding: 2px 9px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .pill.active,
        .pill.read {
            background: var(--soft-green);
            color: var(--green);
        }

        .pill.trialing,
        .pill.unread {
            background: var(--soft-amber);
            color: var(--amber);
        }

        .pill.suspended,
        .pill.canceled {
            background: var(--soft-red);
            color: var(--red);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 1px solid var(--line);
            padding: 10px 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .module-list {
            display: grid;
            gap: 8px;
        }

        .module-option {
            display: grid;
            grid-template-columns: 20px minmax(0, 1fr);
            gap: 8px;
            align-items: start;
        }

        .module-option input {
            min-height: auto;
            margin-top: 3px;
        }

        .message-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .message {
            padding: 14px;
        }

        .message-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .body {
            margin: 8px 0 12px;
            color: #344054;
            white-space: pre-wrap;
        }

        .empty {
            border-radius: 8px;
            background: #f1f4f8;
            color: var(--muted);
            padding: 12px 14px;
        }

        @media (max-width: 980px) {
            .topbar,
            .layout,
            .message-columns,
            .selectors,
            .stats {
                grid-template-columns: 1fr;
            }

            .topbar {
                display: grid;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="topbar">
            <div>
                <p class="muted">Cloud SaaS prototype</p>
                <h1>Multi-Tenant Messaging System</h1>
            </div>

            <div class="selectors">
                <form method="GET" action="{{ route('messages.index') }}">
                    <label for="company_id">Tenant company</label>
                    <select id="company_id" name="company_id" onchange="this.form.submit()">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected($currentCompany?->id === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <form method="GET" action="{{ route('messages.index') }}">
                    <input type="hidden" name="company_id" value="{{ $currentCompany?->id }}">
                    <label for="user_id">Company user</label>
                    <select id="user_id" name="user_id" onchange="this.form.submit()" @disabled($users->isEmpty())>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($currentUser?->id === $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </section>

        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="layout">
            <aside>
                <div class="panel">
                    <h2>Super Admin Console</h2>
                    <div class="stack">
                        @foreach ($companies as $company)
                            <div>
                                <div class="message-head">
                                    <div>
                                        <h3>{{ $company->name }}</h3>
                                        <p class="muted">{{ $company->contact_email }}</p>
                                    </div>
                                    <span class="pill {{ $company->status }}">{{ $company->status }}</span>
                                </div>
                                <p class="muted">
                                    {{ $company->users_count }} users · {{ $company->messages_count }} messages ·
                                    {{ $company->subscription?->plan_name ?? 'No plan' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($currentCompany)
                    <div class="panel section-gap">
                        <h2>Company Control</h2>
                        <form method="POST" action="{{ route('admin.companies.status', $currentCompany) }}">
                            @csrf
                            @method('PATCH')
                            <div class="field">
                                <label for="status">Company status</label>
                                <select id="status" name="status">
                                    <option value="active" @selected($currentCompany->status === 'active')>active</option>
                                    <option value="trialing" @selected($currentCompany->status === 'trialing')>trialing</option>
                                    <option value="suspended" @selected($currentCompany->status === 'suspended')>suspended</option>
                                </select>
                            </div>
                            <button class="button secondary" type="submit">Save Status</button>
                        </form>

                        <form class="section-gap" method="POST" action="{{ route('admin.companies.modules', $currentCompany) }}">
                            @csrf
                            @method('PATCH')
                            <label>Enabled modules</label>
                            <div class="module-list">
                                @foreach ($availableModules as $module)
                                    <label class="module-option">
                                        <input type="checkbox" name="module_ids[]" value="{{ $module->id }}" @checked(in_array($module->id, $enabledModuleIds, true))>
                                        <span>
                                            <strong>{{ $module->name }}</strong><br>
                                            <span class="muted">{{ $module->description }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <button class="button secondary section-gap" type="submit">Save Modules</button>
                        </form>
                    </div>
                @endif
            </aside>

            <section>
                @if ($currentCompany)
                    <div class="stats" aria-label="Tenant statistics">
                        <div class="stat">
                            <span class="muted">Tenant</span>
                            <strong>{{ $currentCompany->name }}</strong>
                        </div>
                        <div class="stat">
                            <span class="muted">Subscription</span>
                            <strong>{{ $currentCompany->subscription?->status ?? 'none' }}</strong>
                        </div>
                        <div class="stat">
                            <span class="muted">Inbox</span>
                            <strong>{{ $inbox->count() }}</strong>
                        </div>
                        <div class="stat">
                            <span class="muted">Unread</span>
                            <strong>{{ $unreadCount }}</strong>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="message-head">
                            <div>
                                <h2>Tenant Workspace</h2>
                                <p class="muted">
                                    Plan: {{ $currentCompany->subscription?->plan_name ?? 'No plan' }}
                                    · Seats: {{ $currentCompany->subscription?->seats ?? 0 }}
                                    · Modules: {{ $currentCompany->modules->pluck('name')->join(', ') ?: 'none' }}
                                </p>
                            </div>
                            <span class="pill {{ $canSendMessages ? 'active' : 'suspended' }}">
                                {{ $canSendMessages ? 'Messaging enabled' : 'Messaging blocked' }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('messages.store') }}">
                            @csrf
                            <input type="hidden" name="sender_id" value="{{ $currentUser?->id }}">

                            <div class="field">
                                <label for="recipient_id">Send to</label>
                                <select id="recipient_id" name="recipient_id" required @disabled(! $canSendMessages || $recipients->isEmpty())>
                                    @foreach ($recipients as $recipient)
                                        <option value="{{ $recipient->id }}" @selected((int) old('recipient_id') === $recipient->id)>
                                            {{ $recipient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="subject">Subject</label>
                                <input id="subject" name="subject" value="{{ old('subject') }}" maxlength="120" required @disabled(! $canSendMessages)>
                            </div>

                            <div class="field">
                                <label for="body">Message</label>
                                <textarea id="body" name="body" maxlength="2000" required @disabled(! $canSendMessages)>{{ old('body') }}</textarea>
                            </div>

                            <button class="button" type="submit" @disabled(! $canSendMessages || $recipients->isEmpty())>Send Message</button>
                        </form>
                    </div>

                    <div class="message-columns">
                        <div class="panel">
                            <h2>Inbox</h2>
                            <div class="stack">
                                @forelse ($inbox as $message)
                                    <article class="message">
                                        <div class="message-head">
                                            <div>
                                                <h3>{{ $message->subject }}</h3>
                                                <p class="muted">From {{ $message->sender->name }} · {{ $message->created_at->format('Y-m-d H:i') }}</p>
                                            </div>
                                            <span class="pill {{ $message->read_at ? 'read' : 'unread' }}">
                                                {{ $message->read_at ? 'read' : 'new' }}
                                            </span>
                                        </div>
                                        <p class="body">{{ $message->body }}</p>

                                        @unless ($message->read_at)
                                            <form method="POST" action="{{ route('messages.read', $message) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
                                                <button class="button secondary" type="submit">Mark as Read</button>
                                            </form>
                                        @endunless
                                    </article>
                                @empty
                                    <div class="empty">No inbox messages for this tenant user.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="panel">
                            <h2>Sent</h2>
                            <div class="stack">
                                @forelse ($sent as $message)
                                    <article class="message">
                                        <div class="message-head">
                                            <div>
                                                <h3>{{ $message->subject }}</h3>
                                                <p class="muted">To {{ $message->recipient->name }} · {{ $message->created_at->format('Y-m-d H:i') }}</p>
                                            </div>
                                            <span class="pill {{ $message->read_at ? 'read' : 'unread' }}">
                                                {{ $message->read_at ? 'read' : 'unread' }}
                                            </span>
                                        </div>
                                        <p class="body">{{ $message->body }}</p>
                                    </article>
                                @empty
                                    <div class="empty">No sent messages for this tenant user.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="panel">
                        <div class="empty">No companies exist yet.</div>
                    </div>
                @endif
            </section>
        </section>
    </main>
</body>
</html>
