<x-guest-layout>
    <div class="flex min-h-screen bg-black items-center justify-center p-4">
        <div class="max-w-md w-full bg-[#111] p-8 rounded-2xl shadow-2xl border border-gray-800">
            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <x-application-logo class="h-10 sm:h-12 fill-current text-primary mx-auto" />
                </a>
                <h1 class="text-3xl font-bold text-white">Reset Password</h1>
                <p class="text-gray-400 mt-2 text-sm leading-relaxed">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                            class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="you@example.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-bold text-black bg-primary hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-transform transform hover:-translate-y-0.5">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
                
                <p class="mt-6 text-center text-sm text-gray-400">
                    <a class="font-bold text-primary hover:text-yellow-400 transition border-b border-primary hover:border-yellow-400" href="{{ route('login') }}">
                        {{ __('Back to login') }}
                    </a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
