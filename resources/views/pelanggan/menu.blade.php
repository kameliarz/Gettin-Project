@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <section class="mx-auto mt-10 max-w-7xl px-6 lg:px-12">
        <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
            Daftar Menu
        </h1>

        <div id="menu-cart-alert"></div>

        <form
            id="menu-filter-form"
            method="GET"
            action="{{ route('pelanggan.menu') }}"
            class="mt-8"
        >
            <div class="rounded-3xl bg-white p-3 shadow-xl ring-1 ring-gray-200">
                <input
                    id="menu-search-input"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari menu, kantin, atau kategori..."
                    autocomplete="off"
                    class="w-full rounded-2xl border-gray-300 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:border-orange-500 focus:ring-orange-500"
                >
            </div>
        </form>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-4">
            <aside class="h-fit rounded-3xl bg-white p-6 shadow-lg ring-1 ring-gray-200">
                <h2 class="text-xl font-black text-gray-950">
                    Filter
                </h2>

                <div class="mt-5">
                    <h3 class="text-sm font-black text-gray-900">
                        Kategori Menu
                    </h3>

                    <div class="mt-3 space-y-3">
                        @foreach ($categories as $category)
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700">
                                <input
                                    form="menu-filter-form"
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id }}"
                                    @checked(in_array($category->id, $selectedCategories))
                                    class="menu-filter-input rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                >

                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-7">
                    <h3 class="text-sm font-black text-gray-900">
                        Range Harga
                    </h3>

                    <div class="mt-3 space-y-3">
                        <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700">
                            <input
                                form="menu-filter-form"
                                type="checkbox"
                                name="price_ranges[]"
                                value="under_10000"
                                @checked(in_array('under_10000', $selectedPriceRanges))
                                class="menu-filter-input rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            >

                            <span>&lt; Rp10.000</span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700">
                            <input
                                form="menu-filter-form"
                                type="checkbox"
                                name="price_ranges[]"
                                value="10000_15000"
                                @checked(in_array('10000_15000', $selectedPriceRanges))
                                class="menu-filter-input rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            >

                            <span>Rp10.000 - Rp15.000</span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700">
                            <input
                                form="menu-filter-form"
                                type="checkbox"
                                name="price_ranges[]"
                                value="above_15000"
                                @checked(in_array('above_15000', $selectedPriceRanges))
                                class="menu-filter-input rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            >

                            <span>&gt; Rp15.000</span>
                        </label>
                    </div>
                </div>

                <div class="mt-7">
                    <h3 class="text-sm font-black text-gray-900">
                        Kantin
                    </h3>

                    <div class="mt-3 space-y-3">
                        @foreach ($canteens as $canteen)
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700">
                                <input
                                    form="menu-filter-form"
                                    type="checkbox"
                                    name="canteens[]"
                                    value="{{ $canteen->id }}"
                                    @checked(in_array($canteen->id, $selectedCanteens))
                                    class="menu-filter-input rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                >

                                <span>{{ $canteen->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-7">
                    <a
                        href="{{ route('pelanggan.menu') }}"
                        id="menu-reset-button"
                        class="block rounded-full border border-gray-300 px-5 py-3 text-center text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-100"
                    >
                        Reset Filter
                    </a>
                </div>
            </aside>

            <div class="relative lg:col-span-3">
                <div
                    id="menu-loading"
                    class="pointer-events-none absolute inset-0 z-20 hidden items-start justify-center rounded-3xl bg-white/70 pt-20 backdrop-blur-sm"
                >
                    <div class="rounded-full bg-white px-5 py-3 text-sm font-black text-orange-500 shadow-lg">
                        Memuat menu...
                    </div>
                </div>

                <div id="menu-results">
                    @include('pelanggan.partials.menu-results', ['menus' => $menus])
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('menu-filter-form');
            const searchInput = document.getElementById('menu-search-input');
            const results = document.getElementById('menu-results');
            const loading = document.getElementById('menu-loading');
            const resetButton = document.getElementById('menu-reset-button');
            const cartAlert = document.getElementById('menu-cart-alert');

            let debounceTimer = null;
            let activeController = null;

            const showLoading = () => {
                loading.classList.remove('hidden');
                loading.classList.add('flex');
                results.classList.add('opacity-50');
            };

            const hideLoading = () => {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
                results.classList.remove('opacity-50');
            };

            const showCartAlert = (message, type = 'success') => {
                if (! message) {
                    cartAlert.innerHTML = '';
                    return;
                }

                const classes = type === 'success'
                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                    : 'bg-red-50 text-red-600 ring-red-100';

                cartAlert.innerHTML = `
                    <div class="mt-6 rounded-3xl px-6 py-4 text-sm font-bold ring-1 ${classes}">
                        ${message}
                    </div>
                `;

                setTimeout(() => {
                    cartAlert.innerHTML = '';
                }, 2500);
            };

            const buildUrl = (customUrl = null) => {
                const url = new URL(customUrl || form.action, window.location.origin);

                if (! customUrl) {
                    const formData = new FormData(form);
                    const params = new URLSearchParams();

                    for (const [key, value] of formData.entries()) {
                        if (value !== '') {
                            params.append(key, value);
                        }
                    }

                    url.search = params.toString();
                }

                url.searchParams.set('ajax', '1');

                return url.toString();
            };

            const fetchMenus = async (customUrl = null, updateHistory = true) => {
                const url = buildUrl(customUrl);

                if (activeController) {
                    activeController.abort();
                }

                activeController = new AbortController();

                showLoading();

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        signal: activeController.signal,
                    });

                    if (! response.ok) {
                        throw new Error('Gagal mengambil data menu.');
                    }

                    const data = await response.json();

                    results.innerHTML = data.html;

                    if (updateHistory) {
                        const historyUrl = new URL(url);
                        historyUrl.searchParams.delete('ajax');

                        window.history.pushState({}, '', historyUrl.toString());
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error(error);
                    }
                } finally {
                    hideLoading();
                }
            };

            const fetchMenusWithDebounce = () => {
                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(() => {
                    fetchMenus();
                }, 350);
            };

            searchInput.addEventListener('input', fetchMenusWithDebounce);

            document.querySelectorAll('.menu-filter-input').forEach((input) => {
                input.addEventListener('change', () => fetchMenus());
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                fetchMenus();
            });

            resetButton.addEventListener('click', (event) => {
                event.preventDefault();

                form.reset();
                searchInput.value = '';

                fetchMenus(resetButton.href);
            });

            results.addEventListener('click', (event) => {
                const paginationLink = event.target.closest('[data-menu-pagination] a');
                const resetLink = event.target.closest('[data-reset-filter]');

                if (paginationLink) {
                    event.preventDefault();
                    fetchMenus(paginationLink.href);
                }

                if (resetLink) {
                    event.preventDefault();

                    form.reset();
                    searchInput.value = '';

                    fetchMenus(resetLink.href);
                }
            });

            results.addEventListener('submit', async (event) => {
                const form = event.target.closest('[data-add-to-cart]');

                if (! form) {
                    return;
                }

                event.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                const formData = new FormData(form);

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-60');
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (! response.ok) {
                        showCartAlert(data.message || 'Menu gagal ditambahkan ke keranjang.', 'error');
                        return;
                    }

                    showCartAlert(data.message || 'Menu berhasil ditambahkan ke keranjang.', 'success');
                } catch (error) {
                    console.error(error);
                    showCartAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-60');
                    }
                }
            });

            window.addEventListener('popstate', () => {
                fetchMenus(window.location.href, false);
            });
        });
    </script>
@endsection
