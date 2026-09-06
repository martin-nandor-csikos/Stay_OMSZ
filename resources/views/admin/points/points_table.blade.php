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
                                            class="point-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                    </td>
                                    <td class="point-value-cell" data-order="{{ $pointUser->penalty_points }}">
                                        <input type="number" name="users[{{ $pointUser->id }}][penalty_points]"
                                            value="{{ $pointUser->penalty_points }}" min="0"
                                            class="point-input rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                    </td>
                                    <td>{{ $pointUser->plus_points > 0 && $pointUser->last_plus_point_at ? \Illuminate\Support\Carbon::parse($pointUser->last_plus_point_at)->format('Y.m.d H:i') : '-' }}</td>
                                    <td>{{ $pointUser->penalty_points > 0 && $pointUser->last_penalty_point_at ? \Illuminate\Support\Carbon::parse($pointUser->last_penalty_point_at)->format('Y.m.d H:i') : '-' }}</td>
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

        function updateSaveButton() {
            $('#points-save-button').prop('disabled', form.serialize() === original);
        }

        form.on('input change', '.point-input', function() {
            const input = $(this);
            input.closest('.point-value-cell').attr('data-order', input.val() || 0);

            if (window.userPointsTable) {
                window.userPointsTable.rows().invalidate();
            }

            updateSaveButton();
        });
    });
</script>
