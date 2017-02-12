<?php

namespace API;

class ProfileController extends \API\APIController {
    
    
      /**
     * Get list of user's profiles
     * 
     * <b>GET /api/v1/user-profile</b>
     *
     * <h3>Request Information:</h3>
     *
     * <table class="apidescription" >
     * <tr><td>Request format</td><td>JSON</td></tr>	
     * <tr><td>Requires authentication?</td><td>Yes</td></tr>	
     * </table><br />
     * 
     * <b>URI parameters:</b>
     * No
     * 
     * <h3>Response Information:</h3>
     *
     * <b>Response:</b>
     *
     * <b>Success:</b>
     * HTTP Status 201
     *
     * The list of objects of the following type:
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
    *     <table class="apidescription" >
       *  <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>id</td><td>integer</td><td>id of profile</td></tr>
    *     <tr><td>first_name</td><td>string</td><td>First name</td></tr>
    *     <tr><td>last_name</td><td>string</td><td>Last name </td></tr>
    *     <tr><td>email</td><td>string</td><td>E-mail</td></tr>
    *     <tr><td>address_id</td><td>integer</td><td>id of address of profile</td></tr>
    *     <tr><td>date_of_birth</td><td>date</td><td>Date of birth</td></tr>
    *     <tr><td>gender</td><td>ENUM</td><td>Gender</td></tr>
    *     <tr><td>image_id</td><td>integer</td><td>id of profile image</td></tr>
    *     <tr><td>user</td><td>object</td><td>Object of profile user
    *         <table class="apidescription" >
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
    *     <tr><td>imglink</td><td>string</td><td>Link on profile image </td></tr>
    *     <tr><td>imglinkthumb</td><td>string</td><td>Link on profile image thumbnail</td></tr>
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
    *         <tr><td>job_title</td><td>string</td><td> </td></tr>
    *         <tr><td>location</td><td>string</td><td> </td></tr>
    *         <tr><td>hire_date</td><td>date</td><td> </td></tr>
    *         <tr><td>personal_email</td><td>string</td><td> </td></tr>
    *         <tr><td>status</td><td>string</td><td> </td></tr>
    *         <tr><td>profile_id</td><td>integer</td><td> </td></tr>
    *         </table></td></tr>
    *     <tr><td>manager_first_name</td><td>string</td><td>First name of manager</td></tr>
    *     <tr><td>manager_last_name</td><td>string</td><td>Last name of manager</td></tr>
    *     </table>
     *</pre> 
     * 
     * <b>Errors:</b>
     * 
     * Can not be any errors
     * 
     * @param integer $id can be null or 0 to add new profile
     * @return string JSON  objects of Profile
     */
    
    public function getIndex($id = null) {
	if (empty($id)) { 
            //$profiles = parent::getIndex($id);
            $currentUser = \Auth::user();
            if ($currentUser->hasRole('Root')) {
                $profiles = \Profile::all();
            } else {
                $profiles = \Profile::whereIn('id',\User::where('tenant_id',$currentUser->tenant_id)->lists('profile_id'))->get();
            }
            foreach ($profiles  as $key => $profile) {
                
                $user = $profile->user()->first();
                $employee = $profile->employee()->first();
                $profiles[$key]->user = $user;
                $profiles[$key]->address = $profile->address()->first();
                $image = $profile->image()->first();
                if (!empty($image)) { 
                    $profiles[$key]->imglink =  $image->imageLink();
                    $profiles[$key]->imglinkthumb = $image->imageLinkThumb(\ImageModel::$thumbWidth,\ImageModel::$thumbHeight);
                }
                if (!empty($employee)) { 
                    $profiles[$key]->employee = $employee;
                    $manager = $employee->manager()->first();
                    if (!empty($manager)) { 
                        $profiles[$key]->manager_first_name = $manager->profile()->first()->first_name;
                        $profiles[$key]->manager_last_name = $manager->profile()->first()->last_name;
                    }
                }
            }
            return \Response::json(['data' => $profiles], 201);
        } else {
            $profile = parent:: getIndex($id);
            self::setAdditionalFields($profile);
            return \Response::json($profile, 201);
        }
    }
    
    
   /**
     * Get single user profile by id
     * 
     * <b>GET /api/v1/user-profile/{id}</b>
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
     * <tr><td>id</td><td>Integer</td><td>profile id</td></tr>
     * </table><br />
     * 
     * <b>Body parameters:</b>
     *
     * <h3>Response Information:</h3>
     *
     * <b>Response:</b>
     *
     * <b>Success:</b>
     * HTTP Status 201
     *
     * <b>Body:</b>
     * The single object of following structure
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
    *     <tr><td>user</td><td>object</td><td>Object of profile user
    *         <table class="apidescription" >
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
    *     <tr><td>imglink</td><td>string</td><td>Link on profile image </td></tr>
    *     <tr><td>imglinkthumb</td><td>string</td><td>Link on profile image thumbnail</td></tr>
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
    *         <tr><td>job_title</td><td>string</td><td> </td></tr>
    *         <tr><td>location</td><td>string</td><td> </td></tr>
    *         <tr><td>hire_date</td><td>date</td><td> </td></tr>
    *         <tr><td>personal_email</td><td>string</td><td> </td></tr>
    *         <tr><td>status</td><td>string</td><td> </td></tr>
    *         <tr><td>profile_id</td><td>integer</td><td> </td></tr>
    *         </table></td></tr>
    *     <tr><td>manager_first_name</td><td>string</td><td>First name of manager</td></tr>
    *     <tr><td>manager_last_name</td><td>string</td><td>Last name of manager</td></tr>
    *     </table>
    * </pre>
     *
     * <b>Errors:</b>
     * 
     * HTTP Status 401
     *
     * <b>Body:</b>
     * <table class="apidescription" >
     * <tr><td>Message</td><td>profile not found</td></tr>
     * </table><br />
     *
     * @param integer $id of the profile for which should be displayed
     * @return string JSON  of Profile or error
     */
    
    public function getView($id = null) {
	if (empty($id)) { 
            return parent::getView($id); 
        } else {
            $profile = parent::getView($id);
            if (!empty($profile->id)) {            
                self::setAdditionalFields($profile);
                return \Response::json($profile, 201);
            } else {
                return \Response::json(['message' => "profile not found"], 401);
            }
        }
    }
    
    private function setAdditionalFields($profile) {
        $user = $profile->user()->first();
        $profile->user = $user;
        $profile->address = $profile->address()->first();
        $image = $profile->image()->first();
        if (!empty($image)) { 
            $profile->imglink =  $image->imageLink();
            $profile->imglinkthumb = $image->imageLinkThumb(\ImageModel::$thumbWidth,\ImageModel::$thumbHeight);
        }
        $employee = $profile->employee()->first();
        if (!empty($employee)) { 
            $profile->employee = $employee;
            $manager = $employee->manager()->first();
            if (!empty($manager)) { 
                $profile->manager_first_name = $manager->profile()->first()->first_name;
                $profile->manager_last_name = $manager->profile()->first()->last_name;
            }
        }
    }


    public function getCurrentProfile() {
        $currentUser = \Auth::user();
        return \Response::json($currentUser->Profile()->first(), 201);
    }
    
    public function postSave($id = null) {
	$currentUser = \Authority::getCurrentUser();
        if ($currentUser->hasRole('User') && $currentUser->Profile->id==$id){ 
            \Authority::allow('update', 'Profile');
        }
        return parent::postSave($id);
    }
	
}



