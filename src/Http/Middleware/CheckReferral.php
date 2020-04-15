<?php

namespace Devi\MultiReferral\Http\Middleware;

use Closure;

class CheckReferral
{
    public function handle($request, Closure $next)
    {
        if (($ref = $request->query('ref')) && app(config('multi_referral.user_model', 'App\User'))->referralExists($ref)) {
            return redirect($request->fullUrl())->withCookie(cookie()->forever('referral', $ref));
        }
        return $next($request);
    }

}
