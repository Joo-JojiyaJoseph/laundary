<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the admin/staff panel. A rider who lands here (e.g. via a stale
 * intended URL) is redirected to their own board instead of seeing a raw 403.
 * Genuine non-staff users without any panel role are sent to login.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next, string $roles = ""): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route("login");
        }

        // Riders belong on the rider board, never the admin panel.
        $isRider = $user->hasRole("rider")
            || $user->roles()->where("name", "rider")->exists()
            || $user->rider()->exists();

        $staffRoles = $roles ? explode("|", $roles) : ["super-admin", "admin", "branch-manager", "counter-staff"];
        $isStaff = $user->hasAnyRole($staffRoles)
            || $user->roles()->whereIn("name", $staffRoles)->exists();

        if ($isRider && ! $isStaff) {
            return redirect()->route("rider.board");
        }

        if (! $isStaff) {
            abort(403, "You do not have access to the admin panel.");
        }

        return $next($request);
    }
}
