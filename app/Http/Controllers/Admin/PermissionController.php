<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->get();
        $this->ensureAdminGroupExists();
        $this->ensureReportPermissionsExist();

        $groups = DB::table('user_groups')
            ->where('status', 1)
            ->orderBy('ordering')
            ->orderBy('name')
            ->get();

        $selectedGroupId = (int) $request->query('group_id', $groups->first()?->id);
        $group = $groups->firstWhere('id', $selectedGroupId) ?? $groups->first();

        $memberUserIds = DB::table('user_group_members')
            ->where('user_group_id', $group->id)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $userGroupNames = DB::table('user_group_members')
            ->join('user_groups', 'user_groups.id', '=', 'user_group_members.user_group_id')
            ->where('user_groups.status', 1)
            ->select('user_group_members.user_id', 'user_groups.name')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->pluck('name')->implode(', '))
            ->all();

        $moduleTypes = DB::table('module_types')
            ->where('status', 1)
            ->orderBy('ordering')
            ->orderBy('id')
            ->get()
            ->map(function ($type) {
                $type->modules = DB::table('modules')
                    ->where('module_type_id', $type->id)
                    ->where('status', 1)
                    ->orderBy('ordering')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($module) {
                        $module->details = DB::table('module_details')
                            ->where('module_id', $module->id)
                            ->orderBy('id')
                            ->get();

                        return $module;
                    });

                return $type;
            });

        $checkedPermissions = DB::table('group_permissions')
            ->where('user_group_id', $group->id)
            ->where('is_allowed', 1)
            ->get()
            ->mapWithKeys(function ($permission) {
                return [$permission->module_id => true];
            })
            ->all();

        return view('admin.permissions.index', compact(
            'users',
            'groups',
            'group',
            'memberUserIds',
            'userGroupNames',
            'moduleTypes',
            'checkedPermissions'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_group_id' => ['required', 'integer', 'exists:user_groups,id'],
            'user_ids' => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'permissions' => ['array'],
            'permissions.*.enabled' => ['sometimes', 'boolean'],
            'permissions.*.module_id' => ['required', 'integer', 'exists:modules,id'],
        ]);

        $userIds = collect($data['user_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $permissions = collect($data['permissions'] ?? [])->filter(fn ($permission) => !empty($permission['enabled']));
        $groupId = (int) $data['user_group_id'];

        DB::transaction(function () use ($groupId, $userIds, $permissions) {
            DB::table('user_group_members')->where('user_group_id', $groupId)->delete();
            DB::table('group_permissions')->where('user_group_id', $groupId)->delete();

            $now = now();
            $memberRows = [];

            if ($userIds->isNotEmpty()) {
                DB::table('user_group_members')->whereIn('user_id', $userIds)->delete();
            }

            foreach ($userIds as $userId) {
                $memberRows[] = [
                    'user_group_id' => $groupId,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($memberRows) {
                DB::table('user_group_members')->insert($memberRows);
            }

            $rows = [];

            foreach ($permissions as $permission) {
                $rows[] = [
                    'user_group_id' => $groupId,
                    'module_id' => (int) $permission['module_id'],
                    'is_allowed' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($rows) {
                DB::table('group_permissions')->insert($rows);
            }
        });

        return redirect()->route('admin.permissions.index', ['group_id' => $groupId])
            ->with('success', 'Permissions updated successfully');
    }

    public function createGroup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:user_groups,name'],
        ]);

        $groupId = DB::table('user_groups')->insertGetId([
            'name' => $data['name'],
            'ordering' => (int) DB::table('user_groups')->max('ordering') + 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.permissions.index', ['group_id' => $groupId])
            ->with('success', 'Group created successfully');
    }

    private function ensureAdminGroupExists(): void
    {
        if (DB::table('user_groups')->where('name', 'Admin')->exists()) {
            return;
        }

        DB::table('user_groups')->insert([
            'name' => 'Admin',
            'ordering' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureReportPermissionsExist(): void
    {
        $now = now();

        DB::table('module_types')->updateOrInsert(
            ['name' => 'Report'],
            [
                'ordering' => 4,
                'status' => 1,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $reportTypeId = DB::table('module_types')->where('name', 'Report')->value('id');

        $modules = [
            [
                'name' => 'Sale Report (View)',
                'ordering' => 1,
                'details' => [
                    ['controllers' => 'reports', 'views' => 'index'],
                ],
            ],
            [
                'name' => 'Sale Report (Export)',
                'ordering' => 2,
                'details' => [
                    ['controllers' => 'reports', 'views' => 'export'],
                ],
            ],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                [
                    'module_type_id' => $reportTypeId,
                    'name' => $module['name'],
                ],
                [
                    'ordering' => $module['ordering'],
                    'status' => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $moduleId = DB::table('modules')
                ->where('module_type_id', $reportTypeId)
                ->where('name', $module['name'])
                ->value('id');

            foreach ($module['details'] as $detail) {
                DB::table('module_details')->updateOrInsert(
                    [
                        'module_id' => $moduleId,
                        'controllers' => $detail['controllers'],
                        'views' => $detail['views'],
                    ],
                    [
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
