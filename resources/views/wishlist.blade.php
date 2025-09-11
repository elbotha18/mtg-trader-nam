<x-layouts.app :title="__('Wishlist')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="flex h-full w-full flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-800">
                    <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                        {{ __('Wishlist') }}
                    </h1>
                    <div class="flex gap-2 items-center">
                        <input id="cardSearchInput" type="text" placeholder="{{ __('Search cards...') }}" class="w-full max-w-xs rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" oninput="filterCards()">
                        <select id="sellerFilterSelect" onchange="handleSellerFilter()" class="w-full max-w-xs rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400">
                            <option value="">{{ __('All sellers') }}</option>
                            @foreach($availableSellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('seller_filter') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button
                            onclick="copyVisibleList()"
                            class="cursor-pointer px-4 py-2 text-sm font-medium text-neutral-700 bg-neutral-100 border border-neutral-300 rounded-md hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 dark:bg-neutral-600 dark:text-neutral-200 dark:border-neutral-500 dark:hover:bg-neutral-700"
                            type="button"
                        >
                            {{ __('Copy List') }}
                        </button>
                        <button
                            onclick="openAddWishlistModal()"
                            class="btn btn-primary cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="button"
                        >
                            {{ __('Add Bulk to Wishlist') }}
                        </button>
                    </div>
                </div>
                <!-- display table with $cards -->
                <div class="flex-1 overflow-auto p-4">
                    @if(request('seller_filter'))
                        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                {{ __('Showing cards sold by: ') }}<strong>{{ $availableSellers->firstWhere('id', request('seller_filter'))->name ?? 'Unknown Seller' }}</strong>
                                <a href="{{ route('wishlist') }}" class="ml-2 text-blue-600 dark:text-blue-400 underline hover:no-underline">{{ __('Clear filter') }}</a>
                            </p>
                        </div>
                    @endif
                    @if($cards->isEmpty())
                        @if(request('seller_filter'))
                            <p class="text-center text-gray-500">{{ __('No cards found from the selected seller. Try a different seller or ') }}<a href="{{ route('wishlist') }}" class="text-blue-600 underline">{{ __('clear the filter') }}</a>{{ __('.') }}</p>
                        @else
                            <p class="text-center text-gray-500">{{ __('No cards found. You can add cards with the button in the top right.') }}</p>
                        @endif
                    @else
                        <table class="w-full max-w-full divide-y divide-neutral-200 dark:divide-neutral-700 table-fixed">
                            <thead class="bg-neutral-100 dark:bg-neutral-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Set') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Number') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Sellers') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                                @foreach($cards as $card)
                                    <tr class="card-name-hover cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 transition"
                                        data-id="{{ $card->id }}"
                                        data-set="{{ $card->set }}"
                                        data-number="{{ $card->collector_number }}"
                                        data-name="{{ $card->name }}"
                                        data-image-url="{{ $card->image_url }}"
                                        onclick="if(!event.target.closest('button,svg,path')) window.location='/card?name={{ urlencode($card->name) }}&set={{ urlencode($card->set) }}&number={{ urlencode($card->collector_number) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->set }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->collector_number ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->sellers }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('wishlist.toggle') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="card_id" value="{{ $card->id }}">
                                                <input type="hidden" name="redirect_back" value="true">
                                                <!-- cross icon -->
                                                <button type="submit" class="cursor-pointer text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Cards Modal -->
    <div id="addCardsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-neutral-800">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-neutral-800 dark:text-neutral-200">
                    {{ __('Add Cards') }}
                </h2>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Enter card details, one per line') }}
                </p>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Fully compatible with Moxfield exports') }}
                </p>
            </div>
            
            <form action="{{ url('/cards/add') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="cards_textarea" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                        {{ __('Cards') }}
                    </label>
                    <textarea 
                        id="cards_textarea" 
                        name="cards" 
                        rows="8" 
                        class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400"
                        placeholder="{{ __('Example: Sol Ring (C21) 263 *F*') }}"
                        required></textarea>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeAddCardsModal()" 
                        class="px-4 py-2 cursor-pointer text-sm font-medium text-neutral-700 bg-neutral-100 border border-neutral-300 rounded-md hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 dark:bg-neutral-600 dark:text-neutral-200 dark:border-neutral-500 dark:hover:bg-neutral-700">
                        {{ __('Cancel') }}
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 cursor-pointer text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Add Cards') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Add to Wishlist Modal -->
    <div id="addWishlistModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-neutral-800">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-neutral-800 dark:text-neutral-200">
                    {{ __('Add Cards to Wishlist') }}
                </h2>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Enter card details, one per line') }}
                </p>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('Fully compatible with Moxfield exports') }}
                </p>
            </div>
            <form id="bulkWishlistForm" method="POST" action="{{ url('/wishlist/bulk-add') }}">
                @csrf
                <div class="mb-4">
                    <label for="wishlist_cards_textarea" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                        {{ __('Cards') }}
                    </label>
                    <textarea
                        id="wishlist_cards_textarea"
                        name="cards"
                        rows="8"
                        class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400"
                        placeholder="{{ __('Example: Sol Ring (C21) 263') }}"
                        required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeAddWishlistModal()"
                        class="px-4 py-2 cursor-pointer text-sm font-medium text-neutral-700 bg-neutral-100 border border-neutral-300 rounded-md hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 dark:bg-neutral-600 dark:text-neutral-200 dark:border-neutral-500 dark:hover:bg-neutral-700">
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 cursor-pointer text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Add to Wishlist') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Image Popup -->
    <div id="card-image-popup" class="hidden fixed z-50 bg-white dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 p-2" style="min-width:200px; pointer-events:none;">
        <span id="popup-loading" class="text-xs text-neutral-400">Loading...</span>
        <img id="popup-img" src="" alt="Card image" class="w-64 h-auto rounded-lg shadow-md hidden" />
    </div>

    <script>
        function openAddCardsModal() {
            const modal = document.getElementById('addCardsModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            // Focus the textarea when modal opens
            setTimeout(() => {
                document.getElementById('cards_textarea').focus();
            }, 100);
        }

        function closeAddCardsModal() {
            const modal = document.getElementById('addCardsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            
            // Clear the textarea
            document.getElementById('cards_textarea').value = '';
        }

        function openAddWishlistModal() {
            const modal = document.getElementById('addWishlistModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                document.getElementById('wishlist_cards_textarea').focus();
            }, 100);
        }
        function closeAddWishlistModal() {
            const modal = document.getElementById('addWishlistModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            document.getElementById('wishlist_cards_textarea').value = '';
        }

        function filterCards() {
            const cardInput = document.getElementById('cardSearchInput').value.toLowerCase();
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.querySelector('td:nth-child(1)')?.textContent?.toLowerCase() || '';
                const set = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
                const number = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
                
                const matchesCard = cardInput === '' || name.includes(cardInput) || set.includes(cardInput) || number.includes(cardInput);
                
                if (matchesCard) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function handleSellerFilter() {
            const sellerFilter = document.getElementById('sellerFilterSelect').value;
            const currentUrl = new URL(window.location);
            
            if (sellerFilter === '') {
                currentUrl.searchParams.delete('seller_filter');
            } else {
                currentUrl.searchParams.set('seller_filter', sellerFilter);
            }
            
            window.location.href = currentUrl.toString();
        }

        function copyVisibleList() {
            const visibleRows = Array.from(document.querySelectorAll('tbody tr'))
                .filter(row => row.style.display !== 'none');
            
            if (visibleRows.length === 0) {
                alert('{{ __("No cards to copy") }}');
                return;
            }
            
            const cardList = visibleRows.map(row => {
                const name = row.querySelector('td:nth-child(1)')?.textContent?.trim() || '';
                const set = row.querySelector('td:nth-child(2)')?.textContent?.trim() || '';
                const number = row.querySelector('td:nth-child(3)')?.textContent?.trim() || '';
                
                // Format as "Card Name (SET) Number" - compatible with Moxfield format
                if (set && number && number !== 'N/A') {
                    return `${name} (${set}) ${number}`;
                } else if (set) {
                    return `${name} (${set})`;
                } else {
                    return name;
                }
            }).join('\n');
            
            // Copy to clipboard
            navigator.clipboard.writeText(cardList).then(() => {
                // Show success message
                showCopySuccess();
            }).catch(err => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = cardList;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showCopySuccess();
            });
        }

        function showCopySuccess() {
            // Create a temporary success message
            const message = document.createElement('div');
            message.textContent = '{{ __("Copied to clipboard!") }}';
            message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300';
            document.body.appendChild(message);
            
            // Remove the message after 2 seconds
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => {
                    if (message.parentNode) {
                        message.parentNode.removeChild(message);
                    }
                }, 300);
            }, 2000);
        }

        // Close modal when clicking outside of it
        document.getElementById('addCardsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddCardsModal();
            }
        });

        document.getElementById('addWishlistModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddWishlistModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const addModal = document.getElementById('addCardsModal');
                const wishlistModal = document.getElementById('addWishlistModal');
                if (!addModal.classList.contains('hidden')) {
                    closeAddCardsModal();
                }
                if (!wishlistModal.classList.contains('hidden')) {
                    closeAddWishlistModal();
                }
            }
        });

var popup = document.getElementById('card-image-popup');
var popupImg = document.getElementById('popup-img');
var popupLoading = document.getElementById('popup-loading');

document.addEventListener('mouseover', async function(e) {
    const target = e.target.closest('.card-name-hover');
    if (target) {
        const cardId = target.getAttribute('data-id');
        const set = target.getAttribute('data-set');
        const number = target.getAttribute('data-number');
        const name = target.getAttribute('data-name');
        const imageUrl = target.getAttribute('data-image-url');
        popup.style.left = (e.clientX + 20) + 'px';
        popup.style.top = (e.clientY + 10) + 'px';
        popup.classList.remove('hidden');
        popupImg.classList.add('hidden');
        popupLoading.classList.remove('hidden');
        if (imageUrl) {
            popupImg.src = imageUrl;
            popupImg.alt = name;
            popupImg.classList.remove('hidden');
            popupLoading.classList.add('hidden');
        } else {
            try {
                const resp = await fetch(`https://api.scryfall.com/cards/${set.toLowerCase()}/${number}`);
                if (resp.ok) {
                    const data = await resp.json();
                    if (data.image_uris && data.image_uris.normal) {
                        popupImg.src = data.image_uris.normal;
                        popupImg.alt = name;
                        popupImg.classList.remove('hidden');
                        popupLoading.classList.add('hidden');
                        // Store image URL in backend
                        if (cardId) {
                            fetch('/cards/add-image', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ card_id: cardId, image_url: data.image_uris.normal })
                            }).then(() => {
                                // Update the DOM so future hovers use the stored image
                                target.setAttribute('data-image-url', data.image_uris.normal);
                            });
                        }

                    } else {
                        popupLoading.textContent = 'No image found.';
                    }
                } else {
                    popupLoading.textContent = 'No image found.';
                }
            } catch {
                popupLoading.textContent = 'Error loading image.';
            }
        }
    }
});
document.addEventListener('mousemove', function(e) {
    if (!popup.classList.contains('hidden')) {
        popup.style.left = (e.clientX + 20) + 'px';
        popup.style.top = (e.clientY + 10) + 'px';
    }
});
document.addEventListener('mouseout', function(e) {
    if (e.target.closest('.card-name-hover')) {
        popup.classList.add('hidden');
    }
});
    </script>
</x-layouts.app>
