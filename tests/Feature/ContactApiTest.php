<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_contact_via_api(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'description' => 'Hello from the API',
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('name', $payload['name'])
            ->assertJsonPath('email', $payload['email']);

        $this->assertDatabaseHas('contacts', [
            'email' => $payload['email'],
        ]);
    }

    public function test_failed_whatsapp_send_shows_error_message(): void
    {
        Contact::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone_number' => '1234567890',
            'description' => 'Customer for testing',
        ]);

        Http::fake([
            'https://web-whatsappjs.infinicodesystem.site/send-message' => Http::response([], 500),
        ]);

        $response = $this->followingRedirects()
            ->post(route('admin.customers.send-whatsapp'), [
                'number' => '1234567890',
                'message' => 'Hello from test',
            ]);

        $response->assertStatus(200)
            ->assertSee('Unable to send WhatsApp message.');
    }
}
