<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Szolgálatok') }}
        </h2>
    </x-slot>

    @session('successful-deletion')
        <div class="alert alert-success" role="alert">
            {{ session('successful-deletion') }}
        </div>
    @endsession

    @session('unsuccessful-deletion')
        <div class="alert alert-danger" role="alert">
            {{ session('unsuccessful-deletion') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 d-flex justify-content-center new-report">
                            <a href="{{ route('duty_time.create') }}">
                                <x-primary-button>
                                        {{ __('Új szolgálat felvétele') }}
                                </x-primary-button>
                            </a>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <table class="table table-striped table-hover view-reports">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Felvétel</th>
                                <th scope="col">Leadás</th>
                                <th scope="col">Idő</th>
                                <th scope="col">Törlés</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($dutyTimes as $dutyTime)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->begin)->format('Y.m.d H:i') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->end)->format('Y.m.d H:i') }}</td>
                                <td>{{ $dutyTime->minutes }} perc</td>
                                <td>
                                    <form action="{{ route('duty_time.delete', $dutyTime->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <x-primary-button>
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
</x-app-layout>