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

    @include('admin.sessions')

    @include('admin.header_navigation')
    @include('admin.weekly_stats.weekly_stats_table')
    @include('admin.weekly_stats.closed_week_stats_table')
    @include('admin.inactivities.inactivities_table')
    @include('admin.users.registered_users_table')
    @include('admin.promotions.promotions_table')
    @include('admin.settings.update_settings')
    @include('admin.admin_logs.admin_logs_table')

    {{-- <script src="js/admin.js"></script> --}}

    {{-- <script src="js/admin_ajax.js"></script> --}}

    <script>
        $(function() {
            var registeredUsers = new DataTable('#registered-users', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'regisztralt_felhasznalok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'regisztralt_felhasznalok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });

            var weeklyStats = new DataTable('#weekly-stats', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'heti_statisztika_' +
                                    '{{ $firstDayOfWeek }}_{{ $lastDayOfWeek }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6, 7, 9]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'heti_statisztika_' +
                                    '{{ $firstDayOfWeek }}_{{ $lastDayOfWeek }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6, 7, 9]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });

            var closedWeeklyStats = new DataTable('#closed-weekly-stats', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'heti_statisztika_' +
                                    '{{ $firstDayOfPreviousWeek }}_{{ $lastDayOfPreviousWeek }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6, 7, 9]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'heti_statisztika_' +
                                    '{{ $firstDayOfPreviousWeek }}_{{ $lastDayOfPreviousWeek }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6, 7, 9]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });

            var adminLogs = new DataTable('#admin-logs', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'admin_logok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'admin_logok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });

            var inactivities = new DataTable('#inactivities', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'excel',
                                filename: 'inaktivitasok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                }
                            },
                            {
                                extend: 'csv',
                                filename: 'inaktivitasok_' + '{{ $currentDay }}',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                }
                            }
                        ]
                    }
                },
                scrollX: true,
            });

            window.ranksTable = new DataTable('#ranks', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                ordering: false,
                paging: false,
                scrollX: true,
            });

            window.promotionsTable = new DataTable('#promotions', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/hu.json',
                },
                ordering: false,
                paging: true,
                scrollX: true,
            });
        });
    </script>
</x-app-layout>
