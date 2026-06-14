<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the rider app. Re-checks the role against the database (not just the
 * cached permission set) so a stale Spatie cache can never lock out a real rider.
 */
class EnsureRider
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route("login");
        }

        $isRider = $user->hasRole("rider")
            || $user->roles()->where("name", "rider")->exists()
            || $user->rider()->exists();

        if (! $isRider) {
            // Send non-riders to wherever they belong instead of a dead 403.
            return redirect()->route("admin.dashboard");
        }

        // Make sure a rider profile row exists for the board queries.
        if (! $user->rider) {
            $user->rider()->create([
                "branch_id" => $user->branch_id,
            ]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return $next($request);
    }
}
