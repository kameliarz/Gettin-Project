@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <section
        x-data="{
            qrisOpen: false,
            qrisOrder: '',
            qrisTotal: '',
            qrisImage: '',
        }"
        class="mx-auto mt-10 max-w-7xl px-6 lg:px-12"
    >
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
                Riwayat Pesanan
            </h1>

            <form method="GET" action="{{ route('pelanggan.riwayat-pemesanan') }}" class="flex flex-col gap-3 sm:flex-row">
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 bg-white text-sm font-medium text-gray-700 focus:border-orange-500 focus:ring-orange-500"
                >
                    <option value="">Status</option>

                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    name="month"
                    onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 bg-white text-sm font-medium text-gray-700 focus:border-orange-500 focus:ring-orange-500"
                >
                    <option value="">Bulan Tahun</option>

                    @foreach ($monthOptions as $month)
                        <option value="{{ $month['value'] }}" @selected($selectedMonth === $month['value'])>
                            {{ $month['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mt-16">
            @forelse ($orders as $order)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <div class="relative hidden lg:col-span-1 lg:flex lg:justify-center">
                        <div class="absolute left-1/2 top-0 h-full w-1 -translate-x-1/2 bg-gray-300"></div>

                        <span class="relative z-10 flex h-10 w-10 self-center rounded-full bg-orange-100">
                            <span class="m-auto h-7 w-7 rounded-full {{ $order->timeline_color }}"></span>
                        </span>
                    </div>

                    <article class="mb-8 rounded-3xl border border-gray-200 bg-white p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:shadow-2xl lg:col-span-11">
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                            <div class="lg:col-span-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-black {{ $order->status_style }}">
                                        {{ $order->status_label }}
                                    </span>

                                    <span class="text-base font-semibold text-gray-800">
                                        {{ $order->code }}
                                    </span>
                                </div>

                                <h2 class="mt-6 text-2xl font-black text-gray-950">
                                    {{ $order->canteen_name }}
                                </h2>

                                <div class="mt-3 space-y-1 text-base font-medium text-gray-900">
                                    @foreach ($order->items as $item)
                                        <p>
                                            {{ $item->quantity }}x {{ $item->name }}
                                        </p>
                                    @endforeach
                                </div>

                                <p class="mt-8 text-3xl font-black {{ $order->status === 'selesai' ? 'text-stone-700' : 'text-orange-600' }}">
                                    {{ $order->formatted_total }}
                                </p>
                            </div>

                            <div class="lg:col-span-8">
                                <div class="flex justify-start lg:justify-end">
                                    @if ($order->status === 'selesai')
                                        <div class="text-left lg:text-right">
                                            <p class="text-base font-medium text-gray-800">
                                                {{ $order->formatted_date }}
                                            </p>

                                            <p class="mt-2 text-3xl font-semibold text-gray-950">
                                                {{ $order->pickup_time !== '-' ? explode(' ~ ', $order->pickup_time)[0] : '-' }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="text-left lg:text-right">
                                            <p class="text-base font-bold text-gray-800">
                                                Waktu Pengambilan
                                            </p>

                                            <p class="mt-1 text-3xl font-semibold text-gray-950">
                                                {{ $order->pickup_time }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if ($order->status !== 'selesai')
                                    <div class="mt-10">
                                        <div class="flex items-center justify-between text-base font-medium text-gray-900">
                                            <span class="text-left">Pending</span>
                                            <span class="text-center">Diproses</span>
                                            <span class="text-center">Siap</span>
                                            <span class="text-right">Selesai</span>
                                        </div>

                                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-gray-300">
                                            <div
                                                class="h-full rounded-full bg-orange-500 transition-all duration-500"
                                                style="width: {{ $order->progress_percent }}%"
                                            ></div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button
                                            type="button"
                                            x-on:click="
                                                qrisOpen = true;
                                                qrisOrder = @js($order->code);
                                                qrisTotal = @js($order->formatted_total);
                                                qrisImage = @js($order->qris_image_url);
                                            "
                                            class="inline-flex h-12 min-w-40 items-center justify-center rounded-full bg-orange-500 px-10 text-lg font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600"
                                        >
                                            QRIS
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="flex min-h-96 items-center justify-center rounded-3xl border border-gray-200 bg-white p-10 text-center shadow-sm">
                    <div>
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-50 text-orange-500">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="h-9 w-9"
                                aria-hidden="true"
                            >
                                <path d="M8 7h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 17h5"></path>
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                            </svg>
                        </div>

                        <h2 class="mt-6 text-2xl font-black text-gray-950">
                            Riwayat pesanan belum ada
                        </h2>

                        <p class="mt-3 text-base font-medium text-gray-600">
                            Pesanan yang sudah dibuat akan tampil di halaman ini.
                        </p>

                        <a
                            href="{{ route('pelanggan.menu') }}"
                            class="mt-7 inline-flex rounded-full bg-orange-500 px-10 py-3 text-sm font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600"
                        >
                            Cari Makanan
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div
            x-show="qrisOpen"
            x-transition.opacity
            x-cloak
            x-on:click.self="qrisOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 px-6"
        >
            <div class="text-center">
                <div
                    x-show="qrisOpen"
                    x-transition.scale.origin.center
                    class="w-full max-w-md rounded-3xl bg-white px-8 pb-8 pt-5 text-center shadow-2xl"
                >
                    <h2 class="text-2xl font-black text-gray-950">
                        QRIS
                    </h2>

                    <p class="mt-1 text-sm font-semibold text-gray-500">
                        <span x-text="qrisOrder"></span>
                        <span> • </span>
                        <span x-text="qrisTotal"></span>
                    </p>

                    <div class="mx-auto mt-4 flex h-80 w-80 items-center justify-center rounded-2xl bg-gray-100 p-2">
                        <template x-if="qrisImage">
                            <img
                                :src="qrisImage"
                                alt="QRIS Pembayaran"
                                class="h-full w-full rounded-xl object-contain"
                            >
                        </template>

                        <template x-if="!qrisImage">
                            <div class="flex h-full w-full items-center justify-center rounded-xl border border-dashed border-gray-300 text-center text-sm font-bold text-gray-400">
                                QRIS belum tersedia
                            </div>
                        </template>
                    </div>

                    <a
                        :href="qrisImage || '#'"
                        download="qris-gettin.png"
                        class="mt-6 inline-flex h-12 w-full items-center justify-center rounded-full bg-orange-500 text-base font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600"
                        :class="!qrisImage ? 'pointer-events-none opacity-50' : ''"
                    >
                        Unduh QR Code
                    </a>

                    <div class="mx-auto mt-5 flex max-w-xs gap-3 border-l-4 border-orange-500 pl-3 text-left text-sm font-medium text-gray-900">
                        <p>
                            Harap tunjukkan bukti pembayaran secara langsung kepada penjual
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    x-on:click="qrisOpen = false"
                    class="mt-4 text-sm font-bold text-white transition hover:text-orange-200"
                >
                    Klik untuk keluar
                </button>
            </div>
        </div>
    </section>
@endsection
