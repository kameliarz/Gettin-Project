@forelse ($transactions as $transaction)
    <tr class="transition hover:bg-orange-50/40">
        <td class="whitespace-nowrap px-6 py-5 text-sm font-extrabold text-slate-800">
            {{ $transaction->code }}
        </td>

        <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-900">
            {{ $transaction->customer_name }}
        </td>

        <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-700">
            {{ $transaction->canteen_name }}
        </td>

        <td class="whitespace-nowrap px-6 py-5 text-sm font-extrabold text-slate-950">
            {{ $transaction->formatted_total }}
        </td>

        <td class="whitespace-nowrap px-6 py-5">
            <span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-orange-600">
                {{ $transaction->pickup_time }}
            </span>
        </td>

        <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-600">
            {{ $transaction->formatted_date }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-16 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16" />
                    <path d="M4 12h16" />
                    <path d="M4 17h10" />
                </svg>
            </div>

            <p class="mt-4 text-base font-extrabold text-slate-950">
                Belum ada transaksi
            </p>

            <p class="mt-1 text-sm font-medium text-slate-500">
                Coba ubah kata kunci pencarian, bulan, atau tahun.
            </p>
        </td>
    </tr>
@endforelse
