<div class="flex items-center gap-4 rounded-3xl bg-gray-50 px-6 py-5">
    <span class="h-4 w-4 rounded-full bg-red-500"></span>

    <p class="text-sm font-bold text-gray-900 md:text-base">
        Setiap checkout hanya bisa dilakukan untuk satu kantin
    </p>
</div>

<div class="mt-4 rounded-3xl bg-gray-50 p-5 md:p-8">
    @forelse ($carts as $cart)
        <div class="{{ ! $loop->first ? 'mt-8' : '' }}">
            <div class="mb-4 flex items-center justify-between gap-4 px-2">
                <h2 class="text-base font-bold text-gray-900">
                    {{ $cart->canteen_name }}
                </h2>

                <input
                    type="radio"
                    name="selected_cart"
                    value="{{ $cart->id }}"
                    data-cart-select-radio
                    data-url="{{ route('pelanggan.keranjang', ['cart_id' => $cart->id]) }}"
                    @checked($selectedCart && $selectedCart->id === $cart->id)
                    aria-label="Pilih {{ $cart->canteen_name }} untuk checkout"
                    class="h-5 w-5 cursor-pointer border-gray-300 text-green-600 focus:ring-green-500"
                >
            </div>

            <div class="space-y-4">
                @foreach ($cart->items as $item)
                    <article class="rounded-3xl border border-gray-200 bg-white p-4 shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex gap-4">
                            <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-gray-200">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->name }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-xl font-black leading-tight text-gray-950">
                                            {{ $item->name }}
                                        </h3>

                                        <p class="mt-1 text-sm font-semibold text-gray-600">
                                            {{ $item->canteen_name }}
                                        </p>
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('pelanggan.keranjang.destroy', $item->id) }}"
                                        data-cart-action
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <input
                                            type="hidden"
                                            name="cart_id"
                                            value="{{ $selectedCart?->id }}"
                                        >

                                        <button
                                            type="submit"
                                            aria-label="Hapus {{ $item->name }}"
                                            class="rounded-full p-2 text-gray-700 transition duration-300 hover:bg-red-50 hover:text-red-500"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="h-5 w-5"
                                                aria-hidden="true"
                                            >
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6V4h8v2"></path>
                                                <path d="M19 6l-1 14H6L5 6"></path>
                                                <path d="M10 11v5"></path>
                                                <path d="M14 11v5"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-7 flex items-end justify-between gap-4">
                                    <p class="text-xl font-black text-orange-500">
                                        {{ $item->formatted_price }}
                                    </p>

                                    <div class="flex items-center rounded-full bg-gray-50">
                                        <form
                                            method="POST"
                                            action="{{ route('pelanggan.keranjang.update', $item->id) }}"
                                            data-cart-action
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="cart_id"
                                                value="{{ $selectedCart?->id }}"
                                            >

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="{{ max(1, $item->quantity - 1) }}"
                                            >

                                            <button
                                                type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-40"
                                                @disabled($item->quantity <= 1)
                                            >
                                                -
                                            </button>
                                        </form>

                                        <span class="flex h-9 min-w-8 items-center justify-center text-base font-bold text-gray-950">
                                            {{ $item->quantity }}
                                        </span>

                                        <form
                                            method="POST"
                                            action="{{ route('pelanggan.keranjang.update', $item->id) }}"
                                            data-cart-action
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="cart_id"
                                                value="{{ $selectedCart?->id }}"
                                            >

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="{{ $item->quantity + 1 }}"
                                            >

                                            <button
                                                type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-black text-gray-700 transition duration-300 hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-40"
                                                @disabled($item->quantity >= $item->stock_qty)
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @empty
        <div class="flex min-h-80 items-center justify-center rounded-3xl bg-white p-10 text-center shadow-sm">
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
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.5 13.5a2 2 0 0 0 2 1.5h8.8a2 2 0 0 0 2-1.6L22 7H6"></path>
                    </svg>
                </div>

                <h2 class="mt-6 text-2xl font-black text-gray-950">
                    Keranjang masih kosong
                </h2>

                <p class="mt-3 text-base font-medium text-gray-600">
                    Tambahkan menu terlebih dahulu sebelum checkout.
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
