<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Admin panel') }}
        </h2>
    </x-slot>

    {{-- <script>
        window.routes = {
            weeklyStats: "{{ route('admin.weeklyStats') }}",
            closedWeekStats: "{{ route('admin.closedWeekStats') }}",
            inactivities: "{{ route('admin.inactivities') }}",
            registratedUsers: "{{ route('admin.registratedUsers') }}",
            adminLogs: "{{ route('admin.adminLogs') }}",
        };
    </script> --}}

    @include('admin.partials.sessions')

    @include('admin.partials.header_navigation')
    @include('admin.partials.view_weekly_stats')
    @include('admin.partials.view_closed_week_stats')
    @include('admin.partials.view_inactivities')
    @include('admin.partials.view_registrated_users')
    @include('admin.partials.view_admin_logs')

    {{-- <script src="js/admin_ajax.js"></script> --}}

    <script>
        $(function() {
            var table = new DataTable('#registered-users', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
            });

            var table1 = new DataTable('#weekly-stats', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
            });

            var table2 = new DataTable('#closed-weekly-stats', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
            });

            var table3 = new DataTable('#admin-logs', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
            });

            var table4 = new DataTable('#inactivities', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                responsive: true,
            });
        });
   </script>
</x-app-layout>