<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin panel') }}
        </h2>
    </x-slot>

    @include('admin.partials.sessions')

    @include('admin.partials.header_navigation')
    @include('admin.partials.view_weekly_stats')
    @include('admin.partials.view_closed_week_stats')
    @include('admin.partials.view_inactivities')
    @include('admin.partials.view_registrated_users')
    @include('admin.partials.view_admin_logs')

    <script>
        var table = new DataTable('#registered-users', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });

        var table1 = new DataTable('#weekly-stats', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });

        var table2 = new DataTable('#closed-weekly-stats', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });

        var table3 = new DataTable('#admin-logs', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });

        var table4 = new DataTable('#inactivities', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
            },
        });
   </script>
</x-app-layout>