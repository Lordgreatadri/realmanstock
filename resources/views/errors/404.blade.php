<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- 404 Number -->
            <div class="mb-6">
                <h1 class="text-9xl font-bold text-indigo-500">404</h1>
            </div>

            <!-- Error Title -->
            <h2 class="text-3xl font-bold mb-4">Page Not Found</h2>
            
            <!-- Error Message -->
            <p class="text-gray-400 mb-8 text-lg">
                Sorry, the page you're looking for doesn't exist or has been moved.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ url('/') }}" class="block w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-center rounded-lg transition font-semibold">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go to Homepage
                </a>

                <button onclick="window.history.back()" class="block w-full px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white text-center rounded-lg transition font-semibold">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Go Back
                </button>

                <!-- Contact Support -->
                <div class="border-t border-gray-700 pt-6 mt-6">
                    <p class="text-gray-400 mb-4">Can't find what you're looking for?</p>
                    <a href="{{ url('/#contact') }}" class="block w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition font-semibold">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
