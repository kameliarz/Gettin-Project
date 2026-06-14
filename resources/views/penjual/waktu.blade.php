@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .seller-time-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 40px 24px 128px;
        }

        .seller-time-layout {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 30px;
            align-items: start;
            margin-top: 36px;
        }

        .seller-time-card {
            background: #ffffff;
            border: 1px solid #d7d7d7;
            border-radius: 32px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.13);
        }

        .seller-time-input {
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

        .seller-time-input:focus {
            outline: none;
            border-color: #ff7300;
            box-shadow: 0 0 0 3px rgba(255, 115, 0, 0.15);
        }

        .seller-time-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .seller-time-field {
            margin-top: 14px;
        }

        .seller-time-table-wrap {
            margin-top: 24px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            border-radius: 18px;
            background: #ffffff;
        }

        .seller-time-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            text-align: center;
            font-size: 14px;
            background: #ffffff;
        }

        .seller-time-table thead th {
            background: #ffffff;
            color: #111827;
            font-weight: 900;
            padding: 14px 12px;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .seller-time-table thead th:last-child {
            border-right: none;
        }

        .seller-time-table tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            color: #111827;
            font-weight: 700;
            vertical-align: middle;
        }

        .seller-time-table tbody td:last-child {
            border-right: none;
        }

        .seller-time-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .seller-time-table tbody tr:nth-child(even) {
            background: #fff7ed;
        }

        .seller-time-table tbody tr:hover {
            background: #ffedd5;
        }

        .seller-time-table tbody tr.is-selected {
            background: #ffe4c7;
            box-shadow: inset 5px 0 0 #ff7300;
        }

        .seller-time-table tbody tr:last-child td {
            border-bottom: none;
        }

        .seller-time-table th:nth-child(1),
        .seller-time-table td:nth-child(1) {
            width: 13%;
        }

        .seller-time-table th:nth-child(2),
        .seller-time-table td:nth-child(2) {
            width: 29%;
        }

        .seller-time-table th:nth-child(3),
        .seller-time-table td:nth-child(3) {
            width: 18%;
        }

        .seller-time-table th:nth-child(4),
        .seller-time-table td:nth-child(4) {
            width: 22%;
        }

        .seller-time-table th:nth-child(5),
        .seller-time-table td:nth-child(5) {
            width: 18%;
        }

        .time-status-active {
            color: #15803d;
            font-weight: 900;
        }

        .time-status-inactive {
            color: #b91c1c;
            font-weight: 900;
        }

        .time-action-button {
            display: inline-flex;
            height: 32px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #111827;
            padding: 0 16px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            transition: 0.2s ease;
            white-space: nowrap;
        }

        .time-action-button:hover {
            background: #ff7300;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .seller-time-layout {
                grid-template-columns: 1fr;
            }

            .seller-time-table {
                font-size: 12px;
            }

            .seller-time-table thead th,
            .seller-time-table tbody td {
                padding: 12px 8px;
            }
        }
    </style>

    <section
        x-data="penjualWaktuPage({
            slots: @js($slots),
            slotOptions: @js($slotOptions),
            query: @js($query),
            dataUrl: '{{ route('penjual.waktu.data') }}',
            showUrlTemplate: '{{ route('penjual.waktu.show', ['slot' => '__SLOT__']) }}',
            storeUrl: '{{ route('penjual.waktu.store') }}',
            updateUrlTemplate: '{{ route('penjual.waktu.update', ['slot' => '__SLOT__']) }}'
        })"
        class="seller-time-page"
    >
        <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
            Kelola Waktu Pengambilan
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

        <div class="seller-time-layout">
            <aside class="seller-time-card">
                <h2 class="text-xl font-black text-gray-950" x-text="isEdit ? 'Edit Waktu' : 'Tambah Waktu'"></h2>

                <form x-on:submit.prevent="submitForm()" class="mt-5">
                    <div class="seller-time-field">
                        <label for="pickup_slot_option_id" class="seller-time-label">
                            Rentang Waktu
                        </label>

                        <select
                            id="pickup_slot_option_id"
                            x-model="form.pickup_slot_option_id"
                            required
                            class="seller-time-input"
                        >
                            <option value="">Pilih rentang waktu</option>

                            <template x-for="option in slotOptions" :key="option.id">
                                <option x-bind:value="option.id" x-text="option.formatted_time"></option>
                            </template>
                        </select>
                    </div>

                    <div class="seller-time-field">
                        <label for="quota" class="seller-time-label">
                            Kuota
                        </label>

                        <input
                            id="quota"
                            type="number"
                            x-model="form.quota"
                            placeholder="10"
                            min="1"
                            required
                            class="seller-time-input"
                        >
                    </div>

                    <div class="seller-time-field">
                        <label for="is_active" class="seller-time-label">
                            Status
                        </label>

                        <select
                            id="is_active"
                            x-model="form.is_active"
                            required
                            class="seller-time-input"
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    <template x-if="isEdit">
                        <div class="mt-7 grid grid-cols-2 gap-5">
                            <button
                                type="button"
                                x-on:click="resetForm()"
                                class="flex h-11 items-center justify-center rounded-full bg-gray-300 text-base font-black text-gray-950 transition duration-300 hover:bg-gray-400"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                x-bind:disabled="isSubmitting"
                                class="flex h-11 items-center justify-center rounded-full bg-orange-500 text-base font-black text-white shadow-lg shadow-orange-200 transition duration-300 hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
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

            <section class="seller-time-card">
                <div class="flex gap-3">
                    <input
                        type="text"
                        x-model="searchQuery"
                        x-on:input.debounce.500ms="searchSlots()"
                        placeholder="Cari id, waktu, kuota, atau status..."
                        class="seller-time-input flex-1"
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
                        <span x-text="slots.length"></span> waktu ditampilkan
                    </p>

                    <p x-cloak x-show="isLoading" class="text-sm font-bold text-orange-600">
                        Memuat data...
                    </p>
                </div>

                <div class="seller-time-table-wrap">
                    <table class="seller-time-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Waktu</th>
                                <th>Kuota</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <template x-for="slot in slots" :key="slot.id">
                                <tr x-bind:class="Number(form.id) === Number(slot.id) ? 'is-selected' : ''">
                                    <td x-text="slot.id"></td>

                                    <td x-text="slot.formatted_time"></td>

                                    <td x-text="slot.quota"></td>

                                    <td>
                                        <span
                                            x-text="slot.status_label"
                                            x-bind:class="slot.is_active ? 'time-status-active' : 'time-status-inactive'"
                                        ></span>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            x-on:click="fetchDetail(slot.id)"
                                            x-bind:disabled="loadingDetailId === slot.id"
                                            class="time-action-button disabled:cursor-not-allowed disabled:opacity-60"
                                            x-text="loadingDetailId === slot.id ? '...' : 'Edit'"
                                        ></button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="slots.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center font-bold text-gray-500">
                                        Waktu pengambilan belum tersedia.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>

    <script>
        function penjualWaktuPage(config) {
            return {
                slots: config.slots,
                slotOptions: config.slotOptions,

                dataUrl: config.dataUrl,
                showUrlTemplate: config.showUrlTemplate,
                storeUrl: config.storeUrl,
                updateUrlTemplate: config.updateUrlTemplate,

                searchQuery: config.query ?? '',

                form: {
                    id: null,
                    pickup_slot_option_id: config.slotOptions.length ? String(config.slotOptions[0].id) : '',
                    quota: '',
                    is_active: '1',
                },

                isLoading: false,
                isSubmitting: false,
                loadingDetailId: null,

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

                async searchSlots() {
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

                        this.slots = data.slots;
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetSearch() {
                    this.searchQuery = '';
                    this.searchSlots();
                },

                async fetchDetail(slotId) {
                    this.clearMessages();
                    this.loadingDetailId = slotId;

                    try {
                        const response = await fetch(
                            this.showUrlTemplate.replace('__SLOT__', slotId),
                            {
                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        const data = await this.parseResponse(response);

                        this.fillForm(data.slot);
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.loadingDetailId = null;
                    }
                },

                fillForm(slot) {
                    this.form = {
                        id: slot.id,
                        pickup_slot_option_id: String(slot.pickup_slot_option_id),
                        quota: slot.quota,
                        is_active: slot.is_active ? '1' : '0',
                    };
                },

                resetForm() {
                    this.form = {
                        id: null,
                        pickup_slot_option_id: this.slotOptions.length ? String(this.slotOptions[0].id) : '',
                        quota: '',
                        is_active: '1',
                    };

                    this.clearMessages();
                },

                async submitForm() {
                    this.clearMessages();
                    this.isSubmitting = true;

                    try {
                        const payload = {
                            pickup_slot_option_id: this.form.pickup_slot_option_id,
                            quota: this.form.quota,
                            is_active: this.form.is_active === '1',
                        };

                        let url = this.storeUrl;
                        let method = 'POST';
                        const wasEdit = this.isEdit;

                        if (this.isEdit) {
                            url = this.updateUrlTemplate.replace('__SLOT__', this.form.id);
                            method = 'PUT';
                        }

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await this.parseResponse(response);

                        await this.searchSlots();

                        if (wasEdit) {
                            this.fillForm(data.slot);
                        } else {
                            this.resetForm();
                        }

                        this.showSuccess(data.message);
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.isSubmitting = false;
                    }
                },
            };
        }
    </script>
@endsection
