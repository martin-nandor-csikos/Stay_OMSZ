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

    @session('user-created')
        <div class="alert alert-success" role="alert">
            {{ session('user-created') }}
        </div>
    @endsession

    @session('user-not-created')
        <div class="alert alert-danger" role="alert">
            {{ session('user-not-created') }}
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

    @session('close-success')
        <div class="alert alert-success" role="alert">
            {{ session('close-success') }}
        </div>
    @endsession

    @include('admin.partials.header_navigation')
    @include('admin.partials.view_weekly_stats')
    @include('admin.partials.view_closed_week_stats')
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
   </script>
</x-app-layout>