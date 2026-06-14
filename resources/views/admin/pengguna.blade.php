@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    @php
        $isEdit = $editingSeller !== null;

        $formAction = $isEdit
            ? route('admin.pengguna.update', $editingSeller->canteen_id)
            : route('admin.pengguna.store');

        $statusValue = old('status', $isEdit ? $editingSeller->status : 'aktif');
    @endphp

    <style>
        .admin-seller-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 40px 24px 128px;
        }

        .admin-seller-title {
            font-size: 32px;
            line-height: 1.2;
            font-weight: 900;
            color: #020617;
            letter-spacing: -0.03em;
        }

        .admin-seller-layout {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 30px;
            align-items: start;
            margin-top: 36px;
        }

        .admin-seller-card {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 32px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.13);
        }

        .admin-seller-card-title {
            font-size: 20px;
            font-weight: 900;
            color: #020617;
            letter-spacing: -0.02em;
        }

        .admin-seller-field {
            margin-top: 14px;
        }

        .admin-seller-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 800;
            color: #111827;
        }

        .admin-seller-input {
            width: 100%;
            height: 42px;
            border: 1px solid #cfd4dc;
            border-radius: 8px;
            padding: 0 14px;
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            background: #ffffff;
            transition: 0.2s ease;
        }

        .admin-seller-input::placeholder {
            color: #6b7280;
            font-weight: 500;
        }

        .admin-seller-input:focus {
            outline: none;
            border-color: #ff7300;
            box-shadow: 0 0 0 3px rgba(255, 115, 0, 0.15);
        }

        .admin-seller-image-box {
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

        .admin-seller-image-box:hover {
            border-color: #ff7300;
            background: #fff3ea;
        }

        .admin-seller-image-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
        }

        .admin-seller-placeholder {
            padding: 0 24px;
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            color: #555555;
        }

        .admin-seller-button-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 24px;
        }

        .admin-seller-button {
            height: 42px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 900;
            transition: 0.2s ease;
        }

        .admin-seller-button-secondary {
            background: #d1d5db;
            color: #111827;
        }

        .admin-seller-button-secondary:hover {
            background: #c2c7cf;
        }

        .admin-seller-button-primary {
            background: #ff6b00;
            color: #ffffff;
            box-shadow: 0 10px 18px rgba(255, 107, 0, 0.22);
        }

        .admin-seller-button-primary:hover {
            background: #ea5f00;
            transform: translateY(-1px);
        }

        .admin-seller-search-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .admin-seller-search-input {
            flex: 1;
        }

        .admin-seller-reset-link {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #374151;
            font-size: 15px;
            font-weight: 800;
            transition: 0.2s ease;
            white-space: nowrap;
        }

        .admin-seller-reset-link:hover {
            background: #f3f4f6;
        }

        .admin-seller-count {
            margin-top: 14px;
            font-size: 15px;
            font-weight: 600;
            color: #374151;
        }

        .admin-seller-table-wrap {
            margin-top: 18px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            border-radius: 16px;
            background: #ffffff;
        }

        .admin-seller-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-size: 13px;
            color: #020617;
        }

        .admin-seller-table thead th {
            background: #ffffff;
            padding: 15px 10px;
            border-right: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
            text-align: left;
            font-size: 13px;
            font-weight: 900;
            color: #020617;
            white-space: nowrap;
        }

        .admin-seller-table thead th:last-child {
            border-right: none;
        }

        .admin-seller-table tbody tr:nth-child(odd) {
            background: #fff7ed;
        }

        .admin-seller-table tbody tr:nth-child(even) {
            background: #ffffff;
        }

        .admin-seller-table tbody tr:hover {
            background: #ffedd5;
        }

        .admin-seller-table tbody tr.is-selected {
            background: #fed7aa;
        }

        .admin-seller-table tbody td {
            padding: 15px 10px;
            border-right: 1px solid #d1d5db;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            font-weight: 800;
            color: #020617;
        }

        .admin-seller-table tbody td:last-child {
            border-right: none;
        }

        .admin-seller-table tbody tr:last-child td {
            border-bottom: none;
        }

        .admin-seller-table th:nth-child(1),
        .admin-seller-table td:nth-child(1) {
            width: 32%;
        }

        .admin-seller-table th:nth-child(2),
        .admin-seller-table td:nth-child(2) {
            width: 23%;
        }

        .admin-seller-table th:nth-child(3),
        .admin-seller-table td:nth-child(3) {
            width: 21%;
        }

        .admin-seller-table th:nth-child(4),
        .admin-seller-table td:nth-child(4) {
            width: 13%;
            text-align: center;
        }

        .admin-seller-table th:nth-child(5),
        .admin-seller-table td:nth-child(5) {
            width: 11%;
            text-align: center;
        }

        .admin-seller-text,
        .admin-seller-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-seller-name {
            font-weight: 900;
            color: #020617;
        }

        .admin-seller-status {
            font-size: 13px;
            font-weight: 800;
            color: #020617;
            white-space: nowrap;
        }

        .admin-seller-action {
            display: inline-flex;
            height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #0f172a;
            padding: 0 16px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            transition: 0.2s ease;
            white-space: nowrap;
        }

        .admin-seller-action:hover {
            background: #ff6b00;
            transform: translateY(-1px);
        }

        .admin-seller-empty {
            padding: 44px 16px;
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            color: #6b7280;
        }

        .admin-seller-alert-success {
            margin-top: 26px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            border-radius: 24px;
            padding: 14px 18px;
            color: #15803d;
            font-size: 14px;
            font-weight: 800;
        }

        .admin-seller-alert-error {
            margin-top: 26px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            border-radius: 24px;
            padding: 14px 18px;
            color: #b91c1c;
        }

        .admin-seller-alert-error-title {
            font-size: 14px;
            font-weight: 900;
        }

        .admin-seller-alert-error ul {
            margin-top: 8px;
            padding-left: 20px;
            list-style: disc;
            font-size: 14px;
            font-weight: 700;
        }

        .admin-seller-error-text {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 800;
            color: #dc2626;
        }

        @media (max-width: 980px) {
            .admin-seller-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .admin-seller-page {
                padding: 32px 16px 96px;
            }

            .admin-seller-title {
                font-size: 28px;
            }

            .admin-seller-card {
                border-radius: 24px;
                padding: 18px;
            }

            .admin-seller-search-row {
                flex-direction: column;
                align-items: stretch;
            }

            .admin-seller-reset-link {
                width: 100%;
            }

            .admin-seller-table {
                min-width: 760px;
            }
        }
    </style>

    <section class="admin-seller-page">
        <h1 class="admin-seller-title">
            Kelola Pengguna Penjual
        </h1>

        @if (session('success'))
            <div class="admin-seller-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-seller-alert-error">
                <p class="admin-seller-alert-error-title">
                    Data belum bisa disimpan.
                </p>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="admin-seller-layout">
            <aside class="admin-seller-card">
                <h2 class="admin-seller-card-title">
                    {{ $isEdit ? 'Edit Penjual' : 'Tambah Penjual' }}
                </h2>

                <form
                    method="POST"
                    action="{{ $formAction }}"
                    enctype="multipart/form-data"
                    class="mt-5"
                >
                    @csrf

                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="admin-seller-field">
                        <label for="name" class="admin-seller-label">
                            Nama Kantin
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $isEdit ? $editingSeller->canteen_name : '') }}"
                            placeholder="Nama kantin"
                            class="admin-seller-input"
                        >

                        @error('name')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-field">
                        <label for="email" class="admin-seller-label">
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $isEdit ? $editingSeller->email : '') }}"
                            placeholder="email@gettin.com"
                            class="admin-seller-input"
                        >

                        @error('email')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-field">
                        <label for="password" class="admin-seller-label">
                            Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                            class="admin-seller-input"
                        >

                        @error('password')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-field">
                        <label for="location" class="admin-seller-label">
                            Lokasi
                        </label>

                        <input
                            id="location"
                            type="text"
                            name="location"
                            value="{{ old('location', $isEdit ? $editingSeller->location : '') }}"
                            placeholder="Lobby Fasilkom"
                            class="admin-seller-input"
                        >

                        @error('location')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-field">
                        <label for="status" class="admin-seller-label">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="admin-seller-input"
                        >
                            <option value="aktif" @selected($statusValue === 'aktif')>
                                Aktif
                            </option>

                            <option value="tidak_aktif" @selected($statusValue === 'tidak_aktif')>
                                Tidak Aktif
                            </option>
                        </select>

                        @error('status')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-field">
                        <label class="admin-seller-label">
                            QRIS
                        </label>

                        <label for="qris_image" class="admin-seller-image-box">
                            <img
                                id="qris-preview-image"
                                src="{{ $isEdit ? $editingSeller->qris_image_url : '' }}"
                                alt="Preview QRIS"
                                class="{{ $isEdit && $editingSeller->qris_image_url ? 'block' : 'hidden' }}"
                            >

                            <div
                                id="qris-preview-placeholder"
                                class="admin-seller-placeholder {{ $isEdit && $editingSeller->qris_image_url ? 'hidden' : 'block' }}"
                            >
                                Klik untuk memilih QRIS
                            </div>
                        </label>

                        <input
                            id="qris_image"
                            type="file"
                            name="qris_image"
                            accept="image/png,image/jpeg,image/jpg"
                            class="hidden"
                        >

                        <p class="mt-2 text-xs font-medium text-gray-500">
                            Format: JPG atau PNG. Maksimal 2MB.
                        </p>

                        @error('qris_image')
                            <p class="admin-seller-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-seller-button-row">
                        @if ($isEdit)
                            <a
                                href="{{ route('admin.pengguna') }}"
                                class="admin-seller-button admin-seller-button-secondary flex items-center justify-center"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="admin-seller-button admin-seller-button-primary"
                            >
                                Simpan
                            </button>
                        @else
                            <button
                                type="reset"
                                id="seller-form-reset"
                                class="admin-seller-button admin-seller-button-secondary"
                            >
                                Reset
                            </button>

                            <button
                                type="submit"
                                class="admin-seller-button admin-seller-button-primary"
                            >
                                Tambah
                            </button>
                        @endif
                    </div>
                </form>
            </aside>

            <section class="admin-seller-card">
                <form
                    id="seller-search-form"
                    method="GET"
                    action="{{ route('admin.pengguna') }}"
                    class="admin-seller-search-row"
                >
                    <input
                        id="seller-search-input"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari email, username, atau lokasi..."
                        class="admin-seller-input admin-seller-search-input"
                    >

                    <a
                        href="{{ route('admin.pengguna') }}"
                        class="admin-seller-reset-link"
                    >
                        Reset
                    </a>
                </form>

                <p class="admin-seller-count">
                    {{ $sellers->count() }} pengguna ditampilkan
                </p>

                <div class="admin-seller-table-wrap">
                    <div class="overflow-x-auto">
                        <table class="admin-seller-table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($sellers as $seller)
                                    <tr class="{{ $isEdit && $editingSeller->canteen_id === $seller->canteen_id ? 'is-selected' : '' }}">
                                        <td>
                                            <div class="admin-seller-text">
                                                {{ $seller->email }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="admin-seller-name">
                                                {{ $seller->username }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="admin-seller-text">
                                                {{ $seller->location ?: '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="admin-seller-status {{ $seller->status === 'aktif' ? 'is-active' : 'is-inactive' }}">
                                                {{ $seller->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>

                                        <td>
                                            <a
                                                href="{{ route('admin.pengguna', ['edit' => $seller->canteen_id, 'search' => $search]) }}"
                                                class="admin-seller-action"
                                            >
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="admin-seller-empty">
                                            Penjual tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrisInput = document.getElementById('qris_image');
            const previewImage = document.getElementById('qris-preview-image');
            const previewPlaceholder = document.getElementById('qris-preview-placeholder');
            const resetButton = document.getElementById('seller-form-reset');

            if (qrisInput) {
                qrisInput.addEventListener('change', function () {
                    const file = this.files && this.files[0];

                    if (!file) {
                        return;
                    }

                    const imageUrl = URL.createObjectURL(file);

                    previewImage.src = imageUrl;
                    previewImage.classList.remove('hidden');
                    previewImage.classList.add('block');

                    previewPlaceholder.classList.add('hidden');
                    previewPlaceholder.classList.remove('block');
                });
            }

            if (resetButton && qrisInput && previewImage && previewPlaceholder) {
                resetButton.addEventListener('click', function () {
                    setTimeout(function () {
                        qrisInput.value = '';

                        previewImage.src = '';
                        previewImage.classList.add('hidden');
                        previewImage.classList.remove('block');

                        previewPlaceholder.classList.remove('hidden');
                        previewPlaceholder.classList.add('block');
                    }, 0);
                });
            }

            const searchInput = document.getElementById('seller-search-input');
            const searchForm = document.getElementById('seller-search-form');
            let searchTimer = null;

            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimer);

                    searchTimer = setTimeout(function () {
                        searchForm.submit();
                    }, 500);
                });
            }
        });
    </script>
@endsection
