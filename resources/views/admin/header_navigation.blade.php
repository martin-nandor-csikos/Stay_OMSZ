<div class="py-12" id="header-navigation">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-50 dark:bg-gray-800 sm:py-2 overflow-hidden shadow-sm sm:rounded-lg pb-4">
            <div class="p-6 text-gray-900 dark:text-gray-100 view-reports-padding">
                <p class="top5">Ugrás a táblához</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mx-4">
                    <div>
                        <a href="#heti-statisztika">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Heti statisztika') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#elozo-het">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Előző hét (lezárt)') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#eloleptetesek">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Előléptetések') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#inaktivitasok">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Inaktivitások') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#regisztralt-felhasznalok">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Regisztrált felhasználók') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#volt-felhasznalok">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Volt felhasználók') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#beallitasok">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Beállítások') }}
                            </x-primary-button>
                        </a>
                    </div>
                    <div>
                        <a href="#admin-logok">
                            <x-primary-button class="w-full flex justify-center">
                                {{ __('Admin logok') }}
                            </x-primary-button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="#header-navigation" id="scroll-to-nav-btn"
    class="fixed bottom-6 right-6 z-50 hidden inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow-lg transition duration-150 ease-in-out cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
    {{ __('Ugrás a táblához') }}
</a>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const headerNav = document.getElementById('header-navigation');
        const scrollBtn = document.getElementById('scroll-to-nav-btn');

        if (headerNav && scrollBtn) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        scrollBtn.classList.add('hidden');
                    } else {
                        scrollBtn.classList.remove('hidden');
                    }
                });
            }, {
                root: null,
                threshold: 0
            });

            observer.observe(headerNav);
        }
    });
</script>
