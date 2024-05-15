<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új jelentés felvétele') }}
        </h2>
    </x-slot>

    @session('successful-creation')
        <div class="alert alert-success" role="alert">
            {{ session('successful-creation') }}
        </div>
    @endsession

    <form method="POST" action="{{ route('reports.store') }}">
        @csrf

        <div class="create-report">
            <!-- Price -->
            <div>
                <x-input-label for="price" :value="__('Ár ($)')" class="price-currency" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required autofocus autocomplete="price" max="300000" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <!-- Diagnosis -->
            <div class="mt-4">
                <x-input-label for="diagnosis" :value="__('Diagnózis')" />
                <x-text-input id="diagnosis" class="block mt-1 w-full" type="text" name="diagnosis" :value="old('diagnosis')" required autocomplete="diagnosis" maxlength="100" />
                <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
            </div>
            <!-- withWho -->
            <div class="mt-4">
                <x-input-label for="withWho" :value="__('Társad (nem kötelező)')" />
                <x-text-input id="withWho" class="block mt-1 w-full" type="text" name="withWho" :value="old('withWho')" autocomplete="withWho" maxlength="100" />
                <x-input-error :messages="$errors->get('withWho')" class="mt-2" />
            </div>
            <!-- img -->
            <div class="mt-4">
                <x-input-label for="img" :value="__('Kép (imgur link)')" />
                <x-text-input id="img" class="block mt-1 w-full" type="text" name="img" :value="old('img')" required maxlength="100" />
                <x-input-error :messages="$errors->get('img')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ url()->previous() }}">
                    <x-secondary-button>
                        {{ __('Mégse') }}
                    </x-secondary-button>
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Felvétel') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
