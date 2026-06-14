@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .seller-menu-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 40px 24px 128px;
        }

        .seller-menu-layout {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 30px;
            align-items: start;
            margin-top: 36px;
        }

        .seller-menu-card {
            background: #ffffff;
            border: 1px solid #d7d7d7;
            border-radius: 32px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.13);
        }

        .seller-menu-input {
            width: 100%;
            height: 42px;
            border: 1px solid #cfd4dc;
            border-radius: 8px;
            padding: 0 14px;
            font-size: 15px;
            font-weight: 500;
            color: #374151;
            background: #ffffff;
        }

        .seller-menu-input:focus {
            outline: none;
            border-color: #ff7300;
            box-shadow: 0 0 0 3px rgba(255, 115, 0, 0.15);
        }

        .seller-menu-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .seller-menu-field {
            margin-top: 14px;
        }

        .seller-menu-image-box {
            margin-top: 8px;
            height: 205px;
            width: 100%;
            border: 2px dashed #555555;
            border-radius: 8px;
            background: #e5e5e5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .seller-menu-image-box:hover {
            border-color: #ff7300;
            background: #fff3ea;
        }

        .seller-menu-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .seller-menu-table-wrap {
            margin-top: 24px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            border-radius: 18px;
            background: #ffffff;
        }

        .seller-menu-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            text-align: left;
            font-size: 13px;
            background: #ffffff;
        }

        .seller-menu-table thead th {
            background: #ffffff;
            color: #111827;
            font-weight: 900;
            padding: 14px 10px;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .seller-menu-table thead th:last-child {
            border-right: none;
        }

        .seller-menu-table tbody td {
            padding: 14px 10px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            color: #111827;
            font-weight: 700;
            vertical-align: middle;
        }

        .seller-menu-table tbody td:last-child {
            border-right: none;
        }

        .seller-menu-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .seller-menu-table tbody tr:nth-child(even) {
            background: #fff7ed;
        }

        .seller-menu-table tbody tr:hover {
            background: #ffedd5;
        }

        .seller-menu-table tbody tr.is-selected {
            background: #ffe4c7;
            box-shadow: inset 5px 0 0 #ff7300;
        }

        .seller-menu-table tbody tr:last-child td {
            border-bottom: none;
        }

        .seller-menu-table th:nth-child(1),
        .seller-menu-table td:nth-child(1) {
            width: 13%;
        }

        .seller-menu-table th:nth-child(2),
        .seller-menu-table td:nth-child(2) {
            width: 23%;
        }

        .seller-menu-table th:nth-child(3),
        .seller-menu-table td:nth-child(3) {
            width: 15%;
        }

        .seller-menu-table th:nth-child(4),
        .seller-menu-table td:nth-child(4) {
            width: 15%;
        }

        .seller-menu-table th:nth-child(5),
        .seller-menu-table td:nth-child(5) {
            width: 10%;
            text-align: center;
        }

        .seller-menu-table th:nth-child(6),
        .seller-menu-table td:nth-child(6) {
            width: 11%;
            text-align: center;
        }

        .seller-menu-table th:nth-child(7),
        .seller-menu-table td:nth-child(7) {
            width: 13%;
            text-align: center;
        }

        .menu-plain-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .menu-name-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 900;
            color: #111827;
        }

        .menu-price {
            color: #ff7300;
            font-weight: 900;
            white-space: nowrap;
        }

        .menu-stock-value {
            font-weight: 900;
            color: #111827;
        }

        .menu-popular-check {
            display: inline-flex;
            height: 24px;
            width: 24px;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #ff7300;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
        }

        .menu-popular-empty {
            display: inline-flex;
            height: 24px;
            width: 24px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: #ffffff;
        }

        .menu-detail-button {
            display: inline-flex;
            height: 32px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #111827;
            padding: 0 13px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            transition: 0.2s ease;
            white-space: nowrap;
        }

        .menu-detail-button:hover {
            background: #ff7300;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .seller-menu-layout {
                grid-template-columns: 1fr;
            }

            .seller-menu-table {
                font-size: 12px;
            }

            .seller-menu-table thead th,
            .seller-menu-table tbody td {
                padding: 12px 8px;
            }
        }
    </style>

    <section
        x-data="penjualMenuPage({
            menus: @js($menus),
            categories: @js($categories),
            query: @js($query),
            dataUrl: '{{ route('penjual.menu.data') }}',
            showUrlTemplate: '{{ route('penjual.menu.show', ['menu' => '__MENU__']) }}',
            storeUrl: '{{ route('penjual.menu.store') }}',
            updateUrlTemplate: '{{ route('penjual.menu.update', ['menu' => '__MENU__']) }}',
            deleteUrlTemplate: '{{ route('penjual.menu.destroy', ['menu' => '__MENU__']) }}'
        })"
        class="seller-menu-page"
    >
        <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
            Kelola Menu
        </h1>

        <div
            x-cloak
            x-show="successMessage"
            x-transition
            x-text="successMessage"
            class="mt-8 rounded-3xl border border-green-200 bg-green-50 px-6 py-4 text-sm font-bold text-green-700"
        ></div>

        <div
            x-cloak
            x-show="errorMessage"
            x-transition
            x-text="errorMessage"
            class="mt-8 rounded-3xl border border-red-200 bg-red-50 px-6 py-4 text-sm font-bold text-red-700"
        ></div>

        <div class="seller-menu-layout">
            <aside class="seller-menu-card">
                <h2 class="text-xl font-black text-gray-950" x-text="isEdit ? 'Detail Menu' : 'Tambah Menu'"></h2>

                <form x-on:submit.prevent="submitForm()" class="mt-5">
                    <div class="seller-menu-field">
                        <label for="name" class="seller-menu-label">
                            Nama Menu
                        </label>

                        <input
                            id="name"
                            type="text"
                            x-model="form.name"
                            placeholder="Nasi ayam geprek"
                            required
                            class="seller-menu-input"
                        >
                    </div>

                    <div class="seller-menu-field">
                        <label for="category_id" class="seller-menu-label">
                            Kategori
                        </label>

                        <select
                            id="category_id"
                            x-model="form.category_id"
                            required
                            class="seller-menu-input"
                        >
                            <option value="">Pilih kategori</option>

                            <template x-for="category in categories" :key="category.id">
                                <option x-bind:value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="seller-menu-field">
                        <label for="price" class="seller-menu-label">
                            Harga
                        </label>

                        <input
                            id="price"
                            type="number"
                            x-model="form.price"
                            placeholder="12000"
                            min="0"
                            required
                            class="seller-menu-input"
                        >
                    </div>

                    <div class="seller-menu-field">
                        <label class="seller-menu-label">
                            Gambar
                        </label>

                        <label for="image" class="seller-menu-image-box">
                            <template x-if="imagePreview">
                                <img
                                    x-bind:src="imagePreview"
                                    alt="Preview gambar menu"
                                >
                            </template>

                            <template x-if="! imagePreview">
                                <div class="px-6 text-center text-sm font-bold text-gray-500">
                                    Klik untuk memilih gambar
                                </div>
                            </template>
                        </label>

                        <input
                            id="image"
                            type="file"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            x-ref="imageInput"
                            x-on:change="previewImage($event)"
                            class="hidden"
                        >

                        <p class="mt-2 text-xs font-medium text-gray-500">
                            Format: JPG, PNG, atau WEBP. Maksimal 2MB.
                        </p>
                    </div>

                    <label class="mt-5 flex cursor-pointer items-center gap-3 text-sm font-bold text-gray-950">
                        <input
                            type="checkbox"
                            x-model="form.is_popular"
                            class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                        >

                        <span>Tampilkan sebagai menu populer</span>
                    </label>

                    <template x-if="isEdit">
                        <div>
                            <div class="mt-7 grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    x-on:click="openDeleteModal()"
                                    class="flex h-11 items-center justify-center rounded-full bg-gray-300 text-base font-black text-gray-950 transition duration-300 hover:bg-red-100 hover:text-red-700"
                                >
                                    Hapus
                                </button>

                                <button
                                    type="button"
                                    x-on:click="resetForm()"
                                    class="flex h-11 items-center justify-center rounded-full bg-gray-300 text-base font-black text-gray-950 transition duration-300 hover:bg-gray-400"
                                >
                                    Batal
                                </button>
                            </div>

                            <button
                                type="submit"
                                x-bind:disabled="isSubmitting"
                                class="mt-3 flex h-12 w-full items-center justify-center rounded-full bg-orange-500 text-base font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                                x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"
                            ></button>
                        </div>
                    </template>

                    <template x-if="! isEdit">
                        <div class="mt-7 grid grid-cols-2 gap-5">
                            <button
                                type="button"
                                x-on:click="resetForm()"
                                class="flex h-11 items-center justify-center rounded-full bg-gray-300 text-base font-black text-gray-950 transition duration-300 hover:bg-gray-400"
                            >
                                Reset
                            </button>

                            <button
                                type="submit"
                                x-bind:disabled="isSubmitting"
                                class="flex h-11 items-center justify-center rounded-full bg-orange-500 text-base font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                                x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"
                            ></button>
                        </div>
                    </template>
                </form>
            </aside>

            <section class="seller-menu-card">
                <div class="flex gap-3">
                    <input
                        type="text"
                        x-model="searchQuery"
                        x-on:input.debounce.500ms="searchMenus()"
                        placeholder="Cari nama menu, kode, atau kategori..."
                        class="seller-menu-input flex-1"
                    >

                    <button
                        type="button"
                        x-on:click="resetSearch()"
                        class="flex h-[42px] items-center justify-center rounded-lg border border-gray-300 px-6 text-base font-bold text-gray-700 transition duration-300 hover:bg-gray-100"
                    >
                        Reset
                    </button>
                </div>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <p class="text-base font-medium text-gray-700">
                        <span x-text="menus.length"></span> menu ditampilkan
                    </p>

                    <p x-cloak x-show="isLoading" class="text-sm font-bold text-orange-600">
                        Memuat data...
                    </p>
                </div>

                <div class="seller-menu-table-wrap">
                    <table class="seller-menu-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Populer</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="menu in menus" :key="menu.id">
                                <tr x-bind:class="Number(form.id) === Number(menu.id) ? 'is-selected' : ''">
                                    <td>
                                        <div class="menu-plain-text" x-text="menu.code"></div>
                                    </td>

                                    <td>
                                        <div class="menu-name-text" x-text="menu.name"></div>
                                    </td>

                                    <td>
                                        <div class="menu-plain-text" x-text="menu.category_name"></div>
                                    </td>

                                    <td>
                                        <span class="menu-price" x-text="menu.formatted_price"></span>
                                    </td>

                                    <td>
                                        <span class="menu-stock-value" x-text="menu.stock_qty"></span>
                                    </td>

                                    <td>
                                        <template x-if="menu.is_popular">
                                            <span class="menu-popular-check">✓</span>
                                        </template>

                                        <template x-if="! menu.is_popular">
                                            <span class="menu-popular-empty"></span>
                                        </template>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            x-on:click="fetchDetail(menu.id)"
                                            x-bind:disabled="loadingDetailId === menu.id"
                                            class="menu-detail-button disabled:cursor-not-allowed disabled:opacity-60"
                                            x-text="loadingDetailId === menu.id ? '...' : 'Detail'"
                                        ></button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="menus.length === 0">
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center font-bold text-gray-500">
                                        Menu belum tersedia.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div
            x-cloak
            x-show="deleteModalOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        >
            <div
                x-on:click.outside="closeDeleteModal()"
                class="w-full max-w-md rounded-[2rem] bg-white px-10 py-8 text-center shadow-2xl"
            >
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border-4 border-orange-500 text-4xl font-black text-orange-500">
                    !
                </div>

                <h2 class="mt-6 text-xl font-black text-gray-950">
                    Apakah Anda yakin ingin menghapus?
                </h2>

                <div class="mt-8 grid grid-cols-2 gap-5">
                    <button
                        type="button"
                        x-on:click="closeDeleteModal()"
                        class="flex h-14 items-center justify-center rounded-2xl border-2 border-orange-500 text-lg font-black text-orange-500 transition duration-300 hover:bg-orange-50"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        x-on:click="confirmDelete()"
                        x-bind:disabled="isDeleting"
                        class="flex h-14 w-full items-center justify-center rounded-2xl bg-orange-500 text-lg font-black text-white transition duration-300 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                        x-text="isDeleting ? 'Menghapus...' : 'Ya'"
                    ></button>
                </div>
            </div>
        </div>
    </section>

    <script>
        function penjualMenuPage(config) {
            return {
                menus: config.menus,
                categories: config.categories,

                dataUrl: config.dataUrl,
                showUrlTemplate: config.showUrlTemplate,
                storeUrl: config.storeUrl,
                updateUrlTemplate: config.updateUrlTemplate,
                deleteUrlTemplate: config.deleteUrlTemplate,

                searchQuery: config.query ?? '',

                form: {
                    id: null,
                    name: '',
                    category_id: '',
                    price: '',
                    is_popular: false,
                },

                imageFile: null,
                imagePreview: null,

                isLoading: false,
                isSubmitting: false,
                isDeleting: false,
                loadingDetailId: null,
                deleteModalOpen: false,

                successMessage: '',
                errorMessage: '',

                get isEdit() {
                    return Boolean(this.form.id);
                },

                csrfToken() {
                    return document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content');
                },

                clearMessages() {
                    this.successMessage = '';
                    this.errorMessage = '';
                },

                showSuccess(message) {
                    this.successMessage = message;

                    setTimeout(() => {
                        this.successMessage = '';
                    }, 2200);
                },

                async parseResponse(response) {
                    const data = await response.json().catch(() => ({
                        message: 'Respons server tidak valid.',
                    }));

                    if (! response.ok) {
                        if (data.errors) {
                            const firstError = Object.values(data.errors).flat()[0];

                            throw new Error(firstError || data.message || 'Validasi gagal.');
                        }

                        throw new Error(data.message || 'Terjadi kesalahan.');
                    }

                    return data;
                },

                async searchMenus() {
                    this.clearMessages();
                    this.isLoading = true;

                    try {
                        const url = new URL(this.dataUrl, window.location.origin);

                        if (this.searchQuery) {
                            url.searchParams.set('q', this.searchQuery);
                        }

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        const data = await this.parseResponse(response);

                        this.menus = data.menus;
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetSearch() {
                    this.searchQuery = '';
                    this.searchMenus();
                },

                async fetchDetail(menuId) {
                    this.clearMessages();
                    this.loadingDetailId = menuId;

                    try {
                        const response = await fetch(
                            this.showUrlTemplate.replace('__MENU__', menuId),
                            {
                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        const data = await this.parseResponse(response);

                        this.fillForm(data.menu);
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.loadingDetailId = null;
                    }
                },

                fillForm(menu) {
                    this.form = {
                        id: menu.id,
                        name: menu.name,
                        category_id: String(menu.category_id),
                        price: menu.price_plain,
                        is_popular: Boolean(menu.is_popular),
                    };

                    this.imageFile = null;
                    this.imagePreview = menu.image_url ?? null;

                    if (this.$refs.imageInput) {
                        this.$refs.imageInput.value = '';
                    }
                },

                resetForm() {
                    this.form = {
                        id: null,
                        name: '',
                        category_id: '',
                        price: '',
                        is_popular: false,
                    };

                    this.imageFile = null;
                    this.imagePreview = null;
                    this.deleteModalOpen = false;

                    if (this.$refs.imageInput) {
                        this.$refs.imageInput.value = '';
                    }

                    this.clearMessages();
                },

                previewImage(event) {
                    const file = event.target.files[0];

                    if (! file) {
                        return;
                    }

                    this.imageFile = file;

                    const reader = new FileReader();

                    reader.onload = (readerEvent) => {
                        this.imagePreview = readerEvent.target.result;
                    };

                    reader.readAsDataURL(file);
                },

                async submitForm() {
                    this.clearMessages();
                    this.isSubmitting = true;

                    try {
                        const formData = new FormData();

                        formData.append('name', this.form.name);
                        formData.append('category_id', this.form.category_id);
                        formData.append('price', this.form.price);
                        formData.append('is_popular', this.form.is_popular ? '1' : '0');

                        if (this.imageFile) {
                            formData.append('image', this.imageFile);
                        }

                        let url = this.storeUrl;

                        if (this.isEdit) {
                            url = this.updateUrlTemplate.replace('__MENU__', this.form.id);
                            formData.append('_method', 'PUT');
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            body: formData,
                        });

                        const data = await this.parseResponse(response);

                        this.fillForm(data.menu);
                        await this.searchMenus();
                        this.showSuccess(data.message);
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                openDeleteModal() {
                    if (! this.form.id) {
                        return;
                    }

                    this.deleteModalOpen = true;
                },

                closeDeleteModal() {
                    this.deleteModalOpen = false;
                },

                async confirmDelete() {
                    if (! this.form.id) {
                        return;
                    }

                    this.clearMessages();
                    this.isDeleting = true;

                    try {
                        const response = await fetch(
                            this.deleteUrlTemplate.replace('__MENU__', this.form.id),
                            {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken(),
                                },
                            }
                        );

                        const data = await this.parseResponse(response);

                        this.menus = this.menus.filter((menu) => Number(menu.id) !== Number(data.deleted_id));
                        this.resetForm();
                        this.closeDeleteModal();
                        this.showSuccess(data.message);
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.isDeleting = false;
                    }
                },
            };
        }
    </script>
@endsection
