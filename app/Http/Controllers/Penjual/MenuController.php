<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $stockFilter = $request->query('stock');

        $categories = DB::table('menu_categories')
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $menus = $this->menuList(
            $canteen->id,
            $request->query('q'),
            $stockFilter
        );

        return view('penjual.menu', [
            'canteen' => $canteen,
            'categories' => $categories,
            'menus' => $menus,
            'query' => $request->query('q', ''),
            'stockFilter' => $stockFilter,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $stockFilter = $request->query('stock');

        $menus = $this->menuList(
            $canteen->id,
            $request->query('q'),
            $stockFilter
        );

        return response()->json([
            'menus' => $menus,
            'count' => $menus->count(),
        ]);
    }

    public function show(int $menu): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $menuData = $this->menuById($menu, $canteen->id, true);

        abort_if(! $menuData, 404, 'Menu tidak ditemukan.');

        return response()->json([
            'menu' => $menuData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('menus', 'name')
                    ->where(fn ($query) => $query->where('canteen_id', $canteen->id)),
            ],
            'category_id' => ['required', 'integer', 'exists:menu_categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_popular' => ['nullable', 'boolean'],
        ]);

        $imageContent = null;

        if ($request->hasFile('image')) {
            $imageContent = file_get_contents($request->file('image')->getRealPath());
        }

        $menuId = DB::table('menus')->insertGetId([
            'canteen_id' => $canteen->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'image' => $imageContent,
            'stock_qty' => 0,
            'is_popular' => $request->boolean('is_popular'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan.',
            'menu' => $this->menuById($menuId, $canteen->id, true),
        ], 201);
    }

    public function update(Request $request, int $menu): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $existingMenu = DB::table('menus')
            ->where('id', $menu)
            ->where('canteen_id', $canteen->id)
            ->first();

        abort_if(! $existingMenu, 404, 'Menu tidak ditemukan.');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('menus', 'name')
                    ->where(fn ($query) => $query->where('canteen_id', $canteen->id))
                    ->ignore($menu),
            ],
            'category_id' => ['required', 'integer', 'exists:menu_categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_popular' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'is_popular' => $request->boolean('is_popular'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $payload['image'] = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table('menus')
            ->where('id', $menu)
            ->where('canteen_id', $canteen->id)
            ->update($payload);

        return response()->json([
            'message' => 'Menu berhasil diperbarui.',
            'menu' => $this->menuById($menu, $canteen->id, true),
        ]);
    }

    public function destroy(int $menu): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $existingMenu = DB::table('menus')
            ->where('id', $menu)
            ->where('canteen_id', $canteen->id)
            ->first();

        abort_if(! $existingMenu, 404, 'Menu tidak ditemukan.');

        $hasOrderItems = DB::table('order_items')
            ->where('menu_id', $menu)
            ->exists();

        if ($hasOrderItems) {
            return response()->json([
                'message' => 'Menu tidak bisa dihapus karena sudah pernah masuk ke pesanan.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($menu, $canteen) {
                if (Schema::hasTable('cart_items')) {
                    DB::table('cart_items')
                        ->where('menu_id', $menu)
                        ->delete();
                }

                DB::table('menus')
                    ->where('id', $menu)
                    ->where('canteen_id', $canteen->id)
                    ->delete();
            });
        } catch (QueryException) {
            return response()->json([
                'message' => 'Menu gagal dihapus karena masih terhubung dengan data lain.',
            ], 422);
        }

        return response()->json([
            'message' => 'Menu berhasil dihapus.',
            'deleted_id' => $menu,
        ]);
    }

    private function ensureSeller(): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->role ?? null) === 'penjual',
            403,
            'Akses hanya untuk penjual.'
        );
    }

    private function currentCanteen(): object
    {
        $canteen = DB::table('canteens')
            ->where('user_id', auth()->id())
            ->first();

        abort_if(! $canteen, 403, 'Akun penjual ini belum memiliki data kantin.');

        return $canteen;
    }

    private function menuList(int $canteenId, ?string $keyword = null, ?string $stockFilter = null)
    {
        $query = DB::table('menus')
            ->join('menu_categories', 'menus.category_id', '=', 'menu_categories.id')
            ->select([
                'menus.id',
                'menus.name',
                'menus.price',
                'menus.stock_qty',
                'menus.is_popular',
                'menus.category_id',
                'menu_categories.name as category_name',
            ])
            ->where('menus.canteen_id', $canteenId);

        if ($stockFilter === 'low') {
            $query
                ->where('menus.stock_qty', '>', 0)
                ->where('menus.stock_qty', '<=', 5);
        }

        if ($keyword) {
            $keyword = trim($keyword);

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('menus.name', 'like', "%{$keyword}%")
                    ->orWhere('menu_categories.name', 'like', "%{$keyword}%");

                if (preg_match('/^MNU0*(\d+)$/i', $keyword, $matches)) {
                    $subQuery->orWhere('menus.id', (int) $matches[1]);
                }

                if (is_numeric($keyword)) {
                    $subQuery->orWhere('menus.id', (int) $keyword);
                }
            });
        }

        return $query
            ->orderBy('menus.name')
            ->get()
            ->map(fn ($menu) => $this->decorateMenu($menu, false))
            ->values();
    }

    private function menuById(int $menuId, int $canteenId, bool $includeImage = false): ?object
    {
        $selects = [
            'menus.id',
            'menus.name',
            'menus.price',
            'menus.stock_qty',
            'menus.is_popular',
            'menus.category_id',
            'menu_categories.name as category_name',
        ];

        if ($includeImage) {
            $selects[] = 'menus.image';
        }

        $menu = DB::table('menus')
            ->join('menu_categories', 'menus.category_id', '=', 'menu_categories.id')
            ->select($selects)
            ->where('menus.id', $menuId)
            ->where('menus.canteen_id', $canteenId)
            ->first();

        if (! $menu) {
            return null;
        }

        return $this->decorateMenu($menu, $includeImage);
    }

    private function decorateMenu(object $menu, bool $includeImage = false): object
    {
        $menu->id = (int) $menu->id;
        $menu->category_id = (int) $menu->category_id;
        $menu->price_plain = (int) $menu->price;
        $menu->stock_qty = (int) ($menu->stock_qty ?? 0);
        $menu->is_popular = (bool) $menu->is_popular;

        $menu->code = 'MNU' . str_pad((string) $menu->id, 3, '0', STR_PAD_LEFT);
        $menu->formatted_price = 'Rp' . number_format((float) $menu->price, 0, ',', '.');

        if ($includeImage && property_exists($menu, 'image')) {
            $menu->image_url = $menu->image
                ? $this->imageDataUrl($menu->image)
                : null;

            unset($menu->image);
        }

        return $menu;
    }

    private function imageDataUrl(string $binary): string
    {
        $mime = 'image/jpeg';

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
