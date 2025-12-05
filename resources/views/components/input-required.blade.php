@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700']) }}>
    {{ $value }}

    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>
