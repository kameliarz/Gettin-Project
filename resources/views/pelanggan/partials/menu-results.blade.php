@if ($menus->count() > 0)
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($menus as $menu)
            <article class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-md transition duration-300 hover:-translate-y-2 hover:shadow-2xl">
                <div class="relative h-48 overflow-hidden rounded-2xl bg-gray-100">
                    @if ($menu->is_popular)
                        <span class="absolute left-3 top-3 z-10 inline-flex h-8 items-center rounded-full bg-orange-500 px-4 text-xs font-black text-white shadow-lg">
                            Populer
                        </span>
                    @endif

                    <img
                        src="{{ $menu->image_url }}"
                        alt="{{ $menu->name }}"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>
                </div>

                <div class="pt-5">
                    <span class="inline-flex h-7 items-center rounded-full bg-orange-50 px-4 text-xs font-black text-orange-500">
                        {{ $menu->category_name }}
                    </span>

                    <h2 class="mt-4 text-xl font-black leading-tight tracking-tight text-gray-950 transition duration-300 group-hover:text-orange-500">
                        {{ $menu->name }}
                    </h2>

                    <p class="mt-1 text-sm font-medium text-gray-500">
                        {{ $menu->canteen_name }}
                    </p>

                    <div class="mt-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xl font-black tracking-tight text-orange-500">
                                {{ $menu->formatted_price }}
                            </p>

                            <p @class([
                                'mt-1 text-xs font-black',
                                'text-emerald-600' => $menu->stock_qty > 0,
                                'text-red-500' => $menu->stock_qty <= 0,
                            ])>
                                {{ $menu->stock_qty > 0 ? 'Stok tersedia' : 'Stok habis' }}
                            </p>
                        </div>

                        @if (Route::has('pelanggan.keranjang.store'))
                            <form method="POST" action="{{ route('pelanggan.keranjang.store') }}" data-add-to-cart>
                                @csrf

                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                                <button
                                    type="submit"
                                    aria-label="Tambah {{ $menu->name }} ke keranjang"
                                    class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-orange-500 text-white shadow-lg shadow-orange-200 transition duration-300 group-hover:rotate-90 group-hover:scale-110 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50"
                                    @disabled($menu->stock_qty <= 0)
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    >
                                        <path d="M12 5v14"></path>
                                        <path d="M5 12h14"></path>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <button
                                type="button"
                                aria-label="Tambah {{ $menu->name }} ke keranjang"
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-orange-500 text-white shadow-lg shadow-orange-200 transition duration-300 group-hover:rotate-90 group-hover:scale-110 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50"
                                @disabled($menu->stock_qty <= 0)
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="h-6 w-6"
                                    aria-hidden="true"
                                >
                                    <path d="M12 5v14"></path>
                                    <path d="M5 12h14"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    @if ($menus->hasPages())
        <nav class="mt-10 flex items-center justify-center gap-3" data-menu-pagination>
            @if ($menus->onFirstPage())
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black text-gray-400">
                    &lt;
                </span>
            @else
                <a
                    href="{{ $menus->previousPageUrl() }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-100"
                >
                    &lt;
                </a>
            @endif

            @for ($page = 1; $page <= $menus->lastPage(); $page++)
                @if ($page === $menus->currentPage())
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-neutral-800 text-sm font-black text-white">
                        {{ $page }}
                    </span>
                @else
                    <a
                        href="{{ $menus->url($page) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-100"
                    >
                        {{ $page }}
                    </a>
                @endif
            @endfor

            @if ($menus->hasMorePages())
                <a
                    href="{{ $menus->nextPageUrl() }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-100"
                >
                    &gt;
                </a>
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-black text-gray-400">
                    &gt;
                </span>
            @endif
        </nav>
    @endif
@else
    <div class="flex min-h-96 items-center justify-center rounded-3xl border border-gray-200 bg-white p-10 text-center shadow-sm">
        <div>
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-violet-100 text-violet-400">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-10 w-10"
                    aria-hidden="true"
                >
                    <path d="M7 3v8"></path>
                    <path d="M5 3v8"></path>
                    <path d="M9 3v8"></path>
                    <path d="M5 11a2 2 0 0 0 4 0"></path>
                    <path d="M7 13v8"></path>
                    <path d="M17 3v18"></path>
                    <path d="M17 3c2 2 3 4 3 7s-1 5-3 7"></path>
                </svg>
            </div>

            <h2 class="mt-6 text-2xl font-black text-gray-950">
                Menu tidak ditemukan
            </h2>

            <p class="mt-3 text-base font-medium text-gray-600">
                Coba ganti kata kunci atau reset filter
            </p>

            <a
                href="{{ route('pelanggan.menu') }}"
                class="mt-7 inline-flex rounded-full bg-orange-500 px-10 py-3 text-sm font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600"
                data-reset-filter
            >
                Reset Semua
            </a>
        </div>
    </div>
@endif
