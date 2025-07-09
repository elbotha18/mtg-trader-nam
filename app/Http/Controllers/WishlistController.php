<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\UserWishlist;
use App\Models\Card;

class WishlistController extends Controller
{
    public function index()
    {
        // Get the logged-in user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the user's wishlist items
        $wishlistItems = UserWishlist::with('card')
            ->where('user_id', $user->id)
            ->get();
        
        $cards = $wishlistItems->map(function ($item) {
            if (!$item->card) {
                return null; // Skip if card does not exist
            }
            return (object) [
                'id' => $item->card->id,
                'name' => str_replace("'", '’', $item->card->name),
                'set' => $item->card->set,
                'number' => $item->card->number,
                'image_url' => $item->card->image_url
            ];
        });

        return view('wishlist', compact('cards'));
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
            $card = Card::firstOrCreate([
                'name' => $name,
                'set' => $set,
                'number' => $number,
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
