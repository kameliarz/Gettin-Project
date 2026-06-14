@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <section
        id="cart-page"
        x-data="{ confirmCheckout: false }"
        class="mx-auto mt-10 max-w-7xl px-6 lg:px-12"
    >
        <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
            Keranjang
        </h1>

        <div id="cart-alert">
            @if (session('success'))
                <div class="mt-6 rounded-3xl bg-emerald-50 px-6 py-4 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-6 rounded-3xl bg-red-50 px-6 py-4 text-sm font-bold text-red-600 ring-1 ring-red-100">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-5">
            <div id="cart-list-wrapper" class="lg:col-span-3">
                @include('pelanggan.partials.cart-list')
            </div>

            <div id="checkout-panel-wrapper" class="lg:col-span-2">
                @include('pelanggan.partials.checkout-panel')
            </div>
        </div>

        <div
            id="cart-loading"
            class="fixed inset-0 z-40 hidden items-center justify-center bg-black/10"
        >
            <div class="rounded-full bg-white px-6 py-3 text-sm font-black text-orange-500 shadow-xl">
                Memperbarui keranjang...
            </div>
        </div>

        <div
            x-show="confirmCheckout"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 px-6"
        >
            <div
                x-show="confirmCheckout"
                x-transition.scale.origin.center
                x-on:click.outside="confirmCheckout = false"
                class="w-full max-w-md rounded-3xl bg-white p-8 text-center shadow-2xl"
            >
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl text-orange-500">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-20 w-20"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                        />
                    </svg>
                </div>

                <h2 class="mt-6 text-xl font-bold text-gray-950">
                    Apakah Anda yakin ingin checkout?
                </h2>

                <div class="mt-8 grid grid-cols-2 gap-5">
                    <button
                        type="button"
                        x-on:click="confirmCheckout = false"
                        class="h-14 rounded-2xl border-2 border-orange-500 text-lg font-black text-orange-500 transition duration-300 hover:bg-orange-50"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        x-on:click="document.getElementById('checkout-form')?.submit()"
                        class="h-14 rounded-2xl bg-orange-500 text-lg font-black text-white transition duration-300 hover:bg-orange-600"
                    >
                        Ya
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cartPage = document.getElementById('cart-page');
            const cartListWrapper = document.getElementById('cart-list-wrapper');
            const checkoutPanelWrapper = document.getElementById('checkout-panel-wrapper');
            const cartAlert = document.getElementById('cart-alert');
            const loading = document.getElementById('cart-loading');

            const showLoading = () => {
                loading.classList.remove('hidden');
                loading.classList.add('flex');

                cartListWrapper.classList.add('opacity-50');
                checkoutPanelWrapper.classList.add('opacity-50');
            };

            const hideLoading = () => {
                loading.classList.add('hidden');
                loading.classList.remove('flex');

                cartListWrapper.classList.remove('opacity-50');
                checkoutPanelWrapper.classList.remove('opacity-50');
            };

            const showAlert = (message, type = 'success') => {
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
            };

            const reinitializeAlpine = () => {
                if (! window.Alpine) {
                    return;
                }

                window.Alpine.initTree(cartListWrapper);
                window.Alpine.initTree(checkoutPanelWrapper);
            };

            const replaceCartHtml = (data) => {
                cartListWrapper.innerHTML = data.cart_list_html;
                checkoutPanelWrapper.innerHTML = data.checkout_panel_html;

                reinitializeAlpine();

                showAlert(data.message, 'success');
            };

            const requestCart = async (url, options = {}) => {
                showLoading();

                try {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            ...(options.headers || {}),
                        },
                    });

                    const data = await response.json();

                    if (! response.ok) {
                        showAlert(data.message || 'Keranjang gagal diperbarui.', 'error');
                        return;
                    }

                    replaceCartHtml(data);
                } catch (error) {
                    console.error(error);
                    showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
                } finally {
                    hideLoading();
                }
            };

            const getCheckedCartId = () => {
                const checkedCartRadio = cartPage.querySelector('[data-cart-select-radio]:checked');

                return checkedCartRadio ? checkedCartRadio.value : null;
            };

            cartPage.addEventListener('submit', (event) => {
                const form = event.target.closest('[data-cart-action]');

                if (! form) {
                    return;
                }

                event.preventDefault();

                const formData = new FormData(form);
                const checkedCartId = getCheckedCartId();

                if (checkedCartId) {
                    formData.set('cart_id', checkedCartId);
                }

                requestCart(form.action, {
                    method: 'POST',
                    body: formData,
                });
            });

            cartPage.addEventListener('change', (event) => {
                const radio = event.target.closest('[data-cart-select-radio]');

                if (! radio) {
                    return;
                }

                requestCart(radio.dataset.url, {
                    method: 'GET',
                });

                window.history.pushState({}, '', radio.dataset.url);
            });

            window.addEventListener('popstate', () => {
                requestCart(window.location.href, {
                    method: 'GET',
                });
            });
        });
    </script>
@endsection
