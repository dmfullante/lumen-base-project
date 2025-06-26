<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\JsonResponse;

class ResponseTimeMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);

        /** @var JsonResponse $response */
        $response = $next($request);

        $duration = microtime(true) - $start; // raw duration in seconds

        // Format to exactly 4 decimal places, e.g. 0.0440
        $formattedDuration = number_format($duration, 4, '.', '');

        // Modify JSON response
        if ($response instanceof JsonResponse) {
            $original = $response->getData(true);
            $original['completed_in'] = (float) $formattedDuration; // cast back to float if you want
            $response->setData($original);
        }

        return $response;
    }
}
