<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WithoutSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // JANGAN mulai session untuk request ini
        // Ini mencegah session locking pada SSE stream
        
        // Matikan session cookie
        config(['session.driver' => 'array']);
        
        return $next($request);
    }
}
