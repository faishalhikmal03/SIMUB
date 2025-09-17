@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-white dark:text-black']) }}>
    {{ $value ?? $slot }}
</label>
