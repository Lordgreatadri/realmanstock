<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Lock Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Title -->
            <h1 class="text-4xl font-bold mb-4">Access Denied</h1>
            
            <!-- Error Message -->
            <p class="text-gray-400 mb-8 text-lg">
                @if(isset($exception) && $exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    You don't have permission to access this page.
                @endif
            </p>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ url('/') }}" class="block w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-center rounded-lg transition font-semibold">
                    Go to Homepage
                </a>

                <button onclick="window.history.back()" class="block w-full px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white text-center rounded-lg transition font-semibold">
                    Go Back
                </button>

                <div class="border-t border-gray-700 pt-6 mt-6">
                    <p class="text-gray-400 mb-4">Need access? Contact an administrator</p>
                    <a href="{{ url('/#contact') }}" class="block w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition font-semibold">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
