<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            @if (request()->routeIs('admin.viewClosedUserDuty'))
                {{ __($charactername . ' lezárt szolgálatai') }}
            @else
                {{ __($charactername . ' szolgálatai') }}
            @endif
        </h2>
    </x-slot>

    @session('successful-user-duty-deletion')
        <div class="alert alert-success" role="alert">
            {{ session('successful-user-duty-deletion') }}
        </div>
    @endsession

    @session('unsuccessful-user-duty-deletion')
        <div class="alert alert-danger" role="alert">
            {{ session('unsuccessful-user-duty-deletion') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                    <table class="display view-reports" id="userDuties">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Felvétel</th>
                                <th scope="col">Leadás</th>
                                <th scope="col">Idő</th>
                                @if (!request()->routeIs('admin.viewClosedUserDuty'))
                                    <th scope="col">Törlés</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($dutyTimes as $dutyTime)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->begin)->format('Y.m.d H:i') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->end)->format('Y.m.d H:i') }}</td>
                                <td>{{ $dutyTime->minutes }} perc</td>
                                @if (!request()->routeIs('admin.viewClosedUserDuty'))
                                    <td>
                                        <form action="{{ route('admin.deleteDutyTime', $dutyTime->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <x-primary-button onclick="return confirm('Ez egy visszafordíthatatlan esemény. Biztos törölni akarod?')">
                                                {{ __('Törlés') }}
                                            </x-primary-button>
                                        </form>
                                    </td>
                                @endif
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
        var userDutiesTable = new DataTable('#userDuties', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
            responsive: true,
        });
    </script>
</x-app-layout>
