@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'fs-6 text-success samim']) }}>
        {{ $status }}
    </div>
@endif
