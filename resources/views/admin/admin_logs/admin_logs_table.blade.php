<div class="py-12" id="admin-logok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Admin logok</p>
                <table class="display view-reports" id="admin-logs">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Mit csinált</th>
                            <th scope="col">Mikor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admin_logs as $admin_log)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $admin_log->charactername }}</td>
                                <td>
                                    @if (str_contains($admin_log->didWhat, '<a href='))
                                        {!! $admin_log->didWhat !!}
                                    @else
                                        {{ $admin_log->didWhat }}
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Carbon::parse($admin_log->created_at)->format('Y.m.d H:i') }}
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
    $(function() {
        const adminLogsUrl = "{{ route('admin.getAdminLogs') }}";

        function refreshAdminLogs() {
            $.get(adminLogsUrl, function(adminLogs) {
                if (!window.adminLogsTable) {
                    return;
                }

                window.adminLogsTable.clear();
                window.adminLogsTable.rows.add(adminLogs.map(function(adminLog) {
                    return [
                        adminLog.row_number,
                        adminLog.charactername,
                        adminLog.didWhat,
                        adminLog.created_at,
                    ];
                }));
                window.adminLogsTable.draw(false);
            });
        }

        setInterval(refreshAdminLogs, 5000);
    });
</script>
