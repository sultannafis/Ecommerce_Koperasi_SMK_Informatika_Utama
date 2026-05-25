<?php
namespace App\Helpers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartHelper
{
    /**
     * Method utama untuk mengambil semua data keranjang untuk view.
     * Method ini sudah mencakup pengecekan user dan rolenya.
     *
     * @return array
     */
    public static function getCartData(): array
    {
        $user = Auth::user();
        $defaultData = [
            'items' => collect(), // Collection kosong
            'count' => 0,         // Jumlah 0
            'total' => 0.0,       // Total harga 0
            'formattedCount' => '0' // Badge 0
        ];

        // Jika tidak ada user atau role-nya bukan customer, kembalikan data default
        if (!$user || $user->role !== 'customer') {
            return $defaultData;
        }

        try {
            $userId = $user->user_id;
            $count = self::getCartItemsCount($userId);
            $items = self::getCartItems($userId, 5);

            return [
                'items' => $items,
                'count' => $count,
                'total' => self::getCartTotal($userId),
                'formattedCount' => self::formatBadgeCount($count)
            ];
        } catch (\Exception $e) {
            Log::error('Error in CartHelper::getCartData(): ' . $e->getMessage());
            return $defaultData;
        }
    }

    /**
     * PERBAIKAN: Menghitung jumlah PRODUK UNIK di keranjang, bukan total quantity
     * Seperti Shopee - hanya menghitung berapa jenis produk, bukan total quantity
     *
     * @param string|null $userId - Jika null, ambil dari user yang login
     * @return int
     */
    public static function getCartItemsCount($userId = null): int
    {
        try {
            if (!$userId) {
                $user = Auth::user();
                if (!$user) {
                    return 0;
                }
                $userId = $user->user_id;
            }

            // PERUBAHAN: Gunakan count() untuk menghitung jumlah row/produk unik
            // Bukan sum('quantity') yang menghitung total quantity
            return Cart::where('user_id', $userId)->count();
        } catch (\Exception $e) {
            Log::error('Error in CartHelper::getCartItemsCount(): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mengambil cart items untuk user tertentu dengan limit
     * PERBAIKAN: Menambahkan eager loading yang lebih tepat dan error handling
     *
     * @param string|null $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCartItems($userId = null, int $limit = 5)
    {
        try {
            if (!$userId) {
                $user = Auth::user();
                if (!$user) {
                    return collect();
                }
                $userId = $user->user_id;
            }

            return Cart::with([
                    'product' => function($query) {
                        $query->select('product_id', 'name', 'image', 'price', 'stock')
                              ->where('is_active', true); // Hanya produk yang aktif
                    },
                    'admin' => function($query) {
                        $query->select('user_id', 'full_name');
                    }
                ])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->filter(function($cartItem) {
                    // Filter cart items yang produknya masih tersedia
                    return $cartItem->product !== null;
                });
        } catch (\Exception $e) {
            Log::error('Error in CartHelper::getCartItems(): ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Cek apakah user memiliki item di keranjang
     *
     * @param string|null $userId
     * @return bool
     */
    public static function hasCartItems($userId = null): bool
    {
        return self::getCartItemsCount($userId) > 0;
    }

    /**
     * Format badge count untuk cart
     */
    public static function formatBadgeCount($count)
    {
        if ($count > 99) {
            return '99+';
        }
        return (string)$count;
    }

    /**
     * Mendapatkan total harga dari semua item cart
     *
     * @param string|null $userId
     * @return float
     */
    public static function getCartTotal($userId = null): float
    {
        try {
            if (!$userId) {
                $user = Auth::user();
                if (!$user) {
                    return 0.0;
                }
                $userId = $user->user_id;
            }

            return (float) Cart::where('user_id', $userId)->sum('subtotal') ?? 0.0;
        } catch (\Exception $e) {
            Log::error('Error in CartHelper::getCartTotal(): ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * TAMBAHAN: Method untuk membersihkan cart items yang produknya sudah tidak tersedia
     * 
     * @param string|null $userId
     * @return int Jumlah item yang dibersihkan
     */
    public static function cleanInvalidCartItems($userId = null): int
    {
        try {
            if (!$userId) {
                $user = Auth::user();
                if (!$user) {
                    return 0;
                }
                $userId = $user->user_id;
            }

            // Hapus cart items yang produknya sudah tidak ada atau tidak aktif
            $deletedCount = Cart::where('user_id', $userId)
                ->whereDoesntHave('product', function($query) {
                    $query->where('is_active', true);
                })
                ->delete();

            if ($deletedCount > 0) {
                Log::info("Cleaned {$deletedCount} invalid cart items for user {$userId}");
            }

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Error in CartHelper::cleanInvalidCartItems(): ' . $e->getMessage());
            return 0;
        }
    }
}