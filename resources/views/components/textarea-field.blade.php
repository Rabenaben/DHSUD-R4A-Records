@props(['label', 'name', 'rows' => 3, 'value' => ''])

<div>
    <label class="block text-sm font-medium text-gray-700" for="{{ $name }}">{{ $label }}</label>
    <textarea
        id="{{ $attributes->get('id', $name) }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
