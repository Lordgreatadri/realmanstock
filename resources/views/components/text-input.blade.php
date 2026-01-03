@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-700 border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
