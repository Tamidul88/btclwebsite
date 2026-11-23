<?php

namespace App\Http\Requests;

use App\BookingSlots;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:150'],
            'topic' => ['nullable', 'string', 'max:150'],
            'preferred_at' => ['required', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                $slots = app(BookingSlots::class);
                $slot = $slots->parse($value);

                if (! $slot || ! $slots->isAvailable($slot)) {
                    $fail('That time is no longer available. Please pick another slot.');
                }
            }],
            'notes' => ['nullable', 'string', 'max:2000'],
            // Honeypot: real visitors never fill this in.
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'preferred_at' => 'date and time',
        ];
    }

    public function messages(): array
    {
        return [
            'website.prohibited' => 'This submission looks automated.',
        ];
    }
}
