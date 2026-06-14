@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    @php
        $manualMenuPayload = $manualMenus->map(fn ($menu) => [
            'id' => (int) $menu->id,
            'name' => $menu->name,
            'price' => (int) $menu->price,
            'formatted_price' => $menu->formatted_price,
            'stock_qty' => (int) $menu->stock_qty,
            'image_url' => $menu->image_url,
        ])->values();

        $pickupSlotPayload = $pickupSlots->map(fn ($slot) => [
            'id' => (int) $slot->id,
            'formatted_time' => $slot->formatted_time,
        ])->values();

        $orderStatusPayload = $orderGroups
            ->flatMap(fn ($group) => $group->orders)
            ->mapWithKeys(fn ($order) => [
                (int) $order->id => $order->status,
            ]);
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

   <main
        x-data="sellerDashboard({
            menus: @js($manualMenuPayload),
            pickupSlots: @js($pickupSlotPayload),
            orderStatuses: @js($orderStatusPayload),
            statusCounts: {
                active: {{ $stats['diproses'] + $stats['siap_ambil'] }},
                diproses: {{ $stats['diproses'] }},
                siap_ambil: {{ $stats['siap_ambil'] }},
                selesai: {{ $stats['selesai'] }}
            },
            storeUrl: '{{ route('penjual.dashboard.manual.store') }}',
            updateUrlTemplate: '{{ route('penjual.dashboard.manual.update', ['order' => '__ORDER__']) }}',
            updateStatusUrlTemplate: '{{ route('penjual.dashboard.orders.status', ['order' => '__ORDER__']) }}'
        })"
        class="mx-auto max-w-7xl px-6 pt-10 pb-32 lg:px-12"
    >
        <section>
            <h1 class="text-3xl font-black tracking-tight text-gray-950">
                Dashboard Penjual
            </h1>

            <p class="mt-2 text-4xl font-medium tracking-tight text-gray-950 md:text-5xl">
                Halo, {{ $canteen->name }}! <span aria-hidden="true">👋</span>
            </p>
        </section>

        @if (session('success'))
            <div class="mt-8 rounded-3xl border border-green-200 bg-green-50 px-6 py-4 text-sm font-bold text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-8 rounded-3xl border border-red-200 bg-red-50 px-6 py-4 text-sm font-bold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-8 rounded-3xl border border-red-200 bg-red-50 px-6 py-4 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="mt-14 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <button
                type="button"
                x-on:click="setStatusFilter('diproses')"
                x-bind:class="isFilterActive('diproses') ? 'ring-4 ring-orange-200 scale-[1.02]' : 'hover:-translate-y-1'"
                class="relative overflow-hidden rounded-3xl border border-gray-100 bg-white px-6 py-6 text-left shadow-sm transition duration-300"
            >
                <div class="absolute -right-6 -top-6 h-20 w-20 "></div>

                <p class="relative text-xs font-black uppercase text-gray-950">
                    Pesanan Diproses
                </p>

                <p class="relative mt-4 text-4xl font-black text-orange-900" x-text="formatTwoDigits(statusCounts.diproses)">
                    {{ str_pad($stats['diproses'], 2, '0', STR_PAD_LEFT) }}
                </p>

                <p class="relative mt-3 text-xs font-bold text-orange-600">
                    Klik untuk filter
                </p>
            </button>

            <button
                type="button"
                x-on:click="setStatusFilter('siap_ambil')"
                x-bind:class="isFilterActive('siap_ambil') ? 'ring-4 ring-green-200 scale-[1.02]' : 'hover:-translate-y-1'"
                class="rounded-3xl border border-gray-100 bg-white px-6 py-6 text-left shadow-sm transition duration-300"
            >
                <p class="text-xs font-black uppercase text-gray-950">
                    Siap Diambil
                </p>

                <p class="mt-4 text-4xl font-black text-gray-950" x-text="formatTwoDigits(statusCounts.siap_ambil)">
                    {{ str_pad($stats['siap_ambil'], 2, '0', STR_PAD_LEFT) }}
                </p>

                <p class="mt-3 text-xs font-bold text-gray-500">
                    {{ $readyWarningCount }} pesanan belum diambil &gt;15m
                </p>
            </button>

            <button
                type="button"
                x-on:click="setStatusFilter('selesai')"
                x-bind:class="isFilterActive('selesai') ? 'ring-4 ring-gray-300 scale-[1.02]' : 'hover:-translate-y-1'"
                class="rounded-3xl border border-gray-100 bg-white px-6 py-6 text-left shadow-sm transition duration-300"
            >
                <p class="text-xs font-black uppercase text-gray-950">
                    Pesanan Selesai
                </p>

                <p class="mt-4 text-4xl font-black text-gray-950" x-text="formatTwoDigits(statusCounts.selesai)">
                    {{ str_pad($stats['selesai'], 2, '0', STR_PAD_LEFT) }}
                </p>

                <p class="mt-3 text-xs font-bold text-gray-500">
                    Klik untuk lihat selesai
                </p>
            </button>
            <a
                href="{{ route('penjual.menu', ['stok_maksimal' => 5]) }}"
                class="block rounded-3xl border border-red-100 bg-red-50 px-6 py-6 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:ring-4 hover:ring-red-200"
            >
                <p class="text-xs font-black uppercase text-red-700">
                    Stok Menipis
                </p>

                <p class="mt-4 text-4xl font-black text-red-700">
                    {{ str_pad($stats['stok_menipis'] ?? 0, 2, '0', STR_PAD_LEFT) }}
                </p>

                <p class="mt-3 text-xs font-bold text-red-600">
                    Klik untuk lihat menu (Stok ≤ 5)
                </p>
            </a>
        </section>

        <div class="mt-6 flex flex-col gap-3 rounded-3xl border border-gray-200 bg-white px-5 py-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <p class="text-sm font-bold text-gray-700">
                Filter antrean:
                <span class="text-orange-600" x-text="selectedFilterLabel"></span>
            </p>

            <button
                type="button"
                x-on:click="setStatusFilter('active')"
                class="inline-flex h-10 items-center justify-center rounded-full bg-gray-100 px-5 text-sm font-black text-gray-800 transition hover:bg-gray-200"
            >
                Tampilkan Antrean Aktif
            </button>
        </div>

        <section class="mt-16">
            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <h2 class="text-3xl font-black tracking-tight text-gray-950">
                    Antrian
                </h2>

                <button
                    type="button"
                    x-on:click="openCreateModal()"
                    class="inline-flex h-12 items-center justify-center rounded-full bg-neutral-800 px-8 text-base font-black text-white shadow-lg transition duration-300 hover:-translate-y-1 hover:bg-neutral-950"
                >
                    + Tambah Pesanan Manual
                </button>
            </div>

            <div class="mt-7 space-y-10">
                @forelse ($orderGroups as $group)
                    <section>
                        <div class="mb-4 flex items-center gap-4">
                            <div class="inline-flex h-8 items-center rounded-full bg-orange-700 px-4 text-xs font-black text-white">
                                {{ $group->pickup_time }}
                            </div>

                            <div class="h-px flex-1 bg-gray-200"></div>

                            <span class="text-xs font-black text-gray-500">
                                {{ $group->order_count }} Pesanan
                            </span>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($group->orders as $order)
                                {{-- Ditambahkan x-show & x-transition untuk fitur filter card --}}
                                <article
                                    x-show="isOrderVisible({{ $order->id }})"
                                    x-transition
                                    class="rounded-3xl border border-gray-200 bg-white p-5 shadow-lg"
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('penjual.dashboard.orders.status', $order->id) }}"
                                        class="flex h-full min-h-[255px] flex-col"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 class="text-xl font-black leading-tight text-gray-950">
                                                    {{ $order->customer_name }}
                                                </h3>

                                                <p class="text-base font-semibold leading-tight text-gray-700">
                                                    {{ $order->code }}
                                                </p>
                                            </div>

                                            <select
                                                name="status"
                                                class="rounded-xl border-0 {{ $order->status_classes }} px-4 py-2 text-sm font-black focus:ring-2 focus:ring-orange-500"
                                            >
                                                @foreach ($statusOptions as $statusValue => $statusLabel)
                                                    <option value="{{ $statusValue }}" @selected($order->status === $statusValue)>
                                                        {{ $statusLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mt-4 min-h-20 space-y-1 text-base font-medium leading-snug text-gray-950">
                                            @foreach ($order->items as $item)
                                                <p>{{ $item->quantity }}x {{ $item->name }}</p>
                                            @endforeach
                                        </div>

                                        @if ($order->note_text)
                                            <div class="mt-5 rounded-xl bg-orange-50 px-4 py-3 text-sm font-medium text-red-900">
                                                {{ $order->note_text }}
                                            </div>
                                        @endif

                                        <div class="mt-auto pt-5">
                                            @if ($order->can_edit_manual)
                                                <button
                                                    type="button"
                                                    x-on:click='openEditModal(@json($order->manual_payload))'
                                                    class="mb-3 flex h-11 w-full items-center justify-center rounded-full bg-gray-200 text-base font-black text-gray-950 transition duration-300 hover:bg-gray-300"
                                                >
                                                    Edit Pesanan
                                                </button>
                                            @endif

                                            <button
                                                type="submit"
                                                class="flex h-12 w-full items-center justify-center rounded-full bg-orange-500 text-base font-black text-white shadow-md shadow-orange-200 transition duration-300 hover:bg-orange-600"
                                            >
                                                Ubah Status
                                            </button>
                                        </div>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 px-6 py-14 text-center">
                        <h3 class="text-2xl font-black text-gray-950">
                            Belum ada antrean aktif hari ini.
                        </h3>

                        <p class="mt-2 text-base font-medium text-gray-600">
                            Pesanan dengan status diproses atau siap diambil akan muncul di sini.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Modal Pesanan Manual --}}
        <div
            x-cloak
            x-show="modalOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-10"
        >
            <div
                x-on:click.outside="closeModal()"
                class="flex max-h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl"
            >
                <div class="rounded-t-[2.5rem] bg-neutral-900 px-8 py-8 text-white md:px-12">
                    <h2 class="text-3xl font-black">
                        Pesanan Manual
                    </h2>
                </div>

                <form method="POST" x-bind:action="formAction" class="flex min-h-0 flex-1 flex-col">
                    @csrf

                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 md:px-12">
                        <template x-if="menus.length === 0">
                            <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center text-base font-bold text-gray-600">
                                Belum ada menu untuk kantin ini.
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="menu in menus" :key="menu.id">
                                <article class="grid grid-cols-[6rem_1fr_auto] items-center gap-4 rounded-3xl border border-gray-200 bg-white p-4 shadow-md">
                                    <img
                                        x-bind:src="menu.image_url"
                                        x-bind:alt="menu.name"
                                        class="h-24 w-24 rounded-xl object-cover"
                                    >

                                    <div>
                                        <h3 class="text-xl font-black text-gray-950" x-text="menu.name"></h3>

                                        <p class="mt-1 text-base font-semibold text-gray-950">
                                            {{ $canteen->name }}
                                        </p>

                                        <p
                                            class="mt-6 text-lg font-black text-orange-500"
                                            x-text="formatRupiah(menu.price)"
                                        ></p>
                                    </div>

                                    <div class="flex min-w-36 flex-col items-end gap-7">
                                        <p class="text-base font-semibold text-gray-950" x-text="'Stok : ' + availableStock(menu)"></p>

                                        <div class="flex h-12 items-center gap-4 rounded-full bg-gray-200 px-4">
                                            <button
                                                type="button"
                                                x-on:click="decrease(menu.id)"
                                                class="text-xl font-black text-gray-950 disabled:text-gray-400"
                                                x-bind:disabled="quantity(menu.id) <= 0"
                                            >
                                                -
                                            </button>

                                            <span
                                                class="min-w-6 text-center text-2xl font-medium text-gray-950"
                                                x-text="quantity(menu.id)"
                                            ></span>

                                            <button
                                                type="button"
                                                x-on:click="increase(menu.id)"
                                                class="text-xl font-black text-gray-950 disabled:text-gray-400"
                                                x-bind:disabled="quantity(menu.id) >= availableStock(menu)"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </div>

                    <div class="rounded-b-[2.5rem] bg-white/95 px-6 py-6 shadow-[0_-12px_30px_rgba(0,0,0,0.08)] md:px-12">
                        <template x-for="menu in menus" :key="'input-' + menu.id">
                            <input
                                type="hidden"
                                x-bind:name="`items[${menu.id}]`"
                                x-bind:value="quantity(menu.id)"
                            >
                        </template>

                        <div class="grid gap-5 md:grid-cols-[1fr_auto] md:items-end">
                            <div>
                                <div class="flex flex-wrap items-center gap-x-8 gap-y-2">
                                    <p class="text-xl font-black text-gray-950">
                                        Total (<span x-text="itemCount"></span> item)
                                    </p>

                                    <p class="text-3xl font-black text-orange-500" x-text="formatRupiah(total)"></p>
                                </div>

                                <label for="manual-pickup-slot" class="mt-4 block text-sm font-bold text-gray-950">
                                    Pilih Waktu Pengambilan
                                </label>

                                <select
                                    id="manual-pickup-slot"
                                    name="canteen_pickup_slot_id"
                                    x-model="selectedPickupSlotId"
                                    required
                                    class="mt-2 h-12 w-full rounded-xl border-gray-300 bg-gray-50 px-4 text-lg font-medium text-gray-950 focus:border-orange-500 focus:ring-orange-500 md:w-80"
                                >
                                    <template x-for="slot in pickupSlots" :key="slot.id">
                                        <option x-bind:value="slot.id" x-text="slot.formatted_time"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="flex flex-col gap-3 md:w-56">
                                <button
                                    type="button"
                                    x-on:click="closeModal()"
                                    class="flex h-12 items-center justify-center rounded-full border border-gray-300 bg-white text-base font-black text-gray-700 transition duration-300 hover:bg-gray-100"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    x-bind:disabled="itemCount < 1 || ! selectedPickupSlotId"
                                    class="flex h-12 items-center justify-center rounded-full bg-neutral-900 text-base font-black text-white shadow-md transition duration-300 hover:bg-neutral-950 disabled:cursor-not-allowed disabled:opacity-50"
                                    x-text="mode === 'edit' ? 'Ubah' : 'Tambahkan'"
                                ></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function sellerDashboard(config) {
            return {
                // Binding data config ke Alpine state
                menus: config.menus,
                pickupSlots: config.pickupSlots,
                orderStatuses: config.orderStatuses || {},
                statusCounts: config.statusCounts || { active: 0, diproses: 0, siap_ambil: 0, selesai: 0 },

                // State filter (Default diset ke 'active' sesuai tombol bawaanmu)
                currentFilter: 'active',

                storeUrl: config.storeUrl,
                updateUrlTemplate: config.updateUrlTemplate,

                modalOpen: false,
                mode: 'create',
                formAction: config.storeUrl,
                selectedPickupSlotId: config.pickupSlots.length ? String(config.pickupSlots[0].id) : '',

                quantities: {},
                reservedQuantities: {},

                // Fungsi filter & Helper format angka
                formatTwoDigits(value) {
                    return String(value).padStart(2, '0');
                },

                setStatusFilter(filter) {
                    this.currentFilter = filter;
                },

                isFilterActive(filter) {
                    return this.currentFilter === filter;
                },

                get selectedFilterLabel() {
                    const labels = {
                        active: 'Antrean Aktif',
                        diproses: 'Pesanan Diproses',
                        siap_ambil: 'Siap Diambil',
                        selesai: 'Pesanan Selesai'
                    };
                    return labels[this.currentFilter] || 'Semua';
                },

                isOrderVisible(orderId) {
                    const status = this.orderStatuses[orderId];
                    if (this.currentFilter === 'active') {
                        return status === 'diproses' || status === 'siap_ambil';
                    }
                    return status === this.currentFilter;
                },

                // Fungsi Modal & Logic Keranjang Manual
                openCreateModal() {
                    this.mode = 'create';
                    this.formAction = this.storeUrl;
                    this.quantities = {};
                    this.reservedQuantities = {};
                    this.selectedPickupSlotId = this.pickupSlots.length ? String(this.pickupSlots[0].id) : '';
                    this.modalOpen = true;
                },

                openEditModal(order) {
                    this.mode = 'edit';
                    this.formAction = this.updateUrlTemplate.replace('__ORDER__', order.id);
                    this.quantities = {};
                    this.reservedQuantities = {};
                    this.selectedPickupSlotId = String(order.pickup_slot_id ?? '');

                    order.items.forEach((item) => {
                        this.quantities[item.menu_id] = Number(item.quantity);
                        this.reservedQuantities[item.menu_id] = Number(item.quantity);
                    });

                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                },

                quantity(menuId) {
                    return Number(this.quantities[menuId] ?? 0);
                },

                availableStock(menu) {
                    return Number(menu.stock_qty) + Number(this.reservedQuantities[menu.id] ?? 0);
                },

                increase(menuId) {
                    const menu = this.menus.find((item) => Number(item.id) === Number(menuId));

                    if (! menu) {
                        return;
                    }

                    const nextQuantity = this.quantity(menuId) + 1;

                    if (nextQuantity <= this.availableStock(menu)) {
                        this.quantities[menuId] = nextQuantity;
                    }
                },

                decrease(menuId) {
                    this.quantities[menuId] = Math.max(this.quantity(menuId) - 1, 0);
                },

                get itemCount() {
                    return Object.values(this.quantities)
                        .reduce((total, quantity) => total + Number(quantity), 0);
                },

                get total() {
                    return this.menus.reduce((total, menu) => {
                        return total + (Number(menu.price) * this.quantity(menu.id));
                    }, 0);
                },

                formatRupiah(value) {
                    return 'Rp ' + Number(value).toLocaleString('id-ID');
                }
            };
        }
    </script>
@endsection
