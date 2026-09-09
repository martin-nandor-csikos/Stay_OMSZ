<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __($charactername . ' plusz- és hibapontjai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                    <p class="top5 text-lg">Pluszpontok</p>
                    <table class="display view-reports" id="plusPointHistories">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Változás (régi --> új)</th>
                                <th scope="col">Dátum</th>
                                <th scope="col">Indok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plusPointHistories as $pointHistory)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $pointHistory->old_value }} --> {{ $pointHistory->new_value }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($pointHistory->changed_at)->format('Y.m.d H:i') }}</td>
                                    <td>{{ $pointHistory->reason }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p class="top5 text-lg mt-5">Hibapontok</p>
                    <table class="display view-reports" id="penaltyPointHistories">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Változás (régi --> új)</th>
                                <th scope="col">Dátum</th>
                                <th scope="col">Indok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penaltyPointHistories as $pointHistory)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $pointHistory->old_value }} --> {{ $pointHistory->new_value }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($pointHistory->changed_at)->format('Y.m.d H:i') }}</td>
                                    <td>{{ $pointHistory->reason }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.index') }}">
                            <x-primary-button>
                                {{ __('Vissza') }}
                            </x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        ['#plusPointHistories', '#penaltyPointHistories'].forEach(function(tableId) {
            new DataTable(tableId, {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'pont_valtozasok_{{ $charactername }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'pont_valtozasok_{{ $charactername }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });
        });
    </script>
</x-app-layout>
