<div class="py-12" id="registrated-users-block">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 view-reports-padding">
                <p class="top5">Regisztrált felhasználók</p>
                <form action="{{ route('admin.userRegistrationPage') }}" method="get" style="margin-bottom: 20px">
                    <x-primary-button>
                        {{ __('Új felhasználó regisztrálása') }}
                    </x-primary-button>
                </form>
                <table class="table table-striped table-hover view-reports" id="registered-users">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Felhasználónév</th>
                            <th scope="col">Regisztráció ideje</th>
                            <th scope="col">Admin?</th>
                            <th scope="col">Módosítás</th>
                            <th scope="col">Törlés</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->charactername }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($user->created_at)->format('Y.m.d H:i') }}</td>
                            <td>
                                @if ($user->isAdmin == 1)
                                    igen
                                @endif

                                @if ($user->isAdmin == 0)
                                    -
                                @endif
                            </td>
                            <td>
                                @if (($user->canGiveAdmin == 1 && Auth::user()->canGiveAdmin == 1) || ($user->canGiveAdmin == 0 && Auth::user()->canGiveAdmin == 0) || ($user->canGiveAdmin == 0 && Auth::user()->canGiveAdmin == 1))
                                    <form action="{{ route('admin.editUser', $user->id) }}" method="get">
                                        <x-primary-button>
                                            {{ __('Módosítás') }}
                                        </x-primary-button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if (($user->canGiveAdmin == 1 && Auth::user()->canGiveAdmin == 1) || ($user->canGiveAdmin == 0 && Auth::user()->canGiveAdmin == 0) || ($user->canGiveAdmin == 0 && Auth::user()->canGiveAdmin == 1))
                                    @if (Auth::user()->id != $user->id)
                                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <x-primary-button onclick="return confirm('Ez egy visszafordíthatatlan esemény. Biztos törölni akarod?')">
                                                {{ __('Törlés') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>