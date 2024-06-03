<div class="py-12" id="inactivities-block">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 view-reports-padding">
                <p class="top5">Inaktivitások</p>
                <table class="table table-striped table-hover view-reports" id="inactivities">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Ettől</th>
                            <th scope="col">Eddig</th>
                            <th scope="col">Indok</th>
                            <th scope="col">Elfogadva?</th>
                            <th scope="col">Folyamatban?</th>
                            <th scope="col">Elfogadás</th>
                            <th scope="col">Törlés</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($inactivities as $inactivity)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $inactivity->id }}</td>
                            <td>{{ $inactivity->charactername }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($inactivity->begin)->format('Y.m.d') }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($inactivity->end)->format('Y.m.d') }}</td>
                            <td>{{ $inactivity->reason }}</td>
                            @if ($inactivity->accepted == 1)
                                <td>I</td>
                            @else
                                <td>N</td>
                            @endif

                            @if (\Illuminate\Support\Carbon::now()->between($inactivity->begin, $inactivity->end) && $inactivity->accepted == 1)
                                <td>I</td>
                            @else
                                <td>N</td>
                            @endif
                            <td>
                                <form action="{{ route('admin.updateInactivity', $inactivity->id) }}" method="POST">
                                    @csrf
                                    @if ($inactivity->accepted == 0)
                                        <x-primary-button>
                                            {{ __('Elfogadás') }}
                                        </x-primary-button>
                                    @else
                                        <x-primary-button>
                                            {{ __('Visszavonás') }}
                                        </x-primary-button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.destroyInactivity', $inactivity->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <x-primary-button onclick="return confirm('Ez egy visszafordíthatatlan esemény. Biztos törölni akarod?')">
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