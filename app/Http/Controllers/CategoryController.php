<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Category;


class CategoryController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {

    }
    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategories(Request $request){
        return Category::all();
    }
}