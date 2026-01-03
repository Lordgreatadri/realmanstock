<x-guest-layout>
    <div class="p-8">
        <h2 class="text-2xl font-bold text-white mb-2">Verify Your Phone</h2>
        <p class="text-gray-400 mb-6">Enter the 6-digit code sent to {{ session('phone') ?? 'your phone' }}</p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 p-4 bg-green-900/50 border border-green-500/50 rounded-lg text-green-400 text-sm">
                A new verification code has been sent to your phone.
            </div>
        @endif

        <form method="POST" action="{{ route('verify-phone.store') }}" class="space-y-5">
            @csrf

            <!-- OTP Input -->
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-300 mb-2">Verification Code</label>
                <input id="otp" type="text" name="otp" required autofocus maxlength="6"
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder-gray-500 text-center text-2xl tracking-widest"
                    placeholder="000000">
                @error('otp')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium hover:shadow-lg hover:shadow-indigo-500/50 transition">
                Verify Phone Number
            </button>

            <!-- Resend Code -->
            <div class="text-center">
                <form method="POST" action="{{ route('verify-phone.resend') }}">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-400 hover:text-indigo-300 transition font-medium">
                        Didn't receive code? Resend
                    </button>
                </form>
            </div>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-sm text-gray-400 hover:text-white transition">
                Cancel and Logout
            </button>
        </form>
    </div>
</x-guest-layout>
