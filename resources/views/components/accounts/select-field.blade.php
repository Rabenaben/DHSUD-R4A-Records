@props(['label', 'name', 'options' => [], 'value' => '', 'required' => false])

<div>
    <label class="block text-sm font-medium text-gray-700" for="{{ $name }}">
        {{ $label }}
        <x-input-required-mark :required="$required" />
    </label>
    
    <select id="{{ $attributes->get('id', $name) }}" name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}>
        <option value="" disabled>Select {{ $label }}</option>
        @foreach ($options as $option)
            <option value="{{ $option }}" {{ old($name, $value) == $option ? 'selected' : '' }}>{{ $option }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
