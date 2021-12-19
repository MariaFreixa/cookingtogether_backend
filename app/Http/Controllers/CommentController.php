<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Comment;
use App\User;

class CommentController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['getCommentsByRecipeId']]);
    }
    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentsByRecipeId(Request $request) {
        Log::debug($request->id);
        $comments =  DB::table('comments')->where('id_recipe', '=', $request->id)->get();

        foreach ($comments as $key => $value) {
            Log::debug($comments[$key]);
            Log::debug($comments[$key]['id_user']);

           $comments[$key]->userName = DB::table('users')->where('id', '=', $comments[$key]['id_user'])->get();
        }
        return $comments;
    }
}