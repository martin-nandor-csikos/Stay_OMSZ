<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
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
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('duty_time.storeNewDuty') }}">
                        @csrf
                        <!-- Begin -->
                        <div>
                            <x-input-label for="begin" :value="__('Felvétel')" />
                            <x-text-input id="begin" class="required-field-input block mt-1 w-full" type="datetime-local" name="begin"
                                :value="old('begin')" required autofocus />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                A felvétel ideje nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('begin')" class="mt-2" />
                        </div>

                        <!-- End -->
                        <div class="mt-4">
                            <x-input-label for="end" :value="__('Leadás')" />
                            <x-text-input id="end" class="required-field-input block mt-1 w-full" type="datetime-local" name="end"
                                :value="old('end')" required />
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
                                {{ __('Felvétel') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            function isEmpty($el) {
                return $el.val().toString().trim() === '';
            }

            function updateFieldError($el) {
                const empty = isEmpty($el);

                $el.toggleClass('border-red-500', empty);
                $el.siblings('.required-field-error').toggleClass('hidden', !empty);
            }

            function hasFieldErrors() {
                return $('.required-field-input').toArray().some(function(field) {
                    return isEmpty($(field));
                });
            }

            function updateSaveButtonState() {
                $('#save-duty-button').prop('disabled', hasFieldErrors());
            }

            // Only the field being edited gets its own error toggled, not the other one
            $('#begin, #end').on('input change', function() {
                updateFieldError($(this));
                updateSaveButtonState();
            });

            // Only toggle the button silently on load; error messages appear once the user interacts
            updateSaveButtonState();
        });
    </script>
</x-app-layout>
