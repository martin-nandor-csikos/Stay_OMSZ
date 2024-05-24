<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új szolgálat felvétele') }}
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
                    <form method="POST" action="{{ route('duty_time.store') }}">
                        @csrf
                        <!-- Begin -->
                        <div>
                            <x-input-label for="begin" :value="__('Felvétel')" />
                            <x-text-input id="begin" class="block mt-1 w-full" type="datetime-local" name="begin" :value="old('begin')" required autofocus />
                            <x-input-error :messages="$errors->get('begin')" class="mt-2" />
                        </div>

                        <!-- End -->
                        <div class="mt-4">
                            <x-input-label for="end" :value="__('Leadás')" />
                            <x-text-input id="end" class="block mt-1 w-full" type="datetime-local" name="end" :value="old('end')" required />
                            <x-input-error :messages="$errors->get('end')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('duty_time.index') }}">
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