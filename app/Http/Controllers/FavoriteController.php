<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Favorite;
use App\User;


class FavoriteController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
    $this->middleware('auth:api', ['except' => []]);
    }

    /**
     * Get favorites recipes by user ID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFav(Request $request) {
        $user = auth()->user();

        $recipes = DB::table('recipes')
        ->join('favorites', 'favorites.id_recipe', '=', 'recipes.id')
        ->where('favorites.id_user', '=', $user->id)
        ->get(array('recipes.*'));
                
        return response()->json($recipes);
    }

    public function setFavorite(Request $request) {
        $user = auth()->user();
        $favorite = array('id_recipe'=>$request->id, 'id_user'=>$user->id);
        Favorite::create($favorite);
    }

    public function removeFavorite(Request $request) {
        $user = auth()->user();
        Favorite::where('id_recipe', $request->id)
        ->where('id_user', '=', $user->id)
        ->delete();
    }
}