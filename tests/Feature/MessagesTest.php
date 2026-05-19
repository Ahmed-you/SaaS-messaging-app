<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Message;
use App\Models\Module;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_internal_message(): void
    {
        $company = $this->createActiveCompany();
        $sender = User::factory()->create(['company_id' => $company->id]);
        $recipient = User::factory()->create(['company_id' => $company->id]);

        $response = $this->post('/messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Project update',
            'body' => 'The first version is ready.',
        ]);

        $response->assertRedirect(route('messages.index', ['company_id' => $company->id, 'user_id' => $sender->id]));

        $this->assertDatabaseHas('messages', [
            'company_id' => $company->id,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Project update',
        ]);
    }

    public function test_recipient_can_mark_message_as_read(): void
    {
        $company = $this->createActiveCompany();
        $sender = User::factory()->create(['company_id' => $company->id]);
        $recipient = User::factory()->create(['company_id' => $company->id]);
        $message = Message::create([
            'company_id' => $company->id,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Approval',
            'body' => 'Please approve this.',
        ]);

        $response = $this->patch(route('messages.read', $message), [
            'user_id' => $recipient->id,
        ]);

        $response->assertRedirect(route('messages.index', ['company_id' => $company->id, 'user_id' => $recipient->id]));

        $this->assertNotNull($message->refresh()->read_at);
    }

    public function test_message_cannot_be_sent_to_user_in_another_company(): void
    {
        $company = $this->createActiveCompany('alpha');
        $otherCompany = $this->createActiveCompany('beta');
        $sender = User::factory()->create(['company_id' => $company->id]);
        $recipient = User::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->post('/messages', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Cross tenant',
            'body' => 'This should not be allowed.',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseMissing('messages', [
            'subject' => 'Cross tenant',
        ]);
    }

    private function createActiveCompany(string $slug = 'demo-company'): Company
    {
        $company = Company::create([
            'name' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'contact_email' => "{$slug}@example.test",
            'status' => 'active',
        ]);
        Subscription::create([
            'company_id' => $company->id,
            'plan_name' => 'Business',
            'status' => 'active',
            'seats' => 10,
            'monthly_price' => 49,
        ]);
        $module = Module::query()->firstOrCreate([
            'key' => 'messaging',
        ], [
            'name' => 'Messaging',
            'description' => 'Send internal messages.',
        ]);
        $company->modules()->syncWithoutDetaching([$module->id => ['enabled_at' => now()]]);

        return $company->load(['subscription', 'modules']);
    }
}
