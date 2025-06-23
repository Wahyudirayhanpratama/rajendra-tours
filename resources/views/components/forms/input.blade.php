@props(['type' => 'text'])

<div class="form-group searchbox2">
    <input {{ $attributes->merge([
        'class' => 'form-control',
        'type' => $type
    ]) }}>
    {{ $slot }}
</div>
