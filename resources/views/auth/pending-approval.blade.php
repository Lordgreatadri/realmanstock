<x-guest-layout>
    <div class="p-8 text-center">
        <!-- Icon -->
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-yellow-500 to-orange-600 flex items-center justify-center">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-white mb-3">Account Under Review</h2>
        <p class="text-gray-400 mb-6 max-w-md mx-auto">
            Thank you for registering! Your account is currently pending approval from our admin team. 
            We'll notify you via SMS once your account is activated.
        </p>

        <div class="p-4 bg-indigo-900/30 border border-indigo-500/30 rounded-lg mb-6">
            <p class="text-sm text-indigo-300">
                <strong>Phone:</strong> {{ auth()->user()->phone }}
            </p>
            @if(auth()->user()->company_name)
                <p class="text-sm text-indigo-300 mt-1">
                    <strong>Company:</strong> {{ auth()->user()->company_name }}
                </p>
            @endif
        </div>

        <p class="text-xs text-gray-500 mb-6">
            This usually takes 1-2 business days. Need help? Contact us at support@realman.com
        </p>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-6 py-3 rounded-lg bg-gray-800 text-white font-medium hover:bg-gray-700 transition">
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>
