<?php

namespace App\Http\Middleware;

use App\Models\Outlet;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOutletAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $outletId = $request->route('outletId');
        $outlet = Outlet::find($outletId);

        if (! $outlet) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet tidak ditemukan',
            ], 404);
        }

        // TODO: cek akses user ke outlet bila sudah multi-outlet.
        $request->attributes->set('outlet', $outlet);

        return $next($request);
    }
}
