<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
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
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p style="text-align: center;">Az inaktivitási kérelem akkor lesz érvényes, ha egy leader elfogadta.
                    </p>
                    <br>
                    <form method="POST" action="{{ route('inactivity.storeNewInactivity') }}">
                        @csrf
                        <!-- Begin -->
                        <div>
                            <x-input-label for="begin" :value="__('Ettől')" />
                            <x-text-input id="begin" class="required-field-input block mt-1 w-full" type="date" name="begin"
                                :value="old('begin')" required autofocus />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                A kezdet nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('begin')" class="mt-2" />
                        </div>

                        <!-- End -->
                        <div class="mt-4">
                            <x-input-label for="end" :value="__('Eddig')" />
                            <x-text-input id="end" class="required-field-input block mt-1 w-full" type="date" name="end"
                                :value="old('end')" required />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                A vég nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('end')" class="mt-2" />
                        </div>

                        <!-- Reason -->
                        <div class="mt-4">
                            <x-input-label for="reason" :value="__('Indok')" />
                            <x-text-input id="reason" class="required-field-input block mt-1 w-full" type="text" name="reason"
                                :value="old('reason')" required />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                Az indok nem lehet üres.
                            </p>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('inactivity.index') }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button id="save-inactivity-button"
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
                $('#save-inactivity-button').prop('disabled', hasFieldErrors());
            }

            // Only the field being edited gets its own error toggled, not the others
            $('#begin, #end, #reason').on('input change', function() {
                updateFieldError($(this));
                updateSaveButtonState();
            });

            // Only toggle the button silently on load; error messages appear once the user interacts
            updateSaveButtonState();
        });
    </script>
</x-app-layout>
