<div class="py-12" id="alosztalyok">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Alosztályok</p>
                <form method="POST" action="{{ route('admin.updateUserDepartments') }}" id="departments-form">
                    @csrf
                    @php
                        $departmentLabels = [
                            'MOK' => 'Mentőorvosi alosztály',
                            'MM' => 'Mentőmotor alosztály',
                            'LMSZ' => 'Légimentő alosztály',
                            'MGK' => 'Mentő- és roham gépkocsis alosztály',
                        ];
                    @endphp
                    <table class="display view-reports" id="departments">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">IC név</th>
                                <th scope="col">Rang</th>
                                <th scope="col">Alosztály</th>
                                <th scope="col">Utolsó módosítás</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departmentUsers as $departmentUser)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $departmentUser->charactername }}</td>
                                    <td>{{ $departmentUser->rank_name ?? '-' }}</td>
                                    <td class="department-cell"
                                        data-search="{{ $departmentUser->department }} {{ $departmentLabels[$departmentUser->department] ?? '' }}">
                                        <select name="users[{{ $departmentUser->id }}][department]"
                                            class="department-select rounded border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @disabled(Auth::user()->adminLevel < 1)>
                                            @foreach (['MOK' => 'Mentőorvosi alosztály', 'MM' => 'Mentőmotor alosztály', 'LMSZ' => 'Légimentő alosztály', 'MGK' => 'Mentő- és roham gépkocsis alosztály'] as $code => $label)
                                                <option value="{{ $code }}" @selected($departmentUser->department === $code)>
                                                    {{ $code }} ({{ $label }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $departmentUser->last_department_change_at ? \Illuminate\Support\Carbon::parse($departmentUser->last_department_change_at)->format('Y.m.d H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (Auth::user()->adminLevel >= 1)
                        <div class="flex justify-end mt-4">
                            <x-primary-button id="departments-save-button"
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
        const form = $('#departments-form');
        const original = form.serialize();

        form.on('input change', '.department-select', function() {
            const select = $(this);
            const selectedCode = select.val();
            const selectedLabel = select.find('option:selected').text().trim();

            select.closest('.department-cell').attr('data-search', selectedCode + ' ' + selectedLabel);

            if (window.departmentsTable) {
                window.departmentsTable.rows().invalidate();
            }

            $('#departments-save-button').prop('disabled', form.serialize() === original);
        });
    });
</script>
