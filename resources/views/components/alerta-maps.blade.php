@props(['type', 'message', 'visible' => false])

@if ($visible)
    <div class="alert alert-{{ $type }} {{ $attributes->merge(['class' => 'alert']) }}">
        {!! $message !!}
    </div>
@endif
