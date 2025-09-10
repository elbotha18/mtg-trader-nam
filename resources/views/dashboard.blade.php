<x-layouts.app :title="__('My Cards')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="flex h-full w-full flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-800">
                    <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                        {{ __('My Cards') }}
                    </h1>
                    
                    <!-- Quick Add Section -->
                    <div class="flex-1 flex justify-center gap-4">                        
                        <input id="cardSearchInput" type="text" placeholder="{{ __('Search cards...') }}" class="w-full max-w-xs rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" oninput="filterCards()">
                    </div>
                    
                    <!-- Add Card and Add Bulk Buttons -->
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input 
                                id="quickAddInput" 
                                type="text" 
                                placeholder="{{ __('Type 3 letters to quick add...') }}" 
                                class="w-64 rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" 
                                oninput="quickSearch(this.value)"
                            >
                            <div id="quickSearchResults" class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>
                        <button onclick="openAddCardsModal()" class="btn btn-primary cursor-pointer px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{ __('Add Cards') }}
                        </button>
                    </div>
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
                                        {{ __('Type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Set') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Number') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Attributes') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                                @foreach($cards as $card)
                                    @if(!$card)
                                        continue;
                                    @endif
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            <span class="card-name-hover cursor-pointer"
                                                data-id="{{ $card->id }}"
                                                data-set="{{ $card->set }}"
                                                data-number="{{ $card->number }}"
                                                data-name="{{ $card->name }}"
                                                data-image-url="{{ $card->image_url }}">
                                                {{ $card->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->type_line ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->set }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $card->number ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="card-attributes" style="flex-wrap: wrap; max-width: 100vw; overflow-x: auto;">
                                                @if($card->is_foil)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ __('Foil') }}
                                                    </span>
                                                @endif
                                                @if($card->is_borderless)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        {{ __('Borderless') }}
                                                    </span>
                                                @endif
                                                @if($card->is_retro_frame)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        {{ __('Retro Frame') }}
                                                    </span>
                                                @endif
                                                @if($card->is_etched_foil)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        {{ __('Etched Foil') }}
                                                    </span>
                                                @endif
                                                @if($card->is_judge_promo_foil)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        {{ __('Judge Promo Foil') }}
                                                    </span>
                                                @endif
                                                @if($card->is_japanese_language)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200">
                                                        {{ __('Japanese Language') }}
                                                    </span>
                                                @endif
                                                @if($card->is_signed_by_artist)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">
                                                        {{ __('Signed by Artist') }}
                                                    </span>
                                                @endif
                                                @if($card->is_private)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 ml-2">
                                                        {{ __('Private') }}
                                                    </span>
                                                @endif
                                            </div>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="#" 
                                                class="edit-card-link inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-900 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 dark:hover:text-white mr-2"
                                                data-card='@json($card)'>
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ url('/card/delete') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $card->id }}">
                                                <button type="submit" class="inline-flex cursor-pointer items-center px-3 py-1 rounded-md text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-900 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800 dark:hover:text-white">
                                                    {{ __('Delete') }}
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

    <!-- Edit Card Modal -->
    <div id="editCardModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="mx-4 w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-neutral-800">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-neutral-800 dark:text-neutral-200">
                    {{ __('Edit Card') }}
                </h2>
            </div>
            <form id="editCardForm" action="{{ url('/card/update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_card_id">
                
                <!-- Card Information Fields -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200 mb-3">
                        {{ __('Card Information') }}
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label for="edit_card_name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                {{ __('Name') }}
                            </label>
                            <input id="edit_card_name" name="name" type="text" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400">
                        </div>
                        <div>
                            <label for="edit_card_type_line" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                {{ __('Type') }}
                            </label>
                            <input id="edit_card_type_line" name="type_line" type="text" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" placeholder="e.g., Creature — Human Wizard">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="edit_card_set" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                    {{ __('Set') }}
                                </label>
                                <input id="edit_card_set" name="set" type="text" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" placeholder="e.g., DOM">
                            </div>
                            <div>
                                <label for="edit_card_collector_number" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                    {{ __('Number') }}
                                </label>
                                <input id="edit_card_collector_number" name="collector_number" type="text" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400" placeholder="e.g., 123">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Attributes') }}
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <label for="edit_card_is_foil" class="inline-flex items-center">
                                <input id="edit_card_is_foil" name="is_foil" type="checkbox" class="h-4 w-4 text-blue-600 border-neutral-300 rounded focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-blue-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Foil') }}</span>
                            </label>
                            <label for="edit_card_is_borderless" class="inline-flex items-center">
                                <input id="edit_card_is_borderless" name="is_borderless" type="checkbox" class="h-4 w-4 text-green-600 border-neutral-300 rounded focus:ring-green-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-green-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Borderless') }}</span>
                            </label>
                            <label for="edit_card_is_retro_frame" class="inline-flex items-center">
                                <input id="edit_card_is_retro_frame" name="is_retro_frame" type="checkbox" class="h-4 w-4 text-yellow-600 border-neutral-300 rounded focus:ring-yellow-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-yellow-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Retro Frame') }}</span>
                            </label>
                            <label for="edit_card_is_etched_foil" class="inline-flex items-center">
                                <input id="edit_card_is_etched_foil" name="is_etched_foil" type="checkbox" class="h-4 w-4 text-purple-600 border-neutral-300 rounded focus:ring-purple-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-purple-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Etched Foil') }}</span>
                            </label>
                            <label for="edit_card_is_judge_promo_foil" class="inline-flex items-center">
                                <input id="edit_card_is_judge_promo_foil" name="is_judge_promo_foil" type="checkbox" class="h-4 w-4 text-red-600 border-neutral-300 rounded focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-red-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Judge Promo Foil') }}</span>
                            </label>
                            <label for="edit_card_is_japanese_language" class="inline-flex items-center">
                                <input id="edit_card_is_japanese_language" name="is_japanese_language" type="checkbox" class="h-4 w-4 text-teal-600 border-neutral-300 rounded focus:ring-teal-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-teal-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Japanese Language') }}</span>
                            </label>
                            <label for="edit_card_is_signed_by_artist" class="inline-flex items-center">
                                <input id="edit_card_is_signed_by_artist" name="is_signed_by_artist" type="checkbox" class="h-4 w-4 text-pink-600 border-neutral-300 rounded focus:ring-pink-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-pink-400">
                                <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">{{ __('Signed by Artist') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Privacy') }}
                        </label>
                        <label for="edit_card_is_private" class="inline-flex items-center">
                            <input id="edit_card_is_private" name="is_private" type="checkbox" class="h-4 w-4 text-blue-600 border-neutral-300 rounded focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-blue-400">
                            <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300 flex items-center">
                                {{ __('Private') }}
                                <span class="ml-1 relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                    <span class="absolute left-1/2 bottom-full mb-2 w-56 -translate-x-1/2 rounded bg-neutral-800 px-3 py-2 text-xs text-white opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50 text-center">
                                        {{ __("Other users can't see your private cards") }}
                                    </span>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditCardModal()" class="px-4 py-2 cursor-pointer text-sm font-medium text-neutral-700 bg-neutral-100 border border-neutral-300 rounded-md hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-500 dark:bg-neutral-600 dark:text-neutral-200 dark:border-neutral-500 dark:hover:bg-neutral-700">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 cursor-pointer text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('Save Changes') }}
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
        let quickSearchTimeout;
        let currentQuery = '';
        let currentOffset = 0;
        
        function quickSearch(query) {
            const resultsDiv = document.getElementById('quickSearchResults');
            
            // Clear previous timeout
            if (quickSearchTimeout) {
                clearTimeout(quickSearchTimeout);
            }
            
            // Hide results if less than 3 characters
            if (query.length < 3) {
                resultsDiv.classList.add('hidden');
                currentQuery = '';
                currentOffset = 0;
                return;
            }
            
            // Reset if this is a new search
            if (query !== currentQuery) {
                currentQuery = query;
                currentOffset = 0;
            }
            
            // Debounce the search to avoid too many requests
            quickSearchTimeout = setTimeout(async () => {
                await performSearch(query, 0, true);
            }, 300);
        }
        
        async function performSearch(query, offset = 0, resetResults = false) {
            const resultsDiv = document.getElementById('quickSearchResults');
            
            try {
                const response = await fetch(`/cards/search?q=${encodeURIComponent(query)}&offset=${offset}`);
                const data = await response.json();
                
                if (data.cards && data.cards.length > 0) {
                    const cardElements = data.cards.map(card => 
                        `<div class="px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-700 cursor-pointer border-b border-neutral-200 dark:border-neutral-600" onclick="selectQuickCard(${card.id}, '${card.name.replace(/'/g, "\\'")}')">
                            ${card.name}
                        </div>`
                    ).join('');
                    
                    if (resetResults) {
                        resultsDiv.innerHTML = cardElements;
                    } else {
                        // Remove existing load more button if present
                        const existingLoadMore = resultsDiv.querySelector('.load-more-btn');
                        if (existingLoadMore) {
                            existingLoadMore.remove();
                        }
                        resultsDiv.insertAdjacentHTML('beforeend', cardElements);
                    }
                    
                    // Add load more button if there are more results
                    if (data.hasMore) {
                        const loadMoreBtn = `<div class="load-more-btn px-3 py-2 text-center border-t border-neutral-200 dark:border-neutral-600 bg-neutral-50 dark:bg-neutral-750 hover:bg-neutral-100 dark:hover:bg-neutral-700 cursor-pointer text-blue-600 dark:text-blue-400 font-medium" onclick="loadMoreResults()">
                            Load More
                        </div>`;
                        resultsDiv.insertAdjacentHTML('beforeend', loadMoreBtn);
                    }
                    
                    resultsDiv.classList.remove('hidden');
                    currentOffset = offset + data.cards.length;
                } else if (resetResults) {
                    resultsDiv.innerHTML = '<div class="px-3 py-2 text-neutral-500 dark:text-neutral-400">No cards found</div>';
                    resultsDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching cards:', error);
                if (resetResults) {
                    resultsDiv.innerHTML = '<div class="px-3 py-2 text-red-500">Error searching cards</div>';
                    resultsDiv.classList.remove('hidden');
                }
            }
        }
        
        async function loadMoreResults() {
            if (currentQuery) {
                await performSearch(currentQuery, currentOffset, false);
            }
        }
        
        function selectQuickCard(cardId, cardName) {
            // Clear the input and hide results first
            document.getElementById('quickAddInput').value = '';
            document.getElementById('quickSearchResults').classList.add('hidden');
            
            // Send request to add the card
            fetch('/card/quick-add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    card_id: cardId 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page to show the new card
                    window.location.reload();
                } else {
                    // Show error message
                    alert(data.message || 'Failed to add card.');
                }
            })
            .catch(error => {
                console.error('Error adding card:', error);
                alert('An error occurred while adding the card.');
            });
        }
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            const quickAddInput = document.getElementById('quickAddInput');
            const quickSearchResults = document.getElementById('quickSearchResults');
            
            if (!quickAddInput.contains(e.target) && !quickSearchResults.contains(e.target)) {
                quickSearchResults.classList.add('hidden');
            }
        });

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

        function openEditCardModal(card) {
            const modal = document.getElementById('editCardModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            // Fill form fields
            document.getElementById('edit_card_id').value = card.id;
            document.getElementById('edit_card_is_foil').checked = card.is_foil;
            document.getElementById('edit_card_is_borderless').checked = card.is_borderless;
            document.getElementById('edit_card_is_retro_frame').checked = card.is_retro_frame;
            document.getElementById('edit_card_is_etched_foil').checked = card.is_etched_foil;
            document.getElementById('edit_card_is_judge_promo_foil').checked = card.is_judge_promo_foil;
            document.getElementById('edit_card_is_japanese_language').checked = card.is_japanese_language;
            document.getElementById('edit_card_is_signed_by_artist').checked = card.is_signed_by_artist;
            document.getElementById('edit_card_is_private').checked = card.is_private;
        }

        function closeEditCardModal() {
            const modal = document.getElementById('editCardModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function filterCards() {
            const input = document.getElementById('cardSearchInput').value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const name = row.querySelector('td:nth-child(1)')?.textContent?.toLowerCase() || '';
                const type = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
                const set = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
                const number = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
                if (name.includes(input) || type.includes(input) || set.includes(input) || number.includes(input)) {
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
        document.getElementById('editCardModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditCardModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const addModal = document.getElementById('addCardsModal');
                const editModal = document.getElementById('editCardModal');
                if (!addModal.classList.contains('hidden')) {
                    closeAddCardsModal();
                }
                if (!editModal.classList.contains('hidden')) {
                    closeEditCardModal();
                }
            }
        });

        // Add event listeners to Edit links
        document.querySelectorAll('.edit-card-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const card = JSON.parse(this.dataset.card);
                openEditCardModal(card);
            });
        });

        // Card image popup
        var popup = document.getElementById('card-image-popup');
        var popupImg = document.getElementById('popup-img');
        var popupLoading = document.getElementById('popup-loading');

        document.addEventListener('mouseover', async function(e) {
            const target = e.target.classList.contains('card-name-hover') ? e.target : null;
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
            if (e.target.classList.contains('card-name-hover')) {
                popup.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app>
