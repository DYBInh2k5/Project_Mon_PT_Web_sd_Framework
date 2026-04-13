<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAge
{
    public function handle(Request $request, Closure $next): Response
    {
        $age = (int) $request->route('age', $request->input('age', 0));

        if ($age >= 200) {
            return redirect('/check_fail');
        }

        return $next($request);
    }
}
