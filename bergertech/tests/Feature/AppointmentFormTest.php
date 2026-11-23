<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AppointmentFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Sunday 4 October 2026, 9:00 AM in Dhaka. Bookings open Sunday to
     * Thursday, 9:00 AM to 6:00 PM, with four hours' notice.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(CarbonImmutable::parse('2026-10-04 09:00', 'Asia/Dhaka'));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nusrat Jahan',
            'email' => 'nusrat@example.com',
            'phone' => '01700000000',
            'company' => 'Acme Ltd',
            'topic' => 'SAP',
            'preferred_at' => '2026-10-05T10:00',
            'notes' => 'S/4HANA roadmap discussion.',
            'website' => '',
        ], $overrides);
    }

    private function bookMondayAtTen(): void
    {
        Appointment::create([
            'name' => 'Earlier Visitor',
            'email' => 'earlier@example.com',
            'phone' => '01800000000',
            'preferred_at' => CarbonImmutable::parse('2026-10-05 10:00', 'Asia/Dhaka')->utc(),
        ]);
    }

    public function test_the_calendar_links_only_working_days(): void
    {
        $this->get('/appointment?month=2026-10')
            ->assertOk()
            ->assertSee('October 2026')
            ->assertSee('date=2026-10-05', false)
            ->assertDontSee('date=2026-10-09', false);
    }

    public function test_a_chosen_day_lists_its_half_hour_slots(): void
    {
        $this->get('/appointment?month=2026-10&date=2026-10-05')
            ->assertOk()
            ->assertSee('Monday, October 5')
            ->assertSeeInOrder(['9:00am', '9:30am', '5:30pm'])
            ->assertDontSee('6:00pm');
    }

    public function test_a_booked_slot_is_not_offered_again(): void
    {
        $this->bookMondayAtTen();

        $this->get('/appointment?month=2026-10&date=2026-10-05')
            ->assertSee('time=09%3A30', false)
            ->assertDontSee('time=10%3A00', false);
    }

    public function test_choosing_a_time_shows_the_details_form(): void
    {
        $this->get('/appointment?month=2026-10&date=2026-10-05&time=10:00')
            ->assertOk()
            ->assertSee('Enter Details')
            ->assertSee('10:00am – 10:30am, Monday, October 5, 2026')
            ->assertSee('value="2026-10-05T10:00"', false);
    }

    public function test_a_valid_booking_is_stored_in_utc(): void
    {
        $this->post('/appointment', $this->payload())
            ->assertRedirect('/appointment')
            ->assertSessionHas('status', 'Your consultation is booked for 5 October 2026, 10:00 AM. We will confirm by email.');

        $this->assertDatabaseHas('appointments', [
            'email' => 'nusrat@example.com',
            'topic' => 'SAP',
            'preferred_at' => '2026-10-05 04:00:00',
        ]);
    }

    #[TestWith(['2026-10-09T10:00'], 'friday')]
    #[TestWith(['2026-10-05T18:00'], 'after closing')]
    #[TestWith(['2026-10-05T10:15'], 'off the half hour')]
    #[TestWith(['2026-10-04T11:00'], 'inside the notice period')]
    #[TestWith(['2026-12-31T10:00'], 'beyond the booking window')]
    #[TestWith(['not-a-date'], 'malformed')]
    public function test_an_unavailable_time_is_rejected(string $preferredAt): void
    {
        $this->post('/appointment', $this->payload(['preferred_at' => $preferredAt]))
            ->assertSessionHasErrors(['preferred_at' => 'That time is no longer available. Please pick another slot.']);

        $this->assertSame(0, Appointment::count());
    }

    public function test_an_already_booked_time_is_rejected(): void
    {
        $this->bookMondayAtTen();

        $this->post('/appointment', $this->payload())
            ->assertSessionHasErrors(['preferred_at' => 'That time is no longer available. Please pick another slot.']);

        $this->assertSame(1, Appointment::count());
    }

    public function test_name_email_phone_and_time_are_required(): void
    {
        $this->post('/appointment', ['website' => ''])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'preferred_at']);

        $this->assertSame(0, Appointment::count());
    }

    public function test_a_filled_honeypot_is_rejected(): void
    {
        $this->post('/appointment', $this->payload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Appointment::count());
    }
}
