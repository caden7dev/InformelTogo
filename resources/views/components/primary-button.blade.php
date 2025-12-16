<button
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg
             font-bold text-white tracking-wide hover:bg-indigo-700 active:bg-indigo-800 transition duration-150 ease-in-out w-full'
    ]) }}
>
    {{ $slot }}
</button>
