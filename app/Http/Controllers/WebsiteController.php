<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\AllCard;
use App\Models\User;
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
            $cards = AllCard::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('set', 'like', '%' . $query . '%')
                    ->orWhere('collector_number', 'like', '%' . $query . '%');
                })
                ->orderBy('name', 'asc')
                ->whereHas('user_card', function($q) {
                    $q->where('is_private', false);
                })
                ->get();
            
        } else {
            $query = $request->input('search', '');
            $attributes = explode(',', $request->input('attributes', ''));
            $type = $request->input('type', '');
            $cards = AllCard::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('set', 'like', '%' . $query . '%')
                      ->orWhere('collector_number', 'like', '%' . $query . '%');
                })
                ->where(function($q) use ($type) {
                    if ($type) {
                        $q->where('type_line', 'like', '%' . $type . '%');
                    }
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
                ->with('user_card')
                ->get();
        }

        // 2. Add is_wishlisted property and added_at to each card
        $cards = $cards->map(function($card) use ($wishlistIds) {
            $card->name = str_replace("'", '’', $card->name);
            $card->is_wishlisted = in_array($card->id, $wishlistIds);
            // Get the most recent user_card's created_at as added_at
            $addedAt = null;
            if ($card->relationLoaded('user_card') && $card->user_card->count()) {
                $addedAt = $card->user_card->max('created_at');
            } elseif (method_exists($card, 'user_card')) {
                $userCard = $card->user_card()->orderByDesc('created_at')->first();
                $addedAt = $userCard ? $userCard->created_at : null;
            }
            $card->added_at = $addedAt ? $addedAt->format('Y-m-d H:i:s') : null;
            return $card;
        });

        // Group by name, set, and numbcollector_numberer (if collector_number is present)
        $grouped = $cards->unique(function($item) {
            return $item->name . '|' . $item->set . '|' . ($item->collector_number ?? '') . '|' . $item->added_at;
        })->values();

        return response()->json($grouped);
    }

    public function addImage(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'image_url' => 'required|url'
        ]);
        $card = AllCard::find($request->card_id);
        $card->image_url = $request->image_url;
        $card->save();
        return response()->json(['success' => true]);
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

        $query = AllCard::whereRaw("REPLACE(name, '''', '’') = ?", [$name])
            ->where('set', $set)
            ->where('collector_number', $number)
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
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
            $user->load('sellers');
        }

        $sellers = [];
        foreach ($card->user_card as $userCard) {
            if (!$userCard) {
                continue; // Skip if user_card is not found
            }
            if (!$userCard->user) {
                continue; // Skip if user does not exist
            }
            $is_favourited = false;
            if ($user) {
                $seller = $user->sellers->where('seller_id', $userCard->user->id);
                $is_favourited = $seller->isNotEmpty();
            }
            $sellers[] = (object) [
                'id' => $userCard->user->id,
                'name' => $userCard->user->name,
                'cellphone' => $userCard->user->cellphone,
                'is_favourited' => $is_favourited,
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
            'id' => $card->id,
            'name' => $name,
            'set' => $set,
            'number' => $number,
            'image_url' => $card->image_url,
            'sellers' => $sellers
        ];

        return view('card', compact('card'));
    }

    /**
     * Display the seller's information.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showSeller($id)
    {
        $seller = User::find($id);
        if (!$seller) {
            return redirect()->route('home')->withErrors(['Seller not found']);
        }

        $cards = AllCard::whereHas('user_card', function($query) use ($id) {
            $query->where('user_id', $id)->where('is_private', false);
        })->with('user_card')->get();
        
        $cards->map(function($card) {
            $userCard = $card->user_card->first();
            if (!$userCard) {
                return null; // Skip if user_card does not exist
            }
            $card->name = str_replace("'", '’', $card->name);
            $card->is_foil = $userCard->is_foil;
            $card->is_borderless = $userCard->is_borderless;
            $card->is_retro_frame = $userCard->is_retro_frame;
            $card->is_etched_foil = $userCard->is_etched_foil;
            $card->is_judge_promo_foil = $userCard->is_judge_promo_foil;
            $card->is_japanese_language = $userCard->is_japanese_language;
            $card->is_signed_by_artist = $userCard->is_signed_by_artist;
            $card->image_url = $card->image_url ?? null;
            $card->uploaded_at = $userCard->created_at->format('d/m/Y') ?? 'N/A';
            return $card;
        });

        // Check if the seller is a favourite if user is logged in
        $seller->is_favourite = false;
        if (Auth::check()) {
            $user = Auth::user();
            $isFavourite = $user->sellers()->where('seller_id', $id)->exists();
            $seller->is_favourited = $isFavourite;
        }

        return view('seller', compact('seller', 'cards'));
    }


}
