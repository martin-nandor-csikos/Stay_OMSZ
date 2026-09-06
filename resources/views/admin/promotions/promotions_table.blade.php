<div class="py-12" id="eloleptetesek">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Előléptetések</p>

                <form method="POST" action="{{ route('admin.updateUserRanks') }}" id="promotions-form">
                    @csrf
                    <table class="display view-reports" id="promotions">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IC név</th>
                                <th scope="col">Rang</th>
                                <th scope="col">Sikeres hetek</th>
                                <th scope="col">Előléptetés</th>
                                <th scope="col">Utolsó ranglépés</th>
                                <th scope="col">Rangon eltöltött napok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promotionUsers as $promoUser)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $promoUser->charactername }}</td>
                                    <td>
                                        <select name="user_ranks[{{ $promoUser->id }}]"
                                            class="user-rank-select rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel != 2)>
                                            @foreach ($ranks as $rankOption)
                                                <option value="{{ $rankOption->id }}" @selected($promoUser->rank_id == $rankOption->id)>
                                                    {{ $rankOption->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        @if ($promoUser->is_current_leader)
                                            <span class="line-through">-</span>
                                        @else
                                            {{ $promoUser->successful_weeks }} / {{ $promoUser->required_weeks }} hét
                                        @endif
                                    </td>
                                    <td>
                                        @if ($promoUser->is_current_leader)
                                            <button type="button" disabled
                                                class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md font-semibold text-xs uppercase tracking-widest cursor-not-allowed">
                                                {{ __('Leader rank') }}
                                            </button>
                                        @elseif ($promoUser->is_next_leader)
                                            @if ($promoUser->is_eligible)
                                                <button type="button" disabled
                                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md font-semibold text-xs uppercase tracking-widest cursor-not-allowed">
                                                    {{ __('Leader rankba léptethető') }}
                                                </button>
                                            @else
                                                <button type="button" disabled
                                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md font-semibold text-xs uppercase tracking-widest cursor-not-allowed">
                                                    {{ __('Még nem léptethető leader rankba') }}
                                                </button>
                                            @endif
                                        @elseif ($promoUser->is_max_rank)
                                            <span class="text-gray-500 font-semibold">{{ __('Legmagasabb rang') }}</span>
                                        @elseif ($promoUser->is_eligible)
                                            @if (Auth::user()->adminLevel == 2)
                                                <button type="button"
                                                    class="promote-btn inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-semibold text-xs uppercase tracking-widest transition cursor-pointer"
                                                    data-user-name="{{ $promoUser->charactername }}"
                                                    data-current-rank="{{ $promoUser->rank_name ?? 'Nincs' }}"
                                                    data-next-rank="{{ $promoUser->next_rank->name }}"
                                                    data-next-rank-id="{{ $promoUser->next_rank->id }}"
                                                    data-requires-exam="{{ $promoUser->next_rank->requires_exam ? '1' : '0' }}">
                                                    {{ __('Előléptetés') }} ({{ $promoUser->next_rank->name }})
                                                </button>
                                            @else
                                                <span class="text-green-600 font-semibold">{{ __('Előléptethető') }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-sm">{{ __('Még nem léptethető elő') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $promoUser->last_rank_change_display }}</td>
                                    <td>{{ $promoUser->days_at_rank }} nap</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (Auth::user()->adminLevel == 2)
                        <div class="flex justify-end mt-4">
                            <x-primary-button id="promotions-save-button"
                                class="admin-button disabled:!bg-gray-400 dark:disabled:!bg-gray-600 disabled:!text-gray-200 disabled:hover:!bg-gray-400 dark:disabled:hover:!bg-gray-600 disabled:cursor-not-allowed"
                                disabled>
                                {{ __('Mentés') }}
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const promotionsForm = $('#promotions-form');
        const originalPromotionsValues = promotionsForm.serialize();

        function checkPromotionsFormChanged() {
            promotionsForm.find('#promotions-save-button').prop('disabled', promotionsForm.serialize() === originalPromotionsValues);
        }

        promotionsForm.on('change input', '.user-rank-select', checkPromotionsFormChanged);

        function markAsPromoted(btn) {
            btn.text('Előléptetve')
                .removeClass('bg-green-600 hover:bg-green-700 text-white')
                .addClass('bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed')
                .prop('disabled', true);
        }

        $('#promotions').on('click', '.promote-btn', function() {
            const btn = $(this);
            const row = btn.closest('tr');
            const select = row.find('.user-rank-select');
            const userName = btn.data('userName');
            const nextRank = btn.data('nextRank');
            const nextRankId = btn.data('nextRankId');
            const requiresExam = btn.data('requiresExam') == '1';

            if (requiresExam) {
                Swal.fire({
                    title: 'Vizsga megerősítése',
                    text: 'A(z) ' + nextRank + ' rang eléréséhez vizsga szükséges. Átment ' + userName + ' a vizsgán?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#dc2626',
                    confirmButtonText: 'Igen, átment',
                    cancelButtonText: 'Nem, nem ment át',
                }).then((result) => {
                    if (result.isConfirmed) {
                        select.val(nextRankId).trigger('change');
                        markAsPromoted(btn);
                    }
                });
            } else {
                select.val(nextRankId).trigger('change');
                markAsPromoted(btn);
            }
        });
    });
</script>
