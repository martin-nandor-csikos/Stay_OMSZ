<div class="py-12" id="elozo-het">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 view-reports-padding">
                <p class="top5">Előző hét (lezárt)</p>
                <form action="{{ route('admin.setDutyTime') }}" method="post">
                    @csrf
                    <div>
                        <x-input-label for="dutyTime" :value="__('Minimum heti szolgálati idő (perc)')" />
                        <x-text-input id="dutyTime" class="block mt-1 w-full" type="number" name="dutyTime" value="{{ $dutyTime }}" required autofocus />
                        <x-input-error :messages="$errors->get('dutyTime')" class="mt-2" />
                        
                            <x-primary-button class="ms-4">
                                {{ __('Mentés') }}
                            </x-primary-button>
                    </div>
                </form>

                <form action="{{ route('admin.setReportAmount') }}" method="post">
                    @csrf
                    <div>
                        <x-input-label for="reportAmount" :value="__('Minimum heti jelentés szám')" />
                        <x-text-input id="reportAmount" class="block mt-1 w-full" type="number" name="reportAmount" value="{{ $reportAmount }}" required autofocus />
                        <x-input-error :messages="$errors->get('reportAmount')" class="mt-2" />
                        
                            <x-primary-button class="ms-4">
                                {{ __('Mentés') }}
                            </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>