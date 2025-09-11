<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\AllCard;
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
                $card->number = $card->collector_number ?? '';
                $card->type_line = $card->type_line ?? '';
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
        
        $cards = explode("\n", $request->input('cards'));
        $data = [];
        $userId = Auth::id();
        $now = now();
        
        foreach ($cards as $cardLine) {
            $cardLine = trim($cardLine);
            if (!empty($cardLine)) {
                // Parse the card details
                preg_match('/^(?P<quantity>\d+)?\s*(?P<name>.+?)(?:\s*\((?P<set>[^)]+)\))?\s*(?P<number>[\w-]+)?\s*(?P<foil>\*F\*)?$/i', $cardLine, $matches);
                
                $quantity = isset($matches['quantity']) ? (int)$matches['quantity'] : 1;
                $name = $matches['name'] ?? '';
                $set = $matches['set'] ?? '';
                $number = $matches['number'] ?? '';
                $isfoil = isset($matches['foil']);
                
                if (empty($name)) {
                    continue; // Skip if no name found
                }
                
                // Find or create the card in all_cards
                $card_id = $this->findOrCreateCard($name, $set, $number);
                if (!$card_id) {
                    continue; // Skip if card ID could not be determined
                }

                $data[] = [
                    'user_id' => $userId,
                    'card_id' => $card_id,
                    'is_foil' => $isfoil,
                    'is_borderless' => false,
                    'is_retro_frame' => false,
                    'is_etched_foil' => false,
                    'is_judge_promo_foil' => false,
                    'is_japanese_language' => false,
                    'is_signed_by_artist' => false,
                    'is_private' => false,
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

    private function findOrCreateCard($name, $set, $collector_number)
    {
        // First try to find an exact match
        $card = AllCard::where('name', $name)
            ->when($set, function ($query, $set) {
                $query->where('set', strtolower($set));
            })
            ->when($collector_number, function ($query, $collector_number) {
                $query->where('collector_number', $collector_number);
            })
            ->first();

        if ($card) {
            return $card->id;
        }

        // If no exact match, try to find by name only
        $card = AllCard::where('name', $name)->first();
        if ($card) {
            return $card->id;
        }

        // If still no match, create a new card
        try {
            $newCard = AllCard::create([
                'name' => trim($name),
                'set' => $set ?: null,
                'collector_number' => $collector_number ?: null,
                'image_url' => null,
            ]);
            return $newCard->id;
        } catch (\Exception $e) {
            \Log::error('Failed to create card: ' . $e->getMessage());
            return null;
        }
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
            'id' => 'required|exists:user_cards,id',
            'name' => 'sometimes|string|max:255',
            'type_line' => 'sometimes|string|max:255',
            'set' => 'sometimes|string|max:10',
            'collector_number' => 'sometimes|string|max:20',
        ]);
        
        $userCard = UserCard::findOrFail($request->input('id'));
        
        // Update UserCard attributes
        $userCard->update([
            'is_foil' => $request->input('is_foil', false) ? true : false,
            'is_borderless' => $request->input('is_borderless', false) ? true : false,
            'is_retro_frame' => $request->input('is_retro_frame', false) ? true : false,
            'is_etched_foil' => $request->input('is_etched_foil', false) ? true : false,
            'is_judge_promo_foil' => $request->input('is_judge_promo_foil', false) ? true : false,
            'is_japanese_language' => $request->input('is_japanese_language', false) ? true : false,
            'is_signed_by_artist' => $request->input('is_signed_by_artist', false) ? true : false,
            'is_private' => $request->input('is_private', false) ? true : false,
        ]);
        
        // Update AllCard fields if provided
        $cardData = [];
        if ($request->has('name')) {
            $cardData['name'] = $request->input('name');
        }
        if ($request->has('type_line')) {
            $cardData['type_line'] = $request->input('type_line');
        }
        if ($request->has('set')) {
            $cardData['set'] = $request->input('set');
        }
        if ($request->has('collector_number')) {
            $cardData['collector_number'] = $request->input('collector_number');
        }
        
        if (!empty($cardData)) {
            $userCard->card->update($cardData);
        }
        
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
            'id' => 'required|exists:user_cards,id',
        ]);
        $card = UserCard::findOrFail($request->input('id'));
        $card->delete();
        return redirect()->route('dashboard')->with('success', __('Card deleted successfully.'));
    }

    /**
     * Quick add a card to user's collection.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickAddCard(Request $request)
    {
        try {
            $request->validate([
                'card_id' => 'required|exists:all_cards,id',
            ]);

            $userId = Auth::id();
            $cardId = $request->input('card_id');

            // Check if user already has this card
            $existingCard = UserCard::where('user_id', $userId)
                ->where('card_id', $cardId)
                ->first();

            if ($existingCard) {
                return response()->json([
                    'success' => false,
                    'message' => __('You already have this card in your collection.')
                ]);
            }

            // Add the card to user's collection
            UserCard::create([
                'user_id' => $userId,
                'card_id' => $cardId,
                'is_foil' => false,
                'is_borderless' => false,
                'is_retro_frame' => false,
                'is_etched_foil' => false,
                'is_judge_promo_foil' => false,
                'is_japanese_language' => false,
                'is_signed_by_artist' => false,
                'is_private' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Card added to your collection successfully!')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in quickAddCard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error: ') . $e->getMessage()
            ]);
        }
    }
}
