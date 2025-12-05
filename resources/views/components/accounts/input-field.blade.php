@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div>
    <label class="block text-sm font-medium text-gray-700" for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input id="{{ $attributes->get('id', $name) }}" type="{{ $type }}" name="{{ $name }}"
        value="{{ old($name, $value) }}" {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}>

    @error($name)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
