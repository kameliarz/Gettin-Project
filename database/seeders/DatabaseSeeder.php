<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            $requireId = function ($id, string $message): int {
                if ($id === null) {
                    throw new RuntimeException($message);
                }

                return (int) $id;
            };

            $readPublicImage = function (string $filename): string {
                $path = public_path('images/' . $filename);

                if (! is_file($path)) {
                    throw new RuntimeException("File gambar tidak ditemukan: {$path}");
                }

                $content = file_get_contents($path);

                if ($content === false) {
                    throw new RuntimeException("Gagal membaca file gambar: {$path}");
                }

                return $content;
            };

            /*
            |--------------------------------------------------------------------------
            | 1. Users
            |--------------------------------------------------------------------------
            */

            DB::table('users')->updateOrInsert(
                ['email' => 'admin@gettin.com'],
                [
                    'username' => 'admin_gettin',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'status' => 'aktif',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'budi@gettin.com'],
                [
                    'username' => 'mahasiswa_budi',
                    'password' => Hash::make('password123'),
                    'role' => 'pelanggan',
                    'status' => 'aktif',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'sari@gettin.com'],
                [
                    'username' => 'mahasiswa_sari',
                    'password' => Hash::make('password123'),
                    'role' => 'pelanggan',
                    'status' => 'aktif',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'barokah@gettin.com'],
                [
                    'username' => 'kantin_barokah',
                    'password' => Hash::make('password123'),
                    'role' => 'penjual',
                    'status' => 'aktif',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'dharmaw@gettin.com'],
                [
                    'username' => 'kantin_dharma_wanita',
                    'password' => Hash::make('password123'),
                    'role' => 'penjual',
                    'status' => 'aktif',
                    'email_verified_at' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $budiId = $requireId(
                DB::table('users')->where('email', 'budi@gettin.com')->value('id'),
                'User Budi tidak ditemukan.'
            );

            $sariId = $requireId(
                DB::table('users')->where('email', 'sari@gettin.com')->value('id'),
                'User Sari tidak ditemukan.'
            );

            $barokahUserId = $requireId(
                DB::table('users')->where('email', 'barokah@gettin.com')->value('id'),
                'User penjual Kantin Barokah tidak ditemukan.'
            );

            $dharmaUserId = $requireId(
                DB::table('users')->where('email', 'dharmaw@gettin.com')->value('id'),
                'User penjual Kantin Dharma Wanita tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 2. Canteens
            |--------------------------------------------------------------------------
            */

            DB::table('canteens')->updateOrInsert(
                ['user_id' => $barokahUserId],
                [
                    'name' => 'Kantin Barokah',
                    'location' => 'Lobby Fasilkom',
                    'qris_image' => $readPublicImage('qrcode-kantin-barokah.png'),
                    'is_open' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('canteens')->updateOrInsert(
                ['user_id' => $dharmaUserId],
                [
                    'name' => 'Kantin Dharma Wanita',
                    'location' => 'Lobby Fasilkom',
                    'qris_image' => $readPublicImage('qrcode-kantin-dharmaw.png'),
                    'is_open' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $barokahCanteenId = $requireId(
                DB::table('canteens')->where('user_id', $barokahUserId)->value('id'),
                'Kantin Barokah tidak ditemukan.'
            );

            $dharmaCanteenId = $requireId(
                DB::table('canteens')->where('user_id', $dharmaUserId)->value('id'),
                'Kantin Dharma Wanita tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 3. Menu Categories
            |--------------------------------------------------------------------------
            */

            $categories = [
                'Makanan',
                'Minuman',
                'Camilan',
            ];

            foreach ($categories as $category) {
                DB::table('menu_categories')->updateOrInsert(
                    ['name' => $category],
                    ['name' => $category]
                );
            }

            $makananId = $requireId(
                DB::table('menu_categories')->where('name', 'Makanan')->value('id'),
                'Kategori Makanan tidak ditemukan.'
            );

            $minumanId = $requireId(
                DB::table('menu_categories')->where('name', 'Minuman')->value('id'),
                'Kategori Minuman tidak ditemukan.'
            );

            $camilanId = $requireId(
                DB::table('menu_categories')->where('name', 'Camilan')->value('id'),
                'Kategori Camilan tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 4. Pickup Slot Options
            |--------------------------------------------------------------------------
            */

            $slotOptionIds = [];
            $regularSlotOptionIds = [];
            $slotOptionIdsByStartTime = [];

            $slotStart = $now->copy()->setTime(8, 0, 0);
            $slotLimit = $now->copy()->setTime(12, 0, 0);

            while ($slotStart->lt($slotLimit)) {
                $currentStartTime = $slotStart->format('H:i:s');
                $currentEndTime = $slotStart->copy()->addMinutes(15)->format('H:i:s');

                DB::table('pickup_slot_options')->updateOrInsert(
                    [
                        'start_time' => $currentStartTime,
                        'end_time' => $currentEndTime,
                    ],
                    [
                        'is_active' => true,
                    ]
                );

                $slotOptionId = $requireId(
                    DB::table('pickup_slot_options')
                        ->where('start_time', $currentStartTime)
                        ->where('end_time', $currentEndTime)
                        ->value('id'),
                    "Slot {$currentStartTime}-{$currentEndTime} tidak ditemukan."
                );

                $slotOptionIds[] = $slotOptionId;
                $regularSlotOptionIds[] = $slotOptionId;
                $slotOptionIdsByStartTime[$currentStartTime] = $slotOptionId;

                $slotStart->addMinutes(15);
            }

            $extraSlotStartTime = '23:15:00';
            $extraSlotEndTime = '23:30:00';

            DB::table('pickup_slot_options')->updateOrInsert(
                [
                    'start_time' => $extraSlotStartTime,
                    'end_time' => $extraSlotEndTime,
                ],
                [
                    'is_active' => true,
                ]
            );

            $extraSlotOptionId = $requireId(
                DB::table('pickup_slot_options')
                    ->where('start_time', $extraSlotStartTime)
                    ->where('end_time', $extraSlotEndTime)
                    ->value('id'),
                "Slot {$extraSlotStartTime}-{$extraSlotEndTime} tidak ditemukan."
            );

            $slotOptionIds[] = $extraSlotOptionId;
            $slotOptionIdsByStartTime[$extraSlotStartTime] = $extraSlotOptionId;

            DB::table('pickup_slot_options')
                ->whereNotIn('id', $slotOptionIds)
                ->update([
                    'is_active' => false,
                ]);

            /*
            |--------------------------------------------------------------------------
            | 5. Canteen Pickup Slots
            |--------------------------------------------------------------------------
            */

            $buildCanteenSlots = function (
                int $canteenId,
                array $slotOptionIds,
                int $usedSlotsBeforeBreak,
                int $quota
            ): array {
                $canteenSlots = [];
                $cycleLength = $usedSlotsBeforeBreak + 1;

                foreach (array_values($slotOptionIds) as $index => $slotOptionId) {
                    $slotNumber = $index + 1;
                    $isBreakSlot = ($slotNumber % $cycleLength) === 0;

                    if ($isBreakSlot) {
                        continue;
                    }

                    $canteenSlots[] = [
                        'canteen_id' => $canteenId,
                        'pickup_slot_option_id' => $slotOptionId,
                        'quota' => $quota,
                    ];
                }

                return $canteenSlots;
            };

            $canteenSlots = array_merge(
                $buildCanteenSlots($barokahCanteenId, $regularSlotOptionIds, 5, 10),
                $buildCanteenSlots($dharmaCanteenId, $regularSlotOptionIds, 4, 12),
                [
                    [
                        'canteen_id' => $barokahCanteenId,
                        'pickup_slot_option_id' => $extraSlotOptionId,
                        'quota' => 10,
                    ],
                    [
                        'canteen_id' => $dharmaCanteenId,
                        'pickup_slot_option_id' => $extraSlotOptionId,
                        'quota' => 12,
                    ],
                ]
            );

            DB::table('canteen_pickup_slots')
                ->whereIn('canteen_id', [$barokahCanteenId, $dharmaCanteenId])
                ->update([
                    'is_active' => false,
                    'updated_at' => $now,
                ]);

            foreach ($canteenSlots as $canteenSlot) {
                DB::table('canteen_pickup_slots')->updateOrInsert(
                    [
                        'canteen_id' => $canteenSlot['canteen_id'],
                        'pickup_slot_option_id' => $canteenSlot['pickup_slot_option_id'],
                    ],
                    [
                        'quota' => $canteenSlot['quota'],
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Menus
            |--------------------------------------------------------------------------
            */

            $desiredMenuNamesByCanteen = [
                $barokahCanteenId => [
                    'Ayam Geprek',
                    'Nasi Gila',
                ],
                $dharmaCanteenId => [
                    'Es Jeruk',
                    'Es Teh',
                    'Tahu Kocek',
                ],
            ];

            foreach ($desiredMenuNamesByCanteen as $canteenId => $desiredNames) {
                $menuIdsToDelete = DB::table('menus')
                    ->where('canteen_id', $canteenId)
                    ->whereNotIn('name', $desiredNames)
                    ->pluck('id');

                if ($menuIdsToDelete->isNotEmpty()) {
                    DB::table('cart_items')
                        ->whereIn('menu_id', $menuIdsToDelete)
                        ->delete();

                    DB::table('order_items')
                        ->whereIn('menu_id', $menuIdsToDelete)
                        ->delete();

                    DB::table('menus')
                        ->whereIn('id', $menuIdsToDelete)
                        ->delete();
                }
            }

            $menus = [
                [
                    'canteen_id' => $barokahCanteenId,
                    'category_id' => $makananId,
                    'name' => 'Ayam Geprek',
                    'price' => 12000,
                    'image_filename' => 'ayam-geprek.jpg',
                    'stock_qty' => 25,
                    'is_popular' => true,
                ],
                [
                    'canteen_id' => $barokahCanteenId,
                    'category_id' => $makananId,
                    'name' => 'Nasi Gila',
                    'price' => 14000,
                    'image_filename' => 'nasi-gila.jpg',
                    'stock_qty' => 15,
                    'is_popular' => true,
                ],
                [
                    'canteen_id' => $dharmaCanteenId,
                    'category_id' => $minumanId,
                    'name' => 'Es Jeruk',
                    'price' => 6000,
                    'image_filename' => 'esjeruk.jpg',
                    'stock_qty' => 30,
                    'is_popular' => true,
                ],
                [
                    'canteen_id' => $dharmaCanteenId,
                    'category_id' => $minumanId,
                    'name' => 'Es Teh',
                    'price' => 5000,
                    'image_filename' => 'esteh.jpg',
                    'stock_qty' => 40,
                    'is_popular' => false,
                ],
                [
                    'canteen_id' => $dharmaCanteenId,
                    'category_id' => $camilanId,
                    'name' => 'Tahu Kocek',
                    'price' => 8000,
                    'image_filename' => 'tahu-kocek.jpg',
                    'stock_qty' => 20,
                    'is_popular' => true,
                ],
            ];

            foreach ($menus as $menu) {
                DB::table('menus')->updateOrInsert(
                    [
                        'canteen_id' => $menu['canteen_id'],
                        'name' => $menu['name'],
                    ],
                    [
                        'category_id' => $menu['category_id'],
                        'price' => $menu['price'],
                        'image' => $readPublicImage($menu['image_filename']),
                        'stock_qty' => $menu['stock_qty'],
                        'is_popular' => $menu['is_popular'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            $ayamGeprekId = $requireId(
                DB::table('menus')
                    ->where('canteen_id', $barokahCanteenId)
                    ->where('name', 'Ayam Geprek')
                    ->value('id'),
                'Menu Ayam Geprek tidak ditemukan.'
            );

            $nasiGilaId = $requireId(
                DB::table('menus')
                    ->where('canteen_id', $barokahCanteenId)
                    ->where('name', 'Nasi Gila')
                    ->value('id'),
                'Menu Nasi Gila tidak ditemukan.'
            );

            $esTehId = $requireId(
                DB::table('menus')
                    ->where('canteen_id', $dharmaCanteenId)
                    ->where('name', 'Es Teh')
                    ->value('id'),
                'Menu Es Teh tidak ditemukan.'
            );

            $tahuKocekId = $requireId(
                DB::table('menus')
                    ->where('canteen_id', $dharmaCanteenId)
                    ->where('name', 'Tahu Kocek')
                    ->value('id'),
                'Menu Tahu Kocek tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 7. Carts
            |--------------------------------------------------------------------------
            */

            DB::table('carts')->updateOrInsert(
                [
                    'customer_id' => $budiId,
                    'canteen_id' => $barokahCanteenId,
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            DB::table('carts')->updateOrInsert(
                [
                    'customer_id' => $sariId,
                    'canteen_id' => $dharmaCanteenId,
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $budiCartId = $requireId(
                DB::table('carts')
                    ->where('customer_id', $budiId)
                    ->where('canteen_id', $barokahCanteenId)
                    ->value('id'),
                'Cart Budi untuk Kantin Barokah tidak ditemukan.'
            );

            $sariCartId = $requireId(
                DB::table('carts')
                    ->where('customer_id', $sariId)
                    ->where('canteen_id', $dharmaCanteenId)
                    ->value('id'),
                'Cart Sari untuk Kantin Dharma Wanita tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 8. Cart Items
            |--------------------------------------------------------------------------
            */

            DB::table('cart_items')->updateOrInsert(
                [
                    'cart_id' => $budiCartId,
                    'menu_id' => $ayamGeprekId,
                ],
                [
                    'quantity' => 2,
                ]
            );

            DB::table('cart_items')->updateOrInsert(
                [
                    'cart_id' => $budiCartId,
                    'menu_id' => $nasiGilaId,
                ],
                [
                    'quantity' => 1,
                ]
            );

            DB::table('cart_items')->updateOrInsert(
                [
                    'cart_id' => $sariCartId,
                    'menu_id' => $esTehId,
                ],
                [
                    'quantity' => 1,
                ]
            );

            DB::table('cart_items')->updateOrInsert(
                [
                    'cart_id' => $sariCartId,
                    'menu_id' => $tahuKocekId,
                ],
                [
                    'quantity' => 1,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 9. Sample Orders
            |--------------------------------------------------------------------------
            */

            $barokahPickupSlotId = $requireId(
                DB::table('canteen_pickup_slots')
                    ->where('canteen_id', $barokahCanteenId)
                    ->where('pickup_slot_option_id', $slotOptionIdsByStartTime['10:00:00'])
                    ->where('is_active', true)
                    ->value('id'),
                'Pickup slot 10:00 Kantin Barokah tidak ditemukan atau sedang tidak aktif.'
            );

            $samplePickupDate = $now->toDateString();

            DB::table('orders')->updateOrInsert(
                [
                    'customer_id' => $budiId,
                    'canteen_id' => $barokahCanteenId,
                    'canteen_pickup_slot_id' => $barokahPickupSlotId,
                    'pickup_date' => $samplePickupDate,
                ],
                [
                    'status' => 'diproses',
                    'note' => 'Tidak terlalu pedas.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $budiOrderId = $requireId(
                DB::table('orders')
                    ->where('customer_id', $budiId)
                    ->where('canteen_id', $barokahCanteenId)
                    ->where('canteen_pickup_slot_id', $barokahPickupSlotId)
                    ->where('pickup_date', $samplePickupDate)
                    ->value('id'),
                'Order Budi tidak ditemukan.'
            );

            /*
            |--------------------------------------------------------------------------
            | 10. Order Items
            |--------------------------------------------------------------------------
            */

            DB::table('order_items')->updateOrInsert(
                [
                    'order_id' => $budiOrderId,
                    'menu_id' => $ayamGeprekId,
                ],
                [
                    'quantity' => 1,
                ]
            );

            DB::table('order_items')->updateOrInsert(
                [
                    'order_id' => $budiOrderId,
                    'menu_id' => $nasiGilaId,
                ],
                [
                    'quantity' => 1,
                ]
            );
        });
    }
}
