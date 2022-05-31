<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Cookie;
use Closure;
use Illuminate\Http\Request;

class CheckReferral
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

// Check that there is not already a cookie set and that we have 'ref' in the url
if (! $request->hasCookie('referral') && $request->query('ref') ) {
  // Add a cookie to the response that lasts 5 years (in minutes)
  $response->Cookie( 'referral', encrypt( $request->query('ref') ), 60 );
}

return $response;
    }
}
