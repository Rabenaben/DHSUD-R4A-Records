@props(['variant' => 'dhsud'])

@if ($variant === 'bp')
    <img src="{{ asset('images/logo2.png') }}" alt="Application Logo" {{ $attributes }}>
@else
    <img src="{{ asset('images/logo1.png') }}" alt="Application Logo" {{ $attributes }}>
@endif
