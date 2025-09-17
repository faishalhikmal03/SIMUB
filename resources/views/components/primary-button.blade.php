<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => '
        inline-flex items-center 
        px-6 py-3 
        bg-gradient-to-r from-purple-600 to-indigo-600 
        border border-transparent 
        rounded-lg 
        font-semibold text-sm text-white 
        uppercase tracking-widest 
        shadow-lg
        transition-all duration-150 ease-in-out 
        hover:from-purple-700 hover:to-indigo-700 
        hover:shadow-xl
        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
        transform hover:-translate-y-0.5
    '
]) }}>
    {{ $slot }}
</button>
