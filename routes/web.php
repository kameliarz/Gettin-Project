<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pelanggan\CartController;
use App\Http\Controllers\Pelanggan\MenuController;
use App\Http\Controllers\Pelanggan\RiwayatPesananController;
use App\Http\Controllers\Penjual\DashboardController as PenjualDashboardController;
use App\Http\Controllers\Penjual\MenuController as PenjualMenuController;
use App\Http\Controllers\Penjual\WaktuPengambilanController as PenjualWaktuController;
use App\Http\Controllers\Penjual\LaporanController as PenjualLaporanController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PenggunaController as AdminPenggunaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (($user->role ?? null) === 'penjual') {
        return redirect()->route('penjual.dashboard');
    }

    if (($user->role ?? null) === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('pelanggan')
    ->name('pelanggan.')
    ->group(function () {
        Route::get('/menu', [MenuController::class, 'index'])->name('menu');

        Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');
        Route::post('/keranjang', [CartController::class, 'store'])->name('keranjang.store');
        Route::patch('/keranjang/{item}', [CartController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{item}', [CartController::class, 'destroy'])->name('keranjang.destroy');

        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout.store');

        Route::get('/riwayat-pemesanan', [RiwayatPesananController::class, 'index'])->name('riwayat-pemesanan');
    });

Route::middleware(['auth', 'verified'])
    ->prefix('penjual')
    ->name('penjual.')
    ->group(function () {
        Route::get('/dashboard', [PenjualDashboardController::class, 'index'])
            ->name('dashboard');

        Route::patch('/dashboard/orders/{order}/status', [PenjualDashboardController::class, 'updateStatus'])
            ->name('dashboard.orders.status');

        Route::post('/dashboard/manual', [PenjualDashboardController::class, 'storeManual'])
            ->name('dashboard.manual.store');

        Route::put('/dashboard/manual/{order}', [PenjualDashboardController::class, 'updateManual'])
            ->name('dashboard.manual.update');

        Route::get('/menu', [PenjualMenuController::class, 'index'])
            ->name('menu');

        Route::get('/menu/data', [PenjualMenuController::class, 'data'])
            ->name('menu.data');

        Route::get('/menu/{menu}', [PenjualMenuController::class, 'show'])
            ->name('menu.show');

        Route::post('/menu', [PenjualMenuController::class, 'store'])
            ->name('menu.store');

        Route::put('/menu/{menu}', [PenjualMenuController::class, 'update'])
            ->name('menu.update');

        Route::delete('/menu/{menu}', [PenjualMenuController::class, 'destroy'])
            ->name('menu.destroy');

        Route::get('/waktu', [PenjualWaktuController::class, 'index'])
            ->name('waktu');

        Route::get('/waktu/data', [PenjualWaktuController::class, 'data'])
            ->name('waktu.data');

        Route::get('/waktu/{slot}', [PenjualWaktuController::class, 'show'])
            ->name('waktu.show');

        Route::post('/waktu', [PenjualWaktuController::class, 'store'])
            ->name('waktu.store');

        Route::put('/waktu/{slot}', [PenjualWaktuController::class, 'update'])
            ->name('waktu.update');

        Route::get('/laporan', [PenjualLaporanController::class, 'index'])
            ->name('laporan');

        Route::get('/laporan/data', [PenjualLaporanController::class, 'data'])
            ->name('laporan.data');

        Route::get('/laporan/{date}', [PenjualLaporanController::class, 'show'])
            ->where('date', '\\d{4}-\\d{2}-\\d{2}')
            ->name('laporan.show');

        Route::get('/laporan/{date}/csv', [PenjualLaporanController::class, 'downloadCsv'])
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('laporan.csv');

        Route::get('/laporan/{date}/pdf', [PenjualLaporanController::class, 'downloadPdf'])
            ->where('date', '\\d{4}-\\d{2}-\\d{2}')
            ->name('laporan.pdf');

    });

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/pengguna', [AdminPenggunaController::class, 'index'])
            ->name('pengguna');

        Route::post('/pengguna', [AdminPenggunaController::class, 'store'])
            ->name('pengguna.store');

        Route::put('/pengguna/{canteen}', [AdminPenggunaController::class, 'update'])
            ->name('pengguna.update');
    });

require __DIR__ . '/auth.php';
