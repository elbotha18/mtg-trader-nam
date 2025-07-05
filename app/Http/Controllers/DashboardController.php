<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Card;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cards = Card::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

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
        foreach ($cards as $card) {
            $card = trim($card);
            if (!empty($card)) {
                // Parse the card details
                preg_match('/^(?P<quantity>\d+)?\s*(?P<name>.+?)(?:\s*\((?P<set>[^)]+)\))?\s*(?P<number>[\w-]+)?\s*(?P<foil>\*F\*)?$/i', $card, $matches);
                
                $data[] = [
                    'user_id' => $userId,
                    'name' => $matches['name'] ?? '',
                    'set' => $matches['set'] ?? '',
                    'number' => $matches['number'] ?? '',
                    'is_foil' => isset($matches['foil']),
                    'is_private' => false, // Default to public
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert the cards into the database
        if (!empty($data)) {
            Card::insert($data);
        }

        return redirect()->route('dashboard')->with('success', __('Cards added successfully.'));
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
