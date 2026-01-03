<x-guest-layout>
    <div class="p-8">
        <h2 class="text-2xl font-bold text-white mb-2">Reset Password</h2>
        <p class="text-gray-400 mb-6">Enter your phone number to receive a verification code</p>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-900/50 border border-green-500/50 rounded-lg text-green-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.phone') }}" class="space-y-5">
            @csrf

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required autofocus
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder-gray-500"
                    placeholder="+1234567890">
                @error('phone')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium hover:shadow-lg hover:shadow-indigo-500/50 transition">
                Send Verification Code
            </button>

            <!-- Back to Login -->
            <div class="text-center text-sm text-gray-400">
                Remember your password?
                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition font-medium">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
