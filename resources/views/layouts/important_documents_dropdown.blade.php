@php
    $importantDocuments = [
        'Frakció szabályzat' => 'https://docs.google.com/document/d/1NX7BJq_gpV4AT91tUuMOOeFmnk_daJifhA0sBBcH0o0',
        'Rádiózási segédlet' => 'https://docs.google.com/document/d/1k9WmCBlmBSuH1W5ccQsniZzk1Fzi1Ltt',
        'Ellátási segédlet' => 'https://docs.google.com/document/d/1YBgXuT4D1HhVlAn_HD6FdxGbD0clXgJP',
        'Parkolóhely kiosztás' => 'https://docs.google.com/spreadsheets/d/15yS_PBe7-qe928YhbZmGEj4m3BXz5Lva/edit?gid=666139380',
    ];
@endphp

<div class="relative inline-flex items-center" style="height: 4rem;" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false">
    <button type="button" @click="open = !open"
        class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
        {{ __('Fontos dokumentumok') }}
        <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 top-full start-0 w-56 rounded-md shadow-lg"
        style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-gray-50 dark:bg-gray-700">
            @foreach ($importantDocuments as $label => $url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    {{ __($label) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
