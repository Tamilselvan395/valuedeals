<x-guest-layout>
    <div class="flex min-h-screen bg-black items-center justify-center p-4">
        <div class="max-w-md w-full bg-[#111] p-8 rounded-2xl shadow-2xl border border-gray-800">
            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <x-application-logo class="h-10 sm:h-12 fill-current text-primary mx-auto" />
                </a>
                <h1 class="text-3xl font-bold text-white">Create Account</h1>
                <p class="text-gray-400 mt-2">Sign up to get started today.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                @if(request()->boolean('checkout'))
                    <input type="hidden" name="checkout" value="1">
                @endif

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Full Name</label>
                    <div class="mt-1">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="John Doe">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-500 text-sm" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="you@example.com">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-sm" />
                </div>
                
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        <div class="mt-1">
                            <input id="password" type="password" name="password" required autocomplete="new-password" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-sm" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm</label>
                        <div class="mt-1">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                                class="block w-full px-4 py-3 rounded-lg border-gray-700 bg-black text-white focus:ring-primary focus:border-primary transition-colors" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-500 text-sm" />
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-bold text-black bg-primary hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-transform transform hover:-translate-y-0.5">
                        {{ __('Create Account') }}
                    </button>
                </div>

                <p class="mt-6 text-center text-sm text-gray-400">
                    {{ __('Already have an account?') }}
                    <a class="font-bold text-primary hover:text-yellow-400 transition border-b border-primary hover:border-yellow-400" href="{{ route('login') }}">
                        {{ __('Log in instead') }}
                    </a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
