<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új inaktivitási kérelem felvétele') }}
        </h2>
    </x-slot>

    @session('successful-creation')
        <div class="alert alert-success" role="alert">
            {{ session('successful-creation') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p style="text-align: center;">Az inaktivitási kérelem akkor lesz érvényes, ha egy admin elfogadta.</p>
                    <br>
                    <form method="POST" action="{{ route('inactivity.store') }}">
                        @csrf
                        <!-- Begin -->
                        <div>
                            <x-input-label for="begin" :value="__('Ettől')" />
                            <x-text-input id="begin" class="block mt-1 w-full" type="date" name="begin" :value="old('begin')" required autofocus />
                            <x-input-error :messages="$errors->get('begin')" class="mt-2" />
                        </div>

                        <!-- End -->
                        <div class="mt-4">
                            <x-input-label for="end" :value="__('Eddig')" />
                            <x-text-input id="end" class="block mt-1 w-full" type="date" name="end" :value="old('end')" required />
                            <x-input-error :messages="$errors->get('end')" class="mt-2" />
                        </div>

                        <!-- Reason -->
                        <div class="mt-4">
                            <x-input-label for="reason" :value="__('Indok')" />
                            <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" :value="old('reason')" required />
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('inactivity.index') }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Felvétel') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>