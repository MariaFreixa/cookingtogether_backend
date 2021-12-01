<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Complexity;


class ComplexityController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['getAllComplexity']]);
    }
    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllComplexity(Request $request){
        return DB::table('complexity')->get();
    }
}