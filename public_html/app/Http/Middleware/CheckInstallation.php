<?php

namespace App\Http\Middleware;

use App\Helpers\Classes\Helper;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $dbConnectionStatus = Helper::dbConnectionStatus();
            if ($dbConnectionStatus && Schema::hasTable('users')) {
                return $next($request);
            } else {
                return redirect('/install');
            }
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Access denied for user')) {
                return redirect('/install');
            }

            throw $e;
        }
    }
}
