<?php

namespace App\Http\Controllers;

use App\Http\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function AdminDestroy0(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Admin Logout Successfully',
            'alert-type' => 'info'
        );

        return redirect('/login')->with($notification);
    }

    public function AdminDestroy(Request $request)
    {
        $userId = Auth::id(); // capture before logout

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log the logout activity
       AuditLogService::log(
            $request,
            'LOGOUT',
            'users',
            $userId,
            null,
            ['message' => 'User logged out successfully']
        );

        $notification = [
            'message' => 'Admin Logout Successfully',
            'alert-type' => 'info'
        ];

        return redirect('/login')->with($notification);
    }

    public function AdminLogoutPage(){

        return view('admin.admin_logout');
    }

    public function AdminProfile(){
        $id = Auth::user()->id;
        $adminData=User::find($id);

        return view('admin.admin_profile_view',compact('adminData'));
    }

    public function AdminProfileStore(Request $request){

        $id = Auth::user()->id;
        $data=User::find($id);
        $data->name=$request->name;
        $data->email=$request->email;
        $data->phone=$request->phone;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_image/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_image'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();
        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    public function ChangePassword(){
        return view('admin.change_password');
    }// End Method

    public function UpdatePassword(Request $request){
     /// Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',

        ]);

        /// Match The Old Password
        if (!Hash::check($request->old_password, auth::user()->password)) {

            $notification = array(
                'message' => 'Old Password Does not Match!!',
                'alert-type' => 'error'
            );
            return back()->with($notification);

        }

        //// Update The New Password

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password),
            'password_reset' => false,
        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );
//        return back()->with($notification);
//        return view('admin.admin_profile_view')->with($notification);
        return redirect()->route('dashboard')->with($notification);
    }


}

