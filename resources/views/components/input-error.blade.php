@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-danger mb-1 fw-bold']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
