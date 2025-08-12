<div class="py-12" id="diagnosis-beallitasok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Ellátási árak</p>
                <form method="POST" action="{{ route('admin.updateServiceCosts') }}">
                    @csrf
                    <div class="grid gap-4 mx-4">
                        @foreach($ticketServices as $name => $cost)
                            <div>
                                <x-input-label for="{{ $name }}_cost" :value="$name" />
                                <x-text-input type="number" name="{{ $name }}_cost" id="{{ $name }}_cost" value="{{ $cost }}" class="rounded border-gray-300 dark:bg-gray-900 dark:text-white" required min="1" max="300000" />
                                <x-input-error :messages="$errors->get($name . '_cost')" class="mt-2" />
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-end mt-4">
                        <x-primary-button class="admin-button">
                            {{ __('Mentés') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
