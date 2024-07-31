<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Főoldal') }}
        </h2>
    </x-slot>

    <div id="dashboard-table">@include('view_dashboard')</div>

    <script src="js/dashboard_ajax.js"></script>
</x-app-layout>