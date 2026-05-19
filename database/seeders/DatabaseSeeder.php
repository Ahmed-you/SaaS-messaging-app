<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Message;
use App\Models\Module;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $modules = collect([
            [
                'key' => 'messaging',
                'name' => 'Messaging',
                'description' => 'Send and receive internal company messages.',
            ],
            [
                'key' => 'company-management',
                'name' => 'Company Management',
                'description' => 'Manage tenant profile and employees.',
            ],
            [
                'key' => 'subscriptions',
                'name' => 'Subscriptions',
                'description' => 'Manage SaaS plans, seats, and subscription status.',
            ],
            [
                'key' => 'reports',
                'name' => 'Reports',
                'description' => 'View company usage reports and message totals.',
            ],
        ])->map(fn (array $module) => Module::query()->firstOrCreate(
            ['key' => $module['key']],
            $module,
        ));

        $northwind = Company::query()->firstOrCreate(
            [
                'slug' => 'northwind-finance',
            ],
            [
                'name' => 'Northwind Finance',
                'contact_email' => 'admin@northwind.test',
                'status' => 'active',
            ],
        );
        $cedar = Company::query()->firstOrCreate(
            [
                'slug' => 'cedar-health',
            ],
            [
                'name' => 'Cedar Health',
                'contact_email' => 'admin@cedar.test',
                'status' => 'trialing',
            ],
        );
        $blueOrbit = Company::query()->firstOrCreate(
            [
                'slug' => 'blue-orbit',
            ],
            [
                'name' => 'Blue Orbit Studio',
                'contact_email' => 'admin@blueorbit.test',
                'status' => 'suspended',
            ],
        );

        Subscription::query()->firstOrCreate(
            ['company_id' => $northwind->id],
            [
                'plan_name' => 'Business',
                'status' => 'active',
                'seats' => 25,
                'monthly_price' => 99,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonth(),
            ],
        );
        Subscription::query()->firstOrCreate(
            ['company_id' => $cedar->id],
            [
                'plan_name' => 'Trial',
                'status' => 'trialing',
                'seats' => 10,
                'monthly_price' => 0,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(9),
            ],
        );
        Subscription::query()->firstOrCreate(
            ['company_id' => $blueOrbit->id],
            [
                'plan_name' => 'Starter',
                'status' => 'canceled',
                'seats' => 5,
                'monthly_price' => 29,
                'starts_at' => now()->subMonths(2),
                'ends_at' => now()->subDays(2),
            ],
        );

        $northwind->modules()->syncWithPivotValues($modules->pluck('id')->all(), ['enabled_at' => now()]);
        $cedar->modules()->syncWithPivotValues(
            $modules->whereIn('key', ['messaging', 'subscriptions'])->pluck('id')->all(),
            ['enabled_at' => now()],
        );
        $blueOrbit->modules()->syncWithPivotValues(
            $modules->where('key', 'company-management')->pluck('id')->all(),
            ['enabled_at' => now()],
        );

        User::query()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'password' => Hash::make('password'),
            ],
        );

        $northwindUsers = collect([
            ['name' => 'Ahmed Ali', 'email' => 'ahmed@northwind.test', 'role' => 'company_admin'],
            ['name' => 'Mona Saleh', 'email' => 'mona@northwind.test', 'role' => 'employee'],
            ['name' => 'Omar Nasser', 'email' => 'omar@northwind.test', 'role' => 'employee'],
        ])->map(fn (array $user) => User::query()->firstOrCreate(
            ['email' => $user['email']],
            [
                'company_id' => $northwind->id,
                'name' => $user['name'],
                'role' => $user['role'],
                'password' => Hash::make('password'),
            ],
        ));

        $cedarUsers = collect([
            ['name' => 'Sara Khaled', 'email' => 'sara@cedar.test', 'role' => 'company_admin'],
            ['name' => 'Lina Haddad', 'email' => 'lina@cedar.test', 'role' => 'employee'],
        ])->map(fn (array $user) => User::query()->firstOrCreate(
            ['email' => $user['email']],
            [
                'company_id' => $cedar->id,
                'name' => $user['name'],
                'role' => $user['role'],
                'password' => Hash::make('password'),
            ],
        ));

        Message::query()->firstOrCreate(
            [
                'company_id' => $northwind->id,
                'sender_id' => $northwindUsers[1]->id,
                'recipient_id' => $northwindUsers[0]->id,
                'subject' => 'Meeting notes',
            ],
            [
                'body' => 'Please review the meeting notes before the end of the day.',
            ],
        );

        Message::query()->firstOrCreate(
            [
                'company_id' => $northwind->id,
                'sender_id' => $northwindUsers[0]->id,
                'recipient_id' => $northwindUsers[2]->id,
                'subject' => 'System update',
            ],
            [
                'body' => 'The SaaS messaging workspace is ready for testing.',
                'read_at' => now(),
            ],
        );

        Message::query()->firstOrCreate(
            [
                'company_id' => $cedar->id,
                'sender_id' => $cedarUsers[1]->id,
                'recipient_id' => $cedarUsers[0]->id,
                'subject' => 'Trial account',
            ],
            [
                'body' => 'The trial company can use only the modules enabled by the Super Admin.',
            ],
        );
    }
}
