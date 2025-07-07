<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\UserCard;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cards = UserCard::where('user_id', Auth::id())
            ->with('card')
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the cards for the dashboard view
        $cards = $cards->map(function ($userCard) {
            $card = $userCard->card;
            if ($card) {
                $card->name = str_replace("'", '’', $card->name);
                $card->set = $card->set ?? '';
                $card->number = $card->number ?? '';
                $card->is_private = $userCard->is_private;
                $card->is_foil = $userCard->is_foil;
                $card->is_borderless = $userCard->is_borderless;
                $card->is_retro_frame = $userCard->is_retro_frame;
                $card->is_etched_foil = $userCard->is_etched_foil;
                $card->is_judge_promo_foil = $userCard->is_judge_promo_foil;
                $card->is_japanese_language = $userCard->is_japanese_language;
                $card->is_signed_by_artist = $userCard->is_signed_by_artist;
                $card->created_at = $userCard->created_at;
                $card->updated_at = $userCard->updated_at;
                $card->id = $userCard->id;
            }
            return $card;
        });

        return view('dashboard', compact('cards'));
    }

    /**
     * Add cards from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCards(Request $request)
    {
        $request->validate([
            'cards' => 'required|string',
        ]);

        $existingCards = Card::get('id', 'set', 'number');
        
        $cards = explode("\n", $request->input('cards'));
        $data = [];
        $userId = Auth::id();
        $now = now();
        foreach ($cards as $card) {
            $card = trim($card);
            if (!empty($card)) {
                // Parse the card details
                preg_match('/^(?P<quantity>\d+)?\s*(?P<name>.+?)(?:\s*\((?P<set>[^)]+)\))?\s*(?P<number>[\w-]+)?\s*(?P<foil>\*F\*)?$/i', $card, $matches);
                // Check if the card already exists else add it
                $card_id = $this->getCardId($matches['name'] ?? '', $matches['set'] ?? '', $matches['number'] ?? '', $existingCards);
                if (!$card_id) {
                    continue; // Skip if card ID could not be determined
                }
                dd($card_id);
                
                $data[] = [
                    'user_id' => $userId,
                    'card_id' => $card_id,
                    'is_foil' => isset($matches['foil']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert the cards into the database
        if (!empty($data)) {
            UserCard::insert($data);
        }

        return redirect()->route('dashboard')->with('success', __('Cards added successfully.'));
    }

    private function getCardId($name, $set, $number, $existingCards)
    {
        // Normalize the name
        $name = str_replace("'", '’', $name);
        // Check if the card already exists
        foreach ($existingCards as $card) {
            if ($card->name === $name && $card->set === $set && $card->number === $number) {
                return $card->id;
            }
        }
        // If not found, create a new card
        return Card::create([
            'user_id' => Auth::id(),
            'name' => $name,
            'set' => $set,
            'number' => $number,
        ])->id;
    }

    /**
     * Update a card.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCard(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cards,id',
            'name' => 'required|string|max:255',
            'set' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:50',
        ]);
        $card = Card::findOrFail($request->input('id'));
        $card->update([
            'name' => $request->input('name'),
            'set' => $request->input('set'),
            'number' => $request->input('number'),
            'is_foil' => $request->input('is_foil', false) ? true : false,
            'is_borderless' => $request->input('is_borderless', false) ? true : false,
            'is_retro_frame' => $request->input('is_retro_frame', false) ? true : false,
            'is_etched_foil' => $request->input('is_etched_foil', false) ? true : false,
            'is_judge_promo_foil' => $request->input('is_judge_promo_foil', false) ? true : false,
            'is_japanese_language' => $request->input('is_japanese_language', false) ? true : false,
            'is_signed_by_artist' => $request->input('is_signed_by_artist', false) ? true : false,
            'is_private' => $request->input('is_private', false) ? true : false,
        ]);
        return redirect()->route('dashboard')->with('success', __('Card updated successfully.'));
    }

    /**
     * Delete a card.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCard(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cards,id',
        ]);
        $card = Card::findOrFail($request->input('id'));
        $card->delete();
        return redirect()->route('dashboard')->with('success', __('Card deleted successfully.'));
    }
}
