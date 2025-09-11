<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\UserWishlist;
use App\Models\AllCard;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $selectedSeller = $request->input('seller_filter', '');

        // Get the user's wishlist items with sellers
        $wishlistItems = UserWishlist::with(['card', 'card.user_card.user'])
            ->where('user_id', $user->id)
            ->get();

        // Collect all unique sellers across all wishlist cards
        $allSellers = collect();
        
        $cards = $wishlistItems->map(function ($item) use ($selectedSeller, &$allSellers) {
            if (!$item->card) {
                return null; // Skip if card does not exist
            }

            // Get all sellers for this card (excluding private listings)
            $sellers = $item->card->user_card()
                ->where('is_private', false)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter()
                ->unique('id');

            // Add sellers to the global collection
            $sellers->each(function($seller) use (&$allSellers) {
                $allSellers->push($seller);
            });

            // Filter by selected seller if provided
            if ($selectedSeller && $sellers->isNotEmpty()) {
                $matchingSellers = $sellers->filter(function ($seller) use ($selectedSeller) {
                    return $seller->id == $selectedSeller;
                });
                
                // If no sellers match the selection, skip this card
                if ($matchingSellers->isEmpty()) {
                    return null;
                }
                
                $sellers = $matchingSellers;
            } elseif ($selectedSeller && $sellers->isEmpty()) {
                // If a seller filter is applied but this card has no sellers, skip it
                return null;
            }

            // Format seller information
            $sellerInfo = '';
            if ($sellers->isEmpty()) {
                $sellerInfo = 'No sellers';
            } elseif ($sellers->count() === 1) {
                $sellerInfo = $sellers->first()->name;
            } else {
                if ($selectedSeller) {
                    // Show the selected seller with count of others
                    $mainSeller = $sellers->first();
                    $otherCount = $item->card->user_card()
                        ->where('is_private', false)
                        ->with('user')
                        ->get()
                        ->pluck('user')
                        ->filter()
                        ->unique('id')
                        ->count() - 1;
                    $sellerInfo = $mainSeller->name . ' (' . $otherCount . ' other seller' . ($otherCount > 1 ? 's' : '') . ')';
                } else {
                    $sellerInfo = $sellers->count() . ' sellers';
                }
            }

            return (object) [
                'id' => $item->card->id,
                'name' => str_replace("'", "’", $item->card->name),
                'set' => $item->card->set,
                'collector_number' => $item->card->collector_number,
                'image_url' => $item->card->image_url,
                'sellers' => $sellerInfo,
                'seller_count' => $sellers->count()
            ];
        })->filter(); // Remove null entries

        // Get unique sellers for the dropdown
        $availableSellers = $allSellers->unique('id')->sortBy('name')->values();

        return view('wishlist', compact('cards', 'availableSellers'));
    }

    public function addCards(Request $request)
    {
        $cards = $request->input('cards', '');
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Split the input by new lines and filter out empty lines
        $cardLines = array_filter(explode("\n", $cards));

        foreach ($cardLines as $line) {
            // Extract card name, set, and number
            preg_match('/^(?P<name>.+?)(?:\s*\((?P<set>[^)]+)\))?\s*(?P<number>[\w-]+)?\s*$/i', $line, $matches);
            if (count($matches) < 2) {
                continue; // Skip invalid lines
            }

            $name = trim($matches[1]);
            $set = isset($matches[2]) ? trim($matches[2]) : null;
            $number = isset($matches[3]) ? trim($matches[3]) : null;

            // Find or create the card
            $card = AllCard::firstOrCreate([
                'name' => $name,
                'set' => $set,
                'collector_number' => $number,
            ]);

            // Add to wishlist
            UserWishlist::updateOrCreate(
                ['user_id' => $user->id, 'card_id' => $card->id],
                ['user_id' => $user->id, 'card_id' => $card->id]
            );
        }

        return redirect()->route('wishlist')->with('success', __('Cards added successfully.'));
    }

    public function toggleWishlist(Request $request)
    {
        $cardId = $request->input('card_id');
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // if card exists in wishlist, remove it
        $wishlistItem = UserWishlist::where('user_id', $user->id)
            ->where('card_id', $cardId)
            ->first();
        if ($wishlistItem) {
            $wishlistItem->delete();
        } else {
            // if card does not exist in wishlist, add it
            UserWishlist::create([
                'user_id' => $user->id,
                'card_id' => $cardId,
            ]);
        }
        // Check if the request has a redirect_back parameter
        if ($request->has('redirect_back') && $request->input('redirect_back')) {
            return redirect()->back()->with('success', __('Wishlist updated successfully.'));
        }

        return response()->json(['success' => true]);
    }
}
