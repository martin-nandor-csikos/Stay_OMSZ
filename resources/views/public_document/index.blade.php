<x-public-layout>
    <style>
        table.fixedHeader-floating thead th,
        .dtfh-floatingparent table thead th {
            background-color: rgb(249 250 251) !important;
            color: rgb(17 24 39) !important;
        }

        .dark table.fixedHeader-floating thead th,
        .dark .dtfh-floatingparent table thead th {
            background-color: rgb(31 41 55) !important;
            color: rgb(243 244 246) !important;
        }
    </style>

    <div class="py-12">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 96rem;">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                    <p class="top5">Publikus dokumentum</p>

                    <table class="display view-reports w-full" id="public-document-users">
                        <thead>
                            <tr>
                                <th scope="col">Account ID</th>
                                <th scope="col">Név</th>
                                <th scope="col">Rang</th>
                                <th scope="col">Alosztály</th>
                                <th scope="col">Belépés ideje</th>
                                <th scope="col">Utolsó ranglépés</th>
                                <th scope="col">Rangon eltöltött napok</th>
                                <th scope="col">Frakcióban eltöltött napok</th>
                                <th scope="col">Pluszpontok</th>
                                <th scope="col">Hibapontok</th>
                                <th scope="col">Státusz</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->account_id }}</td>
                                    <td>{{ $user->charactername }}</td>
                                    <td data-order="{{ $user->rank_order ?? 0 }}">{{ $user->rank_name }}</td>
                                    <td>{{ $user->department }}</td>
                                    <td>{{ $user->created_at_display }}</td>
                                    <td>{{ $user->last_rank_change_at_display }}</td>
                                    <td data-order="{{ $user->days_at_rank }}">{{ $user->days_at_rank }} nap</td>
                                    <td data-order="{{ $user->days_in_faction }}">{{ $user->days_in_faction }} nap</td>
                                    <td data-order="{{ $user->plus_points }}">{{ $user->plus_points > 0 ? $user->plus_points : '-' }}</td>
                                    <td data-order="{{ $user->penalty_points }}">{{ $user->penalty_points > 0 ? $user->penalty_points : '-' }}</td>
                                    <td>{{ $user->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            new DataTable('#public-document-users', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                order: [
                    [2, 'desc'],
                    [1, 'asc'],
                ],
                scrollX: true,
                paging: false,
	            fixedHeader: true,
            });
        });
    </script>
</x-public-layout>
