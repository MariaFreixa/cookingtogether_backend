<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Rating;
use App\Recipe;


class RatingController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
    $this->middleware('auth:api', ['except' => ['getRating', 'getMoreRated']]);
    }

    /**
     * Get rating for recipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRating(Request $request) {
        $recipe = $request->id;
        return  DB::table('ratings')->where('id_recipe', '=', $recipe)->avg('rating');
    }

    /**
     * Get more rated recipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMoreRated(Request $request) {

            return DB::table('ratings')
            ->join('recipes','recipes.id', '=', 'ratings.id_recipe')
            ->where('ratings.rating', '>', 0)
            ->orderBy('ratings.id_recipe', 'DESC')
            ->groupBy('recipes.id', 'recipes.name', 'recipes.main_image', 'recipes.id_complexity', 'recipes.id_category', 'recipes.diners', 'recipes.video', 'recipes.id_user', 'recipes.created_at', 'recipes.updated_at')
            ->take(4)
            ->get(array('recipes.*'));
        
    }

    /**
     * Set recipe rating
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setRating(Request $request) {
        $user = auth()->user();
        $rating = array('id_recipe'=>$request->id, 'id_user'=>$user->id, 'rating'=>$request->rating);

        $ifExistsRating = Rating::where('id_recipe', $request->id)
                        ->where('id_user', $user->id)
                        ->get();
        
        Log::debug($ifExistsRating);

        if(!$ifExistsRating->isEmpty()) {
            Rating::where('id_recipe', $request->id)->update(['rating'=>$request->rating]);
        } else {
            Rating::create($rating);
        }
    }
}