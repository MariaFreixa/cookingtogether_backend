<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use File;
use Validator;
use App\User;


class AuthController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'El correo electrónico o la contraseña son incorrectos.'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'Cerrada sesión correctamente']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    /**
     * Update authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfileUser(Request $request) {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|between:2,100',
            'email' => 'string|email|max:100',
            'password' => 'string|confirmed|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if ($request->file('avatar') == null) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ], 201);        
        }
        else {
            $user = auth()->user();
            $files = $request->file('avatar')->getRealPath();
            $logo = file_get_contents($files);
            $base64 = base64_encode($logo);
            $user->avatar = $base64;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ], 201);
        }
    }
}