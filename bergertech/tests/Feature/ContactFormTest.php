<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nusrat Jahan',
            'email' => 'nusrat@example.com',
            'phone' => '01700000000',
            'company' => 'Acme Ltd',
            'subject' => 'SAP S/4HANA',
            'message' => 'We would like to discuss migrating our ECC landscape.',
            'website' => '',
        ], $overrides);
    }

    public function test_a_valid_message_is_stored(): void
    {
        $this->post('/contact', $this->payload())
            ->assertRedirect('/contact')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'nusrat@example.com',
            'subject' => 'SAP S/4HANA',
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post('/contact', ['website' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_short_message_is_rejected(): void
    {
        $this->post('/contact', $this->payload(['message' => 'hi']))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_a_filled_honeypot_is_rejected(): void
    {
        $this->post('/contact', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_the_honeypot_value_is_never_persisted(): void
    {
        $this->post('/contact', $this->payload());

        $this->assertArrayNotHasKey('website', ContactMessage::first()->getAttributes());
    }
}
