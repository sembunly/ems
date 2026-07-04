@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h4 class="m-0">Permissions</h4>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        Please check the permission form and try again.
        @if($errors->has('name'))
            <div>{{ $errors->first('name') }}</div>
        @endif
    </div>
@endif

<div class="mb-3 shadow-sm card">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.permissions.index') }}">
                    <label class="form-label fw-semibold color-black">Permission Group</label>
                    <select name="group_id" class="form-control" onchange="this.form.submit()">
                        @foreach($groups as $availableGroup)
                            <option value="{{ $availableGroup->id }}" @selected($availableGroup->id === $group->id)>
                                {{ $availableGroup->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="col-md-6">
                <form method="POST" action="{{ route('admin.permissions.groups.store') }}" class="d-flex gap-2">
                    @csrf
                    <div class="flex-grow-1">
                        <label class="form-label fw-semibold color-black">Create New Group</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Example: Product">
                    </div>
                    <button type="submit" class="btn btn-primary align-self-end">
                        Create
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.permissions.store') }}" id="permissionForm">
    @csrf

    <input type="hidden" name="user_group_id" value="{{ $group->id }}">
    <div id="selectedUsers"></div>

    <div class="mb-3 shadow-sm card">
        <div class="card-body">
            <div class="mb-3">
                <span class="badge bg-primary">Editing group: {{ $group->name }}</span>
            </div>

            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Available Users</label>
                    <select id="availableUsers" class="form-control" multiple size="9" style="height: 200px;">
                        @foreach($users as $user)
                            @if(!in_array($user->id, $memberUserIds))
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} - {{ $user->email }}
                                    @if(!empty($userGroupNames[$user->id]))
                                        ({{ $userGroupNames[$user->id] }})
                                    @endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="text-center col-md-2">
                    <button type="button" class="mb-2 btn btn-outline-primary btn-sm d-block w-100" id="addUser">
                        <i class="mdi mdi-arrow-right-bold"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm d-block w-100" id="removeUser">
                        <i class="mdi mdi-arrow-left-bold"></i>
                    </button>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Member of Group</label>
                    <select id="memberUsers" class="form-control" multiple size="9" style="height: 200px;">
                        @foreach($users as $user)
                            @if(in_array($user->id, $memberUserIds))
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="shadow-sm card">
        <div class="card-body">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0">Rights</h5>
                <div>
                    <button type="button" class="btn btn-light btn-sm" id="checkAll">Show all / Check all</button>
                    <button type="button" class="btn btn-light btn-sm" id="uncheckAll">Hide all / Uncheck all</button>
                </div>
            </div>

            <div class="permission-tree">
                @foreach($moduleTypes as $type)
                    <div class="mb-3 border rounded module-type-section" data-module-type="{{ $type->id }}">
                        <div class="px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                            <strong>{{ $type->name }}</strong>
                            <label class="m-0 small">
                                <input type="checkbox" class="type-full-right me-1">
                                Full rights
                            </label>
                        </div>

                        @forelse($type->modules as $module)
                            <div class="px-3 py-2 border-top">
                                <label class="m-0 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ $module->name }}</span>
                                    <span>
                                        <input
                                            type="checkbox"
                                            class="right-checkbox me-1"
                                            name="permissions[{{ $module->id }}][enabled]"
                                            value="1"
                                            data-module-type="{{ $type->id }}"
                                            @checked(isset($checkedPermissions[$module->id]))
                                        >
                                        <input type="hidden" name="permissions[{{ $module->id }}][module_id]" value="{{ $module->id }}">
                                    </span>
                                </label>

                                @if($module->details->isNotEmpty())
                                    <!-- <div class="mt-2 small text-muted">
                                        @foreach($module->details as $detail)
                                            <div>{{ $detail->controllers }} / {{ $detail->views }}</div>
                                        @endforeach
                                    </div> -->
                                @endif
                            </div>
                        @empty
                            <div class="px-3 py-3 text-muted border-top">
                                No module rows found.
                            </div>
                        @endforelse
                    </div>
                @endforeach
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i>
                    Save Permissions
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const availableUsers = document.getElementById('availableUsers');
        const memberUsers = document.getElementById('memberUsers');
        const selectedUsers = document.getElementById('selectedUsers');
        const permissionForm = document.getElementById('permissionForm');

        function moveSelected(from, to) {
            Array.from(from.selectedOptions).forEach(function (option) {
                option.selected = false;
                to.appendChild(option);
            });
        }

        function refreshSelectedUsers() {
            selectedUsers.innerHTML = '';

            Array.from(memberUsers.options).forEach(function (option) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = option.value;
                selectedUsers.appendChild(input);
            });
        }

        document.getElementById('addUser').addEventListener('click', function () {
            moveSelected(availableUsers, memberUsers);
        });

        document.getElementById('removeUser').addEventListener('click', function () {
            moveSelected(memberUsers, availableUsers);
        });

        document.querySelectorAll('.type-full-right').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const section = checkbox.closest('.module-type-section');
                section.querySelectorAll('.right-checkbox').forEach(function (right) {
                    right.checked = checkbox.checked;
                });
            });
        });

        document.getElementById('checkAll').addEventListener('click', function () {
            document.querySelectorAll('.right-checkbox, .type-full-right').forEach(function (checkbox) {
                checkbox.checked = true;
            });
        });

        document.getElementById('uncheckAll').addEventListener('click', function () {
            document.querySelectorAll('.right-checkbox, .type-full-right').forEach(function (checkbox) {
                checkbox.checked = false;
            });
        });

        permissionForm.addEventListener('submit', refreshSelectedUsers);
        refreshSelectedUsers();
    });
</script>
@endpush
