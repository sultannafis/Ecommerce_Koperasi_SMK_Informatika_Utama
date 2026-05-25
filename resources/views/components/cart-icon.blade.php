{{-- File: resources/views/components/cart-dropdown.blade.php --}}
@props(['cartItems' => collect(), 'count' => 0])

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full transition-colors duration-200">
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7a1 1 0 00.9 1.5h12.9M7 13L5.4 7M16 21a1 1 0 100-2 1 1 0 000 2zm-8 0a1 1 0 100-2 1 1 0 000 2z" />
</svg>
        
        @if($count > 0)
            <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-pulse shadow-sm">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 py-1 ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Keranjang Belanja</h3>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $count }} Item
                </span>
            </div>
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($cartItems as $cartItem)
                <div class="px-4 py-4 hover:bg-gray-50 border-b border-gray-50 transition-colors duration-150">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($cartItem->product && $cartItem->product->image)
                                <img src="{{ asset('storage/' . $cartItem->product->image) }}" 
                                     alt="{{ $cartItem->product->name ?? 'Product Image' }}" 
                                     class="w-12 h-12 object-cover rounded-lg border-2 border-white shadow-sm"
                                     onerror="this.src='https://via.placeholder.com/48x48/e5e7eb/374151?text=No+Image';">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center border-2 border-white shadow-sm">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex flex-col flex-1 min-w-0">
                                    @if($cartItem->product)
                                        <a href="{{ route('shop.products.show', $cartItem->product->product_id) }}" 
                                           class="text-sm font-semibold text-gray-900 hover:text-blue-700 transition-colors duration-150 truncate group-hover:text-blue-700"
                                           @click="open = false">
                                            {{ $cartItem->product->name }}
                                        </a>
                                    @else
                                        <span class="text-sm font-semibold text-red-500">Produk Tidak Ditemukan</span>
                                    @endif
                                    
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xs text-gray-500">
                                            Qty: {{ $cartItem->quantity }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $cartItem->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-2">
                                @if($cartItem->product)
                                    <div class="flex flex-col">
                                        <p class="text-sm font-bold text-green-600">
                                            Rp {{ number_format($cartItem->subtotal, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @ Rp {{ number_format($cartItem->unit_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    
                                    <a href="{{ route('shop.products.show', $cartItem->product->product_id) }}" 
                                       class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-white hover:bg-blue-600 rounded border border-blue-200 hover:border-blue-600 transition-all duration-200"
                                       @click="open = false">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </a>
                                @else
                                    <div class="flex flex-col">
                                        <p class="text-sm font-bold text-red-500">
                                            Produk Tidak Tersedia
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.68 4.34a2 2 0 001.85 2.66h9.66M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-8m-8 0V9a2 2 0 012-2h4a2 2 0 012 2v4.01"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900 mb-1">Keranjang Kosong</p>
                        <p class="text-xs text-gray-500">Belum ada produk yang ditambahkan.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($count > 0)
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                <a href="{{ route('carts.index') }}" 
                   class="block w-full text-center py-2 px-4 text-sm font-medium text-blue-600 hover:text-white hover:bg-blue-600 rounded-md transition-all duration-200 border border-blue-200 hover:border-blue-600"
                   @click="open = false">
                    Lihat Keranjang Lengkap ({{ $count }})
                </a>
            </div>
        @endif
    </div>
</div>