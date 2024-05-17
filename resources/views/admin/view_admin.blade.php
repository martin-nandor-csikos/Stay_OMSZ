<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin panel') }}
        </h2>
    </x-slot>

    @session('successful-user-deletion')
        <div class="alert alert-success" role="alert">
            {{ session('successful-user-deletion') }}
        </div>
    @endsession

    @session('unsuccessful-user-deletion')
        <div class="alert alert-danger" role="alert">
            {{ session('unsuccessful-user-deletion') }}
        </div>
    @endsession

    @session('user-updated')
        <div class="alert alert-success" role="alert">
            {{ session('user-updated') }}
        </div>
    @endsession

    @session('user-not-updated')
        <div class="alert alert-danger" role="alert">
            {{ session('user-not-updated') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <p class="top5">Heti statisztika</p>
                    <table class="table table-striped table-hover view-reports" id="weekly-stats">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IC név</th>
                                <th scope="col">Jelentések</th>
                                <th scope="col">Utolsó jelentés</th>
                                <th scope="col">Jelentések megtekintése</th>
                                <th scope="col">Szolgálati idő</th>
                                <th scope="col">Utolsó szolgálat leadása</th>
                                <th scope="col">Szolgálatok megtekintése</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($userStats as $userStat)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $userStat->charactername }}</td>
                                <td>{{ $userStat->reportCount }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($userStat->lastReportDate)->format('Y.m.d H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.viewUserReports', $userStat->id) }}" method="get">
                                        <x-primary-button>
                                            {{ __('Jelentések') }}
                                        </x-primary-button>
                                    </form>
                                </td>
                                <td>{{ $userStat->dutyMinuteSum }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($userStat->lastDutyDate)->format('Y.m.d H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.viewUserDuty', $userStat->id) }}" method="get">
                                        <x-primary-button>
                                            {{ __('Szolgálatok') }}
                                        </x-primary-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <p class="top5">Regisztrált felhasználók</p>
                    <table class="table table-striped table-hover view-reports" id="registered-users">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IC név</th>
                                <th scope="col">Felhasználónév</th>
                                <th scope="col">Regisztráció ideje</th>
                                <th scope="col">Admin?</th>
                                <th scope="col">Módosítás</th>
                                <th scope="col">Törlés</th>

                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->charactername }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($user->created_at)->format('Y.m.d H:i') }}</td>
                                <td>
                                    @if ($user->isAdmin == 1)
                                        igen
                                    @endif

                                    @if ($user->isAdmin == 0)
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.editUser', $user->id) }}" method="get">
                                        <x-primary-button>
                                            {{ __('Módosítás') }}
                                        </x-primary-button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('admin.deleteUser', $user->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <x-primary-button onclick="return confirm('Ez egy visszafordíthatatlan esemény. Biztos törölni akarod?')">
                                            {{ __('Törlés') }}
                                        </x-primary-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        var table = new DataTable('#registered-users', {
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });

        var table1 = new DataTable('#weekly-stats', {
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });
   </script>
</x-app-layout>