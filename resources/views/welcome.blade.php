<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAMTG Trader</title>
    <meta name="description" content="Browse the latest Magic: The Gathering cards and sellers in Namibia. Search, filter, and connect with local players.">
    <meta property="og:image" content="/logo.webp">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="/logo.webp">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite('resources/css/app.css')
    <style>
        #card-image-popup {
            transition: opacity 0.15s;
            opacity: 1;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen flex flex-col">
    <!-- Header -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-[#161615] shadow-sm">
        <div class="flex items-center gap-2">
            <img src="/logo.webp" alt="MTG Logo" class="h-8 w-8 rounded" />
            <span class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">NAMTG Trader</span>
        </div>
        <nav class="flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-1.5 rounded bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-1.5 rounded bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-1.5 rounded bg-neutral-200 text-[#1b1b18] font-medium hover:bg-neutral-300 transition">Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </header>

    <!-- Intro -->
    <section class="w-full max-w-2xl mx-auto mt-10 mb-6 px-4">
        <h1 class="text-3xl font-bold mb-2 text-center dark:text-neutral-400">Welcome to NAMTG Trader</h1>
        <h2 class="text-lg text-center text-neutral-600 dark:text-neutral-400 mb-4">Namibia's Magic Card Marketplace</h2>
        <p class="text-center text-neutral-700 dark:text-neutral-300 mb-4">
            This site helps Magic: The Gathering players in Namibia find, buy, and sell cards locally. Browse the latest cards, search for what you need, and connect with sellers in your area.
        </p>
    </section>

    <!-- Search Bar -->
    <section class="w-full max-w-2xl mx-auto px-4 mb-6">
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <input id="searchInput" type="text" placeholder="Search for a card by name, set, or number..." class="w-full rounded-md border border-neutral-300 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:focus:border-blue-400">
                <button id="advancedToggle" type="button" class="flex items-center px-2 py-2 rounded border border-neutral-300 dark:border-blue-500 bg-white dark:bg-blue-950 hover:bg-neutral-100 dark:hover:bg-blue-900 transition focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Advanced search">
                    <svg id="advancedChevronDown" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-700 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    <svg id="advancedChevronUp" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden text-neutral-700 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                </button>
            </div>
            <div id="advancedOptions" class="hidden mt-2 bg-neutral-50 dark:bg-neutral-800 rounded p-3 border border-neutral-200 dark:border-neutral-700">
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-blue-600 border-neutral-300 rounded focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-blue-400" value="is_foil">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Foil</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-green-600 border-neutral-300 rounded focus:ring-green-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-green-400" value="is_borderless">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Borderless</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-yellow-600 border-neutral-300 rounded focus:ring-yellow-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-yellow-400" value="is_retro_frame">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Retro Frame</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-purple-600 border-neutral-300 rounded focus:ring-purple-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-purple-400" value="is_etched_foil">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Etched Foil</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-red-600 border-neutral-300 rounded focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-red-400" value="is_judge_promo_foil">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Judge Promo Foil</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-teal-600 border-neutral-300 rounded focus:ring-teal-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-teal-400" value="is_japanese_language">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Japanese Language</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="adv-attr h-4 w-4 text-pink-600 border-neutral-300 rounded focus:ring-pink-500 dark:border-neutral-600 dark:bg-neutral-700 dark:focus:ring-pink-400" value="is_signed_by_artist">
                        <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">Signed by Artist</span>
                    </label>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards Table -->
    <section class="w-full max-w-4xl mx-auto px-4 flex-1">
        <div class="overflow-x-auto w-full rounded-lg shadow bg-white dark:bg-[#161615]">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700 table-fixed">
                <thead class="bg-neutral-100 dark:bg-neutral-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Set</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Attributes</th>
                    </tr>
                </thead>
                <tbody id="cardsTableBody" class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                    <!-- Cards will be loaded here -->
                </tbody>
            </table>
        </div>
    </section>

    <!-- Pagination -->
    <div id="pagination" class="flex justify-center items-center gap-2 mt-4">
        <!-- Pagination buttons will be rendered here -->
    </div>

    <!-- Card Image Popup -->
    <div id="card-image-popup" class="hidden fixed z-50 bg-white dark:bg-neutral-900 rounded-lg shadow-lg border border-neutral-200 dark:border-neutral-700 p-2" style="min-width:200px; pointer-events:none;">
        <span id="popup-loading" class="text-xs text-neutral-400">Loading...</span>
        <img id="popup-img" src="" alt="Card image" class="w-64 h-auto rounded-lg shadow-md hidden" />
    </div>

    <script>
        // Dark mode logic (system default)
        if (
            (localStorage.theme === 'dark') ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script>
        let allCards = [];
        let currentPage = 1;
        const perPage = 50;

        function getAdvancedAttributes() {
            const attrs = [];
            document.querySelectorAll('.adv-attr').forEach(cb => {
                if (cb.checked) attrs.push(cb.value);
            });
            return attrs;
        }
        function isAdvancedActive() {
            return !document.getElementById('advancedOptions').classList.contains('hidden');
        }
        async function fetchCards(query = '') {
            let url = `/cards?search=${encodeURIComponent(query)}`;
            if (isAdvancedActive()) {
                const attrs = getAdvancedAttributes();
                if (attrs.length > 0) {
                    url += `&advanced=1&attributes=${encodeURIComponent(attrs.join(','))}`;
                }
            }
            const res = await fetch(url);
            allCards = await res.json();
            currentPage = 1;
            renderCards();
            renderPagination();
        }
        function renderCards() {
            const tbody = document.getElementById('cardsTableBody');
            tbody.innerHTML = '';
            if (!allCards.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-neutral-500">No cards found.</td></tr>`;
                return;
            }
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const cards = allCards.slice(start, end);
            for (const card of cards) {
                // Build showCard URL with advanced/attributes if present
                let url = `/card?name=${encodeURIComponent(card.name)}&set=${encodeURIComponent(card.set)}&number=${encodeURIComponent(card.number || '')}`;
                if (isAdvancedActive()) {
                    const attrs = getAdvancedAttributes();
                    if (attrs.length > 0) {
                        url += `&advanced=1&attributes=${encodeURIComponent(attrs.join(','))}`;
                    }
                }
                tbody.innerHTML += `
                    <tr class="cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 transition"
                        onclick="if(!event.target.closest('.wishlist-btn')) window.location='${url}'">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200 card-name-hover"
                            data-set="${card.set}"
                            data-number="${card.number || ''}"
                            data-name="${card.name}"
                            data-image-url="${card.image_url || ''}">
                            ${window.isLoggedIn ? `
                                <button class="wishlist-btn mr-2" data-card-id="${card.id}" aria-label="Add to wishlist" style="background:none;border:none;cursor:pointer;">
                                    <svg class="w-5 h-4 ${card.is_wishlisted ? 'text-red-500 fill-red-400' : 'text-red-400'} hover:text-red-600 transition" 
                                         fill="${card.is_wishlisted ? 'currentColor' : 'none'}" 
                                         stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21C12 21 4 13.5 4 8.5C4 5.42 6.42 3 9.5 3C11.24 3 12.91 3.81 14 5.08C15.09 3.81 16.76 3 18.5 3C21.58 3 24 5.42 24 8.5C24 13.5 16 21 16 21H12Z"/>
                                    </svg>
                                </button>
                            ` : ''}
                            ${card.name}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">${card.set}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">${card.number || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200 max-w-xs overflow-x-auto">
                            <div class="card-attributes flex flex-wrap gap-1">
                                ${card.is_foil ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Foil</span>' : ''}
                                ${card.is_borderless ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Borderless</span>' : ''}
                                ${card.is_retro_frame ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Retro Frame</span>' : ''}
                                ${card.is_etched_foil ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Etched Foil</span>' : ''}
                                ${card.is_judge_promo_foil ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Judge Promo Foil</span>' : ''}
                                ${card.is_japanese_language ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200">Japanese Language</span>' : ''}
                                ${card.is_signed_by_artist ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">Signed by Artist</span>' : ''}
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
        function renderPagination() {
            const lastPage = Math.ceil(allCards.length / perPage);
            let html = '';
            if (lastPage <= 1) {
                document.getElementById('pagination').innerHTML = '';
                return;
            }
            html += `<nav class="flex justify-center items-center gap-1 mt-6" aria-label="Pagination">`;

            // Previous button
            html += `<button class="px-3 py-1 rounded-full border border-neutral-300 cursor-pointer dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition disabled:opacity-50 disabled:cursor-not-allowed"
                ${currentPage === 1 ? 'disabled' : ''}
                onclick="goToPage(${currentPage - 1})"
                aria-label="Previous page">&lt;</button>`;

            let start = Math.max(1, currentPage - 2);
            let end = Math.min(lastPage, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);

            if (start > 1) {
                html += `<button class="px-3 py-1 rounded-full border border-neutral-300 cursor-pointer dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition"
                    onclick="goToPage(1)">1</button>`;
                if (start > 2) html += `<span class="px-2 text-neutral-400">…</span>`;
            }
            for (let i = start; i <= end; i++) {
                html += `<button class="px-3 py-1 rounded-full border ${i === currentPage
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'border-neutral-300 cursor-pointer dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition'}"
                    onclick="goToPage(${i})"
                    ${i === currentPage ? 'aria-current="page"' : ''}>${i}</button>`;
            }
            if (end < lastPage) {
                if (end < lastPage - 1) html += `<span class="px-2 text-neutral-400">…</span>`;
                html += `<button class="px-3 py-1 rounded-full border border-neutral-300 cursor-pointer dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition"
                    onclick="goToPage(${lastPage})">${lastPage}</button>`;
            }

            // Next button
            html += `<button class="px-3 py-1 rounded-full border border-neutral-300 cursor-pointer dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-blue-100 dark:hover:bg-blue-900 transition disabled:opacity-50 disabled:cursor-not-allowed"
                ${currentPage === lastPage ? 'disabled' : ''}
                onclick="goToPage(${currentPage + 1})"
                aria-label="Next page">&gt;</button>`;

            html += `</nav>`;
            document.getElementById('pagination').innerHTML = html;
        }
        function goToPage(page) {
            const lastPage = Math.ceil(allCards.length / perPage);
            if (page < 1 || page > lastPage || page === currentPage) return;
            currentPage = page;
            renderCards();
            renderPagination();
        }
        // Debounced search with advanced
        let searchTimeout;
        function triggerSearch() {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchCards(document.getElementById('searchInput').value);
            }, 300);
        }
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (searchTimeout) clearTimeout(searchTimeout);
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchCards(this.value);
            } else {
                triggerSearch();
            }
        });
        // Advanced toggle logic
        document.getElementById('advancedToggle').addEventListener('click', function() {
            const adv = document.getElementById('advancedOptions');
            const chevronDown = document.getElementById('advancedChevronDown');
            const chevronUp = document.getElementById('advancedChevronUp');
            if (adv.classList.contains('hidden')) {
                adv.classList.remove('hidden');
                chevronDown.classList.add('hidden');
                chevronUp.classList.remove('hidden');
            } else {
                adv.classList.add('hidden');
                chevronDown.classList.remove('hidden');
                chevronUp.classList.add('hidden');
            }
        });
        // Trigger search when advanced checkboxes change
        document.querySelectorAll('.adv-attr').forEach(cb => {
            cb.addEventListener('change', triggerSearch);
        });
        // Initial load
        fetchCards();

        // Card image popup logic
        // Only declare if not already declared
        if (typeof popup === 'undefined') {
            var popup = document.getElementById('card-image-popup');
            var popupImg = document.getElementById('popup-img');
            var popupLoading = document.getElementById('popup-loading');
        }

        document.addEventListener('mouseover', async function(e) {
            const target = e.target.closest('.card-name-hover');
            if (target) {
                const set = target.getAttribute('data-set');
                const number = target.getAttribute('data-number');
                const name = target.getAttribute('data-name');
                const cardId = target.querySelector('.wishlist-btn')?.getAttribute('data-card-id');
                const imageUrl = target.getAttribute('data-image-url'); // Add this attribute when rendering if available

                popup.style.left = (e.clientX + 20) + 'px';
                popup.style.top = (e.clientY + 10) + 'px';
                popup.classList.remove('hidden');
                popupImg.classList.add('hidden');
                popupLoading.classList.remove('hidden');

                if (imageUrl) {
                    // Use stored image
                    popupImg.src = imageUrl;
                    popupImg.alt = name;
                    popupImg.classList.remove('hidden');
                    popupLoading.classList.add('hidden');
                } else {
                    // Fetch from Scryfall
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
                // Use clientX/clientY for viewport-relative positioning
                popup.style.left = (e.clientX + 20) + 'px';
                popup.style.top = (e.clientY + 10) + 'px';
            }
        });
        document.addEventListener('mouseout', function(e) {
            if (e.target.closest('.card-name-hover')) {
                popup.classList.add('hidden');
            }
        });
        // Wishlist button logic
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.wishlist-btn');
            if (btn) {
                e.stopPropagation(); // Prevent row click navigation
                e.preventDefault();
                const cardId = btn.getAttribute('data-card-id');
                try {
                    const res = await fetch('/toggle-wishlist', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ card_id: cardId })
                    });
                    const data = await res.json();
                    // Optionally, update the heart icon or show a message
                    if (data.success) {
                        btn.classList.toggle('active');
                        const icon = btn.querySelector('svg');
                        if (icon) {
                            icon.classList.toggle('text-red-500');
                            icon.classList.toggle('fill-red-400');
                            icon.classList.toggle('text-red-400');
                        }
                    } else {
                        alert(data.message || 'Could not update wishlist. Please try again.');
                    }
                } catch (err) {
                    alert('Could not update wishlist. Please try again.');
                }
            }
        });
    </script>
    <script>
        window.isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    </script>
</body>

@include('partials.foot')
</html>
