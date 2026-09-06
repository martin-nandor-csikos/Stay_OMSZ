<div class="py-12" id="beallitasok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Beállítások</p>

                <form method="POST" action="{{ route('admin.updateSettings') }}" id="settings-form">
                    @csrf

                    <div class="grid gap-6" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));">
                        <div>
                            <p class="top5 text-lg mt-4">Minimumok</p>
                            <div class="grid gap-4 mx-4">
                                <div>
                                    <x-input-label for="minimum_report_count" value="Minimum jelentés szám" />
                                    <x-text-input type="number" name="minimum_report_count" id="minimum_report_count"
                                        value="{{ $settings->minimum_report_count }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="1000" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A minimum jelentés szám nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('minimum_report_count')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="double_week_report_count" value="Dupla hét jelentés szám" />
                                    <x-text-input type="number" name="double_week_report_count"
                                        id="double_week_report_count"
                                        value="{{ $settings->double_week_report_count }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="1000" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A dupla hét jelentés szám nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('double_week_report_count')"
                                        class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="minimum_duty_time" value="Minimum szolgálati idő (perc)" />
                                    <x-text-input type="number" name="minimum_duty_time" id="minimum_duty_time"
                                        value="{{ $settings->minimum_duty_time }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="100000" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A minimum szolgálati idő nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('minimum_duty_time')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="double_week_duty_time"
                                        value="Dupla hét szolgálati idő (perc)" />
                                    <x-text-input type="number" name="double_week_duty_time"
                                        id="double_week_duty_time" value="{{ $settings->double_week_duty_time }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="100000" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A dupla hét szolgálati idő nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('double_week_duty_time')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="top5 text-lg mt-4">Bónuszok</p>
                            <div class="grid gap-4 mx-4">
                                <div>
                                    <x-input-label for="bonus_first_percentage"
                                        value="Legtöbbet leadott jelentésért járó bónusz (%)" />
                                    <x-text-input type="number" name="bonus_first_percentage"
                                        id="bonus_first_percentage" value="{{ $settings->bonus_first_percentage }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="100" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A bónusz nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('bonus_first_percentage')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="bonus_second_percentage"
                                        value="Második legtöbbet leadott jelentésért járó bónusz (%)" />
                                    <x-text-input type="number" name="bonus_second_percentage"
                                        id="bonus_second_percentage" value="{{ $settings->bonus_second_percentage }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="100" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A bónusz nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('bonus_second_percentage')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="bonus_third_percentage"
                                        value="Harmadik legtöbbet leadott jelentésért járó bónusz (%)" />
                                    <x-text-input type="number" name="bonus_third_percentage"
                                        id="bonus_third_percentage" value="{{ $settings->bonus_third_percentage }}"
                                        class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                        required min="0" max="100" :readonly="Auth::user()->adminLevel != 2" />
                                    <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                        A bónusz nem lehet üres.
                                    </p>
                                    <x-input-error :messages="$errors->get('bonus_third_percentage')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="top5 text-lg">Ellátási árak</p>
                        <table class="display view-reports" id="ticket-services">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Név</th>
                                    <th scope="col">Ár ($)</th>
                                    <th scope="col">Utolsó árfrissítés</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticketServicesWithUpdateTimes as $service)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $service->service_name }}</td>
                                        <td>
                                            <x-text-input type="number" name="{{ $service->service_name }}_cost"
                                                id="{{ $service->service_name }}_cost" value="{{ $service->cost }}"
                                                class="required-field-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                required min="1" max="300000" :readonly="Auth::user()->adminLevel != 2" />
                                            <p class="required-field-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                                Az ár nem lehet üres.
                                            </p>
                                            <x-input-error :messages="$errors->get($service->service_name . '_cost')" class="mt-2" />
                                        </td>
                                        <td>{{ $service->updated_at ? \Carbon\Carbon::parse($service->updated_at)->format('Y.m.d H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <p class="top5 text-lg">Rangok</p>
                        <table class="display view-reports" id="ranks">
                            <thead>
                                <tr>
                                    <th scope="col">Sorrend</th>
                                    <th scope="col">Név</th>
                                    <th scope="col">Fizetés ($)</th>
                                    <th scope="col">Min. sikeres hét</th>
                                    <th scope="col">Vizsga szükséges?</th>
                                    <th scope="col">Leader rank?</th>
                                    @if (Auth::user()->adminLevel == 2)
                                        <th scope="col">Kezelés</th>
                                        <th scope="col">Fel</th>
                                        <th scope="col">Le</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ranks as $rank)
                                    <tr class="rank-row">
                                        <td>
                                            <span class="rank-order-display">{{ $rank->rank_order }}</span>
                                            <input type="hidden" class="rank-order-input"
                                                name="ranks[{{ $rank->id }}][order]" value="{{ $rank->rank_order }}">
                                        </td>
                                        <td>
                                            <x-text-input type="text" class="rank-name-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                name="ranks[{{ $rank->id }}][name]" value="{{ $rank->name }}"
                                                required maxlength="255" :readonly="Auth::user()->adminLevel != 2" />
                                            <p class="rank-name-error text-red-600 dark:text-red-400 text-sm mt-2 hidden"></p>
                                            <x-input-error :messages="$errors->get('ranks.' . $rank->id . '.name')" class="mt-2" />
                                        </td>
                                        <td>
                                            <x-text-input type="number" class="rank-salary-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                name="ranks[{{ $rank->id }}][salary]" value="{{ $rank->salary }}"
                                                required min="0" max="1000000" :readonly="Auth::user()->adminLevel != 2" />
                                            <p class="rank-salary-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                                Fizetés nem lehet üres
                                            </p>
                                            <x-input-error :messages="$errors->get('ranks.' . $rank->id . '.salary')" class="mt-2" />
                                        </td>
                                        <td>
                                            <x-text-input type="number" class="rank-min-weeks-input rounded border-gray-300 dark:bg-gray-900 dark:text-white {{ $rank->is_leader ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : '' }}"
                                                name="ranks[{{ $rank->id }}][minimum_successful_weeks]" value="{{ $rank->is_leader ? 0 : ($rank->minimum_successful_weeks ?? 2) }}"
                                                required min="0" max="100"
                                                :readonly="Auth::user()->adminLevel != 2 || (bool)$rank->is_leader" />
                                            <x-input-error :messages="$errors->get('ranks.' . $rank->id . '.minimum_successful_weeks')" class="mt-2" />
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="ranks[{{ $rank->id }}][requires_exam]" value="0">
                                            <input type="checkbox" name="ranks[{{ $rank->id }}][requires_exam]" value="1"
                                                class="rank-exam-input rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                @checked($rank->requires_exam && $rank->rank_order != 1 && !$rank->is_leader)
                                                @disabled(Auth::user()->adminLevel != 2 || $rank->rank_order == 1 || $rank->is_leader)>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="ranks[{{ $rank->id }}][is_leader]" value="0">
                                            <input type="checkbox" name="ranks[{{ $rank->id }}][is_leader]" value="1"
                                                class="rank-leader-input rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                @checked($rank->is_leader)
                                                @disabled(Auth::user()->adminLevel != 2)>
                                        </td>
                                        @if (Auth::user()->adminLevel == 2)
                                            <td>
                                                <button type="button"
                                                    class="rank-delete-btn w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white text-lg font-bold leading-none"
                                                    title="Rang törlése" aria-label="Rang törlése"
                                                    data-rank-name="{{ $rank->name }}">&minus;</button>
                                            </td>
                                            <td>
                                                <x-secondary-button type="button" class="rank-move-up"
                                                    :disabled="$loop->first">&uarr;</x-secondary-button>
                                            </td>
                                            <td>
                                                <x-secondary-button type="button" class="rank-move-down"
                                                    :disabled="$loop->last">&darr;</x-secondary-button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                                @if (Auth::user()->adminLevel == 2)
                                    <tr id="new-rank-row">
                                        <td></td>
                                        <td>
                                            <x-text-input type="text" id="new_rank_name_draft"
                                                placeholder="Név..."
                                                class="rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                maxlength="255" />
                                            <p id="new-rank-name-error" class="text-red-600 dark:text-red-400 text-sm mt-2 hidden">
                                                Van már ilyen nevű rank
                                            </p>
                                            <x-input-error :messages="$errors->get('rank_name')" class="mt-2" />
                                        </td>
                                        <td>
                                            <x-text-input type="number" id="new_rank_salary_draft"
                                                placeholder="Fizetés ($)"
                                                class="rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                min="0" max="1000000" />
                                        </td>
                                        <td>
                                            <x-text-input type="number" id="new_rank_min_weeks_draft"
                                                placeholder="Min. hetek" value="2"
                                                class="rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                                min="0" max="100" />
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" id="new_rank_exam_draft" value="1"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" id="new_rank_leader_draft" value="1"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </td>
                                        <td>
                                            <x-primary-button type="button" id="add-rank-button"
                                                class="w-8 h-8 flex items-center justify-center rounded-full !bg-green-600 hover:!bg-green-700 text-lg font-bold leading-none p-0"
                                                title="Rang hozzáadása" aria-label="Rang hozzáadása">+</x-primary-button>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-4">
                        <x-primary-button id="settings-save-button"
                            class="admin-button disabled:!bg-gray-400 dark:disabled:!bg-gray-600 disabled:!text-gray-200 disabled:hover:!bg-gray-400 dark:disabled:hover:!bg-gray-600 disabled:cursor-not-allowed"
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
        const settingsForm = $('#settings-form');
        const originalSettingsValues = settingsForm.serialize();
        const canManageSettings = @json(Auth::user()->adminLevel == 2);
        let newRankCounter = 0;

        function hasFieldErrors() {
            const visibleErrors = $('#ranks .rank-name-error:not(.hidden), #ranks .rank-salary-error:not(.hidden), #new-rank-name-error:not(.hidden), .required-field-error:not(.hidden)').length > 0;

            const emptyRankFields = $('#ranks tbody tr.rank-row .rank-name-input:not(:disabled), #ranks tbody tr.rank-row .rank-salary-input:not(:disabled)')
                .toArray()
                .some(function(field) {
                    return $(field).val().trim() === '';
                });

            const emptyRequiredFields = $('.required-field-input')
                .toArray()
                .some(function(field) {
                    return $(field).val().toString().trim() === '';
                });

            return visibleErrors || emptyRankFields || emptyRequiredFields;
        }

        function checkSettingsFormChanged() {
            const unchanged = settingsForm.serialize() === originalSettingsValues;

            settingsForm.find('#settings-save-button').prop('disabled', !canManageSettings || unchanged || hasFieldErrors());
        }

        settingsForm.on('input change', 'input, select', checkSettingsFormChanged);

        // Show/hide the "required" error message next to Ellátási árak and Minimumok inputs
        settingsForm.on('input change', '.required-field-input', function() {
            const $input = $(this);
            const empty = $input.val().toString().trim() === '';

            $input.toggleClass('border-red-500', empty);
            $input.siblings('.required-field-error').toggleClass('hidden', !empty);
        });

        function updateRankMoveButtons() {
            const rankRows = $('#ranks tbody tr.rank-row');

            rankRows.each(function(index) {
                $(this).find('.rank-move-up').prop('disabled', index === 0);
                $(this).find('.rank-move-down').prop('disabled', index === rankRows.length - 1);
            });
        }

        function updateRankExamAndLeaderStates() {
            const rankRows = $('#ranks tbody tr.rank-row');
            rankRows.each(function(index) {
                const row = $(this);
                const isLeader = row.find('.rank-leader-input').is(':checked');
                const examInput = row.find('.rank-exam-input');
                const minWeeksInput = row.find('.rank-min-weeks-input');

                if (!canManageSettings) {
                    examInput.prop('disabled', true);
                    minWeeksInput.prop('readonly', true);
                    return;
                }

                if (index === 0) {
                    examInput.prop('checked', false).prop('disabled', true);
                } else if (isLeader) {
                    examInput.prop('checked', false).prop('disabled', true);
                } else {
                    examInput.prop('disabled', false);
                }

                if (isLeader) {
                    minWeeksInput.val(0).prop('readonly', true).addClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                } else {
                    minWeeksInput.prop('readonly', false).removeClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                }
            });
        }

        function renumberRankRows() {
            $('#ranks tbody tr.rank-row').each(function(index) {
                const order = index + 1;
                $(this).find('.rank-order-display').text(order);
                $(this).find('.rank-order-input').val(order);
            });
            updateRankExamAndLeaderStates();
        }

        function moveRankRow(button, direction) {
            const row = $(button).closest('tr.rank-row');
            const targetRow = direction === 'up' ? row.prev('.rank-row') : row.next('.rank-row');

            if (targetRow.length === 0) {
                return;
            }

            if (direction === 'up') {
                row.insertBefore(targetRow);
            } else {
                row.insertAfter(targetRow);
            }

            if (window.ranksTable) {
                window.ranksTable.rows().invalidate();
            }

            renumberRankRows();
            updateRankMoveButtons();
            checkSettingsFormChanged();
        }

        $('#ranks').on('click', '.rank-move-up', function() {
            moveRankRow(this, 'up');
        });

        $('#ranks').on('click', '.rank-move-down', function() {
            moveRankRow(this, 'down');
        });

        $('#ranks').on('change', '.rank-leader-input', function() {
            const isLeader = $(this).is(':checked');
            const row = $(this).closest('tr.rank-row');
            const minWeeksInput = row.find('.rank-min-weeks-input');
            const examInput = row.find('.rank-exam-input');
            const rankRows = $('#ranks tbody tr.rank-row');
            const rowIndex = rankRows.index(row);

            if (isLeader) {
                minWeeksInput.val(0).prop('readonly', true).addClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                examInput.prop('checked', false).prop('disabled', true);
            } else {
                if (minWeeksInput.val() == '0') {
                    minWeeksInput.val(2);
                }
                minWeeksInput.prop('readonly', false).removeClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                if (rowIndex === 0) {
                    examInput.prop('checked', false).prop('disabled', true);
                } else {
                    examInput.prop('disabled', false);
                }
            }
            checkSettingsFormChanged();
        });

        $('#new_rank_leader_draft').on('change', function() {
            const isLeader = $(this).is(':checked');
            const minWeeksInput = $('#new_rank_min_weeks_draft');
            const examInput = $('#new_rank_exam_draft');

            if (isLeader) {
                minWeeksInput.val(0).prop('readonly', true).addClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                examInput.prop('checked', false).prop('disabled', true);
            } else {
                minWeeksInput.val(2).prop('readonly', false).removeClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
                examInput.prop('disabled', false);
            }
        });

        // Alert in real time (while typing) if a rank name is empty or clashes with another rank's name
        function checkRankNameDuplicate(input) {
            const $input = $(input);
            const name = $input.val().trim();
            const row = $input.closest('tr.rank-row');
            const error = row.find('.rank-name-error');

            if (name === '') {
                error.text('Rank név nem lehet üres').removeClass('hidden');
                $input.addClass('border-red-500');
                return;
            }

            const duplicate = $('#ranks tbody tr.rank-row .rank-name-input').toArray().some(function(other) {
                return other !== input && $(other).val().trim().toLowerCase() === name.toLowerCase();
            });

            if (duplicate) {
                error.text('Van már ilyen nevű rank').removeClass('hidden');
                $input.addClass('border-red-500');
            } else {
                error.addClass('hidden');
                $input.removeClass('border-red-500');
            }
        }

        // Alert in real time (while typing) if a rank's salary field is left empty
        function checkRankSalaryEmpty(input) {
            const $input = $(input);
            const empty = $input.val().toString().trim() === '';

            $input.toggleClass('border-red-500', empty);
            $input.siblings('.rank-salary-error').toggleClass('hidden', !empty);
        }

        $('#ranks').on('input', '.rank-name-input', function() {
            checkRankNameDuplicate(this);
        });

        $('#ranks').on('input', '.rank-salary-input', function() {
            checkRankSalaryEmpty(this);
        });

        // Deleting only stages the removal locally; nothing is persisted until Mentés is pressed
        $('#ranks').on('click', '.rank-delete-btn', function() {
            const button = this;
            const rankName = button.dataset.rankName;

            function removeRow() {
                $(button).closest('tr.rank-row').remove();

                if (window.ranksTable) {
                    window.ranksTable.rows().invalidate();
                }

                renumberRankRows();
                updateRankMoveButtons();
                checkSettingsFormChanged();
            }

            // Newly added (not yet saved) ranks can be removed without confirmation
            if (!rankName) {
                removeRow();
                return;
            }

            Swal.fire({
                title: 'Rang törlése',
                text: 'Biztos törölni akarod a(z) ' + rankName + ' rangot? A törlés csak a Mentés gomb megnyomásakor lép érvénybe.',
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

        // Adding only stages a new row locally; nothing is persisted until Mentés is pressed
        $('#add-rank-button').on('click', function() {
            const nameInput = $('#new_rank_name_draft');
            const salaryInput = $('#new_rank_salary_draft');
            const minWeeksInput = $('#new_rank_min_weeks_draft');
            const examInput = $('#new_rank_exam_draft');
            const leaderInput = $('#new_rank_leader_draft');
            const nameError = $('#new-rank-name-error');

            const name = nameInput.val().trim();
            const salary = salaryInput.val().trim();
            const isLeader = leaderInput.is(':checked');
            const minWeeks = isLeader ? 0 : (minWeeksInput.val() ? minWeeksInput.val().trim() : '2');
            const requiresExam = !isLeader && examInput.is(':checked');

            nameError.addClass('hidden');
            nameInput.removeClass('border-red-500');

            if (!name || salary === '') {
                Swal.fire({
                    text: 'A rang nevét és fizetését is meg kell adni.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                });
                return;
            }

            const nameExists = $('#ranks tbody tr.rank-row .rank-name-input').toArray().some(function(input) {
                return $(input).val().trim().toLowerCase() === name.toLowerCase();
            });

            if (nameExists) {
                nameError.removeClass('hidden');
                nameInput.addClass('border-red-500');
                return;
            }

            const index = newRankCounter++;
            const nextOrder = $('#ranks tbody tr.rank-row').length + 1;

            const newRow = $('<tr class="rank-row"></tr>');
            newRow.append(
                '<td><span class="rank-order-display">' + nextOrder +
                '</span><input type="hidden" class="rank-order-input" name="new_ranks[' + index +
                '][order]" value="' + nextOrder + '"></td>'
            );
            newRow.append(
                '<td><input type="text" class="rank-name-input text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm rounded border-gray-300 dark:bg-gray-900 dark:text-white" name="new_ranks[' +
                index + '][name]" maxlength="255"><p class="rank-name-error text-red-600 dark:text-red-400 text-sm mt-2 hidden"></p></td>'
            );
            newRow.append(
                '<td><input type="number" class="rank-salary-input text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm rounded border-gray-300 dark:bg-gray-900 dark:text-white" name="new_ranks[' +
                index + '][salary]" min="0" max="1000000"><p class="rank-salary-error text-red-600 dark:text-red-400 text-sm mt-2 hidden">Fizetés nem lehet üres</p></td>'
            );
            newRow.append(
                '<td><input type="number" class="rank-min-weeks-input text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm rounded border-gray-300 dark:bg-gray-900 dark:text-white' + (isLeader ? ' bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : '') + '" name="new_ranks[' +
                index + '][minimum_successful_weeks]" value="' + (isLeader ? 0 : minWeeks) + '" min="0" max="100"' + (isLeader ? ' readonly' : '') + '></td>'
            );
            newRow.append(
                '<td class="text-center"><input type="hidden" name="new_ranks[' + index +
                '][requires_exam]" value="0"><input type="checkbox" name="new_ranks[' + index +
                '][requires_exam]" value="1" class="rank-exam-input rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"' +
                (!isLeader && nextOrder !== 1 && requiresExam ? ' checked' : '') +
                (isLeader || nextOrder === 1 ? ' disabled' : '') + '></td>'
            );
            newRow.append(
                '<td class="text-center"><input type="hidden" name="new_ranks[' + index +
                '][is_leader]" value="0"><input type="checkbox" name="new_ranks[' + index +
                '][is_leader]" value="1" class="rank-leader-input rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"' +
                (isLeader ? ' checked' : '') + '></td>'
            );
            newRow.append(
                '<td><button type="button" class="rank-delete-btn w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white text-lg font-bold leading-none" title="Rang törlése" aria-label="Rang törlése">&minus;</button></td>'
            );
            newRow.append(
                '<td><button type="button" class="rank-move-up inline-flex items-center px-4 py-2 bg-gray-50 dark:bg-gray-800 dark:text-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">&uarr;</button></td>'
            );
            newRow.append(
                '<td><button type="button" class="rank-move-down inline-flex items-center px-4 py-2 bg-gray-50 dark:bg-gray-800 dark:text-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">&darr;</button></td>'
            );

            newRow.find('.rank-name-input').val(name);
            newRow.find('.rank-salary-input').val(salary);

            newRow.insertBefore('#new-rank-row');

            nameInput.val('');
            salaryInput.val('');
            minWeeksInput.val('2').prop('readonly', false).removeClass('bg-gray-100 dark:bg-gray-700 cursor-not-allowed');
            examInput.prop('checked', false).prop('disabled', false);
            leaderInput.prop('checked', false);

            renumberRankRows();
            updateRankMoveButtons();
            checkSettingsFormChanged();
        });

        $('#new_rank_name_draft').on('input', function() {
            $('#new-rank-name-error').addClass('hidden');
            $(this).removeClass('border-red-500');
        });

        updateRankMoveButtons();
        updateRankExamAndLeaderStates();
    });
</script>
