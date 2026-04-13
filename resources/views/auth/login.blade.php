<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Left Side: Image -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-indigo-900 overflow-hidden">
            <img src="{{ asset('images/auth_splash.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-90 scale-105" alt="Authentic UI" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-10 left-10 text-white max-w-xl">
                <h2 class="text-4xl font-bold mb-4">Empowering your knowledge journey.</h2>
                <p class="text-lg text-gray-200">Sign in to access your customized dashboard, manage your orders, and explore the largest selection of premium literature.</p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="max-w-md w-full">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <div class="text-center mb-10">
                    <a href="/" class="inline-block mb-6">
                        <x-application-logo class="w-16 h-16 fill-current text-indigo-600 mx-auto" />
                    </a>
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">Welcome Back</h1>
                    <p class="text-gray-500 mt-2">Please enter your details to sign in.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-gray-50 focus:bg-white" placeholder="you@example.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input id="password" type="password" name="password" required autocomplete="current-password" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-gray-50 focus:bg-white" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                            <span class="ml-2 block text-sm text-gray-700">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:-translate-y-0.5">
                            {{ __('Sign In') }}
                        </button>
                    </div>

                    @if (Route::has('register'))
                        <p class="mt-8 text-center text-sm text-gray-600">
                            {{ __('Don\'t have an account?') }}
                            <a class="font-bold text-indigo-600 hover:text-indigo-500 transition border-b border-indigo-600 hover:border-indigo-500" href="{{ route('register') }}">
                                {{ __('Create one now') }}
                            </a>
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
