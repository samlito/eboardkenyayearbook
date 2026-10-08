<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\modules;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Services\RolePermissionService;
class RoleController extends Controller
{
    //
    public function AllPermission(){

        $permissions = Permission::all();
        return view('backend.pages.permission.all_permission',compact('permissions'));

    } // End Method
    public function AddPermission(){
        $data['modules'] = modules::latest()->select('name')->get();


        return view('backend.pages.permission.add_permission',$data);

    } // End Method


    public function StorePermission(Request $request){

//        dd($request);
        $role = Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,

        ]);


        $notification = array(
            'message' => 'Permission Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.permission')->with($notification);

    }// End Method

    public function EditPermission($id){

        $permission = Permission::findOrFail($id);
        $modules=modules::latest()->get();
        return view('backend.pages.permission.edit_permission',compact('permission','modules'));

    }// End Method


    public function UpdatePermission(Request $request){

        $per_id = $request->id;

        Permission::findOrFail($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,

        ]);

        $notification = array(
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.permission')->with($notification);

    }// End Method


    public function DeletePermission($id){

        Permission::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method

    public function AllRoles(){

        $roles = Role::withCount('users', 'permissions')->get();

        $systemRoles = [
            'Superadmin', 'admin', 'CEO', 'Corporation Secretary',
            'Director', 'Division Head', 'DD Internal Audit', 'DD ICT',
            'Board Member', 'Officer',
        ];

        $roleLabelMap = [
            'CEO' => 'role_label_ceo',
            'Corporation Secretary' => 'role_label_corporation_secretary',
            'Board Member' => 'role_label_board_member',
            'DD Internal Audit' => 'role_label_dd_internal_audit',
            'Director' => 'role_label_director',
            'Division Head' => 'role_label_division_head',
        ];

        return view('backend.pages.roles.all_roles', compact('roles', 'systemRoles', 'roleLabelMap'));

    }// End Method


    public function AddRoles(){

        return view('backend.pages.roles.add_roles');

    }// End Method


    public function StoreRoles(Request $request){

        $role = Role::create([
            'name' => $request->name,

        ]);

        $notification = array(
            'message' => 'Role Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles')->with($notification);

    }// End Method
    public function EditRoles($id){

        $roles = Role::findOrFail($id);
        return view('backend.pages.roles.edit_roles',compact('roles'));

    }// End Method

    public function UpdateRoles(Request $request){

        $role_id = $request->id;

        Role::findOrFail($role_id)->update([
            'name' => $request->name,

        ]);

        $notification = array(
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles')->with($notification);

    }// End Method


    public function DeleteRoles($id){

        Role::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method



    public function AddRolesPermission(){

        $roles = Role::all();
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.roles.add_roles_permission',compact('roles','permissions','permission_groups'));

    }// End Method


    public function StoreRolesPermission(Request $request){

        $data = array();
        $permissions = $request->permission;

        foreach($permissions as $key => $item){
            $data['role_id'] = $request->role_id;
            $data['permission_id'] = $item;

            DB::table('role_has_permissions')->insert($data);

        }

        $notification = array(
            'message' => 'Role Permission Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles.permission')->with($notification);

    }// End Method

    public function AllRolesPermission(){

        $roles = Role::all();
        return view('backend.pages.roles.all_roles_permission',compact('roles'));

    } // End Method

    public function AdminEditRoles($id){

        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.roles.edit_roles_permission',compact('role','permissions','permission_groups'));

    } // End Method

    public function RolePermissionUpdate(Request $request,$id){

        $role = Role::findOrFail($id);
        $permissions = $request->permission;

//        dd($request->permission);
//        if (!empty($permissions)) {
//            $role->syncPermissions($request->permission);
//        }
        $role->syncPermissions($request->permission);

        $notification = array(
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.roles.permission')->with($notification);

    }// End Method

    public function AdminDeleteRoles($id){

        $role = Role::findOrFail($id);
        if (!is_null($role)) {
            $role->delete();
        }

        $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }// End Method

}
class RoleController1 extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    public function StorePermission(Request $request)
    {
        $this->service->storePermission($request);

        return redirect()->route('all.permission')->with([
            'message' => 'Permission Added Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UpdatePermission(Request $request)
    {
        $this->service->updatePermission($request, $request->id);

        return redirect()->route('all.permission')->with([
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function DeletePermission(Request $request, $id)
    {
        $this->service->deletePermission($request, $id);

        return redirect()->back()->with([
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function StoreRoles(Request $request)
    {
        $this->service->storeRole($request);

        return redirect()->route('all.roles')->with([
            'message' => 'Role Added Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UpdateRoles(Request $request)
    {
        $this->service->updateRole($request, $request->id);

        return redirect()->route('all.roles')->with([
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteRoles(Request $request, $id)
    {
        $this->service->deleteRole($request, $id);

        return redirect()->back()->with([
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function StoreRolesPermission(Request $request)
    {
        $this->service->assignPermissionsToRole($request);

        return redirect()->route('all.roles.permission')->with([
            'message' => 'Role Permission Added Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function RolePermissionUpdate(Request $request, $id)
    {
        $this->service->updateRolePermissions($request, $id);

        return redirect()->route('all.roles.permission')->with([
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success',
        ]);
    }
}
