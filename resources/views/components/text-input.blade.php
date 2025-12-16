@props(['disabled' => false])

<input 
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' =>
            'w-full border-gray-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-lg shadow-sm'
    ]) !!}
>
