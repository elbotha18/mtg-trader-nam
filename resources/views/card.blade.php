<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $card->name }} - MTG Trader</title>
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
        <div class="flex items-center gap-2">
            <img src="/mtg.png" alt="MTG Logo" class="h-8 w-8 rounded" />
            <span class="text-xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">MTG Trader</span>
        </div>
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
            <hr class="my-6 border-neutral-200 dark:border-neutral-700">
            <h2 class="text-lg font-semibold mb-3 text-neutral-700 dark:text-neutral-200">Sellers ({{ count($card->sellers) }})</h2>
            @if(empty($card->sellers))
                <p class="text-neutral-500">No sellers currently have this card listed.</p>
            @else
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 mb-4">
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
                                    <div class="flex flex-wrap gap-1">
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
            @endif
            <a href="/" class="inline-block mt-4 text-blue-600 hover:underline">&larr; Back to search</a>
        </div>
    </section>
</body>
</html>
