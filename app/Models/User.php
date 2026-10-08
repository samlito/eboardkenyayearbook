<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\BaseModel;
use App\Notifications\CustomVerifyEmail;

class User extends BaseModel implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'photo',
        'email',
        'password',
        'staff_number',
        'created_by',
        'password_reset',
        'email_verified_at',
        'division_id',
        'locked',
        'failed_logins',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public static  function getpermissionGroups(){
        $get_permissionGroups= DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $get_permissionGroups;
    }

    public static function getpermissionByGroupName($group_name){
        $permissions = DB::table('permissions')
            ->select('name','id')
            ->where('group_name',$group_name)
            ->get();
        return $permissions;

    }// End Method
    public static function roleHasPermissions($role, $permissions){

        $hasPermission = true;
        foreach($permissions as $permission){
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
            return $hasPermission;
        }

    }// End Method
    public function boardAssignments()
    {
        return $this->hasMany(BoardMeetingAssignment::class, 'user_id');
    }
    public function boardMember()
    {
        return $this->hasOne(BoardMember::class, 'user_id', 'id');
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function directorateMappings()
    {
        return $this->hasMany(\App\Models\Twwda\TwwdaUserDirectorateMapping::class);
    }

    public function activeDirectorateMapping()
    {
        return $this->hasOne(\App\Models\Twwda\TwwdaUserDirectorateMapping::class)->where('is_active', true);
    }

    public function twwdaNotifications()
    {
        return $this->hasMany(\App\Models\Twwda\TwwdaNotification::class);
    }

    public function unreadTwwdaNotifications()
    {
        return $this->twwdaNotifications()->whereNull('read_at');
    }

}
