<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="py-12" id="jarmuvek">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Járművek</p>
                <form method="POST" action="{{ route('admin.updateVehicles') }}" id="vehicles-form">
                    @csrf
                    <table class="display view-reports" id="vehicles">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Jármű ID</th>
                                <th scope="col">Rendszám</th>
                                <th scope="col">Típus</th>
                                <th scope="col">Ápoló</th>
                                <th scope="col">II. Ápoló</th>
                                @if (Auth::user()->adminLevel == 2)
                                    <th scope="col">Kezelés</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $vehicle)
                                <tr class="vehicle-row">
                                    <th scope="row" class="vehicle-row-number">{{ $loop->iteration }}</th>
                                    <td>
                                        <x-text-input type="text" name="vehicles[{{ $vehicle->id }}][vehicle_identifier]"
                                            value="{{ $vehicle->vehicle_identifier }}"
                                            class="vehicle-admin-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            required maxlength="255" :readonly="Auth::user()->adminLevel != 2" />
                                    </td>
                                    <td>
                                        <x-text-input type="text" name="vehicles[{{ $vehicle->id }}][plate_number]"
                                            value="{{ $vehicle->plate_number }}"
                                            class="vehicle-admin-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            required maxlength="255" :readonly="Auth::user()->adminLevel != 2" />
                                    </td>
                                    <td>
                                        <select name="vehicles[{{ $vehicle->id }}][type]"
                                            class="vehicle-type-select vehicle-admin-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            required @disabled(Auth::user()->adminLevel != 2)>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                <option value="{{ $vehicleType }}" @selected($vehicle->type === $vehicleType)>
                                                    {{ $vehicleType }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="vehicles[{{ $vehicle->id }}][caregiver_user_id]"
                                            class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                            <option value="">-</option>
                                            @foreach ($vehicleUsers as $vehicleUser)
                                                <option value="{{ $vehicleUser->id }}" @selected((int) $vehicle->caregiver_user_id === (int) $vehicleUser->id)>
                                                    {{ $vehicleUser->charactername }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="vehicles[{{ $vehicle->id }}][secondary_caregiver_user_id]"
                                            class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                            <option value="">-</option>
                                            @foreach ($vehicleUsers as $vehicleUser)
                                                <option value="{{ $vehicleUser->id }}" @selected((int) $vehicle->secondary_caregiver_user_id === (int) $vehicleUser->id)>
                                                    {{ $vehicleUser->charactername }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @if (Auth::user()->adminLevel == 2)
                                        <td>
                                            <button type="button"
                                                class="vehicle-delete-btn w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white text-lg font-bold leading-none"
                                                title="Jármű törlése" aria-label="Jármű törlése"
                                                data-vehicle-name="{{ $vehicle->vehicle_identifier }}">&minus;</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            @if (Auth::user()->adminLevel == 2)
                                <tr id="new-vehicle-row">
                                    <th scope="row"></th>
                                    <td>
                                        <x-text-input type="text" id="new_vehicle_identifier_draft"
                                            placeholder="Jármű ID..."
                                            class="rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            maxlength="255" />
                                    </td>
                                    <td>
                                        <x-text-input type="text" id="new_vehicle_plate_draft"
                                            placeholder="Rendszám..."
                                            class="rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            maxlength="255" />
                                    </td>
                                    <td>
                                        <select id="new_vehicle_type_draft"
                                            class="vehicle-type-select rounded border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value=""></option>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                <option value="{{ $vehicleType }}">{{ $vehicleType }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="new_vehicle_caregiver_draft"
                                            class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">-</option>
                                            @foreach ($vehicleUsers as $vehicleUser)
                                                <option value="{{ $vehicleUser->id }}">{{ $vehicleUser->charactername }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select id="new_vehicle_secondary_caregiver_draft"
                                            class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">-</option>
                                            @foreach ($vehicleUsers as $vehicleUser)
                                                <option value="{{ $vehicleUser->id }}">{{ $vehicleUser->charactername }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <x-primary-button type="button" id="add-vehicle-button"
                                            class="w-8 h-8 flex items-center justify-center rounded-full !bg-green-600 hover:!bg-green-700 text-lg font-bold leading-none p-0"
                                            title="Jármű hozzáadása" aria-label="Jármű hozzáadása">+</x-primary-button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    @if (Auth::user()->adminLevel >= 1)
                        <div class="flex justify-end mt-4">
                            <x-primary-button id="vehicles-save-button"
                                class="admin-button disabled:!bg-gray-400 dark:disabled:!bg-gray-600 disabled:!text-gray-200 disabled:hover:!bg-gray-400 dark:disabled:hover:!bg-gray-600 disabled:cursor-not-allowed"
                                disabled>
                                {{ __('Mentés') }}
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const vehicleUsers = @json($vehicleUsers->map(fn($user) => ['id' => $user->id, 'name' => $user->charactername])->values());
        const vehicleTypes = @json($vehicleTypes);
        const initialUnassignedNames = @json($unassignedVehicleUserNames);
        const canManageVehicles = @json(Auth::user()->adminLevel == 2);
        const form = $('#vehicles-form');
        let original = form.serialize();
        let newVehicleCounter = 0;

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function initializeVehicleUserSelects(context) {
            if (!$.fn.select2) {
                return;
            }

            $(context).find('.vehicle-user-select').each(function() {
                const select = $(this);

                if (select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                select.select2({
                    width: '180px',
                    placeholder: '-',
                    allowClear: true,
                });
            });
        }

        function initializeVehicleTypeSelects(context) {
            if (!$.fn.select2) {
                return;
            }

            $(context).find('.vehicle-type-select').each(function() {
                const select = $(this);

                if (select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                select.select2({
                    width: '220px',
                    placeholder: 'Típus...',
                    tags: true,
                    createTag: function(params) {
                        const term = $.trim(params.term);

                        if (term === '') {
                            return null;
                        }

                        return {
                            id: term,
                            text: term,
                            newTag: true,
                        };
                    },
                });
            });
        }

        function buildVehicleUserOptions(selectedId) {
            let options = '<option value="">-</option>';

            vehicleUsers.forEach(function(user) {
                const selected = String(user.id) === String(selectedId) ? ' selected' : '';
                options += '<option value="' + escapeHtml(user.id) + '"' + selected + '>' + escapeHtml(user.name) + '</option>';
            });

            return options;
        }

        function buildVehicleTypeOptions(selectedType) {
            let options = '<option value=""></option>';
            let selectedTypeExists = false;

            vehicleTypes.forEach(function(type) {
                const selected = type === selectedType ? ' selected' : '';
                selectedTypeExists = selectedTypeExists || type === selectedType;
                options += '<option value="' + escapeHtml(type) + '"' + selected + '>' + escapeHtml(type) + '</option>';
            });

            if (selectedType && !selectedTypeExists) {
                options += '<option value="' + escapeHtml(selectedType) + '" selected>' + escapeHtml(selectedType) + '</option>';
            }

            return options;
        }

        function updateVehicleRowNumbers() {
            $('#vehicles tbody tr.vehicle-row').each(function(index) {
                $(this).find('.vehicle-row-number').text(index + 1);
            });
        }

        function refreshVehiclesTable() {
            if (!window.vehiclesTable) {
                return;
            }

            window.vehiclesTable.rows().invalidate();
            window.vehiclesTable.columns.adjust();
        }

        function getUnassignedVehicleUserNames() {
            const assignedUserIds = new Set();

            $('#vehicles tbody tr.vehicle-row .vehicle-user-select').each(function() {
                const value = $(this).val();

                if (value) {
                    assignedUserIds.add(String(value));
                }
            });

            return vehicleUsers
                .filter(function(user) {
                    return !assignedUserIds.has(String(user.id));
                })
                .map(function(user) {
                    return user.name;
                });
        }

        function renderNameList(names) {
            return '<ul class="text-left">' + names.map(function(name) {
                return '<li>' + escapeHtml(name) + '</li>';
            }).join('') + '</ul>';
        }

        function updateSaveButton() {
            $('#vehicles-save-button').prop('disabled', form.serialize() === original);
        }

        initializeVehicleUserSelects(document);
        initializeVehicleTypeSelects(document);
        updateVehicleRowNumbers();

        if (initialUnassignedNames.length > 0) {
            window.queueAdminAlert(function() {
                return Swal.fire({
                    title: 'Jármű nélküliek',
                    html: renderNameList(initialUnassignedNames),
                    icon: 'warning',
                    confirmButtonText: 'Rendben',
                });
            });
        }

        form.on('input change', 'input, select', updateSaveButton);
        form.on('select2:select select2:clear', '.vehicle-user-select, .vehicle-type-select', updateSaveButton);

        $('#add-vehicle-button').on('click', function() {
            const vehicleIdentifier = $('#new_vehicle_identifier_draft').val().trim();
            const plateNumber = $('#new_vehicle_plate_draft').val().trim();
            const type = ($('#new_vehicle_type_draft').val() || '').trim();
            const caregiverUserId = $('#new_vehicle_caregiver_draft').val();
            const secondaryCaregiverUserId = $('#new_vehicle_secondary_caregiver_draft').val();

            if (!vehicleIdentifier || !plateNumber || !type) {
                Swal.fire({
                    text: 'A jármű ID, rendszám és típus megadása kötelező.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                });
                return;
            }

            if (caregiverUserId && caregiverUserId === secondaryCaregiverUserId) {
                Swal.fire({
                    text: 'Egy járműnél az Ápoló és II. Ápoló nem lehet ugyanaz a felhasználó.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                });
                return;
            }

            const vehicleIdentifierExists = $('#vehicles tbody tr.vehicle-row input[name$="[vehicle_identifier]"]').toArray().some(function(input) {
                return $(input).val().trim().toLowerCase() === vehicleIdentifier.toLowerCase();
            });

            if (vehicleIdentifierExists) {
                Swal.fire({
                    text: 'Van már ilyen jármű ID.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                });
                return;
            }

            const index = newVehicleCounter++;
            const newRow = $('<tr class="vehicle-row"></tr>');

            newRow.append('<th scope="row" class="vehicle-row-number"></th>');
            newRow.append('<td><input type="text" name="new_vehicles[' + index + '][vehicle_identifier]" value="' + escapeHtml(vehicleIdentifier) + '" class="vehicle-admin-input text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm rounded border-gray-300 dark:bg-gray-900 dark:text-white" required maxlength="255"></td>');
            newRow.append('<td><input type="text" name="new_vehicles[' + index + '][plate_number]" value="' + escapeHtml(plateNumber) + '" class="vehicle-admin-input text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm rounded border-gray-300 dark:bg-gray-900 dark:text-white" required maxlength="255"></td>');
            newRow.append('<td><select name="new_vehicles[' + index + '][type]" class="vehicle-type-select vehicle-admin-input rounded border-gray-300 dark:bg-gray-900 dark:text-white" required>' + buildVehicleTypeOptions(type) + '</select></td>');
            newRow.append('<td><select name="new_vehicles[' + index + '][caregiver_user_id]" class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white">' + buildVehicleUserOptions(caregiverUserId) + '</select></td>');
            newRow.append('<td><select name="new_vehicles[' + index + '][secondary_caregiver_user_id]" class="vehicle-user-select rounded border-gray-300 dark:bg-gray-900 dark:text-white">' + buildVehicleUserOptions(secondaryCaregiverUserId) + '</select></td>');
            newRow.append('<td><button type="button" class="vehicle-delete-btn w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white text-lg font-bold leading-none" title="Jármű törlése" aria-label="Jármű törlése">&minus;</button></td>');

            newRow.insertBefore('#new-vehicle-row');
            initializeVehicleUserSelects(newRow);
            initializeVehicleTypeSelects(newRow);

            $('#new_vehicle_identifier_draft').val('');
            $('#new_vehicle_plate_draft').val('');
            $('#new_vehicle_type_draft').val('').trigger('change');
            $('#new_vehicle_caregiver_draft, #new_vehicle_secondary_caregiver_draft').val('').trigger('change');

            updateVehicleRowNumbers();
            refreshVehiclesTable();
            updateSaveButton();
        });

        $('#vehicles').on('click', '.vehicle-delete-btn', function() {
            if (!canManageVehicles) {
                return;
            }

            const button = this;
            const vehicleName = button.dataset.vehicleName;

            function removeRow() {
                const row = $(button).closest('tr.vehicle-row');
                row.find('.vehicle-user-select').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                row.remove();
                updateVehicleRowNumbers();
                refreshVehiclesTable();
                updateSaveButton();
            }

            if (!vehicleName) {
                removeRow();
                return;
            }

            Swal.fire({
                title: 'Jármű törlése',
                text: 'Biztos törölni akarod a(z) ' + vehicleName + ' járművet? A törlés csak a Mentés gomb megnyomásakor lép érvénybe.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Törlés',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Mégse',
            }).then((result) => {
                if (result.isConfirmed) {
                    removeRow();
                }
            });
        });

        form.on('submit', function(event) {
            const unassignedNames = getUnassignedVehicleUserNames();

            if (unassignedNames.length === 0 || form.data('unassigned-confirmed')) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: 'Jármű nélküli játékosok',
                html: renderNameList(unassignedNames),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Mentés',
                cancelButtonText: 'Mégse',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.data('unassigned-confirmed', true);
                    form.trigger('submit');
                }
            });
        });
    });
</script>
