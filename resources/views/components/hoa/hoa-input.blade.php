@props(['id', 'type' => 'text', 'placeholder' => '', 'readonly' => false])

<input id="{{ $id }}" type="{{ $type }}" placeholder="{{ $placeholder }}" @if($readonly) readonly @endif class="flex-1 rounded-lg border border-gray-300 px-2 py-2 outline-none focus:border-blue-600" {{ $attributes }}>
