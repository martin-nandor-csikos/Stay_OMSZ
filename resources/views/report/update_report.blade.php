<script>
    const services = @json($services);
</script>
@vite('resources/js/report_checkbox.js')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jelentés frissítése') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('reports.updateReport', $report->id) }}" id="update-report-form">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="cost" :value="__('Ár ($)')" class="price-currency" />
                            <x-text-input id="cost"
                                class="block mt-1 w-full bg-gray-100 dark:bg-gray-500 dark:text-white cursor-not-allowed"
                                type="number" name="cost" value="{{ old('cost', $report->price) }}" required
                                max="300000" min="0" readonly />
                            <x-input-error :messages="$errors->get('cost')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="services" :value="__('Ellátások')" />
                            <x-text-input id="services"
                                class="required-field-input block mt-1 w-full  bg-gray-100 dark:bg-gray-500 dark:text-white cursor-not-allowed"
                                type="text" name="services" value="{{ old('services', $report->diagnosis) }}" required
                                readonly />
                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                Legalább egy ellátást ki kell választani.
                            </p>
                            <x-input-error :messages="$errors->get('services')" class="mt-2" />

                            <div class="mt-4">
                                <div class="row">
                                    @foreach ($services as $service_name => $cost)
                                        @if ($loop->iteration % 3 == 1)
                                            <div class="col-md-4 col-sm-12 checkbox-wrapper">
                                        @endif

                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="{{ $service_name }}" name="{{ $service_name }}"
                                                value="{{ $service_name }}"
                                                @checked(in_array($service_name, $selectedServices))
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="{{ $service_name }}"> {{ $service_name }}</label><br>
                                        </div>

                                        @if ($loop->iteration % 3 == 0 || $loop->last)
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="withWho" :value="__('Társaid (nem kötelező)')" />
                    <x-text-input id="withWho" class="block mt-1 w-full" type="text" name="withWho"
                        value="{{ old('withWho', $report->withWho) }}" autocomplete="withWho" maxlength="100" />
                    <x-input-error :messages="$errors->get('withWho')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="img" :value="__('Kép (imgur link)')" />
                    <x-text-input id="img" class="required-field-input block mt-1 w-full" type="text" name="img"
                        :value="old('img', $report->img)" required maxlength="100" />
                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                        A kép megadása kötelező.
                    </p>
                    <x-input-error :messages="$errors->get('img')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('reports.index') }}">
                        <x-secondary-button>
                            {{ __('Vissza') }}
                        </x-secondary-button>
                    </a>

                    <x-primary-button id="save-report-button"
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
            const originalReportValues = {
                cost: $('#cost').val(),
                services: $('#services').val(),
                withWho: $('#withWho').val(),
                img: $('#img').val(),
            };

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
                const changed =
                    $('#cost').val() !== originalReportValues.cost ||
                    $('#services').val() !== originalReportValues.services ||
                    $('#withWho').val() !== originalReportValues.withWho ||
                    $('#img').val() !== originalReportValues.img;

                $('#save-report-button').prop('disabled', !changed || hasFieldErrors());
            }

            // Ellátások: only react to checkbox changes, after report_checkbox.js updates the field
            $('input[type=checkbox]').on('click', function() {
                setTimeout(function() {
                    updateFieldError($('#services'));
                    updateSaveButtonState();
                }, 0);
            });

            // Kép: only shows its own error once the user edits it
            $('#img').on('input', function() {
                updateFieldError($(this));
                updateSaveButtonState();
            });

            $('#withWho').on('input', updateSaveButtonState);
        });
    </script>
</x-app-layout>
