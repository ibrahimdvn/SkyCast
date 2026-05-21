<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoriteCity;

class FavoriteCityController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['city_name' => 'required|string']);
        $cityName = $request->input('city_name');
        $user = Auth::user();

        $favorite = FavoriteCity::where('user_id', $user->id)
            ->where('city_name', $cityName)
            ->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            FavoriteCity::create([
                'user_id' => $user->id,
                'city_name' => $cityName
            ]);
        }

        return back();
    }
}
