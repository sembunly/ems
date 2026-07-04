<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupPermission
{
    public function handle(Request $request, Closure $next, string $controller, string $view): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        $hasPermission = DB::table('user_group_members')
            ->join('group_permissions', 'group_permissions.user_group_id', '=', 'user_group_members.user_group_id')
            ->join('modules', 'modules.id', '=', 'group_permissions.module_id')
            ->join('module_details', 'module_details.module_id', '=', 'modules.id')
            ->where('user_group_members.user_id', $user->id)
            ->where('group_permissions.is_allowed', 1)
            ->where('modules.status', 1)
            ->where('module_details.controllers', $controller)
            ->where('module_details.views', $view)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
