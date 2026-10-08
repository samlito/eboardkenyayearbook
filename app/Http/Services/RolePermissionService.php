<?php

namespace App\Http\Services;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\AuditLogService;

class RolePermissionService
{
    /* ================= PERMISSIONS ================= */
    public function storePermission(Request $request)
    {
        $permission = Permission::create([
            'name'       => $request->name,
            'group_name' => $request->group_name,
        ]);

        AuditLogService::log(
            $request,
            'INSERT',
            'permissions',
            $permission->id,
            null,
            $permission->toArray()
        );

        return $permission;
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $old = $permission->toArray();

        $permission->update([
            'name'       => $request->name,
            'group_name' => $request->group_name,
        ]);

        AuditLogService::log(
            $request,
            'UPDATE',
            'permissions',
            $permission->id,
            $old,
            $permission->toArray()
        );

        return $permission;
    }

    public function deletePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $old = $permission->toArray();

        $permission->delete();

        AuditLogService::log(
            $request,
            'DELETE',
            'permissions',
            $id,
            $old,
            null
        );

        return true;
    }

    /* ================= ROLES ================= */
    public function storeRole(Request $request)
    {
        $role = Role::create([
            'name' => $request->name,
        ]);

        AuditLogService::log(
            $request,
            'INSERT',
            'roles',
            $role->id,
            null,
            $role->toArray()
        );

        return $role;
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $old = $role->toArray();

        $role->update([
            'name' => $request->name,
        ]);

        AuditLogService::log(
            $request,
            'UPDATE',
            'roles',
            $role->id,
            $old,
            $role->toArray()
        );

        return $role;
    }

    public function deleteRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $old = $role->toArray();

        $role->delete();

        AuditLogService::log(
            $request,
            'DELETE',
            'roles',
            $id,
            $old,
            null
        );

        return true;
    }

    /* ================= ROLES & PERMISSIONS ================= */
    public function assignPermissionsToRole(Request $request)
    {
        $data = [];

        foreach ($request->permission as $permId) {
            $data[] = [
                'role_id'       => $request->role_id,
                'permission_id' => $permId,
            ];
        }

        DB::table('role_has_permissions')->insert($data);

        AuditLogService::log(
            $request,
            'INSERT',
            'role_has_permissions',
            $request->role_id,
            null,
            $data
        );

        return true;
    }

    public function updateRolePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $old = $role->permissions->pluck('id')->toArray();

        $role->syncPermissions($request->permission);

        AuditLogService::log(
            $request,
            'UPDATE',
            'role_has_permissions',
            $role->id,
            $old,
            $request->permission
        );

        return true;
    }
}
