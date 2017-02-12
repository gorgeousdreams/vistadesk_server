<?php

namespace API;

class ResourceAssignmentController extends \API\APIController {


	public function beforeView(&$obj) {
        $this->fillAdditionalFields($obj);
		return $obj;
	}

    private function fillAdditionalFields($assignment) {
        $assignment->employee->profile;
        $assignment->company;
        $assignment->project;
    }

    public function getIndex($id = null) {
		if (empty($id)) { 
            $currentUser = \Auth::user();
            // Time period begins today
            $periodBegin = date('Y-m-d');
            // Time period ends one year from today
            $periodEnd = date('Y-m-d', strtotime($periodBegin." +1 year"));
            $assignments = \ResourceAssignment::where('allocation', '>', 0)->where(function($query) use ($periodBegin, $periodEnd) {
            	$query->where('start_date', '<=', $periodEnd)->orWhere('end_date', '>=', $periodBegin);
            })->orderBy('employee_id')->get();
            foreach ($assignments as $assignment) {
            	// Include some relationships in the response json
                $this->fillAdditionalFields($assignment);
            }
            return \Response::json(['data' => $assignments], 201);
        } else {
            $assignment = parent::getIndex($id);
            $this->fillAdditionalFields($assignment);
            self::setAdditionalFields($assignment);
            return \Response::json($assignment, 201);
        }
    }

    public function getEmployee($employee_id) {
        if (!empty($employee_id)) {
            // Time period begins today
            $periodBegin = date('Y-m-d');
            // Time period ends one year from today
            $periodEnd = date('Y-m-d', strtotime($periodBegin." +1 year"));
            $assignments = \ResourceAssignment::where('allocation', '>', 0)->where(function($query) use ($periodBegin, $periodEnd) {
                $query->where('start_date', '<=', $periodEnd)->orWhere('end_date', '>=', $periodBegin);
            })->where('employee_id', $employee_id)->get();
            foreach ($assignments as $assignment) {
                // Include some relationships in the response json
                $this->fillAdditionalFields($assignment);
            }
            return \Response::json(['data' => $assignments], 201);
        } else {
            return \Response::json(['message' => ['error' => ["Allocations for employee not found" ]]], 404);
        }
    }

    public function getProject($project_id) {
        if (!empty($project_id)) {
            // Time period begins today
            $periodBegin = date('Y-m-d');
            // Time period ends one year from today
            $periodEnd = date('Y-m-d', strtotime($periodBegin." +1 year"));
            $assignments = \ResourceAssignment::where('allocation', '>', 0)->where(function($query) use ($periodBegin, $periodEnd) {
                $query->where('start_date', '<=', $periodEnd)->orWhere('end_date', '>=', $periodBegin);
            })->where('project_id', $project_id)->get();
            foreach ($assignments as $assignment) {
                // Include some relationships in the response json
                $this->fillAdditionalFields($assignment);
            }
            return \Response::json(['data' => $assignments], 201);
        } else {
            return \Response::json(['message' => ['error' => ["Allocations for project not found" ]]], 404);
        }
    }

    public function update($id = 0) {
        $validation = $this->validation();
        if ($validation) {
            return \Response::json($validation, 400);
        }
        parent::update($id);
    }

    public function store($id = 0) {
        $validation = $this->validation();
        if ($validation) {
            return \Response::json($validation, 400);
        }
        parent::store($id);
    }

    private function validation() {
        $input = \Input::json()->all();
        $dataValidation = \Validator::make($input,\ResourceAssignment::validation());
        if ($dataValidation->fails() ) {
            return ['message' => $dataValidation->messages()];
        }
        $checkResourceAllocation =  $this->checkResourceAllocation($input);
        if ($checkResourceAllocation["flag"]) {
            return $checkResourceAllocation;
        }
        return false;
    }

    private $maxPersent = 100;

    /**
     * Check if resource allocation of employee greater than 100% per projec and in all cases
     *
     * @param array $input all input information about new or updated resource allocation
     * @return string JSON  with parameters:
     * "flag" - true if allocation excess,
     * "allocationWarning" - true if have possibility to continue save allocation, but needing approve on frontend
     */
    private function checkResourceAllocation ($input) {
        $companyEmployee = \ResourceAssignment::where("employee_id", $input["employee_id"])
            ->where("company_id", $input["company_id"])
            ->where("id", "!=", $input["id"])
            ->where(function ($query) use ($input) {
                $query->where("start_date", "<=", $input["end_date"])->where("start_date", ">=", $input["start_date"])
                    ->orWhere("end_date", ">=", $input["start_date"])->where("end_date", "<=", $input["end_date"])
                    ->orWhere("end_date", ">=", $input["end_date"])->where("start_date", "<=", $input["start_date"]);
            });
        $companyEmployeeAllocation = $this->maxAllocationCalculate($companyEmployee, $input, true);
        if ($companyEmployeeAllocation > $this->maxPersent) {
            return [
                'message' => ['allocation' => ["Allocation per company can not be greater than 100% (now ".$companyEmployeeAllocation."%)" ]],
                "flag" => true,
                "allocationWarning" => false
            ];
        }

        $fullEmployee = \ResourceAssignment::where("employee_id", $input["employee_id"])
            ->where("id", "!=", $input["id"])
            ->where(function ($query) use ($input) {
                $query->where("start_date", "<=", $input["end_date"])->where("start_date", ">=", $input["start_date"])
                    ->orWhere("end_date", ">=", $input["start_date"])->where("end_date", "<=", $input["end_date"])
                    ->orWhere("end_date", ">=", $input["end_date"])->where("start_date", "<=", $input["start_date"]);
            });
        $fullEmployeeAllocation = $this->maxAllocationCalculate($fullEmployee, $input, false);
        if (($fullEmployeeAllocation > $this->maxPersent) && (empty($input["allocationWarning"]) || !$input["allocationWarning"])) {
            return [
                'message' => ['allocation' => ["Full allocation for employee greater than 100% (now ".$fullEmployeeAllocation."%). Please approve this."]],
                "flag" => true,
                "allocationWarning" => true
            ];
        }

        return ["flag" => false];
    }

    /**
     * Calculate max allocation for time period from $input["start_date"] to $input["end_date"]
     * @param object $employeeQuery
     * @param array $input all input information about new or updated resource allocation
     * @param boolean $isCompany
     * @return int maximal allocation number
     */
    private function maxAllocationCalculate($employeeQuery, $input, $isCompany) {
        $resultAllocation = (int)$employeeQuery->sum('allocation')+(int)$input["allocation"];
        if ($resultAllocation > $this->maxPersent) {
            $employeeArray = $employeeQuery->get();
            $peakDateArray = array();
            foreach ($employeeArray as $employeeItem) {
                if ($employeeItem->start_date > $input["start_date"]) {
                    $peakDateArray[] = $employeeItem->start_date;
                }
                if ($employeeItem->end_date < $input["end_date"]) {
                    $peakDateArray[] = $employeeItem->end_date;
                }
            }
            $peakDateArray = array_unique($peakDateArray);
            $allocationArray = array();
            foreach ($peakDateArray as $peakDateItem) {
                if ($isCompany) {
                    $allocationArray[] = (int) \ResourceAssignment::where("employee_id", $input["employee_id"])
                        ->where("company_id", $input["company_id"])
                        ->where("id", "!=", $input["id"])
                        ->where("start_date", "<=", $peakDateItem)
                        ->where("end_date", ">=", $peakDateItem)
                        ->sum('allocation');
                } else {
                    $allocationArray[] = (int) \ResourceAssignment::where("employee_id", $input["employee_id"])
                        ->where("id", "!=", $input["id"])
                        ->where("start_date", "<=", $peakDateItem)
                        ->where("end_date", ">=", $peakDateItem)
                        ->sum('allocation');
                }
            }
            if (count($allocationArray) > 0) {
                $resultAllocation = max($allocationArray) + (int)$input["allocation"];
            }
        }
        return $resultAllocation;
    }

}



