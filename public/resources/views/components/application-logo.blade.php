@props(['variant' => 'dhsud'])

@if ($variant === 'bp')
    <img src="{{ asset('images/bp.png') }}" alt="Application Logo" {{ $attributes }}>
@else
    <img src="{{ asset('images/dhsudlogo.png') }}" alt="Application Logo" {{ $attributes }}>
@endif
