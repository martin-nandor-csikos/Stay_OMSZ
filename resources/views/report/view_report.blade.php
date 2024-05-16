<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jelentéseim') }}
        </h2>
    </x-slot>

    @session('successful-deletion')
        <div class="alert alert-success" role="alert">
            {{ session('successful-deletion') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 d-flex justify-content-center new-report">
                            <a href="{{ route('reports.create') }}">
                                <x-primary-button>
                                        {{ __('Új jelentés felvétele') }}
                                </x-primary-button>
                            </a>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <table class="table table-striped table-hover view-reports">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Ár ($)</th>
                                <th scope="col">Diagnózis</th>
                                <th scope="col">Társaid</th>
                                <th scope="col">Kép</th>
                                <th scope="col">Felvéve</th>
                                <th scope="col">Törlés</th>
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
                                <td>
                                    <form action="{{ route('reports.delete', $report->id) }}" method="post">
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
