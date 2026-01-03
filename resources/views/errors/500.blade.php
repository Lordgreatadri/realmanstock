<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-8 md:p-12">
            <!-- Error Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Title -->
            <h1 class="text-4xl font-bold text-center mb-4">Oops! Something Went Wrong</h1>
            
            <!-- Error Message -->
            <p class="text-gray-400 text-center mb-8 text-lg">
                We're sorry, but something unexpected happened. Our team has been notified and we're working to fix it.
            </p>

            <!-- Error Details (Only in debug mode) -->
            @if(config('app.debug') && isset($exception))
                <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-400 mb-2">Error Details:</p>
                    <p class="text-red-400 text-sm font-mono">{{ $exception->getMessage() }}</p>
                    @if($exception->getFile())
                        <p class="text-gray-500 text-xs mt-2">
                            {{ basename($exception->getFile()) }} : {{ $exception->getLine() }}
                        </p>
                    @endif
                </div>
            @endif

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
                    <p class="text-center text-gray-400 mb-4">Need help? Contact our support team</p>
                    <a href="{{ route('contact.store') }}" 
                       onclick="event.preventDefault(); showContactForm();" 
                       class="block w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition font-semibold">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>

            <!-- Additional Help Text -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Error Reference: {{ now()->format('YmdHis') }}</p>
                <p class="mt-1">Please provide this reference when contacting support</p>
            </div>
        </div>
    </div>

    <!-- Contact Form Modal -->
    <div id="contactModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl max-w-md w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Contact Support</h2>
                <button onclick="hideContactForm()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="message_prefix" value="Error Report - Reference: {{ now()->format('YmdHis') }}">
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone</label>
                    <input type="tel" name="phone" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">What were you trying to do?</label>
                    <textarea name="message" required rows="4" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500" placeholder="Please describe what you were doing when the error occurred...add the reference code {{ now()->format('YmdHis') }}"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-semibold">
                        Send Message
                    </button>
                    <button type="button" onclick="hideContactForm()" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showContactForm() {
            document.getElementById('contactModal').classList.remove('hidden');
        }

        function hideContactForm() {
            document.getElementById('contactModal').classList.add('hidden');
        }
    </script>
</body>
</html>
