@php
    $user = auth()->user();

    $isPenjual = $user && (($user->role ?? null) === 'penjual');
    $isPelanggan = $user && (($user->role ?? null) === 'pelanggan');

    $homeUrl = Route::has('home') ? route('home') : url('/');

    if ($isPenjual) {
        $navigationLinks = [
            [
                'label' => 'Dashboard',
                'url' => Route::has('penjual.dashboard') ? route('penjual.dashboard') : '#',
            ],
            [
                'label' => 'Menu',
                'url' => Route::has('penjual.menu') ? route('penjual.menu') : '#',
            ],
            [
                'label' => 'Laporan',
                'url' => Route::has('penjual.laporan') ? route('penjual.laporan') : '#',
            ],
        ];
    } else {
        $navigationLinks = [
            [
                'label' => 'Beranda',
                'url' => $homeUrl,
            ],
            [
                'label' => 'Menu',
                'url' => Route::has('pelanggan.menu') ? route('pelanggan.menu') : '#menu-populer',
            ],
            [
                'label' => 'Riwayat',
                'url' => Route::has('pelanggan.riwayat-pemesanan') ? route('pelanggan.riwayat-pemesanan') : '#',
            ],
        ];
    }
@endphp

<footer class="mt-24 bg-neutral-800 text-white">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-12">
        <div class="grid gap-10 md:grid-cols-3">
            <div>
                <a
                    href="{{ $homeUrl }}"
                    class="inline-flex items-center gap-3 transition duration-300 hover:scale-105"
                    aria-label="Beranda Gettin"
                >
                    <img
                        src="{{ asset('images/gettin-icon.ico') }}"
                        alt="Gettin"
                        class="h-10 w-10 object-contain"
                    >

                    <span class="text-2xl font-black tracking-tight">
                        Gettin
                    </span>
                </a>

                <p class="mt-5 max-w-xs text-base font-medium leading-relaxed text-white/90">
                    Pesan makananmu di kantin tanpa ribet antri
                </p>
            </div>

            <nav>
                <h3 class="text-xl font-bold">
                    Navigation
                </h3>

                <ul class="mt-5 space-y-3 text-base font-medium text-white/90">
                    @foreach ($navigationLinks as $link)
                        <li>
                            <a
                                href="{{ $link['url'] }}"
                                class="transition duration-300 hover:text-orange-500"
                            >
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div>
                <h3 class="text-xl font-bold">
                    Contact
                </h3>

                <div class="mt-5 space-y-4 text-base font-medium leading-relaxed text-white/90">
                    <p>
                        Jl. Kalimantan No.37,<br>
                        Sumbersari, Jember
                    </p>

                    <p>
                        gettinmef00d@gmail.com
                    </p>

                    <p>
                        (201) 372-3702
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-5 border-t border-white/20 pt-8 text-sm font-medium text-white/90 md:flex-row md:items-center md:justify-between">
            <span>
                © Copyright by Gettin 2026
            </span>

            <div class="flex flex-wrap items-center gap-6">
                <a
                    href="#"
                    class="transition duration-300 hover:text-orange-500"
                >
                    Privacy Policy
                </a>

                <a
                    href="#"
                    class="transition duration-300 hover:text-orange-500"
                >
                    Terms of Use
                </a>
            </div>
        </div>
    </div>
</footer>
