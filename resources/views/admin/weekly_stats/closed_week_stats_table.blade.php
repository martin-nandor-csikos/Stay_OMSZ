<div class="py-12" id="elozo-het">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Előző hét (lezárt)</p>
                @php
                    $totalReports = $closedUserStats->sum('reportCount');
                    $totalDutyMinutes = $closedUserStats->sum('dutyMinuteSum');
                    $totalSalary = $closedUserStats->sum('salary');
                @endphp
                <table class="display view-reports" id="closed-weekly-stats">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Rank</th>
                            <th scope="col">Jelentések</th>
                            <th scope="col">Szolgálati idő (perc)</th>
                            <th scope="col">Fizetés ($)</th>
                            <th scope="col">Kifizetve?</th>
                            <th scope="col">Utolsó jelentés</th>
                            <th scope="col">Utolsó szolgálat</th>
                            <th scope="col">Jelentések megtekintése</th>
                            <th scope="col">Szolgálatok megtekintése</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($closedUserStats as $closedUserStat)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $closedUserStat->charactername }}</td>
                                <td>{{ $closedUserStat->rank_name ?? '-' }}</td>
                                <td>{{ $closedUserStat->reportCount }}</td>
                                <td>{{ $closedUserStat->dutyMinuteSum }}</td>
                                <td title="{{ $closedUserStat->salary_tooltip ?? '' }}">${{ number_format($closedUserStat->salary, 0, '.', ' ') }}</td>
                                <td class="text-center">
                                    @if ($closedUserStat->salary > 0)
                                        <input type="checkbox" class="closed-week-paid-status rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            data-user-id="{{ $closedUserStat->id }}" @checked($closedUserStat->is_paid)
                                            @disabled(Auth::user()->adminLevel < 1)>
                                    @else
                                        -
                                    @endif
                                </td>
                                @if ($closedUserStat->lastReportDate != '-')
                                    <td>{{ \Illuminate\Support\Carbon::parse($closedUserStat->lastReportDate)->format('Y.m.d H:i') }}
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($closedUserStat->lastDutyDate != '-')
                                    <td>{{ \Illuminate\Support\Carbon::parse($closedUserStat->lastDutyDate)->format('Y.m.d H:i') }}
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($closedUserStat->lastReportDate != '-')
                                    <td>
                                        <form action="{{ route('admin.viewClosedUserReports', $closedUserStat->id) }}"
                                            method="get" target="_blank_{{ $loop->iteration }}">
                                            <x-primary-button>
                                                {{ __('Jelentések') }}
                                            </x-primary-button>
                                        </form>
                                    </td>
                                @else
                                    <td>-</td>
                                @endif
                                @if ($closedUserStat->lastDutyDate != '-')
                                    <td>
                                        <form action="{{ route('admin.viewClosedUserDuty', $closedUserStat->id) }}"
                                            method="get" target="_blank_{{ $loop->iteration }}">
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

<script>
    $(function() {
        const paidStatusesUrl = "{{ route('admin.getClosedWeekPaidStatuses') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function refreshPaidStatuses() {
            $.get(paidStatusesUrl, function(statuses) {
                $('.closed-week-paid-status').each(function() {
                    const userId = $(this).data('userId');
                    $(this).prop('checked', Boolean(Number(statuses[userId])));
                });
            });
        }

        function updatePaidStatus(checkbox, isPaid) {
            checkbox.prop('disabled', true);

            $.ajax({
                url: "{{ url('/admin/lezart-het-kifizetes') }}/" + checkbox.data('userId'),
                method: 'PUT',
                data: { is_paid: isPaid ? 1 : 0, _token: csrfToken },
            }).fail(function() {
                checkbox.prop('checked', !isPaid);
            }).always(function() {
                checkbox.prop('disabled', false);
            });
        }

        $('.closed-week-paid-status').on('change', function() {
            const checkbox = $(this);
            const isPaid = checkbox.is(':checked');

            if (isPaid) {
                updatePaidStatus(checkbox, true);
                return;
            }

            checkbox.prop('checked', true);

            Swal.fire({
                title: 'Kifizetés visszavonása',
                text: 'Biztosan visszavonod a kifizetett státuszt?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Igen',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Mégse',
            }).then((result) => {
                if (result.isConfirmed) {
                    checkbox.prop('checked', false);
                    updatePaidStatus(checkbox, false);
                }
            });
        });

        setInterval(refreshPaidStatuses, 5000);
    });
</script>
