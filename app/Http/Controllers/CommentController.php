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
        $this->middleware('auth:api', ['except' => ['']]);
    }
    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentsByRecipeId(Request $request) {
        $comments = Comment::findOrFail($request->id);
        foreach ($comments as $key => $value) {
            Log::debug($comments[$key]);
            Log::debug($comments[$key]['id_user']);
           $comments->userName = $comments[$key]['id_user'];
        }

        return Comment::findOrFail($request->id);
    }
}