<?php

namespace API;

// REST services for employees
class EmployeeController extends \API\APIController {

    public function beforeView(&$emp) {        
        if ($emp != null) {
            $emp->manager;
            $emp->profile->user;
            $emp->profile->address;
            $emp->resource;
            $res = new \Resource();
            if (!empty($emp->resource)) {
                $this->applyACLToModel($emp->resource, isset($res->adminSettings['acl']) ? $res->adminSettings['acl'] : null);
            }
            $emp->timeData = \Employee::getTimeData($emp);
            if ($emp->manager != null) {
                $emp->manager->profile;
                $this->applyACLToModel($emp->manager, isset($emp->manager->adminSettings['acl']) ? $emp->manager->adminSettings['acl'] : null);
            }
            // load direct reports

            $emp->direct_reports = \Employee::join('profiles','profiles.id','=','employees.profile_id')->
            orderBy('profiles.last_name')->orderBy('profiles.first_name')->with('profile')->with('resource')->where('manager_id', '=', $emp->id)->get(['employees.*']);

            $tsController = new \API\TimesheetController();
            $emp->timesheets = $tsController->getByEmployee($emp->id);

        }
        return $emp;
    }
    
    public function beforeList(&$list) {
        // Fill in some nested data that wasn't eager loaded
        $today = date("Y-m-d H:i:s");
        foreach ($list as &$e) {
            $e->profile->user;           
            $e->displayStatus = ($e->worker_type == "Employee") ? "Full-time" : $e->worker_type;
            if ($e->status == "Onboarding") $e->displayStatus = "Onboarding";
            if ($e->status == "Active" && ($e->hire_date > $today)) $e->displayStatus = "Onboarding Complete";
            if ($e->termination_date != null && $e->termination_date != '0000-00-00' && $e->termination_date < $today) $e->displayStatus = "Terminated"; 
        }
        return $list;

    }



      /**
     * Save and update employee information
     * 
     * <b>PUT /api/v1/employees/{id}</b>
     *
     * <h3>Request Information:</h3>
     *
     * <table class="apidescription" >
     * <tr><td>Request format</td><td>JSON</td></tr>	
     * <tr><td>Requires authentication?</td><td>Yes</td></tr>	
     * </table><br />
     * 
     * <b>URI parameters:</b>
     * <table class="apidescription" >
     * <tr><td>id</td><td>Integer</td><td>employee id</td></tr>
     * </table><br />
     * 
     * <b>Body parameters:</b>
     * 
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>id</td><td>integer</td><td>id of employee</td></tr>
    *     <tr><td>comp_amount</td><td>integer</td><td>Compensation amount</td></tr>
    *     <tr><td>base_rate</td><td>integer</td><td>Base rate</td></tr>
    *     <tr><td>uuid</td><td>string</td><td>UUID</td></tr>
    *     <tr><td>external_id</td><td>string</td><td> </td></tr>
    *     <tr><td>resource_id</td><td>integer</td><td> </td></tr>
    *     <tr><td>created_at</td><td>datetime</td><td>When employee created</td></tr>
    *     <tr><td>supervisor_email</td><td>string</td><td>supervisor email</td></tr>
    *     <tr><td>full_time</td><td>integer</td><td>full time</td></tr>
    *     <tr><td>daily_billable_hours</td><td>integer</td><td>daily billable hours</td></tr>
    *     <tr><td>tenant_id</td><td>integer</td><td>id of tenant</td></tr>
    *     <tr><td>department</td><td>string</td><td>department</td></tr>
    *     <tr><td>manager_id</td><td>integer</td><td>id of manager</td></tr>
    *     <tr><td>worker_type</td><td>enum</td><td>can be 'Employee','Part-time Employee','Contractor'</td></tr>
    *     <tr><td>comp_type</td><td>enum</td><td>can be 'Hourly', 'Annual', 'Monthly'</td></tr>
    *     <tr><td>updated_at</td><td>datetime</td><td>When employee updated</td></tr>
    *     <tr><td>job_title</td><td>string</td><td>job title</td></tr>
    *     <tr><td>location</td><td>string</td><td>location</td></tr>
    *     <tr><td>hire_date</td><td>date</td><td>hire date</td></tr>
    *     <tr><td>personal_email</td><td>string</td><td>personal email</td></tr>
    *     <tr><td>status</td><td>string</td><td> </td></tr>
    *     <tr><td>profile_id</td><td>integer</td><td>id of profile</td></tr>
    *     <tr><td>manager</td><td>object</td><td>
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>comp_amount</td><td>integer</td><td>Compensation amount</td></tr>
    *         <tr><td>base_rate</td><td>integer</td><td>Base rate</td></tr>
    *         <tr><td>uuid</td><td>string</td><td>UUID</td></tr>
    *         <tr><td>external_id</td><td>string</td><td> </td></tr>
    *         <tr><td>resource_id</td><td>integer</td><td> </td></tr>
    *         <tr><td>created_at</td><td>datetime</td><td>When employee created</td></tr>
    *         <tr><td>supervisor_email</td><td>string</td><td>supervisor email</td></tr>
    *         <tr><td>full_time</td><td>integer</td><td>full time</td></tr>
    *         <tr><td>daily_billable_hours</td><td>integer</td><td>daily billable hours</td></tr>
    *         <tr><td>tenant_id</td><td>integer</td><td>id of tenant</td></tr>
    *         <tr><td>department</td><td>string</td><td>department</td></tr>
    *         <tr><td>manager_id</td><td>integer</td><td>id of manager</td></tr>
    *         <tr><td>worker_type</td><td>enum</td><td>can be 'Employee','Part-time Employee','Contractor'</td></tr>
    *         <tr><td>comp_type</td><td>enum</td><td>can be 'Hourly', 'Annual', 'Monthly'</td></tr>
    *         <tr><td>updated_at</td><td>datetime</td><td>When employee updated</td></tr>
    *         <tr><td>job_title</td><td>string</td><td>job title</td></tr>
    *         <tr><td>location</td><td>string</td><td>location</td></tr>
    *         <tr><td>hire_date</td><td>date</td><td>hire date</td></tr>
    *         <tr><td>personal_email</td><td>string</td><td>personal email</td></tr>
    *         <tr><td>status</td><td>string</td><td> </td></tr>
    *         <tr><td>profile_id</td><td>integer</td><td>id of profile</td></tr>
    *         <tr><td>profile</td><td>object</td><td>
    *             <table class="apidescription" >
    *             <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *             <tr><td>id</td><td>integer</td><td>id of profile</td></tr>
    *             <tr><td>first_name</td><td>string</td><td>First name</td></tr>
    *             <tr><td>last_name</td><td>string</td><td>Last name </td></tr>
    *             <tr><td>email</td><td>string</td><td>E-mail</td></tr>
    *             <tr><td>address_id</td><td>integer</td><td>id of address of profile</td></tr>
    *             <tr><td>date_of_birth</td><td>date</td><td>Date of birth</td></tr>
    *             <tr><td>gender</td><td>ENUM</td><td>Gender</td></tr>
    *             <tr><td>image_id</td><td>integer</td><td>id of profile image</td></tr>
    *             </table></td></tr>
    *         </table></td></tr>
    *     <tr><td>profile</td><td>object</td><td>
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of profile</td></tr>
    *         <tr><td>first_name</td><td>string</td><td>First name</td></tr>
    *         <tr><td>last_name</td><td>string</td><td>Last name </td></tr>
    *         <tr><td>email</td><td>string</td><td>E-mail</td></tr>
    *         <tr><td>address_id</td><td>integer</td><td>id of address of profile</td></tr>
    *         <tr><td>date_of_birth</td><td>date</td><td>Date of birth</td></tr>
    *         <tr><td>gender</td><td>ENUM</td><td>Gender</td></tr>
    *         <tr><td>image_id</td><td>integer</td><td>id of profile image</td></tr>
    *         <tr><td>user</td><td>object</td><td>
    *             <table class="apidescription" >
    *             <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *             <tr><td>id</td><td>integer</td><td>id of user</td></tr>
    *             <tr><td>username</td><td>string</td><td>username</td></tr>
    *             <tr><td>status</td><td>string</td><td>user status</td></tr>
    *             <tr><td>last_login_attempt</td><td>datetime</td><td>last login attempt</td></tr>
    *             <tr><td>failed_logins</td><td>integer</td><td>count of failed logins</td></tr>
    *             <tr><td>created_at</td><td>datetime</td><td>>created_at</td></tr>
    *             <tr><td>updated_at</td><td>datetime</td><td>updated_at</td></tr>
    *             <tr><td>tenant_id</td><td>integer</td><td>id of user tenant</td></tr>
    *             <tr><td>uuid</td><td>string</td><td>UUID</td></tr>
    *             <tr><td>profile_id</td><td>integer</td><td>id of user profile</td></tr>
    *             </table></td></tr>
    *         </table></td></tr>
    *     </table>
       * 
     * <h3>Response Information:</h3>
     *
     * <b>Response:</b>
     *
     * <b>Success:</b>
     * HTTP Status 201
     *
     * <b>Body:</b>
     * The single Employee object of following structure
    * <pre>
    *     <table class="apidescription" >
    *     <tr><td>id</td><td>integer</td><td>id of employee</td></tr>
    *     <tr><td>comp_amount</td><td>integer</td><td>Compensation amount</td></tr>
    *     <tr><td>base_rate</td><td>integer</td><td>Base rate</td></tr>
    *     <tr><td>uuid</td><td>string</td><td>UUID</td></tr>
    *     <tr><td>external_id</td><td>string</td><td> </td></tr>
    *     <tr><td>resource_id</td><td>integer</td><td> </td></tr>
    *     <tr><td>created_at</td><td>datetime</td><td>When employee created</td></tr>
    *     <tr><td>supervisor_email</td><td>string</td><td>supervisor email</td></tr>
    *     <tr><td>full_time</td><td>integer</td><td>full time</td></tr>
    *     <tr><td>daily_billable_hours</td><td>integer</td><td>daily billable hours</td></tr>
    *     <tr><td>tenant_id</td><td>integer</td><td>id of tenant</td></tr>
    *     <tr><td>department</td><td>string</td><td>department</td></tr>
    *     <tr><td>manager_id</td><td>integer</td><td>id of manager</td></tr>
    *     <tr><td>worker_type</td><td>enum</td><td>can be 'Employee','Part-time Employee','Contractor'</td></tr>
    *     <tr><td>comp_type</td><td>enum</td><td>can be 'Hourly', 'Annual', 'Monthly'</td></tr>
    *     <tr><td>updated_at</td><td>datetime</td><td>When employee updated</td></tr>
    *     <tr><td>job_title</td><td>string</td><td>job title</td></tr>
    *     <tr><td>location</td><td>string</td><td>location</td></tr>
    *     <tr><td>hire_date</td><td>date</td><td>hire date</td></tr>
    *     <tr><td>personal_email</td><td>string</td><td>personal email</td></tr>
    *     <tr><td>status</td><td>string</td><td> </td></tr>
    *     <tr><td>profile_id</td><td>integer</td><td>id of profile</td></tr>
    *     </table>
    * </pre>
     *
     * <b>Errors:</b>
     * 
     * HTTP Status 401
     *
     * <b>Body:</b>
    * The message object of following structure
    * <pre>
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>error type (which field are wrong ex. "postal", "street" )</td><td>string array</td><td>Text which told what is wrong</td></tr>
    *     </table>
    * </pre>
     *
     * @param integer $id of the profile for which should be displayed
     * @return string JSON  of Profile or error
     */

      public function save(&$emp) {
        $input = \Input::all();
        $newEmployee = ($emp->exists == false);
          //check unique user  name
        if ($newEmployee ) {
            $emp->status = 'Active';
            $userValidation = \Validator::make(array('email' => $input['profile']['email']), array('email' => 'required|unique:users,username'));
        }
        else {
            $userValidation = \Validator::make(array('email' => $input['profile']['email']), array('email' => 'required|unique:users,username,'.$emp->profile->user->id));
//            $userValidation = \Validator::make(array('email' => $input['profile']['email']), array('email' => 'required', Rule::unique('users,username')->ignore($emp->profile->user->id)));
        }

        // Failing here

        if ($userValidation->fails()) {
            return \Response::json(['message' => $userValidation->messages()], 400);
        }

        $empValidation = \Validator::make($emp->toArray(), \Employee::getAddValidation());
        if ($empValidation->fails() ) {
            return \Response::json(['message' => $empValidation->messages()], 400); 
        }

        $savedOK = parent::save($emp);

        if (!$newEmployee && !empty($input['profile']['id']) && is_int($input['profile']['id'])) {
          $profile = \Profile::find($input['profile']['id']);
          $profile->first_name = $input['profile']['first_name'];
          $profile->last_name = $input['profile']['last_name'];
          $profile->email =  $input['profile']['email'];
          $profile ->save();
      }

      if ($savedOK && $newEmployee) {
        $profile = new \Profile;
        $profile->first_name = $input['profile']['first_name'];
        $profile->last_name = $input['profile']['last_name'];
        $profile->email =  $input['profile']['email'];
        $profile ->save();
        $emp->profile_id = $profile->id;
        $emp->save();

        $user = $this->createUserForEmployee($emp, $profile);
        $user->profile_id = $profile->id;
        $user->save();

        $userToken = \UserToken::createUserToken($user,new \DateInterval('P7D'),'activation');
        $this->sendEmployeeWelcomeEmail($emp, $userToken);

    }
    return $savedOK;
}

    /**
    * This function return List of Locations for  tenant of current user
    * 
    * 
    * <b>GET /api/v1/employees/locations</b>
    *
    * <h3>Request Information:</h3>
    * No
    * 
    * <table class="apidescription" >
    * <tr><td>Requires authentication?</td><td>Yes</td></tr>	
    * </table><br />
    * 
    * <b>URI parameters:</b>
    * No
    * 
    * <b>Body parameters:</b>
    * No
    * 
    * <h3>Response Information:</h3>
    *
    * <b>Response:</b>
    *
    * <b>Success:</b>
    * HTTP Status 200
    * 
    *  <pre> 
    * Array of following structure
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>No</td><td>string</td>Location's title<td>
    *     </table>
    * </pre> 
    * 
    *  @return string JSON  array of Locatios
    */

    
    public function getLocations() {
        $locs = \Employee::getEmployeeLocations(\Auth::user()->tenant->id);
        $locationArray = array();
        foreach($locs as $loc) {
            $locationArray[] = $loc->location;
        }
        return \Response::json($locationArray, 200);
    }
    
    /**
     * Create user account for a new hired employee
     * 
     * @param object $emp Employee
     * @return object User
    */
    private function createUserForEmployee($emp, $profile) {
        $user = new \User;
        $user->username = $profile->email;
        $user->tenant_id = $emp->tenant_id;
        $user->password = \Hash::make('new_user'. mt_rand().mt_rand()); 
        $user->uuid = generateUUID();
        $user->status = 1; 
        $user->save();
        $userRole = \Role::where('name', '=', 'User')->first();
        $user->roles()->attach($userRole);
        return $user;
    }
    
    
    /**
     * Send welcome email message to a new hired employee
     * 
     * @param object $employee Employee
     * @param object $userToken UserToken
    */
    private function sendEmployeeWelcomeEmail($employee, $userToken) {
        $notifyEmail = $employee->profile->email;
        if (!empty($employee->personal_email)) $notifyEmail = $employee->personal_email;
        $data = array(
            'recipient' => $employee->profile->first_name . ($employee->profile->last_name == null ? '' : ' ' . $employee->profile->last_name),
            'tenant' => \Auth::user()->tenant,
            'link' => "http://".$_SERVER['SERVER_NAME']."/access/activate?t=".$userToken->token
            );
        \Mail::send('emails.addemployee', $data, function($message) use ($employee, $notifyEmail) {
            $message->from('no-reply@vistadesk.com', 'VistaDesk');
            $message->to($notifyEmail, $employee->first_name." ".$employee->last_name)->subject('Activate your VistaDesk account');
        });
    }
}




