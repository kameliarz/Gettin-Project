@extends('layouts.app')

@section('title', 'Gettin')

@php
    $loginUrl = Route::has('login') ? route('login') : url('/login');

    $menuUrl = Route::has('pelanggan.menu')
        ? route('pelanggan.menu')
        : '#menu-populer';

    $actionUrl = auth()->check() ? $menuUrl : $loginUrl;
@endphp

@section('content')
    <section class="mx-auto mt-16 grid min-h-96 max-w-7xl grid-cols-1 items-center gap-10 px-6 lg:grid-cols-2 lg:px-12">
        <div>
            <h1 class="text-4xl font-black leading-tight tracking-tight text-gray-950 md:text-5xl">
                Pre-order makanan kampus<br>
                <span class="text-orange-500">tanpa antre</span>
            </h1>

            <p class="mt-7 max-w-xl text-base font-medium leading-relaxed text-black-500">
                Pilih menu favoritmu dari kantin pilihan, tentukan jam pengambilan,
                dan ambil pesananmu tepat waktu. Hemat waktu istirahatmu untuk
                hal yang lebih produktif.
            </p>

            <a
                href="{{ $actionUrl }}"
                class="mt-9 inline-flex h-12 items-center justify-center rounded-full bg-orange-500 px-8 text-sm font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:-translate-y-1 hover:bg-orange-600 hover:shadow-xl"
            >
                Pesan Sekarang
            </a>
        </div>

        <div class="relative mx-auto h-96 w-full max-w-xl">
            <div class="absolute right-8 top-6 h-72 w-72 rounded-full bg-orange-100 blur-3xl"></div>
            <div class="absolute bottom-8 left-8 h-56 w-56 rounded-full bg-orange-50 blur-2xl"></div>

            <div class="absolute left-16 top-10 h-56 w-44 -rotate-6 rounded-xl bg-white p-3 shadow-xl">
                <div class="h-full w-full rounded-lg bg-gradient-to-br from-lime-900 via-lime-200 to-orange-100"></div>
            </div>

            <span class="absolute left-4 top-60 z-20 inline-flex h-9 items-center gap-2 rounded-full bg-orange-500 px-5 text-xs font-black text-white shadow-xl">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-4 w-4"
                    aria-hidden="true"
                >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                </svg>

                <span>Siap Diambil</span>
            </span>

            <span class="absolute right-4 top-16 z-20 inline-flex h-9 animate-bounce items-center gap-2 rounded-full bg-emerald-100 px-5 text-xs font-black text-emerald-600 shadow-xl">
                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-3 w-3"
                        aria-hidden="true"
                    >
                        <path d="M5 12.5 9.5 17 19 7"></path>
                    </svg>
                </span>

                <span>Stok Tersedia</span>
            </span>

            <article class="absolute right-10 top-12 z-10 w-80 rotate-3 rounded-3xl bg-white p-4 shadow-2xl transition duration-300 hover:rotate-0 hover:scale-105">
                <div class="h-64 overflow-hidden rounded-xl bg-orange-100">
                    <img
                        src="{{ asset('images/ayam-geprek.jpg') }}"
                        alt="Ayam Geprek"
                        class="h-full w-full object-cover"
                    >
                </div>

                <h2 class="mt-4 text-2xl font-black leading-tight tracking-tight text-gray-950">
                    Ayam Geprek Spesial
                </h2>

                <p class="mt-1 text-sm font-extrabold text-gray-500">
                    Kantin Barokah
                </p>
            </article>
        </div>
    </section>

    <section id="menu-populer" class="mx-auto mt-24 max-w-7xl px-6 lg:px-12">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-orange-500">
                    Pilihan favorit
                </p>

                <h2 class="mt-2 text-3xl font-black tracking-tight text-neutral-800 md:text-4xl">
                    Menu Populer Minggu ini
                </h2>
            </div>

            <a
                href="{{ $actionUrl }}"
                class="inline-flex items-center gap-2 text-sm font-black text-orange-500 transition hover:gap-3 hover:text-orange-600"
            >
                Lihat semua menu

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-4 w-4"
                    aria-hidden="true"
                >
                    <path d="M5 12h14"></path>
                    <path d="m13 6 6 6-6 6"></path>
                </svg>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($popularMenus as $menu)
                <article class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-md transition duration-300 hover:-translate-y-3 hover:shadow-2xl">
                    <div class="relative h-48 overflow-hidden rounded-2xl bg-gray-100">
                        <span class="absolute left-3 top-3 z-10 inline-flex h-8 items-center rounded-full bg-orange-500 px-4 text-xs font-black text-white shadow-lg transition duration-300 group-hover:scale-105">
                            Populer
                        </span>

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

                        <h3 class="mt-4 text-xl font-black leading-tight tracking-tight text-gray-950 transition duration-300 group-hover:text-orange-500">
                            {{ $menu->name }}
                        </h3>

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

                            <a
                                href="{{ $actionUrl }}"
                                aria-label="Tambah {{ $menu->name }}"
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-orange-500 text-white shadow-lg shadow-orange-200 transition duration-300 group-hover:rotate-90 group-hover:scale-110 hover:bg-orange-600"
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
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                    <p class="text-base font-semibold text-gray-500">
                        Belum ada menu populer yang tersedia.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="mx-auto mt-28 grid max-w-7xl grid-cols-1 gap-8 px-6 lg:grid-cols-3 lg:px-12">
        <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-gray-50 p-8 shadow-xl lg:col-span-2">
            <span class="inline-flex h-9 items-center justify-center rounded-full bg-orange-500 px-5 text-sm font-black text-white">
                How to Order
            </span>

            <ol class="relative z-10 mt-5 max-w-2xl space-y-4 text-sm font-medium leading-relaxed text-gray-900">
                <li class="border-b border-gray-300 pb-4">
                    1. Pilih hidangan yang tersedia pada <strong>Menu</strong>
                </li>

                <li class="border-b border-gray-300 pb-4">
                    2. Masuk ke <strong>Keranjang</strong> untuk memproses pesanan
                </li>

                <li class="border-b border-gray-300 pb-4">
                    3. Input estimasi waktu kapan pesanan ingin diambil
                </li>

                <li class="border-b border-gray-300 pb-4">
                    4. Klik Checkout, lalu lakukan pembayaran dengan QRIS
                </li>

                <li>
                    5. Tunggu hingga pesanan siap, kemudian jemput pesanan Anda di kantin tersayang
                </li>
            </ol>

            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="pointer-events-none absolute right-10 top-12 h-40 w-40 rotate-12 text-orange-200 opacity-50"
                aria-hidden="true"
            >
                <path d="M6 3v18"></path>
                <path d="M10 3v7a4 4 0 0 1-8 0V3"></path>
                <path d="M18 3v18"></path>
                <path d="M18 3c2.5 2.5 3.5 5 3.5 8s-1 5.5-3.5 8"></path>
            </svg>
        </div>

        <aside class="rounded-3xl bg-orange-500 p-8 text-white shadow-xl shadow-orange-200">
            <h2 class="text-3xl font-black tracking-tight">
                Lapar?
            </h2>

            <p class="mt-6 text-base font-bold">
                Pesan makanan yang kamu suka...
            </p>

            <div class="mt-28 border-t-2 border-white/70 pt-5">
                <a
                    href="{{ $actionUrl }}"
                    class="flex h-11 w-full items-center justify-center rounded-full bg-white text-md font-black text-orange-500 transition duration-300 hover:-translate-y-1 hover:bg-orange-50 hover:shadow-lg"
                >
                    Cari Makanan
                </a>
            </div>
        </aside>
    </section>
@endsection
