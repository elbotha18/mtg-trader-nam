<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSeller;
use Auth;

class SellerController extends Controller
{
    public function index()
    {
        // Get the logged-in user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the sellers that the user has favourited
        $sellers = UserSeller::with('seller')
            ->where('user_id', $user->id)
            ->get();

        return view('sellers', compact('sellers'));
    }
    /**
     * Toggle favourite status for a seller for the logged-in user.
     */
    public function toggleFavourite(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $sellerId = $request->input('seller_id');

        // Assuming you have a pivot table 'user_seller_favourites' with user_id and seller_id
        $favourite = UserSeller::where('user_id', $user->id)
            ->where('seller_id', $sellerId)
            ->first();

        if ($favourite) {
            $favourite->delete();
            $isFavourited = false;
        } else {
            UserSeller::create([
                'user_id' => $user->id,
                'seller_id' => $sellerId,
            ]);
            $isFavourited = true;
        }

        return response()->json(['favourited' => $isFavourited]);
    }
}
