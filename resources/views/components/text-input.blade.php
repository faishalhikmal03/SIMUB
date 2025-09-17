@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => '
        w-full
        px-4 py-2
        text-gray-900
        bg-gray-900
        border-2 border-gray-300
        rounded-lg
        shadow-sm
        transition
        duration-150
        ease-in-out
        focus:border-purple-500
        focus:ring
        focus:ring-purple-500
        focus:ring-opacity-50
        dark:bg-white
        dark:border-purple-500
        dark:text-black
        dark:focus:border-purple-600
        dark:focus:ring-purple-600
    '
]) !!}>