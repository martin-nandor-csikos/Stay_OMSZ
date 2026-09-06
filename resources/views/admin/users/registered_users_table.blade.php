@vite('resources/js/swalConfirmDecision.js')

<script>
    function confirmUserDeletion(event) {
        event.preventDefault();
        const form = event.currentTarget.form;

        Swal.fire({
            title: 'Felhasználó törlése',
            input: 'textarea',
            inputLabel: 'Indokold meg a felhasználó törlését:',
            inputPlaceholder: 'Indok...',
            inputAttributes: {
                'aria-label': 'Indokold meg a felhasználó törlését',
            },
            showCancelButton: true,
            confirmButtonText: 'Törlés',
            cancelButtonText: 'Mégse',
            confirmButtonColor: '#dc2626',
            html: '<div style="display:flex; flex-direction:column; align-items:center;"><label for="blacklist" class="text-md">Feketelista:</label><select id="blacklist" class="swal2-select" style="font-size:16px; padding:5px 10px; width:180px;" aria-label="Feketelista"><option value="-">-</option><option value="Aktív">Aktív</option><option value="Erősített">Erősített</option></select></div>',
            preConfirm: (reason) => {
                const blacklist = document.getElementById('blacklist').value;

                if (!reason || !reason.trim() || !blacklist) {
                    Swal.showValidationMessage('A törlés indoklása és a feketelista állapota kötelező.');
                    return false;
                }

                return {
                    reason: reason.trim(),
                    blacklist: blacklist,
                };
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = result.value.reason;
                form.appendChild(reasonInput);

                const blacklistInput = document.createElement('input');
                blacklistInput.type = 'hidden';
                blacklistInput.name = 'blacklist';
                blacklistInput.value = result.value.blacklist;
                form.appendChild(blacklistInput);
                form.submit();
            }
        });
    }
</script>

<div class="py-12" id="regisztralt-felhasznalok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Regisztrált felhasználók</p>
                <form action="{{ route('admin.userRegistrationPage') }}" method="get" class="admin-button">
                    <x-primary-button>
                        {{ __('Új felhasználó regisztrálása') }}
                    </x-primary-button>
                </form>
                <table class="display view-reports" id="registered-users">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Account ID</th>
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
                                <td>{{ $user->account_id }}</td>
                                <td>{{ $user->charactername }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($user->created_at)->format('Y.m.d H:i') }}</td>
                                <td>
                                    @if ($user->adminLevel >= 1)
                                        igen
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if (!($user->adminLevel == 2 && Auth::user()->adminLevel < 2))
                                        <form action="{{ route('admin.editUser', $user->id) }}" method="get">
                                            <x-primary-button>
                                                {{ __('Módosítás') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if (!($user->adminLevel == 2 && Auth::user()->adminLevel < 2))
                                        @if (Auth::user()->id != $user->id)
                                            <form action="{{ route('admin.deleteUser', $user->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <x-primary-button onclick="confirmUserDeletion(event);">
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
