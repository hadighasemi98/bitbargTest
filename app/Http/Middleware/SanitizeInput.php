<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $input[$key][$subKey] = strip_tags($subValue);
                }
            } else {
                $input[$key] = strip_tags($value);
            }
        }

        $request->merge($input);

        return $next($request);
    }
}
