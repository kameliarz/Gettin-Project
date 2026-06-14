<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));
        $editId = $request->query('edit');

        $sellers = $this->sellerQuery($search)
            ->orderBy('canteens.name')
            ->get()
            ->map(fn ($seller) => $this->decorateSeller($seller));

        $editingSeller = null;

        if ($editId) {
            $editingSeller = $this->findSeller((int) $editId);
        }

        return view('admin.pengguna', [
            'search' => $search,
            'sellers' => $sellers,
            'editingSeller' => $editingSeller,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $this->validateSeller($request);

        DB::transaction(function () use ($request, $validated) {
            $now = now();

            $userId = DB::table('users')->insertGetId([
                'username' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'penjual',
                'status' => $validated['status'],
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('canteens')->insert([
                'user_id' => $userId,
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'qris_image' => $this->readUploadedQris($request),
                'is_open' => $validated['status'] === 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return redirect()
            ->route('admin.pengguna')
            ->with('success', 'Penjual berhasil ditambahkan.');
    }

    public function update(Request $request, int $canteen): RedirectResponse
    {
        $this->ensureAdmin();

        $seller = $this->findSeller($canteen);

        $validated = $this->validateSeller($request, (int) $seller->user_id);

        DB::transaction(function () use ($request, $validated, $seller) {
            $now = now();

            $userData = [
                'username' => $validated['name'],
                'email' => $validated['email'],
                'status' => $validated['status'],
                'updated_at' => $now,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            DB::table('users')
                ->where('id', $seller->user_id)
                ->update($userData);

            $canteenData = [
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'is_open' => $validated['status'] === 'aktif',
                'updated_at' => $now,
            ];

            if ($request->hasFile('qris_image')) {
                $canteenData['qris_image'] = $this->readUploadedQris($request);
            }

            DB::table('canteens')
                ->where('id', $seller->canteen_id)
                ->update($canteenData);
        });

        return redirect()
            ->route('admin.pengguna')
            ->with('success', 'Data penjual berhasil diperbarui.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->role ?? null) === 'admin',
            403,
            'Akses hanya untuk admin.'
        );
    }

    private function validateSeller(Request $request, ?int $ignoreUserId = null): array
    {
        $emailRule = Rule::unique('users', 'email');

        if ($ignoreUserId) {
            $emailRule->ignore($ignoreUserId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', $emailRule],
            'password' => [$ignoreUserId ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
            'location' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['aktif', 'tidak_aktif'])],
            'qris_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ], [
            'name.required' => 'Nama kantin wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'qris_image.image' => 'File QRIS harus berupa gambar.',
            'qris_image.mimes' => 'QRIS harus berformat PNG, JPG, atau JPEG.',
            'qris_image.max' => 'Ukuran QRIS maksimal 2 MB.',
        ]);
    }

    private function sellerQuery(string $search = '')
    {
        return DB::table('canteens')
            ->join('users', 'canteens.user_id', '=', 'users.id')
            ->select([
                'canteens.id as canteen_id',
                'canteens.user_id',
                'canteens.name as canteen_name',
                'canteens.location',
                'canteens.qris_image',
                'canteens.is_open',
                'users.email',
                'users.username',
                'users.status',
                'users.created_at',
            ])
            ->where('users.role', 'penjual')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('users.email', 'like', '%' . $search . '%')
                        ->orWhere('users.username', 'like', '%' . $search . '%')
                        ->orWhere('canteens.name', 'like', '%' . $search . '%')
                        ->orWhere('canteens.location', 'like', '%' . $search . '%');
                });
            });
    }

    private function findSeller(int $canteenId)
    {
        $seller = $this->sellerQuery()
            ->where('canteens.id', $canteenId)
            ->first();

        abort_if(! $seller, 404, 'Penjual tidak ditemukan.');

        return $this->decorateSeller($seller);
    }

    private function decorateSeller($seller)
    {
        $seller->status_label = $seller->status === 'aktif'
            ? 'Aktif'
            : 'Tidak Aktif';

        $seller->qris_image_url = $this->qrisDataUrl($seller->qris_image);

        return $seller;
    }

    private function readUploadedQris(Request $request): ?string
    {
        if (! $request->hasFile('qris_image')) {
            return null;
        }

        $file = $request->file('qris_image');

        return file_get_contents($file->getRealPath()) ?: null;
    }

    private function qrisDataUrl(?string $binary): ?string
    {
        if (! $binary) {
            return null;
        }

        $mime = 'image/png';

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if ($finfo) {
                $detectedMime = finfo_buffer($finfo, $binary);
                finfo_close($finfo);

                if ($detectedMime) {
                    $mime = $detectedMime;
                }
            }
        }

        return 'data:' . $mime . ';base64,' . base64_encode($binary);
    }
}
