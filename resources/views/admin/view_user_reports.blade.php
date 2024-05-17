<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($charactername . ' jelentései') }}
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
                                <th scope="col">Ár ($)</th>
                                <th scope="col">Diagnózis</th>
                                <th scope="col">Társai</th>
                                <th scope="col">Kép</th>
                                <th scope="col">Felvéve</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $report->price }}</td>
                                <td>{{ $report->diagnosis }}</td>
                                <td>{{ $report->withWho }}</td>
                                <td><a href="{{ $report->img }}" target="blank">{{ $report->img }}</a></td>
                                <td>{{ \Illuminate\Support\Carbon::parse($report->created_at)->format('Y.m.d H:i') }}</td>
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
