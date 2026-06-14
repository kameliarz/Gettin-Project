<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $categories = DB::table('menu_categories')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $canteens = DB::table('canteens')
            ->select('id', 'name')
            ->where('is_open', true)
            ->orderBy('name')
            ->get();

        $selectedCategories = collect($request->input('categories', []))
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        $selectedCanteens = collect($request->input('canteens', []))
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();

        $selectedPriceRanges = collect($request->input('price_ranges', []))
            ->filter()
            ->values()
            ->all();

        $menus = DB::table('menus')
            ->join('canteens', 'menus.canteen_id', '=', 'canteens.id')
            ->join('menu_categories', 'menus.category_id', '=', 'menu_categories.id')
            ->select([
                'menus.id',
                'menus.name',
                'menus.price',
                'menus.image',
                'menus.stock_qty',
                'menus.is_popular',
                'canteens.name as canteen_name',
                'menu_categories.name as category_name',
            ])
            ->where('canteens.is_open', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->q;

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery
                        ->where('menus.name', 'like', "%{$keyword}%")
                        ->orWhere('canteens.name', 'like', "%{$keyword}%")
                        ->orWhere('menu_categories.name', 'like', "%{$keyword}%");
                });
            })
            ->when(! empty($selectedCategories), function ($query) use ($selectedCategories) {
                $query->whereIn('menus.category_id', $selectedCategories);
            })
            ->when(! empty($selectedCanteens), function ($query) use ($selectedCanteens) {
                $query->whereIn('menus.canteen_id', $selectedCanteens);
            })
            ->when(! empty($selectedPriceRanges), function ($query) use ($selectedPriceRanges) {
                $query->where(function ($priceQuery) use ($selectedPriceRanges) {
                    foreach ($selectedPriceRanges as $range) {
                        if ($range === 'under_10000') {
                            $priceQuery->orWhere('menus.price', '<', 10000);
                        }

                        if ($range === '10000_15000') {
                            $priceQuery->orWhereBetween('menus.price', [10000, 15000]);
                        }

                        if ($range === 'above_15000') {
                            $priceQuery->orWhere('menus.price', '>', 15000);
                        }
                    }
                });
            })
            ->orderByDesc('menus.is_popular')
            ->orderBy('menus.name')
            ->paginate(9)
            ->withQueryString();

        $menus->getCollection()->transform(function ($menu) {
            $menu->formatted_price = 'Rp ' . number_format((float) $menu->price, 0, ',', '.');

            $menu->image_url = $menu->image
                ? 'data:image/jpeg;base64,' . base64_encode($menu->image)
                : asset('images/nasi-gila.jpg');

            return $menu;
        });

        if ($request->boolean('ajax')) {
            return response()->json([
                'html' => view('pelanggan.partials.menu-results', [
                    'menus' => $menus,
                ])->render(),
            ]);
        }

        return view('pelanggan.menu', [
            'menus' => $menus,
            'categories' => $categories,
            'canteens' => $canteens,
            'selectedCategories' => $selectedCategories,
            'selectedCanteens' => $selectedCanteens,
            'selectedPriceRanges' => $selectedPriceRanges,
        ]);
    }
}
