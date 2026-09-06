<div class="py-12" id="volt-felhasznalok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Volt felhasználók</p>
                <table class="display view-reports" id="deleted-users">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Legmagasabb rank</th>
                            <th scope="col">Alosztály</th>
                            <th scope="col">Regisztráció ideje</th>
                            <th scope="col">Törlés ideje</th>
                            <th scope="col">Indok</th>
                            <th scope="col">Hibapontok</th>
                            <th scope="col">Feketelista</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deletedUsers as $deletedUser)
                            @php
                                $previousNames = $deletedUser->previous_characternames
                                    ? json_decode($deletedUser->previous_characternames, true)
                                    : [];
                            @endphp
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $deletedUser->account_id }}</td>
                                <td>
                                    {{ $deletedUser->charactername }}@if (!empty($previousNames))
                                        ({{ implode(' | ', $previousNames) }})
                                    @endif
                                </td>
                                <td>{{ $deletedUser->highest_rank ?? '-' }}</td>
                                <td>{{ $deletedUser->department ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($deletedUser->registered_at)->format('Y.m.d H:i') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($deletedUser->deleted_at)->format('Y.m.d H:i') }}</td>
                                <td>{{ $deletedUser->reason }}</td>
                                <td>{{ $deletedUser->penalty_points > 0 ? $deletedUser->penalty_points : '-' }}</td>
                                <td>{{ $deletedUser->blacklist }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
