@props([
    'type' => 'contact',
    'detailed' => false,
])

{{--
    Every value is rendered with {{ }} (escaped). `old()` is echoed back into
    inputs, so this is exactly where reflected XSS would live if raw output
    were used anywhere. It is not.

    @csrf is mandatory. x-honeypot adds the hidden field plus the
    submitted-too-fast check from spatie/laravel-honeypot.
--}}

@if (session('status'))
    <div role="status" class="mb-6 rounded-lg border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-6 rounded-lg border border-red-300 bg-red-50 px-5 py-4 text-sm text-red-900">
        Please correct the {{ $errors->count() }} highlighted {{ \Illuminate\Support\Str::plural('field', $errors->count()) }} below.
    </div>
@endif

<form method="POST"
      action="{{ route('enquiries.store', $type) }}"
      enctype="multipart/form-data"
      class="grid gap-5 sm:grid-cols-2"
      novalidate>
    @csrf
    <x-honeypot />

    <div>
        <label for="name" class="label">Name <span class="text-red-600" aria-hidden="true">*</span></label>
        <input id="name" name="name" type="text" required autocomplete="name"
               maxlength="120" value="{{ old('name') }}"
               @class(['field', 'field-error' => $errors->has('name')])
               @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
        @error('name')<p id="name-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="label">Work email <span class="text-red-600" aria-hidden="true">*</span></label>
        <input id="email" name="email" type="email" required autocomplete="email"
               maxlength="190" value="{{ old('email') }}"
               @class(['field', 'field-error' => $errors->has('email')])
               @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
        @error('email')<p id="email-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="company" class="label">Company</label>
        <input id="company" name="company" type="text" autocomplete="organization"
               maxlength="150" value="{{ old('company') }}"
               @class(['field', 'field-error' => $errors->has('company')])>
        @error('company')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="label">Phone</label>
        <input id="phone" name="phone" type="tel" autocomplete="tel"
               maxlength="40" value="{{ old('phone') }}"
               @class(['field', 'field-error' => $errors->has('phone')])>
        @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    @if ($detailed)
        <div>
            <label for="service" class="label">Which practice?</label>
            <select id="service" name="service" @class(['field', 'field-error' => $errors->has('service')])>
                <option value="">Not sure yet</option>
                @foreach (config('site.services') as $slug => $service)
                    <option value="{{ $slug }}" @selected(old('service') === $slug)>{{ $service['name'] }}</option>
                @endforeach
            </select>
            @error('service')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="budget" class="label">Indicative budget</label>
            <select id="budget" name="budget" @class(['field', 'field-error' => $errors->has('budget')])>
                <option value="">Prefer not to say</option>
                @foreach (config('site.budget_bands') as $key => $label)
                    <option value="{{ $key }}" @selected(old('budget') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('budget')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endif

    <div class="sm:col-span-2">
        <label for="message" class="label">How can we help? <span class="text-red-600" aria-hidden="true">*</span></label>
        <textarea id="message" name="message" rows="6" required maxlength="5000"
                  @class(['field', 'field-error' => $errors->has('message')])
                  @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message') }}</textarea>
        @error('message')<p id="message-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    @if ($detailed)
        <div class="sm:col-span-2">
            <label for="attachment" class="label">Brief or supporting document</label>
            <input id="attachment" name="attachment" type="file"
                   accept=".pdf,.jpg,.jpeg,.png,.webp"
                   @class(['field', 'field-error' => $errors->has('attachment')])>
            <p class="mt-1.5 text-xs text-slate-500">
                PDF, JPG, PNG or WebP, up to {{ round(config('security.uploads.max_kb') / 1024) }} MB.
                Images are re-encoded on upload and stored privately.
            </p>
            @error('attachment')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endif

    <div class="sm:col-span-2">
        <label for="consent" class="flex items-start gap-3 text-sm">
            <input id="consent" name="consent" type="checkbox" value="1" required
                   @checked(old('consent'))
                   class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600">
            <span>
                I am happy for {{ config('site.company') }} to contact me about this enquiry.
                See our <a href="{{ route('legal', 'privacy') }}" class="font-medium text-brand-700 underline">privacy notice</a>.
            </span>
        </label>
        @error('consent')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <button type="submit" class="btn-primary">
            {{ $detailed ? 'Request a consultation' : 'Send enquiry' }}
        </button>
    </div>
</form>
