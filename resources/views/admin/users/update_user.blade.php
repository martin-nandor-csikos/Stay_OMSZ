<x-app-layout>
    <style>
        .admin-level-help .admin-level-tooltip {
            display: none;
            width: 36rem;
            max-width: calc(100vw - 2rem);
        }

        .admin-level-help:hover .admin-level-tooltip,
        .admin-level-help:focus-within .admin-level-tooltip {
            display: block;
        }

        .admin-level-help-button {
            background-color: #0066db;
            margin-bottom: 2px;
        }

        .admin-level-help-button:hover,
        .admin-level-help-button:focus {
            background-color: #0066db;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Felhasználó frissítése') }}
        </h2>
    </x-slot>

    @session('password-updated')
        <div class="alert alert-success" role="alert">
            {{ session('password-updated') }}
        </div>
    @endsession

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 create-report">
            <div class="bg-gray-50 dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="top5">Csak azok az adatok frissülnek, amik megváltoznak.</p>
                    <form method="POST" action="{{ route('admin.updateUser', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <!-- Account ID -->
                        <div>
                            <x-input-label for="account_id" :value="__('Account ID')" />
                            <x-text-input id="account_id" class="block mt-1 w-full" type="number" name="account_id"
                                value="{{ $user->account_id }}" min="1" required autofocus />
                            <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                        </div>

                        <!-- Character name -->
                        <div class="mt-4">
                            <x-input-label for="charactername" :value="__('IC név')" />
                            <x-text-input id="charactername" class="block mt-1 w-full" type="text"
                                name="charactername" value="{{ $user->charactername }}" maxlength="255" />
                            <x-input-error :messages="$errors->get('charactername')" class="mt-2" />
                        </div>

                        <!-- Username -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Felhasználónév')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username"
                                value="{{ $user->username }}" maxlength="255" autofocus />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>
                        @if (Auth::user()->adminLevel == 2)
                            <div class="mt-4">
                                <div class="flex items-center gap-2">
                                    <x-input-label for="adminLevel" value="Admin szint" />
                                    <div class="admin-level-help relative inline-flex">
                                        <span tabindex="0"
                                            class="admin-level-help-button w-5 h-5 inline-flex items-center justify-center rounded-full text-white text-xs font-bold leading-none cursor-help focus:outline-none focus:ring-2 focus:ring-blue-300"
                                            aria-label="Admin szint korlátozások">?</span>
                                        <div
                                            class="admin-level-tooltip absolute left-0 top-7 z-20 rounded-md bg-gray-800 border border-gray-700 p-3 text-sm text-gray-100 shadow-lg">
                                            <p class="font-semibold mb-2">Az 1-es adminok a következő korlátozásokkal rendelkeznek:</p>
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>- Nem tudnak másnak admin jogot adni vagy admin szintet módosítani.</li>
                                                <li>- Nem tudnak 2-es admin szintű felhasználót módosítani vagy törölni.</li>
                                                <li>- Nem tudják módosítani a Beállítások részt, beleértve a minimumokat, bónuszokat és ellátási árakat.</li>
                                                <li>- Nem tudnak rangokat létrehozni, törölni vagy módosítani.</li>
                                                <li>- Csak jogosult felhasználót tudnak előléptetni, de nem tudnak lefokozni.</li>
                                                <li>- Nem tudnak járművet létrehozni, törölni vagy módosítani (ápolókat viszont tudják kezelni).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @if ($user->username == Auth::user()->username)
                                    <select id="adminLevel" disabled
                                        class="rounded border-gray-300 dark:bg-gray-900 dark:text-white block mt-1 w-full">
                                        <option value="0" @selected($user->adminLevel == 0)>0</option>
                                        <option value="1" @selected($user->adminLevel == 1)>1</option>
                                        <option value="2" @selected($user->adminLevel == 2)>2</option>
                                    </select>
                                    <input type="hidden" name="adminLevel" value="{{ $user->adminLevel }}">
                                @else
                                    <select id="adminLevel" name="adminLevel"
                                        class="rounded border-gray-300 dark:bg-gray-900 dark:text-white block mt-1 w-full">
                                        <option value="0" @selected($user->adminLevel == 0)>0</option>
                                        <option value="1" @selected($user->adminLevel == 1)>1</option>
                                        <option value="2" @selected($user->adminLevel == 2)>2</option>
                                    </select>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    0-s admin szint --> A felhasználó nem admin.
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    1-es admin szint --> A felhasználó admin, de nem tud másnak admin jogot adni.
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    2-es admin szint --> A felhasználó főadmin.
                                </p>
                                <x-input-error :messages="$errors->get('adminLevel')" class="mt-2" />
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Frissítés') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.updateUserPassword', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Új jelszó')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                maxlength="255" autofocus />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Password re -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Új jelszó újra')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                name="password_confirmation" maxlength="255" autofocus />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.index') }}">
                                <x-secondary-button>
                                    {{ __('Vissza') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Jelszó frissítés') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
