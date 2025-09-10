<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AllCard;

class AllCardController extends Controller
{
    public function searchCard(Request $request)
    {
        $query = $request->get('q');
        $offset = $request->get('offset', 0);
        $limit = 10;
        
        if (strlen($query) < 3) {
            return response()->json([
                'cards' => [],
                'hasMore' => false
            ]);
        }
        
        // Fetch one extra record to determine if there are more results
        $cards = AllCard::where('name', 'LIKE', '%' . $query . '%')
            ->selectRaw('MIN(id) as id, name')
            ->groupBy('name')
            ->orderBy('name')
            ->offset($offset)
            ->limit($limit + 1)
            ->get();
        
        // Check if we have more results than the limit
        $hasMore = $cards->count() > $limit;
        
        // If we have more results, remove the extra record
        if ($hasMore) {
            $cards = $cards->take($limit);
        }
            
        return response()->json([
            'cards' => $cards,
            'hasMore' => $hasMore
        ]);
    }
}
