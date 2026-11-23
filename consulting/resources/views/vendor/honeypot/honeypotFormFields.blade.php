{{--
    Overridden from spatie/laravel-honeypot.

    The package hides these fields with an inline `style="display: none"`
    attribute. Our Content-Security-Policy sets style-src without
    'unsafe-inline', so the browser drops that attribute and the honeypot —
    including a long base64 timestamp — renders visibly in the middle of the
    form.

    The package's own `with_csp` option needs spatie/laravel-csp, which we do
    not use. So the rule lives in resources/css/app.css as `.honeypot-field`
    instead: a bundled stylesheet is 'self', needs no CSP exception, and keeps
    the field hidden from humans while remaining in the DOM for bots.

    Do not reintroduce an inline style here.
--}}
@if($enabled)
    <div id="{{ $nameFieldName }}_wrap" class="honeypot-field" aria-hidden="true">
        <label for="{{ $nameFieldName }}">Leave this field blank</label>

        <input id="{{ $nameFieldName }}"
               name="{{ $nameFieldName }}"
               type="text"
               value=""
               @if ($livewireModel ?? false) wire:model.defer="{{ $livewireModel }}.{{ $unrandomizedNameFieldName }}" @endif
               autocomplete="nope"
               tabindex="-1">

        <input name="{{ $validFromFieldName }}"
               type="text"
               value="{{ $encryptedValidFrom }}"
               @if ($livewireModel ?? false) wire:model.defer="{{ $livewireModel }}.{{ $validFromFieldName }}" @endif
               autocomplete="off"
               tabindex="-1">
    </div>
@endif
