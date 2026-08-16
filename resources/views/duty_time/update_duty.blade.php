<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Szolgálat frissítése') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('duty_time.updateDuty', $duty->id) }}">
                        @csrf
                        @method('PUT')
                        <!-- Begin -->
                        <div>
                            <x-input-label for="begin" :value="__('Felvétel')" />
                            <x-text-input id="begin" class="required-field-input block mt-1 w-full" type="datetime-local" name="begin"
                                :value="old('begin', $duty->begin)" required autofocus />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                A felvétel ideje nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('begin')" class="mt-2" />
                        </div>

                        <!-- End -->
                        <div class="mt-4">
                            <x-input-label for="end" :value="__('Leadás')" />
                            <x-text-input id="end" class="required-field-input block mt-1 w-full" type="datetime-local" name="end"
                                :value="old('end', $duty->end)" required />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                A leadás ideje nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('end')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('duty_time.index') }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button id="save-duty-button"
                                class="ms-4 disabled:!bg-gray-400 dark:disabled:!bg-gray-600 disabled:!text-gray-200 disabled:hover:!bg-gray-400 dark:disabled:hover:!bg-gray-600 disabled:cursor-not-allowed"
                                disabled>
                                {{ __('Mentés') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            const originalDutyValues = {
                begin: $('#begin').val(),
                end: $('#end').val(),
            };

            function hasFieldErrors() {
                return $('.required-field-input').toArray().some(function(field) {
                    return $(field).val().toString().trim() === '';
                });
            }

            function updateRequiredFieldErrors() {
                $('.required-field-input').each(function() {
                    const empty = $(this).val().toString().trim() === '';

                    $(this).toggleClass('border-red-500', empty);
                    $(this).siblings('.required-field-error').toggleClass('hidden', !empty);
                });
            }

            function checkDutyFormChanged() {
                const changed =
                    $('#begin').val() !== originalDutyValues.begin ||
                    $('#end').val() !== originalDutyValues.end;

                updateRequiredFieldErrors();

                $('#save-duty-button').prop('disabled', !changed || hasFieldErrors());
            }

            $('#begin, #end').on('input change', checkDutyFormChanged);
        });
    </script>
</x-app-layout>
