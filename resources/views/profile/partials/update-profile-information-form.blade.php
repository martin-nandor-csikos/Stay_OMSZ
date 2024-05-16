<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('E-mail cím változtatás') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Az e-mail címed frissítéséhez add meg az új e-mail címed.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update', $user->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="email" :value="__('Új e-mail cím')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Mentés') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Elmentve.') }}</p>
            @endif
        </div>
    </form>
</section>
