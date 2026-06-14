<form
    id="checkout-form"
    x-ref="checkoutForm"
    method="POST"
    action="{{ route('pelanggan.checkout.store') }}"
    class="space-y-6"
>
    @csrf

    @if ($selectedCart)
        <input type="hidden" name="cart_id" value="{{ $selectedCart->id }}">
    @endif

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl">
        <div class="flex items-center gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <h2 class="text-2xl font-black text-gray-950">
                Informasi Pengambilan
            </h2>
        </div>

        <label for="pickup-slot" class="mt-4 block text-sm font-bold text-gray-900">
            Pilih Waktu Pengambilan
        </label>

        <select
            id="pickup-slot"
            name="canteen_pickup_slot_id"
            class="mt-2 w-full rounded-2xl border-gray-300 bg-gray-50 px-4 py-3 text-lg font-bold text-gray-900 focus:border-orange-500 focus:ring-orange-500"
            @disabled(! $selectedCart || $pickupSlots->isEmpty())
            required
        >
            @forelse ($pickupSlots as $slot)
                <option value="{{ $slot->id }}">
                    {{ $slot->formatted_time }}
                </option>
            @empty
                <option value="">
                    Tidak ada slot tersedia
                </option>
            @endforelse
        </select>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl">
        <div class="flex items-center gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>

            <h2 class="text-2xl font-black text-gray-950">
                Ringkasan Pesanan
            </h2>
        </div>

        <div class="mt-7 space-y-4">
            <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950">
                <span>
                    Subtotal
                    @if ($selectedCart)
                        ({{ $selectedCart->item_count }} item)
                    @else
                        (0 item)
                    @endif
                </span>

                <span>{{ $formattedSubtotal }}</span>
            </div>

            <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950">
                <span>Biaya Layanan</span>
                <span>{{ $formattedServiceFee }}</span>
            </div>
        </div>

        <div class="my-7 border-t border-gray-200"></div>

        <div class="flex items-center justify-between gap-4">
            <h3 class="text-2xl font-black text-gray-950">
                Total Pembayaran
            </h3>

            <p class="text-2xl font-black text-orange-600">
                {{ $formattedTotal }}
            </p>
        </div>

        <button
            type="button"
            x-on:click="confirmCheckout = true"
            class="mt-8 flex h-14 w-full items-center justify-center rounded-full bg-orange-500 text-lg font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50"
            @disabled(! $selectedCart || $pickupSlots->isEmpty())
        >
            Checkout
        </button>

        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4 text-center text-sm font-medium text-gray-700">
            Metode pembayaran - hanya tersedia QRIS & Cash
        </div>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-xl">
        <label for="note" class="block text-base font-black text-gray-950">
            Tambahkan catatan (opsional)
        </label>

        <textarea
            id="note"
            name="note"
            rows="3"
            placeholder="Ketikkan catatan Anda..."
            class="mt-2 w-full resize-none rounded-2xl border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:border-orange-500 focus:ring-orange-500"
        >{{ old('note') }}</textarea>
    </div>
</form>
