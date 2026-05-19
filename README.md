# Cloud SaaS Messaging System

Laravel assignment project that converts a normal internal messaging system into a basic Cloud SaaS application.

## What Makes It SaaS

- Multiple companies use the same Laravel application.
- Each company is treated as a tenant.
- Users and messages are separated by `company_id`.
- Each company has its own subscription record.
- Each company has its own enabled modules.
- A Super Admin console controls companies, company status, and modules.

## Main Features

- Super Admin company overview.
- Company status control: `active`, `trialing`, `suspended`.
- Per-company module control through the `modules` and `company_module` tables.
- Tenant workspace for company users.
- Company-isolated inbox and sent messages.
- Subscription-aware messaging access.
- Test coverage for tenant isolation.

## Researched Packages

- `stancl/tenancy`: multi-tenancy package for Laravel.
- `spatie/laravel-multitenancy`: unopinionated Laravel multi-tenancy package.
- `nwidart/laravel-modules`: module management package for Laravel 13.

## Run Locally

```bash
php ../composer.phar install
php artisan migrate:fresh --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Open:

```text
http://127.0.0.1:8000
```

## Test

```bash
php artisan test
```

Current result:

```text
5 tests passed
```

## Main Files

- `app/Http/Controllers/MessageController.php`
- `app/Http/Controllers/AdminCompanyController.php`
- `app/Models/Company.php`
- `app/Models/Subscription.php`
- `app/Models/Module.php`
- `app/Models/Message.php`
- `database/migrations/0000_01_01_000000_create_saas_tables.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_05_13_170439_create_messages_table.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/messages/index.blade.php`
- `tests/Feature/MessagesTest.php`

## Report

The PDF report is in:

```text
report/laravel_saas_assignment_report.pdf
```
