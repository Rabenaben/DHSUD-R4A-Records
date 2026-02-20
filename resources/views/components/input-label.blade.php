@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {!! $value ?? $slot !!}
    <x-input-required-mark :required="$required" />
</label>
