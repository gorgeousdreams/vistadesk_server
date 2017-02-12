<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $adminSettings = array(
		'list'=>array(
			'fields'=>array('id', 'created_at', 'summary', 'project'),
			),
		'displayField' => 'username'
		);

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
	protected $fillable = array('username');

    public function tenant() {
        return $this->belongsTo('Tenant');
    }

    public function roles() {
        return $this->belongsToMany('Role');
    }

    public function companies() {
        return $this->belongsToMany('Company');
    }

    public function userTokens() {
        return $this->hasMany('userToken');
    }

    public function getPermissions() {
        if (!empty($this->permissions)) return $this->permissions;
        $sql = "select p.* from permissions p ".
            "LEFT JOIN role_user ru on (ru.user_id = " . $this->id . ") " .
            "INNER JOIN permission_assignments pa on pa.permission_id = p.id and (pa.role_id = ru.role_id OR pa.user_id = ".$this->id.")";
        $res = DB::select(DB::raw($sql));
        $this->permissions = array();
        foreach ($res as $r) {
            array_push($this->permissions, (object)$r);
        }
        return $this->permissions;
    }

    public function hasRole($key) {
        foreach($this->roles as $role){
            if($role->name === $key || ($role->name === "Admin" && $key != "Root"))     // 'admin' has all roles, except for one: 'Root'
            {
                return true;
            }
        }
        return false;
    }

    // FIXME: Change this to a hash lookup instead of iterating the list every time.
    public function can($permission) {
        if ($this->hasRole('Admin')) return true;
        foreach ($this->getPermissions() as $permission) {
            if (($permission->action.' '.$permission->resource) == $permission) return true;
        }
        return false;
    }

    public function profile() {
        return $this->belongsTo('Profile');
    }


    public static function isSetUser() {
        $validation = array(
            'username'              => 'required|unique:users',
            );
        return $validation;
    }

    public static function getAddValidation() {
        $validation = array(
            'username'              => 'required|email|unique:users',
            'password'              => 'required|regex:((?=.*\d)(?=.*[a-z]).{6,20})',
            );
        return $validation;
    }

    public static function isUserNotActivated($userName) {
        return User::where('username',"=",$userName)->first()->userTokens()->where('token_type',"=",'activation')->count();
    }

    public static function managers() {
        return User::leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->with('profile')
                ->where('roles.name', '=', 'Manager');
    }

	public static function admins() {
        return User::leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->with('profile')
                ->where('roles.name', '=', 'Admin');
    }

    public static function createNewUser($firstName, $lastName, $email, $password, $tenantId, $status) {
        $user = new User();
        $profile = new Profile();
        $profile->date_of_birth = '1900-01-01';
        $profile->last_name = $lastName;
        $profile->first_name = $firstName;
        $profile->email = $email;
        $profile->save();

        \MultiTenantScope::$tenantId = $tenantId;
        $user->username = $email;
        $user->password = Hash::make($password);
        $user->uuid = generateUUID();
        $user->status = $status;
        $user->profile_id = $profile->id;        
        $user->tenant_id = $tenantId;
        $user->save();

        $adminRole = Role::where('name', '=', 'Admin')->first();
        $user->roles()->attach($adminRole);

        return User::find($user->id);
    }


}
