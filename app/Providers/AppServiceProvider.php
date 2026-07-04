<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(['admin.*', 'partials.side-bar'], function ($view) {
            $permissions = [];

            $view->with('canAccessAdmin', function (string $controller, string $action) use (&$permissions): bool {
                $user = auth()->user();

                if (!$user) {
                    return false;
                }

                $key = $controller.'.'.$action;

                if (array_key_exists($key, $permissions)) {
                    return $permissions[$key];
                }

                $permissions[$key] = DB::table('user_group_members')
                    ->join('group_permissions', 'group_permissions.user_group_id', '=', 'user_group_members.user_group_id')
                    ->join('modules', 'modules.id', '=', 'group_permissions.module_id')
                    ->join('module_details', 'module_details.module_id', '=', 'modules.id')
                    ->where('user_group_members.user_id', $user->id)
                    ->where('group_permissions.is_allowed', 1)
                    ->where('modules.status', 1)
                    ->where('module_details.controllers', $controller)
                    ->where('module_details.views', $action)
                    ->exists();

                return $permissions[$key];
            });
        });
    }
}
