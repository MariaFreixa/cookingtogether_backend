<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return DB::table('comments')->where('id_recipe', '=', $request->id)->get();
    }
}