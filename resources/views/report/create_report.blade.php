<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új jelentés felvétele') }}
        </h2>
    </x-slot>

    @session('successful-creation')
        <div class="alert alert-success" role="alert">
            {{ session('successful-creation') }}
        </div>
    @endsession

    <script>
        // When the checkboxes are checked, update the value of price and diagnosis

        $(document).ready(function() {
            $('input[type=checkbox]').click(function () {
                let price = parseFloat($('#price').val()) || 0;
                let checkedValues = $('input[type=checkbox]:checked').map(function() {
                    return this.value;
                }).get().join(', ');

                $('#diagnosis').val(checkedValues || '');

                let checkboxPrices = {
                    'vizs': 20000,
                    'kot': 30000,
                    'gip': 35000,
                    'gyogy': 30000,
                    'kav': 20000,
                    'as': 30000,
                    'ss': 35000,
                    'emb': 20000,
                    'th': 150000
                };

                price = 0;

                $.each(checkboxPrices, function(id, amount) {
                    if ($('#' + id).is(':checked')) {
                        if ((price + amount) >= 300000) {
                            price = 300000;
                        } else {
                            price += amount;
                        }
                    }
                });

                $('#price').val(price);
            });
        });
    </script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('reports.store') }}">
                        @csrf
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Ár ($)')" class="price-currency" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" value="0" required autofocus autocomplete="price" max="300000" min="0" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Diagnosis -->
                        <div class="mt-4">
                            <x-input-label for="diagnosis" :value="__('Diagnózis')" />
                            <x-text-input id="diagnosis" class="block mt-1 w-full" type="text" name="diagnosis" required autofocus autocomplete="diagnosis" />
                            
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12 checkbox-wrapper">
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="vizs" name="vizs" value="VIZS" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="vizs"> VIZS</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="kot" name="kot" value="KÖT" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="kot"> KÖT</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="gip" name="gip" value="GIP" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="gip"> GIP</label><br>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 checkbox-wrapper">
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="gyogy" name="gyogy" value="GYÓGY" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="gyogy"> GYÓGY</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="kav" name="kav" value="KAV" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="kav"> KAV</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="as" name="as" value="ÁS" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="as"> ÁS</label><br>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 checkbox-wrapper">
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="ss" name="ss" value="SS" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="ss"> SS</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="emb" name="emb" value="EMB" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="emb"> EMB</label><br>
                                        </div>
                                        <div class="form-check form-check-inline checkbox">
                                            <input type="checkbox" id="th" name="th" value="TH" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <label for="th"> TH</label><br>
                                        </div>        
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <!-- withWho -->
                        <div class="mt-4">
                            <x-input-label for="withWho" :value="__('Társaid (nem kötelező)')" />
                            <x-text-input id="withWho" class="block mt-1 w-full" type="text" name="withWho" autocomplete="withWho" maxlength="100" />
                            <x-input-error :messages="$errors->get('withWho')" class="mt-2" />
                        </div>

                        <!-- img -->
                        <div class="mt-4">
                            <x-input-label for="img" :value="__('Kép (imgur link)')" />
                            <x-text-input id="img" class="block mt-1 w-full" type="text" name="img" :value="old('img')" required maxlength="100" />
                            <x-input-error :messages="$errors->get('img')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('reports.index') }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Felvétel') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
