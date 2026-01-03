<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Realman Livestock') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-950 text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/20 via-gray-950 to-purple-900/20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 w-full sm:max-w-md px-6">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">R</span>
                        </div>
                        <span class="text-2xl font-bold text-white">Realman</span>
                    </a>
                </div>

                <!-- Auth Card -->
                <div class="bg-gray-800/50 backdrop-blur-sm shadow-lg rounded-xl overflow-hidden border border-gray-700">
                    {{ $slot }}
                </div>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="/" class="text-gray-400 hover:text-white transition text-sm">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
