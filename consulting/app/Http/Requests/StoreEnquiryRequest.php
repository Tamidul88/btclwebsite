<?php

namespace App\Http\Requests;

use App\Rules\SafeAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

/**
 * Validation boundary for both public forms.
 *
 * Notes on the choices here:
 *  - `email:filter` uses PHP's filter_var rather than the looser default. It
 *    rejects the display-name and quoted-local-part forms that carry CR/LF and
 *    are the usual vehicle for header injection.
 *  - Every string is length-capped. Unbounded text is a cheap DoS and bloats
 *    the database.
 *  - `service` and `budget` are constrained to known values via Rule::in, so
 *    they cannot be used to smuggle arbitrary content into notification mail.
 *  - Attachment rules are only the first layer; AttachmentStorage re-derives
 *    the MIME from content and re-encodes images regardless of what passes here.
 */
class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email:filter', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9 ()+\-.]{6,40}$/'],
            'company' => ['nullable', 'string', 'max:150'],

            'service' => ['nullable', 'string', Rule::in($this->serviceOptions())],
            'budget' => ['nullable', 'string', Rule::in($this->budgetOptions())],

            'message' => ['required', 'string', 'min:20', 'max:5000'],

            'attachment' => [
                'nullable',
                // Extension + size first (cheap), then content inspection.
                File::types(config('security.uploads.extensions'))
                    ->max(config('security.uploads.max_kb')),
                new SafeAttachment,
            ],

            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent.accepted' => 'Please confirm you are happy for us to contact you about this enquiry.',
            'phone.regex' => 'Please enter a phone number using digits and the usual separators only.',
            'message.min' => 'Please give us a little more detail — at least 20 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'attachment' => 'brief or document',
        ];
    }

    /**
     * Normalises input before validation runs.
     *
     * Stripping control characters here means no downstream consumer — mail
     * headers, CSV export, the admin table — has to think about them.
     */
    protected function prepareForValidation(): void
    {
        /*
         * Single-line fields have EVERY control character removed, CR and LF
         * included. These are the values that end up adjacent to mail headers
         * (Reply-To display name, subject context), so a surviving CRLF is a
         * header-injection primitive. Nothing legitimate in a name or company
         * needs a newline.
         */
        $singleLine = function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            return trim((string) preg_replace('/[\x00-\x1F\x7F]/u', '', $value));
        };

        /*
         * The message body is different: real people paste multi-line text
         * into it. Newlines are preserved, everything else in the control
         * range is stripped, and CR is normalised to LF so stored data is
         * consistent regardless of the sender's platform.
         */
        $multiLine = function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            $value = str_replace(["\r\n", "\r"], "\n", $value);
            $value = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);

            return trim((string) $value);
        };

        $email = $singleLine($this->input('email'));

        $this->merge(array_filter([
            'name' => $singleLine($this->input('name')),
            'email' => $email !== null ? mb_strtolower($email) : null,
            'phone' => $singleLine($this->input('phone')),
            'company' => $singleLine($this->input('company')),
            'message' => $multiLine($this->input('message')),
        ], fn ($v) => $v !== null));
    }

    /** @return list<string> */
    public function serviceOptions(): array
    {
        return array_map('strval', array_keys(config('site.services')));
    }

    /** @return list<string> */
    public function budgetOptions(): array
    {
        return array_map('strval', array_keys(config('site.budget_bands')));
    }
}
