<?php

namespace App\Http\Middleware;
use Closure;

class CORS {
    
    public function handle($request, Closure $next) {
        // permite peticiones desde cualquier origen
        header('Access-Control-Allow-Origin: *');
        // permite peticiones con métodos GET, PUT, POST, DELETE y OPTIONS
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        // permite los headers Content-Type y Authorization
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        return $next($request);
    }

}
