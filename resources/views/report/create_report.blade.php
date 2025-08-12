<script>
    const services = @json($services);
</script>
@vite('resources/js/report_checkbox.js')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Új jelentés felvétele') }}
        </h2>
    </x-slot>

    @include('report.sessions')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('reports.store_new_report') }}">
                        @csrf
                        <div>
                            <x-input-label for="cost" :value="__('Ár ($)')" class="price-currency" />
                            <x-text-input id="cost" class="block mt-1 w-full bg-gray-100 dark:bg-gray-500 dark:text-white cursor-not-allowed" type="number" name="cost" value="0" required max="300000" min="0" readonly />
                            <x-input-error :messages="$errors->get('cost')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="services" :value="__('Ellátások')" />
                            <x-text-input id="services" class="block mt-1 w-full  bg-gray-100 dark:bg-gray-500 dark:text-white cursor-not-allowed" type="text" name="services" required readonly />

                            <div class="mt-4">
                                <div class="row">
                                    @foreach ($services as $service_name => $cost)

                                    @if ($loop->iteration % 3 == 1)
                                    <div class="col-md-4 col-sm-12 checkbox-wrapper">
                                    @endif

                                    <div class="form-check form-check-inline checkbox">
                                        <input type="checkbox" id="{{ $service_name }}" name="{{ $service_name }}" value="{{ $service_name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <label for="{{ $service_name }}"> {{ $service_name }}</label><br>
                                    </div>

                                    @if ($loop->iteration % 3 == 0 || $loop->last)
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('services')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="withWho" :value="__('Társaid (nem kötelező)')" />
                            <x-text-input id="withWho" class="block mt-1 w-full" type="text" name="withWho" autocomplete="withWho" maxlength="100" />
                            <x-input-error :messages="$errors->get('withWho')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="img" :value="__('Kép (imgur link)')" />
                            <x-text-input id="img" class="block mt-1 w-full" type="text" name="img" :value="old('img')" required maxlength="100" />
                            <x-input-error :messages="$errors->get('img')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('reports.index') }}">
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
