<x-layouts.app :title="__('Wishlist')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="flex h-full w-full flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-800">
                    <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                        {{ __('Wishlist') }}
                    </h1>
                    <div class="flex-1 flex justify-center">
                        <input id="cardSearchInput" type="text" placeholder="{{ __('Search cards...') }}" class="w-full max-w-xs rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" oninput="filterCards()">
                    </div>
                    <!-- Add this after the search input in your toolbar -->
                    <button
                        onclick="openAddWishlistModal()"
                        class="btn btn-primary cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        type="button"
                    >
                        {{ __('Add Bulk to Wishlist') }}
                    </button>
                </div>
                <!-- display table with $cards -->
                <div class="flex-1 overflow-auto p-4">
                    @if($cards->isEmpty())
                        <p class="text-center text-gray-500">{{ __('No cards found. You can add cards with the button in the top right.') }}</p>
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
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                                @foreach($cards as $card)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->set }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->number ?? __('N/A') }}
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
            const input = document.getElementById('cardSearchInput').value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.querySelector('td:nth-child(1)')?.textContent?.toLowerCase() || '';
                const set = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
                const number = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
                if (name.includes(input) || set.includes(input) || number.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
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
    </script>
</x-layouts.app>
