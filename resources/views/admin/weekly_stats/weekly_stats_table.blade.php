@vite('resources/js/swalConfirmDecision.js')

<script>
    function confirmWeekClosure(event) {
        swalConfirmDecision(event, "Hét lezárása", "Biztos le akarod zárni a hetet?", "Lezárás", "Mégse");
    }
</script>

<div class="py-12" id="heti-statisztika">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Heti statisztika</p>
                <form action="{{ route('admin.closeWeek') }}" method="post">
                    @csrf
                    <x-primary-button onclick="confirmWeekClosure(event);" class="admin-button">
                        {{ __('Hét lezárása') }}
                    </x-primary-button>
                </form>
                @php
                    $totalReports = $userStats->sum('reportCount');
                    $totalDutyMinutes = $userStats->sum('dutyMinuteSum');
                    $totalSalary = $userStats->sum('salary');
                @endphp
                <table class="display view-reports" id="weekly-stats">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Rank</th>
                            <th scope="col">Jelentések</th>
                            <th scope="col">Szolgálati idő (perc)</th>
                            <th scope="col">Fizetés ($)</th>
                            <th scope="col">Utolsó jelentés</th>
                            <th scope="col">Utolsó szolgálat</th>
                            <th scope="col">Jelentések megtekintése</th>
                            <th scope="col">Szolgálatok megtekintése</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userStats as $userStat)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $userStat->charactername }}</td>
                                <td>{{ $userStat->rank_name ?? '-' }}</td>
                                <td>{{ $userStat->reportCount }}</td>
                                <td>{{ $userStat->dutyMinuteSum }}</td>
                                <td title="{{ $userStat->salary_tooltip ?? '' }}">${{ number_format($userStat->salary, 0, '.', ' ') }}</td>
                                @if ($userStat->lastReportDate != '-')
                                    <td>{{ \Illuminate\Support\Carbon::parse($userStat->lastReportDate)->format('Y.m.d H:i') }}
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($userStat->lastDutyDate != '-')
                                    <td>{{ \Illuminate\Support\Carbon::parse($userStat->lastDutyDate)->format('Y.m.d H:i') }}
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($userStat->lastReportDate != '-')
                                    <td>
                                        <form action="{{ route('admin.viewUserReports', $userStat->id) }}"
                                            method="get" target="_blank_{{ $loop->iteration }}">
                                            <x-primary-button>
                                                {{ __('Jelentések') }}
                                            </x-primary-button>
                                        </form>
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($userStat->lastDutyDate != '-')
                                    <td>
                                        <form action="{{ route('admin.viewUserDuty', $userStat->id) }}" method="get"
                                            target="_blank_{{ $loop->iteration }}">
                                            <x-primary-button>
                                                {{ __('Szolgálatok') }}
                                            </x-primary-button>
                                        </form>
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 text-gray-900 dark:text-gray-100">
                    <p>Összes jelentés: <b>{{ $totalReports }}</b></p>
                    <p>Összes szolgálati idő: <b>{{ $totalDutyMinutes }} perc</b></p>
                    <p>Kifizetés összesen: <b>${{ number_format($totalSalary, 0, '.', ' ') }}</b></p>
                </div>
            </div>
        </div>
    </div>
</div>
