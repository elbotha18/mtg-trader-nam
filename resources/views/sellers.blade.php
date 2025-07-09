<x-layouts.app :title="__('Favourite Sellers')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <div class="flex h-full w-full flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-200 bg-neutral-50 px-4 py-2 dark:border-neutral-700 dark:bg-neutral-800">
                    <h1 class="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                        {{ __('Favourite Sellers') }}
                    </h1>
                    <div class="flex-1"></div>
                </div>
                <div class="flex-1 overflow-auto p-4">
                    @if($sellers->isEmpty())
                        <p class="text-center text-gray-500">{{ __('No favourite sellers found.') }}</p>
                    @else
                        <table class="w-full max-w-full divide-y divide-neutral-200 dark:divide-neutral-700 table-fixed">
                            <thead class="bg-neutral-100 dark:bg-neutral-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Cellphone') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Joined') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200 dark:bg-neutral-900 dark:divide-neutral-700">
                                @foreach($sellers as $userSeller)
                                    @php $seller = $userSeller->seller; @endphp
                                    @if($seller)
                                    <tr class="cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 transition"
                                        onclick="if(!event.target.closest('form')) window.location='/{{ $seller->id }}/seller';">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $seller->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            @if($seller->cellphone)
                                                <a href="tel:{{ $seller->cellphone }}" class="underline">{{ $seller->cellphone }}</a>
                                            @else
                                                <span class="text-neutral-400">Not provided</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-800 dark:text-neutral-200">
                                            {{ $seller->created_at ? $seller->created_at->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" action="{{ url('/toggle-favourite-seller') }}" class="inline" onsubmit="event.stopPropagation();">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                <input type="hidden" name="redirect_back" value="true">
                                                <button type="submit" class="cursor-pointer text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" title="Remove from favourites">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>