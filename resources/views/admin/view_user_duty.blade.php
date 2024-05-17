<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($charactername . ' szolgálatai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <table class="table table-striped table-hover view-reports">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Felvétel</th>
                                <th scope="col">Leadás</th>
                                <th scope="col">Idő</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($dutyTimes as $dutyTime)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->begin)->format('Y.m.d H:i') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($dutyTime->end)->format('Y.m.d H:i') }}</td>
                                <td>{{ $dutyTime->minutes }} perc</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ url()->previous() }}">
                            <x-primary-button>
                                {{ __('Vissza') }}
                            </x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
