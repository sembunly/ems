<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('1'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        $this->ensureReportPermissionsExist();
        $this->grantAdminPermissions($admin);
    }

    private function ensureReportPermissionsExist(): void
    {
        if (!Schema::hasTable('module_types') ||
            !Schema::hasTable('modules') ||
            !Schema::hasTable('module_details')) {
            return;
        }

        $now = now();

        DB::table('module_types')->updateOrInsert(
            ['name' => 'Report'],
            [
                'ordering' => 4,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $reportTypeId = DB::table('module_types')->where('name', 'Report')->value('id');

        foreach ([
            ['name' => 'Sale Report (View)', 'ordering' => 1, 'view' => 'index'],
            ['name' => 'Sale Report (Export)', 'ordering' => 2, 'view' => 'export'],
        ] as $module) {
            DB::table('modules')->updateOrInsert(
                [
                    'module_type_id' => $reportTypeId,
                    'name' => $module['name'],
                ],
                [
                    'ordering' => $module['ordering'],
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $moduleId = DB::table('modules')
                ->where('module_type_id', $reportTypeId)
                ->where('name', $module['name'])
                ->value('id');

            DB::table('module_details')->updateOrInsert(
                [
                    'module_id' => $moduleId,
                    'controllers' => 'reports',
                    'views' => $module['view'],
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function grantAdminPermissions(User $admin): void
    {
        if (!Schema::hasTable('user_groups') ||
            !Schema::hasTable('user_group_members') ||
            !Schema::hasTable('modules') ||
            !Schema::hasTable('group_permissions')) {
            return;
        }

        $now = now();

        $groupId = DB::table('user_groups')->where('name', 'Admin')->value('id');

        if (!$groupId) {
            $groupId = DB::table('user_groups')->insertGetId([
                'name' => 'Admin',
                'ordering' => 1,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('user_group_members')->updateOrInsert(
            [
                'user_group_id' => $groupId,
                'user_id' => $admin->id,
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('modules')
            ->where('status', 1)
            ->pluck('id')
            ->each(function ($moduleId) use ($groupId, $now) {
                DB::table('group_permissions')->updateOrInsert(
                    [
                        'user_group_id' => $groupId,
                        'module_id' => $moduleId,
                    ],
                    [
                        'is_allowed' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            });
    }
}
