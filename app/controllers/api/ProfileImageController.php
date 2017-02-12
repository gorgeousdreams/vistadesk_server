<?php

namespace API;

class ProfileImageController extends \API\APIController {


   /**
    * Save or update changes for user profile
    *
    * <b>POST /api/v1/profile-image/update-profile</b>
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
    *
    *     File - image for profile
    *     <br />
    *     <table class="apidescription" >
    *     <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *     <tr><td>file</td><td>file</td><td>File image in format PNG,GIF,JPG,JEPG</td></tr>
    *     </table>
    *
    *     Profile - array with profile data
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
    *     <tr><td>imglink</td><td>string</td><td>Link on profile image </td></tr>
    *     <tr><td>imglinkthumb</td><td>string</td><td>Link on profile image thumbnail</td></tr>
    *     <tr><td>manager_first_name</td><td>string</td><td>First name of manager</td></tr>
    *     <tr><td>manager_last_name</td><td>string</td><td>Last name of manager</td></tr>
    *     <tr><td>$resolved</td><td>Boolean</td><td></td></tr>
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
    *         <tr><td>address_type</td><td>integer</td><td> </td></tr>
    *         <tr><td>status</td><td>integer</td><td> </td></tr>
    *         <tr><td>lat</td><td>integer</td><td> </td></tr>
    *         <tr><td>lon</td><td>integer</td><td> </td></tr>
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
    *         <tr><td>job_title</td><td>string</td><td> </td></tr>
    *         <tr><td>location</td><td>string</td><td> </td></tr>
    *         <tr><td>hire_date</td><td>date</td><td> </td></tr>
    *         <tr><td>personal_email</td><td>string</td><td> </td></tr>
    *         <tr><td>status</td><td>string</td><td> </td></tr>
    *         <tr><td>profile_id</td><td>integer</td><td> </td></tr>
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
    * The message object of following structure
    * <pre>
    *         <table class="apidescription" >
    *         <tr><th>Key</th><th>Type</th><th>Description</th></tr>
    *         <tr><td>error type (which field are wrong ex. "postal", "street" )</td><td>string array</td><td>Text which told what is wrong</td></tr>
    *         </table>
    * </pre>
    *
    * @return string JSON  of Profile or error
    */

   public function anyUpdateProfile() {
    $input = \Input::all();
    $currentUser = \Authority::getCurrentUser();
    if ($currentUser->hasRole('User') && !($currentUser->hasRole('Root')) && !($currentUser->hasRole('Admin'))) {
        if ( $currentUser->profile->id==$input['profile']['id']){
            \Authority::allow('update', 'Profile');
        } else {
            return \Response::json(['message' => ['error' => 'User don`t have  permission to update another user']], 400);
        }
    }
    $profile = \Profile::where('id','=',$input['profile']['id'])->first();
    $profile->fill($input['profile']);
    $address = \Address::findOrNew($profile->address_id);
    $address->fill($input['address']);
    $addressValidation = \Validator::make($address->toArray(),\Address::getAddValidation());
    if ($addressValidation->fails()) {
        return \Response::json(['message' => $addressValidation->messages()], 400);
    }
    $image = new \ImageModel;
    if (\Input::file('file')) {
        if ($profile->image_id) {
            $image = $image->updateImage($profile->image_id, \Input::file('file'),'/images/userpic/');
        } else {
            $image = $image->createNewImage(\Input::file('file'),'/images/userpic/');
        }
        $profile->image_id = $image->id;
    }
    $address->save();
    $profile->address_id = $address->id;
    $profile->save();
    if(! empty($profile->employee->id)) {
        \EmployeeActivity::create(array(
            'employee_id' => $profile->employee->id,
            'action_user_id' => $currentUser->id,
            'content' => " updated his(her) profile",
        ));
    }

    //Check validate G4 form
    $employee = $profile->employee()->first();
	if (!empty($employee->id)) {
		\Onboarding::setValueForEmployee($employee->id);
	}

    $employee = $profile->employee()->first();

    return \Response::json(['profile' => $profile, 'employee' => $employee], 201);
}




public function anyBasicInfo() {
    $input = \Input::all();
    $currentUser = \Authority::getCurrentUser();
    if ($currentUser->hasRole('User') && !($currentUser->hasRole('Root')) && !($currentUser->hasRole('Admin'))) {
        if ( $currentUser->profile->id==$input['profile']['id']){
         \Authority::allow('update', 'Profile');
     } else {
      return \Response::json(['message' => ['error' => 'User don`t have  permission to update another user']], 400);
  }
}
$profile = $currentUser->profile()->first();
$profile->fill($input['profile']);
$profileValidation = \Validator::make($profile->toArray(),\Profile::getAddValidation());
if ($profileValidation->fails() ) {
   return \Response::json(['message' => $profileValidation->messages()], 400);
}
$image = new \ImageModel;
if (\Input::file('file')) {
    if ($profile->image_id) {
        $image = $image->updateImage($profile->image_id, \Input::file('file'),'/images/userpic/');
    } else {
        $image = $image->createNewImage(\Input::file('file'),'/images/userpic/');
    }
    $profile->image_id = $image->id;
}
$profile->save();

\Onboarding::setValueForEmployee($profile->employee()->first()->id, 'basic_info', true);

return \Response::json(['profile' => $profile], 201);
}


public function getCurrentContacts() {
    $currentUser = \Authority::getCurrentUser();
    $profile = $currentUser->profile;
    $contacts = $profile->contacts;
    return \Response::json([ "contacts" =>  $contacts], 201);
}

public function postContactsSave() {
    $input = \Input::all();
    $currentUser = \Authority::getCurrentUser();
    $profile = $currentUser->profile;
    $address = \Address::firstOrNew(array('id' => $profile->address_id));
    $addressValidation = \Validator::make($input['address'],\Address::getAddValidation());
    if ($addressValidation->fails() ) {
        return \Response::json(['message' => $addressValidation->messages()], 400);
    }
    $address->fill($input['address']);
    $address->save();
    $profile->address_id = $address->id;
    $profile->save();

    if ($input['contacts']) {
        foreach ($input['contacts'] as $contactItem) {
            if ($contactItem['name'] && $contactItem['phone']) {
                if (!empty($contactItem['id'])) {
                    $contact = \Contact::firstOrNew(array('id' => $contactItem['id']));
                } else {
                    $contact = new \Contact;
                }
                $contact->profile_id = $profile->id;
                $contact->name = $contactItem['name'];
                $contact->phone = $contactItem['phone'];
                $contact->save();
            }
        }
    }

    \Onboarding::setValueForEmployee($profile->employee()->first()->id, 'contact_info', true);

    return \Response::json(['address' => $address, "contacts" =>  $profile->contacts, 'profile' => $profile], 201);
}


}



