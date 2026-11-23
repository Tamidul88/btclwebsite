@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'rows' => 5,
    'value' => null,
])

@php
    $id = 'field-'.$name;
    $invalid = $errors->has($name);
    $current = old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="label">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}"
                  name="{{ $name }}"
                  rows="{{ $rows }}"
                  placeholder="{{ $placeholder }}"
                  @required($required)
                  @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
                  class="field {{ $invalid ? 'field-error' : '' }}">{{ $current }}</textarea>
    @else
        <input id="{{ $id }}"
               type="{{ $type }}"
               name="{{ $name }}"
               value="{{ $current }}"
               placeholder="{{ $placeholder }}"
               @required($required)
               @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
               class="field {{ $invalid ? 'field-error' : '' }}">
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-[13px] text-red-600">{{ $message }}</p>
    @enderror
</div>
