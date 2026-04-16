<x-guest-layout>
    <div class="flex min-h-screen bg-black items-center justify-center p-4">
        <div class="max-w-md w-full bg-[#111] p-8 rounded-2xl shadow-2xl border border-gray-800">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <x-application-logo class="h-10 sm:h-12 fill-current text-primary mx-auto" />
                </a>
                <h1 class="text-3xl font-bold text-white">Welcome Back</h1>
                <p class="text-gray-400 mt-2">Please enter your details to sign in.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="you@example.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <div class="mt-1">
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-700 bg-black text-primary focus:ring-primary" name="remember">
                        <span class="ml-2 block text-sm text-gray-400">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-primary hover:text-yellow-400 transition" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-bold text-black bg-primary hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-transform transform hover:-translate-y-0.5">
                        {{ __('Sign In') }}
                    </button>
                </div>

                @if (Route::has('register'))
                    <p class="mt-6 text-center text-sm text-gray-400">
                        {{ __('Don\'t have an account?') }}
                        <a class="font-bold text-primary hover:text-yellow-400 transition border-b border-primary hover:border-yellow-400" href="{{ route('register') }}">
                            {{ __('Create one now') }}
                        </a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</x-guest-layout>
