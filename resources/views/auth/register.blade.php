<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Character name -->
        <div>
            <x-input-label for="charactername" :value="__('IC név')" />
            <x-text-input id="charactername" class="block mt-1 w-full" type="text" name="charactername" :value="old('charactername')" required autofocus autocomplete="charactername" />
            <x-input-error :messages="$errors->get('charactername')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Felhasználónév')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Jelszó')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Jelszó megerősítése')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Regisztráció') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
