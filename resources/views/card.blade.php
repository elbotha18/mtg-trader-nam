<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $card->name }} - NAMTG Trader</title>
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
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen flex flex-col">
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
    <!-- Header -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-[#161615] shadow-sm">
        <a href="{{ route('home') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <img src="/logo.webp" alt="MTG Logo" class="h-8 w-8 rounded" />
                 <h1 class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                    NAMTG Trader
                </h1>
            </a>
        <nav class="flex items-center gap-4">
            <a href="/" class="px-5 py-1.5 rounded bg-neutral-200 text-[#1b1b18] font-medium hover:bg-neutral-300 transition">Home</a>
        </nav>
    </header>

    <!-- Card Details -->
    <section class="w-full max-w-2xl mx-auto mt-10 mb-6 px-4">
        <div class="p-6 bg-white dark:bg-neutral-900 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-2 text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                {{ $card->name }}
            </h1>
            <div class="mb-4 text-neutral-600 dark:text-neutral-300">
                <span class="font-semibold">Set:</span> {{ $card->set }}<br>
                <span class="font-semibold">Number:</span> {{ $card->number ?? __('N/A') }}
            </div>
            <div id="card-image" class="my-4">
                <!-- Image will be shown here -->
            </div>

            <hr class="my-6 border-neutral-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold mb-3 text-neutral-700 dark:text-neutral-200">Sellers ({{ count($card->sellers) }})</h2>
            @if(empty($card->sellers))
                <p class="text-neutral-500">No sellers currently have this card listed.</p>
            @else
                <div class="overflow-x-auto w-full">
                    <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700 table-fixed mb-4">
                        <thead class="bg-neutral-100 dark:bg-neutral-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Seller</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Attributes</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Uploaded On</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                            @foreach($card->sellers as $seller)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200">
                                        {{ $seller->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 max-w-xs overflow-x-auto">
                                        <div class="welcome-attributes card-attributes flex flex-wrap gap-1">
                                            @if($seller->is_foil)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Foil</span>
                                            @endif
                                            @if($seller->is_borderless)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Borderless</span>
                                            @endif
                                            @if($seller->is_retro_frame)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Retro Frame</span>
                                            @endif
                                            @if($seller->is_etched_foil)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Etched Foil</span>
                                            @endif
                                            @if($seller->is_judge_promo_foil)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Judge Promo Foil</span>
                                            @endif
                                            @if($seller->is_japanese_language)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200">Japanese Language</span>
                                            @endif
                                            @if($seller->is_signed_by_artist)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">Signed by Artist</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-blue-700 dark:text-blue-300">
                                        @if($seller->name && $seller->cellphone)
                                            <a href="tel:{{ $seller->cellphone }}" class="underline">{{ $seller->cellphone }}</a>
                                        @else
                                            <span class="text-neutral-400">Not provided</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $seller->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <a href="/" class="inline-block mt-4 px-5 py-2 rounded bg-blue-600 text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">&larr; Back to search</a>
        </div>
    </section>
</body>
@include('partials.foot')
</html>

<script>
    async function fetchCardImage() {
        const setName = @json($card->set);
        const setNumber = @json($card->number);
        const cardName = @json($card->name);
        const cardId = @json($card->id);
        const imageUrl = @json($card->image_url);

        const cardImageDiv = document.getElementById('card-image');
        cardImageDiv.innerHTML = ''; // Clear previous image if any

        if (imageUrl) {
            // Use stored image
            const imgElement = document.createElement('img');
            imgElement.src = imageUrl;
            imgElement.alt = cardName;
            imgElement.className = 'w-full h-auto rounded-lg shadow-md';
            cardImageDiv.appendChild(imgElement);
        } else if (setName && setNumber) {
            // Fetch from Scryfall
            try {
                const response = await fetch(`https://api.scryfall.com/cards/${setName.toLowerCase()}/${setNumber}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (data.image_uris && data.image_uris.normal) {
                    const imgElement = document.createElement('img');
                    imgElement.src = data.image_uris.normal;
                    imgElement.alt = cardName;
                    imgElement.className = 'w-full h-auto rounded-lg shadow-md';
                    cardImageDiv.appendChild(imgElement);

                    // Store image URL in backend
                    if (cardId) {
                        fetch('/cards/add-image', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ card_id: cardId, image_url: data.image_uris.normal })
                        });
                    }
                } else {
                    cardImageDiv.innerHTML = '<span class="text-neutral-400">No image found for this card.</span>';
                }
            } catch (error) {
                cardImageDiv.innerHTML = '<span class="text-neutral-400">Error loading image.</span>';
            }
        } else {
            cardImageDiv.innerHTML = '<span class="text-neutral-400">Set name or number is missing.</span>';
        }
    }

    // Call the function to fetch the card image
    fetchCardImage();
</script>

<style>
    #card-image img {
        max-width: 100%;
        max-height: 600px;
        width: auto;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
    }

    @media (max-width: 640px) {
        #card-image img {
            max-height: 400px;
        }
    }
</style>