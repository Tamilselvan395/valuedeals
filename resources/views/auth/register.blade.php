<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Left Side: Image -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-purple-900 overflow-hidden">
            <img src="{{ asset('images/auth_splash.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-90 scale-105" alt="Authentic UI" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-10 left-10 text-white max-w-xl">
                <h2 class="text-4xl font-bold mb-4">Join our community.</h2>
                <p class="text-lg text-gray-200">Create an account to track your orders, save your favorite books, and enjoy a faster checkout process.</p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 bg-white overflow-y-auto">
            <div class="max-w-md w-full">
                <div class="text-center mb-8">
                    <a href="/" class="inline-block mb-4 mt-6 lg:mt-0">
                        <x-application-logo class="w-16 h-16 fill-current text-purple-600 mx-auto" />
                    </a>
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">Create Account</h1>
                    <p class="text-gray-500 mt-2">Sign up to get started today.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5 pb-8 lg:pb-0">
                    @csrf
                    @if(request()->boolean('checkout'))
                        <input type="hidden" name="checkout" value="1">
                    @endif

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="mt-1">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 transition-colors bg-gray-50 focus:bg-white" placeholder="John Doe">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-sm" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="mt-1">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 transition-colors bg-gray-50 focus:bg-white" placeholder="you@example.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-sm" />
                    </div>
                    
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="mt-1">
                                <input id="password" type="password" name="password" required autocomplete="new-password" 
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 transition-colors bg-gray-50 focus:bg-white" placeholder="••••••••">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-sm" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm</label>
                            <div class="mt-1">
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                                    class="block w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 transition-colors bg-gray-50 focus:bg-white" placeholder="••••••••">
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-sm" />
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-transform transform hover:-translate-y-0.5">
                            {{ __('Create Account') }}
                        </button>
                    </div>

                    <p class="mt-6 text-center text-sm text-gray-600">
                        {{ __('Already have an account?') }}
                        <a class="font-bold text-purple-600 hover:text-purple-500 transition border-b border-purple-600 hover:border-purple-500" href="{{ route('login') }}">
                            {{ __('Log in instead') }}
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
