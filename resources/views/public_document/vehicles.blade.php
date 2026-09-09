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
                    <p class="top5">Járművek</p>

                    <table class="display view-reports w-full" id="public-document-vehicles">
                        <thead>
                            <tr>
                                <th scope="col">Jármű ID</th>
                                <th scope="col">Rendszám</th>
                                <th scope="col">Típus</th>
                                <th scope="col">Ápoló</th>
                                <th scope="col">II. Ápoló</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->vehicle_identifier }}</td>
                                    <td>{{ $vehicle->plate_number }}</td>
                                    <td>{{ $vehicle->type }}</td>
                                    <td>{{ $vehicle->caregiver_name }}</td>
                                    <td>{{ $vehicle->secondary_caregiver_name }}</td>
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
            new DataTable('#public-document-vehicles', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                scrollX: true,
                paging: false,
                fixedHeader: true,
            });
        });
    </script>
</x-public-layout>
