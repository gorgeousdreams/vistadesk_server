<?php

namespace API;

// REST services for employees
class EmployeeActivityController extends \API\APIController {

    /**
     * Return activity for employee
     *
     * @param int $employeeId
     * @return string JSON  activity for employee
     */
    public function getActivity($employeeId = 0) {
        $currentUser = \Authority::getCurrentUser();
        if ($currentUser->hasRole('User') && !($currentUser->hasRole('Root')) && !($currentUser->hasRole('Admin'))) {
            if ( $currentUser->profile->employee->id == $employeeId){
                \Authority::allow('update', 'EmployeeActivity');
            } else {
                return \Response::json(['message' => ['error' => 'User don`t have  permission to view another user activity']], 400);
            }
        }
//        if (!$employeeId) {
//            return \Response::json(['message' => ['error' => 'Employee Id is emppty']], 404);
//        }
        if ($employeeId == 0 ) {
            $employeeActivity = \EmployeeActivity::orderBy('created_at', 'DESC');
        } else {
            $employeeActivity = \EmployeeActivity::where("employee_id","=", $employeeId)->orderBy('created_at', 'DESC')->get();
        }

        foreach ($employeeActivity as $key => $employeeActivityItem) {
            $employeeActivity[$key]->employee;
            $employeeActivity[$key]->employee->user;
            $employeeActivity[$key]->employee->profile;
            $employeeActivity[$key]->actionUser;
            if(!empty($employeeActivity[$key]->actionUser)) {
                $employeeActivity[$key]->actionUser->profile;
            }
        }
        return \Response::json($employeeActivity, 200);
    }
    

}




