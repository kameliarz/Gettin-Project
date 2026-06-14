<div class="flex flex-col gap-4 border-t border-slate-100 bg-slate-50 px-6 py-5 md:flex-row md:items-center md:justify-between">
    <p class="text-sm font-semibold text-slate-500">
        @if ($transactions->total() > 0)
            Menampilkan {{ $transactions->firstItem() }} sampai {{ $transactions->lastItem() }}
            dari {{ number_format($transactions->total(), 0, ',', '.') }} transaksi
        @else
            Menampilkan 0 transaksi
        @endif
    </p>

    @if ($transactions->lastPage() > 1)
        <div class="flex items-center gap-2">
            @if ($transactions->onFirstPage())
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                    ‹
                </span>
            @else
                <a
                    href="{{ $transactions->previousPageUrl() }}"
                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-white"
                >
                    ‹
                </a>
            @endif

            @php
                $startPage = max(1, $transactions->currentPage() - 1);
                $endPage = min($transactions->lastPage(), $transactions->currentPage() + 1);
            @endphp

            @for ($page = $startPage; $page <= $endPage; $page++)
                <a
                    href="{{ $transactions->url($page) }}"
                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-extrabold transition
                        {{ $transactions->currentPage() === $page ? 'bg-orange-500 text-white' : 'text-slate-600 hover:bg-white' }}"
                >
                    {{ $page }}
                </a>
            @endfor

            @if ($transactions->hasMorePages())
                <a
                    href="{{ $transactions->nextPageUrl() }}"
                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-white"
                >
                    ›
                </a>
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                    ›
                </span>
            @endif
        </div>
    @endif
</div>
