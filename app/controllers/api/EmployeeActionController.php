<?php
/**
 * @author SLC
 */

namespace API;

// REST services for employees
class EmployeeActionController extends \API\APIController {

    /**
    * This function activate hired employee user
    * 
    * <b>POST /employees-action/activate-hired</b>
    *
    * <h3>Request Information:</h3>
    * 
    * <b>URI parameters:</b>
    * No
    * 
    * 
    * <b>Body parameters:</b>
    *
    * <pre>
    * <style>
    *    table.apidescription {
    *       border:1px solid #BBBBBB;
    *       font-family: Arial;
    *       font-size:14px;
    *    }
    * 
    *    table.apidescription th {
    *       background-color:#DDDDDD;
    *       border:1px solid #BBBBBB;
    *       padding:10px;
    *    }
    * 
    *    table.apidescription td {
    *       border:1px solid #BBBBBB;
    *       padding:10px;
    *    }
    * </style>
    * user object of following structure
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>token</td><td>string</td><td>string with token value</td></tr>
    *     <tr><td>password</td><td>string</td><td>password</td></tr>
    *     <tr><td>password1</td><td>string</td><td>retyped password</td></tr>
    *     <tr><td>agree</td><td>boolean</td><td>agree with terms and conditions</td></tr>
    *     <tr><td>email</td><td>string</td><td>email </td></tr>
    *     </table>
    * </pre>
    * 
    * <h3>Response Information:</h3>
    *
    * <b>Response:</b>
    *
    * <b>Success:</b>
    * HTTP Status 201
    *
    * <b>Body:</b>
    * <pre>
    * The message object of following structure
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *     <tr><td>success</td><td>array</td><td>password set successfully</td></tr>
    *     </table>
    * </pre>
    *
    * <b>Errors:</b>
    * 
    * HTTP Status 400
    *
    * <b>Body:</b>
    * 
    * The message object of following structure
    * <pre>
    * if token parametr is empty
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>user_token is empty</td></tr>
    *         </table>
    * 
    * if token not found in token table
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>user_token not found</td></tr>
    *         </table>
    * 
    * if user typed not same passwords
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>passwords is not same</td></tr>
    *         </table>
    * 
     * if password validation fail
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>password</td><td>string</td><td>The password format is invalid.</td></tr>
    *         </table>
     * 
    * if user can not authentificate after password setting
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>password set fail</td></tr>
    *         </table>
    * </pre>
    *
    * @return string JSON  success or error message
    */
    
    public function postActivateHired() {
        $input = \Input::json()->all();
        if (empty($input['user']['token'])) {
            return \Response::json(['message' => ['error' => "user_token is empty"]], 400);
        }

        $userToken = \UserToken::where('token', '=', $input['user']['token'])->where('token_type','=','activation')->first();

        if (empty($userToken->id)) {
            return \Response::json(['message' => ['error' => "user_token not found"]], 400);
        }
        
        if ($input['user']['password']!=$input['user']['password1']) {
            return \Response::json(['message' => ['error' => "passwords is not same"]], 400);
        }
        
        $validatorPassword = \Validator::make(
            array('password' => $input['user']['password']),
//            array('password' => array('required', 'regex:((?=.*\d)(?=.*[a-z]).{6,20})'))
            array('password' => array('required'))
            );

        if ($validatorPassword->fails()) {
            return \Response::json(['message' => $validatorPassword->messages()], 400);
        }

        $user = \User::where('id', '=', $userToken->user_id)->first();
        $userOpenPassword = $input['user']['password'];
        $user->password = \Hash::make($userOpenPassword);
        $user->save();
        $userToken->delete();
        
        if (\Auth::attempt(array('username'=>$user->username, 'password'=>$userOpenPassword))) {
            return \Response::json(['message' => ['success' => 'password set successfully']], 201);	
        } else {
          return \Response::json(['message' => ['error' => "password set fail "]], 400);	
      }
  }


    /**
    * Verify user token
    * 
    * <b>POST /employees-action/token-verify</b>
    *
    * <h3>Request Information:</h3>
    *
    * <table class="apidescription" >
    * <tr><td>Request format</td><td>JSON</td></tr>	
    * <tr><td>Requires authentication?</td><td>No</td></tr>	
    * </table><br />
    * 
    * <b>URI parameters:</b>
    * No
    * 
    * 
    * <b>Body parameters:</b>
    *
    * <pre>
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>token</td><td>string</td><td>string with token value</td></tr>
    *     </table>
    * </pre>
    * 
    * <h3>Response Information:</h3>
    *
    * <b>Response:</b>
    *
    * <b>Success:</b>
    * HTTP Status 201
    *
    * <b>Body:</b>
    * The single user object of following structure
    * <pre>
    *     <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of user</td></tr>
    *         <tr><td>username</td><td>string</td><td>Username</td></tr>
    *         <tr><td>status</td><td>string</td><td>User status</td></tr>
    *         <tr><td>last_login_attempt</td><td>datetime</td><td>Last attempt of login</td></tr>
    *         <tr><td>failed_logins</td><td>integer</td><td>Cuunt of faild logins</td></tr>
    *         <tr><td>created_at</td><td>datetime</td><td>When user created</td></tr>
    *         <tr><td>updated_at</td><td>datetime</td><td>When user updated</td></tr>
    *         <tr><td>tenant_id</td><td>integer</td><td>id of tenant</td></tr>
    *         <tr><td>uuid</td><td>string</td><td>UUID</td></tr>
    *         <tr><td>profile_id</td><td>integer</td><td>id of profile</td></tr>
    *     </table>
    * </pre>
    *
    * <b>Errors:</b>
    * 
    * HTTP Status 404
    *
    * <b>Body:</b>
    * 
    * The message object of following structure
    * <pre>
    * if token parametr is empty
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>user_token required</td></tr>
    *         </table>
    * 
    * if token not found in token table
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Value</th></tr>
    *         <tr><td>error</td><td>string</td><td>user_token not found</td></tr>
    *         </table>
    * </pre>
    *
    * @return string JSON  user object or error
    */
    
    public function postTokenVerify() {
        $input = \Input::json()->all();
        if (empty($input['token'])) {
            return \Response::json(['message' => ['error' => "user_token required"]], 404);
        }
        $userToken = \UserToken::where('token', $input['token'])->first();
        
        if (empty($userToken->id)) {
            return \Response::json(['message' => ['error' => "user_token not found"]], 404);
        }
        $user = \User::where('id', '=', $userToken->user_id)->first();
        return \Response::json($user, 201);
    }
    
    /**
    * This function return data for employee of current user
    * 
    * 
    * <b>POST /api/v1/employees-action/current-employee-data</b>
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
    * HTTP Status 201
    * 
    *  <pre> 
    * Three objects of following structure
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>employee</td><td>object</td><td>
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of employee</td></tr>
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
    *         </table></td></tr>
    *     <tr><td>address</td><td>object</td><td>Object of address
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of address</td></tr>
    *         <tr><td>street1</td><td>string</td><td>Primary street</td></tr>
    *         <tr><td>street2</td><td>NULL</td><td>secondary street</td></tr>
    *         <tr><td>city</td><td>string</td><td>City</td></tr>
    *         <tr><td>state</td><td>string</td><td>State</td></tr>
    *         <tr><td>postal</td><td>string</td><td>Postal</td></tr>
    *         <tr><td>country</td><td>string</td><td>Country</td></tr>
    *         <tr><td>address_type</td><td>integer</td><td> </td></tr>
    *         <tr><td>status</td><td>integer</td><td> </td></tr>
    *         <tr><td>lat</td><td>integer</td><td> </td></tr>
    *         <tr><td>lon</td><td>integer</td><td> </td></tr>
    *         </table></td></tr>
    *     </table>
    * </pre> 
    * 
    * @return string JSON  profile, address and employee
    */
    
    public function postCurrentEmployeeData() {
     $currentUser = \Auth::user();
     $profile = $currentUser->profile()->first();
     $image = $profile->image()->first();
     if (!empty($image)) { 
        $profile->imglink =  $image->imageLink();
        $profile->imglinkthumb = $image->imageLinkThumb(\ImageModel::$thumbWidth,\ImageModel::$thumbHeight);
    }
    $employee =  $profile->employee()->first();
    $address = $profile->address()->first();
    return \Response::json(['employee' => $employee, 'profile' => $profile, 'address' =>  $address], 201);
}

     /**
    * Create new user profile for new employee snd save employee information
    * 
    * <b>POST /api/v1/employees-action/set-employee-profile</b>
    *
    * <h3>Request Information:</h3>
    *
    * <table class="apidescription" >
    * <tr><td>Request format</td><td>POST</td></tr>	
    * <tr><td>Requires authentication?</td><td>Yes</td></tr>	
    * </table><br />
    * 
    * <b>URI parameters:</b>
    * 
    * No
    * 
    * <b>Body parameters:</b>
    * 
    * <pre>
    *     File - image for profile
    * <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>file</td><td>file</td><td>File image in format PNG,GIF,JPG,JEPG</td></tr>
    *     </table>
      * 
      *  Signature - Signature image file for profile
    * <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>signature_file</td><td>file</td><td>File image in format PNG,GIF,JPG,JEPG</td></tr>
    *     </table>
    * 
    *     Profile - array with profile data
    * <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>id</td><td>integer</td><td>id of profile</td></tr>
    *     <tr><td>first_name</td><td>string</td><td>First name</td></tr>
    *     <tr><td>last_name</td><td>string</td><td>Last name </td></tr>
    *     <tr><td>email</td><td>string</td><td>E-mail</td></tr>
    *     <tr><td>address_id</td><td>integer</td><td>id of address of profile</td></tr>
    *     <tr><td>date_of_birth</td><td>date</td><td>Date of birth</td></tr>
    *     <tr><td>gender</td><td>ENUM</td><td>Gender</td></tr>
      *   <tr><td>signature</td><td>mediumtext</td><td>Signature track in JSON format</td></tr>
      * 
    *     </table>
    * 
    * 
    *     Address - array with address data
    * <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of address</td></tr>
    *         <tr><td>street1</td><td>string</td><td>Primary street</td></tr>
    *         <tr><td>street2</td><td>NULL</td><td>secondary street</td></tr>
    *         <tr><td>city</td><td>string</td><td>City</td></tr>
    *         <tr><td>state</td><td>string</td><td>State</td></tr>
    *         <tr><td>postal</td><td>string</td><td>Postal</td></tr>
    *         <tr><td>country</td><td>string</td><td>Country</td></tr>
    *         </table>
    * 
    *      Employee - array with employee data
    * <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>id</td><td>integer</td><td>id of employee</td></tr>
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
    *         </table>
    * </pre>
    *
    * <h3>Response Information:</h3>
    *
    * <b>Response:</b>
    *
    * <b>Success:</b>
    * HTTP Status 201
    * 
    * <table class="apidescription" >
    * <tr><td>Response format</td><td>JSON</td></tr>	
    * </table><br />
    *  
    * <b>Body:</b>
    * The single profile object of following structure
    * <pre>
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>id</td><td>integer</td><td>id of profile</td></tr>
    *     <tr><td>first_name</td><td>string</td><td>First name</td></tr>
    *     <tr><td>last_name</td><td>string</td><td>Last name </td></tr>
    *     <tr><td>email</td><td>string</td><td>E-mail</td></tr>
    *     <tr><td>address_id</td><td>integer</td><td>id of address of profile</td></tr>
    *     <tr><td>date_of_birth</td><td>date</td><td>Date of birth</td></tr>
    *     <tr><td>gender</td><td>ENUM</td><td>Gender</td></tr>
    *     <tr><td>image_id</td><td>integer</td><td>id of profile image</td></tr>
    *     </table>
    * </pre>
    *
    * <b>Errors:</b>
    * 
    * HTTP Status 400
    *
    * <b>Body:</b>
    * The message object of following structure (validation of address and profile)
    * <pre>
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>error type (which field are wrong ex. "postal", "street" )</td><td>string array</td><td>Text which told what is wrong</td></tr>
    *         </table>
    * </pre>
    *
    * @return string JSON  of Profile or error
    */

     public function postSetEmployeeProfile() {
        $input = \Input::all();
        $currentUser = \Auth::user();
        $profile = $currentUser->profile()->first();

        $employee = \Employee::firstOrNew(array('profile_id' => $profile->id)); 
        $address = \Address::firstOrNew(array('id' => $profile->address_id));

        $addressValidation = \Validator::make($input['address'],\Address::getAddValidation());
        if ($addressValidation->fails() ) {
            return \Response::json(['message' => $addressValidation->messages()], 400);	
        }
        $address->fill($input['address']);
        $address->save();

        $profileValidation = \Validator::make($input['profile'],\UserProfile::getAddValidation());
        if ($profileValidation->fails() ) {
            return \Response::json(['message' => $profileValidation->messages()], 400);	
        }
        
        $image = new \ImageModel;
        if (\Input::file('file')) {
            if ($profile->image_id) {
                $image = $image->updateImage($profile->image_id, \Input::file('file'));
            } else {
                $image = $image->createNewImage(\Input::file('file'),'/images/userpic/');
            }
            $profile->image_id = $image->id;            
        }

        $profile->fill($input['profile']);
        $profile->address_id = $address->id;
        $profile->save();

        $employee->fill($input['employee']);
        $employee->profile_id = $profile->id;
        $employee->save();

        return \Response::json(['profile' => $profile], 201);
    }

// Not finished, not sure if this is needed.
    public function postSaveBasicInfo() {
        $currentUser = \Auth::user();
        $input = \Input::all();

        $profile = \Profile::where('id','=',$currentUser->profile_id);
        if ($profile == null) throw new Exception ("No profile available for user");

        $profile->fill($input['profile']);

        return \Response::json(['profile'=>$profile], 200);

    }

    public function getSecureInfo() {
        $currentUser = \Auth::user();
        $secureInfo = \SecureInfo::firstOrNew(array('employee_id'=>$currentUser->profile()->first()->employee()->first()->id));
        $secureInfo->employee_id = $currentUser->profile()->first()->employee()->first()->id;
        return \Response::json($secureInfo, 200);
    }

    public function postSecureInfo() {
        $employee_id = \Auth::user()->profile()->first()->employee()->first()->id;
        $secureInfo = \SecureInfo::firstOrNew(array('employee_id'=>$employee_id));
        $secureInfo->employee_id = $employee_id;

        $secureInfo->fill(\Input::json()->all()['data']);
        $secureInfo->save();

        \Onboarding::setValueForEmployee($employee_id, 'bank_info', true);

        return \Response::json(['secureInfo'=>$secureInfo]);
    }

}