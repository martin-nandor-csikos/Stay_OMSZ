@if ($waitingForAnswerInInactivites == true)
    <script>
        window.addEventListener('load', function() {
            window.queueAdminAlert(function() {
                return Swal.fire({
                    text: 'Új inaktivitási kérelem érkezett! (Válaszra vár)',
                    icon: 'info',
                    confirmButtonText: 'OK',
                });
            });
        });
    </script>
@endif

@vite('resources/js/swalConfirmDecision.js')
<script>
    function confirmDelete(event) {
        swalConfirmDecision(event, "Inaktivitás törlése", "Biztos törölni akarod az inaktivitást?", "Törlés", "Mégse");
    }
</script>

<div class="py-12" id="inaktivitasok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Inaktivitások</p>
                <table class="display view-reports" id="inactivities">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">IC név</th>
                            <th scope="col">Ettől</th>
                            <th scope="col">Eddig</th>
                            <th scope="col">Indok</th>
                            <th scope="col">Státusz</th>
                            <th scope="col">Folyamatban?</th>
                            <th scope="col">Elfogadás</th>
                            <th scope="col">Elutasítás</th>
                            <th scope="col">Törlés</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inactivities as $inactivity)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $inactivity->account_id }}</td>
                                <td>{{ $inactivity->charactername }}</td>
                                <td>{{ $inactivity->begin }}</td>
                                <td>{{ $inactivity->end }}</td>
                                <td>{{ $inactivity->reason }}</td>
                                <td>{{ $inactivity->status }}</td>

                                @if ($inactivity->inProgress)
                                    <td>Igen</td>
                                @else
                                    <td>Nem</td>
                                @endif
                                <td>
                                    @if ($inactivity->status == \App\Enums\InactivityStatus::Accepted->value)
                                        -
                                    @else
                                        <form action="{{ route('admin.acceptInactivity', $inactivity->id) }}"
                                            method="POST">
                                            @csrf
                                            <x-primary-button>
                                                {{ __('Elfogadás') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if ($inactivity->status == \App\Enums\InactivityStatus::Declined->value)
                                        -
                                    @else
                                        <form action="{{ route('admin.declineInactivity', $inactivity->id) }}"
                                            method="POST">
                                            @csrf
                                            <x-primary-button>
                                                {{ __('Elutasítás') }}
                                            </x-primary-button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.deleteInactivityAsAdmin', $inactivity->id) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <x-primary-button onclick="confirmDelete(event);">
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
