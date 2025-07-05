<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

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
        if (!$request->advanced) {
            $query = $request->input('search', '');
            $cards = Card::where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('set', 'like', '%' . $query . '%')
                    ->orWhere('number', 'like', '%' . $query . '%');
                })
                ->orderBy('name', 'asc')
                ->public()
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
                ->public()
                ->get();
        }

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

        $query = Card::where('name', $name)
            ->where('set', $set)
            ->where('number', $number)
            ->public()
            ->with('user');

        // If advanced attributes are present, filter sellers by those attributes
        if ($request->has('advanced') && !empty($attributes[0])) {
            foreach ($attributes as $attribute) {
                if ($attribute) {
                    $query->where($attribute, true);
                }
            }
        }

        $card_sellers = $query->get();

        $sellers = [];
        foreach ($card_sellers as $card) {
            $sellers[] = (object) [
                'name' => $card->user->name,
                'cellphone' => $card->user->cellphone,
                'is_foil' => $card->is_foil,
                'is_borderless' => $card->is_borderless,
                'is_retro_frame' => $card->is_retro_frame,
                'is_etched_foil' => $card->is_etched_foil,
                'is_judge_promo_foil' => $card->is_judge_promo_foil,
                'is_japanese_language' => $card->is_japanese_language,
                'is_signed_by_artist' => $card->is_signed_by_artist,
                'created_at' => $card->created_at,
            ];
        }

        $card = (object) [
            'name' => $name,
            'set' => $set,
            'number' => $number,
            'sellers' => $sellers,
        ];

        return view('card', compact('card'));
    }

}
