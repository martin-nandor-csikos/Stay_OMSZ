<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Új felhasználó regisztrálása') }}
        </h2>
    </x-slot>

    @session('successful-creation')
        <div class="alert alert-success" role="alert">
            {{ session('successful-creation') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="top5">A játékos Account ID-ját és IC nevét kell megadni.</p>
                    <p class="top5">A regisztrációnál automatikusan generált felhasználónevet, és jelszót kap a
                        játékos, amit belépésekor szabadon megváltoztathat.</p>
                    <form method="POST" action="{{ route('admin.registerUser') }}">
                        @csrf
                        <!-- Account ID -->
                        <div>
                            <x-input-label for="account_id" :value="__('Account ID')" />
                            <x-text-input id="account_id" class="block mt-1 w-full" type="number" name="account_id"
                                :value="old('account_id')" min="1" required autofocus />
                            <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                        </div>

                        <!-- Character name -->
                        <div class="mt-4">
                            <x-input-label for="charactername" :value="__('IC név')" />
                            <x-text-input id="charactername" class="block mt-1 w-full" type="text"
                                name="charactername" required />
                            <x-input-error :messages="$errors->get('charactername')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ url()->previous() }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Regisztráció') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
