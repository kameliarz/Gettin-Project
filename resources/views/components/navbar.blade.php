@php
    $loginUrl = Route::has('login') ? route('login') : '#';
    $homeUrl = Route::has('home') ? route('home') : url('/');
    $dashboardUrl = Route::has('dashboard') ? route('dashboard') : '#';

    $user = auth()->user();

    $isAdmin = false;
    $isPenjual = false;
    $isPelanggan = false;

    if ($user) {
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            || (($user->role ?? null) === 'admin');

        $isPenjual = (method_exists($user, 'hasRole') && $user->hasRole('penjual'))
            || (($user->role ?? null) === 'penjual');

        $isPelanggan = (method_exists($user, 'hasRole') && $user->hasRole('pelanggan'))
            || (($user->role ?? null) === 'pelanggan');
    }

    $pelangganMenuUrl = Route::has('pelanggan.menu')
        ? route('pelanggan.menu')
        : '#menu-populer';

    $pelangganKeranjangUrl = Route::has('pelanggan.keranjang')
        ? route('pelanggan.keranjang')
        : '#';

    $pelangganRiwayatUrl = Route::has('pelanggan.riwayat-pemesanan')
        ? route('pelanggan.riwayat-pemesanan')
        : '#';

    $penjualDashboardUrl = Route::has('penjual.dashboard')
        ? route('penjual.dashboard')
        : $dashboardUrl;

    $penjualMenuUrl = Route::has('penjual.menu')
        ? route('penjual.menu')
        : (Route::has('penjual.kelola-menu') ? route('penjual.kelola-menu') : '#');

    $penjualWaktuUrl = Route::has('penjual.waktu')
        ? route('penjual.waktu')
        : '#';

    $penjualLaporanUrl = Route::has('penjual.laporan')
        ? route('penjual.laporan')
        : '#';

    $adminDashboardUrl = Route::has('admin.dashboard')
        ? route('admin.dashboard')
        : $dashboardUrl;

    $adminPenggunaUrl = Route::has('admin.pengguna')
        ? route('admin.pengguna')
        : (Route::has('admin.kelola-pengguna') ? route('admin.kelola-pengguna') : '#');

    $profileUrl = Route::has('profile.edit') ? route('profile.edit') : '#';

    $userName = $user
        ? ($user->username ?? $user->name ?? 'User')
        : 'User';

    $nameParts = preg_split('/[\s._-]+/', trim($userName), -1, PREG_SPLIT_NO_EMPTY);

    if (count($nameParts) >= 2) {
        $initials = mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1);
    } else {
        $initials = mb_substr($userName, 0, 2);
    }

    $initials = mb_strtoupper($initials ?: 'U');

    $avatarPalettes = [
        ['bg' => 'bg-orange-500', 'hover' => 'hover:bg-orange-600', 'text' => 'text-white'],
        ['bg' => 'bg-emerald-500', 'hover' => 'hover:bg-emerald-600', 'text' => 'text-white'],
        ['bg' => 'bg-sky-500', 'hover' => 'hover:bg-sky-600', 'text' => 'text-white'],
        ['bg' => 'bg-indigo-500', 'hover' => 'hover:bg-indigo-600', 'text' => 'text-white'],
        ['bg' => 'bg-violet-500', 'hover' => 'hover:bg-violet-600', 'text' => 'text-white'],
        ['bg' => 'bg-rose-500', 'hover' => 'hover:bg-rose-600', 'text' => 'text-white'],
        ['bg' => 'bg-teal-500', 'hover' => 'hover:bg-teal-600', 'text' => 'text-white'],
        ['bg' => 'bg-amber-500', 'hover' => 'hover:bg-amber-600', 'text' => 'text-white'],
    ];

    $avatarIndex = (int) (sprintf('%u', crc32(mb_strtolower($userName))) % count($avatarPalettes));
    $avatarColor = $avatarPalettes[$avatarIndex];

    $logoutUrl = Route::has('logout') ? route('logout') : '#';

    if ($isPelanggan) {
        $navLinks = [
            [
                'label' => 'Beranda',
                'url' => $homeUrl,
                'active' => request()->routeIs('home') || request()->is('/'),
            ],
            [
                'label' => 'Menu',
                'url' => $pelangganMenuUrl,
                'active' => request()->routeIs('pelanggan.menu'),
            ],
            [
                'label' => 'Riwayat',
                'url' => $pelangganRiwayatUrl,
                'active' => request()->routeIs('pelanggan.riwayat-pemesanan') || request()->routeIs('pelanggan.pesanan'),
            ],
        ];
    } elseif ($isPenjual) {
        $navLinks = [
            [
                'label' => 'Dashboard',
                'url' => $penjualDashboardUrl,
                'active' => request()->routeIs('penjual.dashboard') || request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Menu',
                'url' => $penjualMenuUrl,
                'active' => request()->routeIs('penjual.menu') || request()->routeIs('penjual.kelola-menu'),
            ],
            [
                'label' => 'Waktu',
                'url' => $penjualWaktuUrl,
                'active' => request()->routeIs('penjual.waktu*'),
            ],
            [
                'label' => 'Laporan',
                'url' => $penjualLaporanUrl,
                'active' => request()->routeIs('penjual.laporan*'),
            ],
        ];
    } elseif ($isAdmin) {
        $navLinks = [
            [
                'label' => 'Dashboard',
                'url' => $adminDashboardUrl,
                'active' => request()->routeIs('admin.dashboard') || request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Pengguna',
                'url' => $adminPenggunaUrl,
                'active' => request()->routeIs('admin.pengguna') || request()->routeIs('admin.kelola-pengguna'),
            ],
        ];
    } elseif ($user) {
        $navLinks = [
            [
                'label' => 'Dashboard',
                'url' => $dashboardUrl,
                'active' => request()->routeIs('dashboard'),
            ],
        ];
    }
@endphp

<header class="mx-auto mt-6 max-w-7xl px-6 lg:px-12">
    <div class="grid h-20 grid-cols-3 items-center rounded-3xl bg-neutral-800 px-6 text-white shadow-xl md:px-8">
        <a
            href="{{ $homeUrl }}"
            class="flex items-center gap-3 justify-self-start transition duration-300 hover:scale-105"
            aria-label="Beranda Gettin"
        >
            <img
                src="{{ asset('images/gettin-icon.ico') }}"
                alt="Gettin"
                class="block h-9 w-9 shrink-0 object-contain"
            >

            <span class="whitespace-nowrap text-2xl font-black leading-none tracking-tight">
                Gettin
            </span>
        </a>

        @auth
            <nav class="hidden items-center justify-self-center gap-8 text-base font-black md:flex">
                @foreach ($navLinks as $link)
                    <a
                        href="{{ $link['url'] }}"
                        class="{{ $link['active'] ? 'text-orange-500' : 'text-white' }} transition duration-300 hover:text-orange-500"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        @else
            <div></div>
        @endauth

        <div class="flex items-center justify-self-end gap-4">
            @auth
                @if ($isPelanggan)
                    <a
                        href="{{ $pelangganKeranjangUrl }}"
                        aria-label="Keranjang"
                        class="flex h-11 w-11 items-center justify-center rounded-full transition duration-300 hover:-translate-y-1 hover:bg-white/10"
                    >
                        <img
                            src="{{ asset('images/shopping-cart.ico') }}"
                            alt="Keranjang"
                            class="block h-6 w-6 object-contain"
                        >
                    </a>
                @endif

                <div x-data="{ open: false }" class="relative">
                    <button
                        type="button"
                        @click="open = ! open"
                        aria-label="Profil {{ $userName }}"
                        title="{{ $userName }}"
                        class="flex h-11 w-11 items-center justify-center rounded-full {{ $avatarColor['bg'] }} {{ $avatarColor['hover'] }} {{ $avatarColor['text'] }} text-base font-black uppercase leading-none tracking-tight shadow-md transition duration-300 hover:shadow-lg"
                    >
                        {{ $initials }}
                    </button>

                    <div
                        x-cloak
                        x-show="open"
                        x-transition
                        @click.outside="open = false"
                        class="absolute top-full right-0 z-50 mt-3 w-36 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-black/5"
                    >
                        <form method="POST" action="{{ $logoutUrl }}">
                            @csrf

                            <button
                                type="submit"
                                class="w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-neutral-700 transition duration-300 hover:bg-neutral-100 hover:text-neutral-900"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a
                    href="{{ $loginUrl }}"
                    class="inline-flex h-11 items-center justify-center rounded-full bg-orange-500 px-8 text-base font-black leading-none text-white shadow-lg shadow-orange-900/20 transition duration-300 hover:-translate-y-1 hover:bg-orange-600 hover:shadow-xl"
                >
                    Login
                </a>
            @endauth
        </div>
    </div>

    @auth
        <nav class="mt-4 flex justify-center gap-6 rounded-2xl bg-neutral-800 px-6 py-4 text-sm font-black text-white md:hidden">
            @foreach ($navLinks as $link)
                <a
                    href="{{ $link['url'] }}"
                    class="{{ $link['active'] ? 'text-orange-500' : 'text-white' }} transition duration-300 hover:text-orange-500"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    @endauth
</header>
