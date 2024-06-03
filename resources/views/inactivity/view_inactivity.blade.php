<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inaktivitási kérelmek') }}
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

    @session('successful-creation')
        <div class="alert alert-success" role="alert">
            {{ session('successful-creation') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 view-reports-padding">
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 d-flex justify-content-center new-report">
                            <a href="{{ route('inactivity.create') }}">
                                <x-primary-button>
                                        {{ __('Új inaktivitási kérelem küldése') }}
                                </x-primary-button>
                            </a>
                        </div>
                        <div class="col-md-4"></div>
                    </div>

                    <table class="table table-striped table-hover view-reports">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Ettől</th>
                                <th scope="col">Eddig</th>
                                <th scope="col">Indok</th>
                                <th scope="col">Elfogadva?</th>
                                <th scope="col">Törlés</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($inactivities as $inactivity)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ \Illuminate\Support\Carbon::parse($inactivity->begin)->format('Y.m.d') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($inactivity->end)->format('Y.m.d') }}</td>
                                <td>{{ $inactivity->reason }}</td>
                                @if ($inactivity->accepted == 1)
                                    <td><input type="checkbox" name="accepted" checked disabled class="rounded text-indigo-600 shadow-sm focus:ring-indigo-500 form-check-input"></td>
                                @else
                                    <td><input type="checkbox" name="accepted" disabled class="rounded text-indigo-600 shadow-sm focus:ring-indigo-500 form-check-input"></td>
                                @endif
                                <td>
                                    <form action="{{ route('inactivity.delete', $inactivity->id) }}" method="post">
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