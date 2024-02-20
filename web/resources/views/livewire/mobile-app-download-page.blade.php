<div x-data="{ os: undefined }" x-init="(() => {
    const { userAgent } = window.navigator;
    const macosPlatforms = /(Macintosh)|(MacIntel)|(MacPPC)|(Mac68K)/i;
    const windowsPlatforms = /(Win32)|(Win64)|(Windows)|(WinCE)/i;
    const iosPlatforms = /(iPhone)|(iPad)|(iPod)/i;

    if (macosPlatforms.test(userAgent)) {
      os = 'macos';
      return;
    }
    if (iosPlatforms.test(userAgent)) {
      os = 'ios';
      return;
    }
    if (windowsPlatforms.test(userAgent)) {
      os = 'windows';
      return;
    }
    if (/Android/i.test(userAgent)) {
      os = 'android';
      return;
    }
    if (/Linux/i.test(userAgent)) {
      os = 'linux';
      return;
    }

    os = 'undetermined';
})()">
    <x-navigation/>
    <div class="p-4">
        <div class="flex flex-col gap-4">
            <h1 class="text-2xl sm:text-4xl font-medium text-center">Hermes mobil alkalmazás</h1>
            <p class="text-gray-500 dark:text-gray-400 text-balance text-center">A Hermes mobil alkalmazás segítségével
                könnyedén tusz értesítéseket kapni, mielőtt a te meccseid következnek, hogy időben el tudj kezdeni
                készülni rájuk.</p>
            <p x-cloak x-show="os !== 'android'" class="text-sm text-danger-600 dark:text-danger-400 text-center">Sajnos
                az alkalmazás még nem érhető el a
                te operációs rendszeredre!</p>
            <div class="flex justify-center">
                <x-filament::button color="gray" wire:click="downloadApp">Letöltés Android rendszerre
                </x-filament::button>
            </div>
            <h2 class="text-center text-2xl font-medium">Telepítési útmutató</h2>
            <ol class="list-decimal ml-4">
                <li class="mb-4">
                    <div>
                        <p class="font-bold mb-4">APK fájl letöltése</p>
                        <p>Először is töltds le a telepítő fájlt (amelyre úgy is gondolhatsz, mint egy exe fájlra
                            windows
                            rendszeren) a fentebbi gombara kattintva. Ha Firefox böngészőt használsz, akkor egy ismert
                            hiba, hogy .zip kiterjesztéssel tölti le az alkalmazást. Ebben az esetben csak nevezd át az
                            alkalmazás telepítő fájlját .apk kiterjesztésűre.</p>
                    </div>
                </li>
                <li class="mb-4">
                    <div>
                        <p class="font-bold mb-4">Külső források engedélyezése</p>
                        <p>Mivel az alkalmazásunk nincs fenn a Google Play áruházban, engedélyezned kell az alkalmazások
                            telepítését ismeretlen forrásokból. Ehhez egy rövid képes útmutatót
                            <x-filament::link
                                href="https://drdroid.hu/ismeretlen-forrasok-android-utmutato/" target="_blank">itt
                            </x-filament::link>
                            találsz. Miért is kell ez? Mivel ahhoz, hogy alkalmazásunkat a Google Play áruházba fel
                            tudjuk tölteni egy hosszadalmas folyamatnak kellene végigmennie és fizetnünk is kellene
                            érte. Ha fenntartásaid vannak az alkalmazás biztonságával kapcsolatban, akkor nézd meg a
                            forráskódját a
                            <x-filament::link href="https://github.com/Xeretis/Hermes" target="_blank">
                                Github
                            </x-filament::link>
                            oldalán.
                        </p>
                        </p>
                    </div>
                </li>
                <li class="mb-4">
                    <div>
                        <p class="font-bold mb-4">Az alkalmazás telepítése</p>
                        <p>
                        <p class="mb-5">
                            Kövesd a képeken látható lépéseket az alkalmazás telepítéséhez. (A képeken látható
                            biztonsági
                            értesítés amiatt jelenik meg, hogy az alkalmazás nem a Google Play áruházból származik)
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <img src="{{ asset('images/app_install_step_1.png') }}" alt="1. lépés"
                                 class="w-full rounded-lg shadow-lg">
                            <img src="{{ asset('images/app_install_step_2.png') }}" alt="1. lépés"
                                 class="w-full rounded-lg shadow-lg">
                            <img src="{{ asset('images/app_install_step_3.png') }}" alt="1. lépés"
                                 class="w-full rounded-lg shadow-lg">
                            <img src="{{ asset('images/app_install_step_4.png') }}" alt="1. lépés"
                                 class="w-full rounded-lg shadow-lg">
                        </div>
                    </div>
                </li>
                <li class="mb-4">
                    <div>
                        <p class="font-bold mb-4">Az alkalmazás megnyitása</p>
                        <p>
                        <p>
                            Nyisd meg az alkalmazást és kövesd az ott leírt lépéseket az összekapcsoláshoz!
                        </p>
                    </div>
                </li>
            </ol>
        </div>
    </div>
</div>
