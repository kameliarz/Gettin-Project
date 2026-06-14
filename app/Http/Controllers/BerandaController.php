<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        $popularMenus = DB::table('menus')
            ->join('canteens', 'menus.canteen_id', '=', 'canteens.id')
            ->join('menu_categories', 'menus.category_id', '=', 'menu_categories.id')
            ->select([
                'menus.id',
                'menus.name',
                'menus.price',
                'menus.image',
                'menus.stock_qty',
                'canteens.name as canteen_name',
                'menu_categories.name as category_name',
            ])
            ->where('menus.is_popular', true)
            ->where('canteens.is_open', true)
            ->orderByDesc('menus.created_at')
            ->limit(4)
            ->get()
            ->map(function ($menu) {
                $menu->formatted_price = 'Rp ' . number_format((float) $menu->price, 0, ',', '.');

                $menu->image_url = $menu->image
                    ? 'data:image/jpeg;base64,' . base64_encode($menu->image)
                    : asset('images/nasi-gila.jpg');

                return $menu;
            });

        return view('beranda', [
            'popularMenus' => $popularMenus,
        ]);
    }
}
