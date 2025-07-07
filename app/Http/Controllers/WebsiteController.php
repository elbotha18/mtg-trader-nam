<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\UserWishlist;
use Illuminate\Support\Facades\Log;

class WebsiteController extends Controller
{
    /**
     * Search for cards based on the query.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCards(Request $request)
    {
        // 1. Get the logged-in user's wishlist card IDs
        $user = Auth::user();
        $wishlistIds = [];
        if ($user) {
            $wishlistIds = UserWishlist::where('user_id', $user->id)->pluck('card_id')->toArray();
        }

        if (!$request->advanced) {
            $query = $request->input('search', '');
            $cards = Card::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('set', 'like', '%' . $query . '%')
                    ->orWhere('number', 'like', '%' . $query . '%');
                })
                ->orderBy('name', 'asc')
                ->whereHas('user_card', function($q) {
                    $q->where('is_private', false);
                })
                ->get();
            
        } else {
            $query = $request->input('search', '');
            $attributes = explode(',', $request->input('attributes', []));
            $cards = Card::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('set', 'like', '%' . $query . '%')
                      ->orWhere('number', 'like', '%' . $query . '%');
                })
                ->where(function($q) use ($attributes) {
                    foreach ($attributes as $attribute) {
                        if ($attribute) {
                            $q->where($attribute, true);
                        }
                    }
                })
                ->orderBy('name', 'asc')
                ->whereHas('user_card', function($q) {
                    $q->where('is_private', false);
                })
                ->get();
        }

        // 2. Add is_wishlisted property to each card
        $cards = $cards->map(function($card) use ($wishlistIds) {
            $card->name = str_replace("'", '’', $card->name);
            $card->is_wishlisted = in_array($card->id, $wishlistIds);
            return $card;
        });

        // Group by name, set, and number (if number is present)
        $grouped = $cards->unique(function($item) {
            return $item->name . '|' . $item->set . '|' . ($item->number ?? '');
        })->values()->take(50);

        return response()->json($grouped);
    }

    /**
     * Show a specific card based on the query parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function showCard(Request $request)
    {
        $name = $request->input('name');
        $set = $request->input('set');
        $number = $request->input('number', '');
        $attributes = explode(',', $request->input('attributes', ''));

        $query = Card::whereRaw("REPLACE(name, '''', '’') = ?", [$name])
            ->where('set', $set)
            ->where('number', $number)
            ->with('seller');

        // If advanced attributes are present, filter sellers by those attributes
        if ($request->has('advanced') && !empty($attributes[0])) {
            foreach ($attributes as $attribute) {
                if ($attribute) {
                    $query->where($attribute, true);
                }
            }
        }

        $card = $query->first();

        $sellers = [];
        foreach ($card->user_card as $userCard) {
            if (!$userCard) {
                continue; // Skip if user_card is not found
            }
            if (!$userCard->user) {
                continue; // Skip if user does not exist
            }
            $sellers[] = (object) [
                'name' => $userCard->user->name,
                'cellphone' => $userCard->user->cellphone,
                'is_foil' => $userCard->is_foil,
                'is_borderless' => $userCard->is_borderless,
                'is_retro_frame' => $userCard->is_retro_frame,
                'is_etched_foil' => $userCard->is_etched_foil,
                'is_judge_promo_foil' => $userCard->is_judge_promo_foil,
                'is_japanese_language' => $userCard->is_japanese_language,
                'is_signed_by_artist' => $userCard->is_signed_by_artist,
                'created_at' => $userCard->created_at,
            ];
        }

        $card = (object) [
            'name' => $name,
            'set' => $set,
            'number' => $number,
            'sellers' => $sellers
        ];

        return view('card', compact('card'));
    }

}
