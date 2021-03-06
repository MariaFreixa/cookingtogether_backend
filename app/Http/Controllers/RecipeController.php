<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use File;
use Validator;
use App\User;
use App\Recipe;
use App\Ingredient;
use App\Favorite;
use App\Step;
use App\Comment;
use App\Rating;


class RecipeController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
    $this->middleware('auth:api', ['except' => ['getRecipeById', 'getLatest', 'getRecipesByCategory', 'getFullRecipeById', 'searchRecipe']]);
    }

    /**
     * Get recipe by id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipeById(Request $request) {
        return Recipe::findOrFail($request->id);
    }

    /**
     * Get full recipe by id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFullRecipeById(Request $request) {
        $recipe = Recipe::findOrFail($request->id);

        $ingredients = DB::table('ingredients')->where('id_recipe', '=', $request->id)->get();
        $steps = DB::table('steps')->where('id_recipe', '=', $request->id)->get();

        return array('recipe'=>$recipe, 'ingredients'=>$ingredients, 'steps'=>$steps);
    }

    /**
     * Get latest recipes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatest() {
        return Recipe::latest()->take(4)->get();
    }

    /**
     * Get recipes by category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipesByCategory(Request $request) {
        return DB::table('recipes')->where('id_category', '=', $request->id)->get();
    }

    /**
     * Get my recipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyRecipes(Request $request) {
        $user = auth()->user();
        return DB::table('recipes')->where('id_user', '=', $user->id)->get();
    }

    /**
     * Get create new recipe.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function newRecipe(Request $request) {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|between:2,100',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'diners' => 'numeric|min:1|max:12',
            'video' => 'nullable|string',
            'id_category' => 'numeric|min:1|max:12',
            'id_complexity' => 'numeric|min:1|max:3',
            'ingredients' => 'required',
            'steps' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try {
            $files = $request->file('main_image')->getRealPath();
            $image = file_get_contents($files);
            $base64 = base64_encode($image);
            $recipeImage = $base64;

            $name = $request->input('name');
            $diners = $request->input('diners');
            $video = $request->input('video');
            $category = $request->input('id_category');
            $complexity = $request->input('id_complexity');

            $recipe = array('name'=>$name,"main_image"=>$recipeImage,"diners"=>$diners,"video"=>$video, 'id_category'=>$category, 'id_complexity'=>$complexity, 'id_user'=>$user->id);
            $recipeCreate = Recipe::create($recipe);

            $ingredientsArray = (array_values($request->ingredients));

            foreach ($ingredientsArray as $key => $value) {
                $replace = str_replace('{"ingredient":"', "", $ingredientsArray[$key]);
                $replace2 = str_replace('"}', "", $replace);
                $ingredients = array('id_recipe'=>$recipeCreate->id, 'ingredient'=>$replace2);

                Ingredient::create($ingredients);
            }

            $stepsArray = (array_values($request->steps));

            foreach ($request->steps as $key => $value) {
                $replace = str_replace('{"step":"', "", $stepsArray[$key]);
                $replace2 = str_replace('"}', "", $replace);
                $step = array('id_recipe'=>$recipeCreate->id, 'step'=>$replace2);
                
                Step::create($step);
            }

            return response()->json([
                'message' => 'La receta ha sido creada'
            ]);
        }
        catch (\Throwable $e) {
            Ingredient::where('id_recipe', $request->id)
            ->delete();

            Step::where('id_recipe', $request->id)
            ->delete();

            Favorite::where('id_recipe', $request->id)
            ->where('id_user', '=', $user->id)
            ->delete();

            Recipe::where('id', $request->id)
            ->where('id_user', '=', $user->id)
            ->delete();

            return response()->json($e);
        }
    }

    /**
     * update recipe.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function updateRecipe(Request $request) {
        $user = auth()->user();
        $recipe = Recipe::findOrFail($request->id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|between:2,100',
            'diners' => 'nullable|numeric|min:1|max:12',
            'video' => 'nullable|string',
            'id_category' => 'nullable|numeric|min:1|max:12',
            'id_complexity' => 'nullable|numeric|min:1|max:3',
            'ingredients' => 'nullable',
            'steps' => 'nullable'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($request->file('main_image') != null) {
            $files = $request->file('main_image')->getRealPath();
            $image = file_get_contents($files);
            $base64 = base64_encode($image);
            $recipe->main_image = $base64;
        }
        
        $recipe->name = $request->input('name');
        $recipe->diners = $request->input('diners');
        $recipe->video = $request->input('video');
        $recipe->id_category = $request->input('id_category');
        $recipe->id_complexity = $request->input('id_complexity');

        $recipe->save();
        Ingredient::where('id_recipe', $recipe->id)->delete();
        Step::where('id_recipe', $recipe->id)->delete();

        $ingredientsArray = (array_values($request->ingredients));

        foreach ($ingredientsArray as $key => $value) {
            $replace = str_replace('{"ingredient":"', "", $ingredientsArray[$key]);
            $replace2 = str_replace('"}', "", $replace);
            $ingredients = array('id_recipe'=>$recipe->id, 'ingredient'=>$replace2);
            
            Ingredient::create($ingredients);
        }

        $stepsArray = (array_values($request->steps));

        foreach ($request->steps as $key => $value) {
            $replace = str_replace('{"step":"', "", $stepsArray[$key]);
            $replace2 = str_replace('"}', "", $replace);
            $step = array('id_recipe'=>$recipe->id, 'step'=>$replace2);
            
            Step::create($step);
        }
    }

    public function removeRecipe(Request $request) {
        $user = auth()->user();

        Comment::where('id_recipe', $request->id)
        ->delete();

        Rating::where('id_recipe', $request->id)
        ->delete();

        Ingredient::where('id_recipe', $request->id)
        ->delete();

        Step::where('id_recipe', $request->id)
        ->delete();

        Favorite::where('id_recipe', $request->id)
        ->where('id_user', '=', $user->id)
        ->delete();

        Recipe::where('id', $request->id)
        ->where('id_user', '=', $user->id)
        ->delete();
    }

    public function searchRecipe(Request $request) {
        $recipes = Recipe::select('*');
        if($request->name != null) {
            $recipes->where('name','like', '%'.$request->name.'%');
        }
        if($request->diners != null) {
            $recipes->where('diners', $request->diners);
        }
        if($request->id_category != null) {
            $recipes->where('id_category', $request->id_category);
        }
        if($request->id_complexity != null) {
            $recipes->where('id_complexity', $request->id_complexity);
        }
        return $recipes->get();
    }

    public function setComment(Request $request) {
        $validator = Validator::make($request->all(), [
            'comment' => 'string|between:2,255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        $comment = array('id_user'=>$user->id, 'id_recipe'=>$request->id, 'comment' => $request->comment, 'user_name'=>$user->name);
        Comment::create($comment);

        return response()->json([
            'message' => 'El comentarioha sido creado'
        ]);
    }
}
