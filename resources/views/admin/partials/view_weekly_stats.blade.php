<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 view-reports-padding">
                <p class="top5">Heti statisztika</p>
                <form action="{{ route('admin.closeWeek') }}" method="post">
                    @csrf
                    <x-primary-button onclick="return confirm('Ez egy visszafordíthatatlan esemény. Biztosan le akarod zárni a hetet?')" style="margin-bottom: 20px">
                        {{ __('Hét lezárása') }}
                    </x-primary-button>
                </form>
                <table class="table table-striped table-hover view-reports" id="weekly-stats">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Jelentések</th>
                            <th scope="col">Utolsó jelentés</th>
                            <th scope="col">Jelentések megtekintése</th>
                            <th scope="col">Szolgálati idő (perc)</th>
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