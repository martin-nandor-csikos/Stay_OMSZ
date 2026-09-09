<div class="py-12" id="plusz-hibapontok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Plusz- és hibapontok</p>
                <form method="POST" action="{{ route('admin.updateUserPoints') }}" id="points-form">
                    @csrf
                    <table class="display view-reports" id="user-points">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IC név</th>
                                <th scope="col">Rank</th>
                                <th scope="col">Pluszpontok</th>
                                <th scope="col">Hibapontok</th>
                                <th scope="col">Utolsó pluszpont</th>
                                <th scope="col">Utolsó hibapont</th>
                                <th scope="col">Megtekintés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pointUsers as $pointUser)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $pointUser->charactername }}</td>
                                    <td>{{ $pointUser->rank_name ?? '-' }}</td>
                                    <td class="point-value-cell" data-order="{{ $pointUser->plus_points }}">
                                        <input type="number" name="users[{{ $pointUser->id }}][plus_points]"
                                            value="{{ $pointUser->plus_points }}" min="0"
                                            data-original-value="{{ $pointUser->plus_points }}"
                                            data-user-id="{{ $pointUser->id }}"
                                            data-user-name="{{ $pointUser->charactername }}"
                                            data-point-field="plus_points"
                                            data-point-label="pluszpont"
                                            class="point-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                    </td>
                                    <td class="point-value-cell" data-order="{{ $pointUser->penalty_points }}">
                                        <input type="number" name="users[{{ $pointUser->id }}][penalty_points]"
                                            value="{{ $pointUser->penalty_points }}" min="0"
                                            data-original-value="{{ $pointUser->penalty_points }}"
                                            data-user-id="{{ $pointUser->id }}"
                                            data-user-name="{{ $pointUser->charactername }}"
                                            data-point-field="penalty_points"
                                            data-point-label="hibapont"
                                            class="point-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                    </td>
                                    <td>{{ $pointUser->plus_points > 0 && $pointUser->last_plus_point_at ? \Illuminate\Support\Carbon::parse($pointUser->last_plus_point_at)->format('Y.m.d H:i') : '-' }}</td>
                                    <td>{{ $pointUser->penalty_points > 0 && $pointUser->last_penalty_point_at ? \Illuminate\Support\Carbon::parse($pointUser->last_penalty_point_at)->format('Y.m.d H:i') : '-' }}</td>
                                    <td>
                                        @if ($pointUser->plus_points > 0 || $pointUser->penalty_points > 0)
                                            <a href="{{ route('admin.viewUserPointHistories', $pointUser->id) }}"
                                                target="_blank_points_{{ $loop->iteration }}"
                                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 dark:text-gray-800 border border-transparent rounded-md font-semibold text-xs text-gray-50 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-300 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('Megtekintés') }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (Auth::user()->adminLevel >= 1)
                        <div class="flex justify-end mt-4">
                            <x-primary-button id="points-save-button"
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
        const form = $('#points-form');
        const original = form.serialize();
        const reasonInputClass = 'point-change-reason-input';

        function updateSaveButton() {
            $('#points-save-button').prop('disabled', form.serialize() === original);
        }

        function getPointChanges() {
            return form.find('.point-input').map(function() {
                const input = $(this);
                const oldValue = parseInt(input.data('original-value'), 10);
                const newValue = parseInt(input.val() || 0, 10);

                if (oldValue === newValue) {
                    return null;
                }

                return {
                    userId: input.data('user-id'),
                    userName: input.data('user-name'),
                    field: input.data('point-field'),
                    label: input.data('point-label'),
                    oldValue,
                    newValue,
                };
            }).get();
        }

        function buildReasonPrompt(changes) {
            const wrapper = $('<div class="space-y-3 text-left"></div>');

            changes.forEach((change, index) => {
                const row = $('<div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_minmax(220px,1fr)] sm:items-center"></div>');
                const label = $('<label class="text-sm font-medium text-gray-700"></label>')
                    .attr('for', `point-change-reason-${index}`)
                    .text(`${change.userName} ${change.label} (${change.oldValue} --> ${change.newValue})`);
                const input = $('<input type="text" class="swal2-input !m-0 !w-full" placeholder="Indoklás">')
                    .attr('id', `point-change-reason-${index}`)
                    .attr('data-change-index', index);

                row.append(label, input);
                wrapper.append(row);
            });

            return wrapper[0];
        }

        form.on('input change', '.point-input', function() {
            const input = $(this);
            input.closest('.point-value-cell').attr('data-order', input.val() || 0);

            if (window.userPointsTable) {
                window.userPointsTable.rows().invalidate();
            }

            updateSaveButton();
        });

        form.on('submit', function(event) {
            const changes = getPointChanges();

            if (changes.length === 0) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: 'Indokold meg a pluszpontokat és/vagy hibapontokat.',
                html: buildReasonPrompt(changes),
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Mentés',
                cancelButtonText: 'Mégse',
                preConfirm: () => {
                    const reasons = {};
                    let missingReason = false;

                    $('.swal2-popup input[data-change-index]').each(function() {
                        const reasonInput = $(this);
                        const index = reasonInput.data('change-index');
                        const reason = reasonInput.val().trim();

                        if (!reason) {
                            missingReason = true;
                        }

                        reasons[index] = reason;
                    });

                    if (missingReason) {
                        Swal.showValidationMessage('Minden módosítást indokolni kell.');
                        return false;
                    }

                    return reasons;
                },
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                form.find(`.${reasonInputClass}`).remove();

                changes.forEach((change, index) => {
                    $('<input type="hidden">')
                        .addClass(reasonInputClass)
                        .attr('name', `users[${change.userId}][${change.field}_reason]`)
                        .val(result.value[index])
                        .appendTo(form);
                });

                form[0].submit();
            });
        });
    });
</script>
