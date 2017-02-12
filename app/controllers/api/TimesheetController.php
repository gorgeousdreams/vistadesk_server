<?php

namespace API;

class TimesheetController extends \API\APIController {

	public function getByEmployee($id) {
		return $this->getTimesheets($id);
	}

	public function index() {
		return $this->getTimesheets(null);		// Gets timesheets for all employees that the currently logged in user can view.
	}

	public function getApproveTimesheet($id) {
		$timesheet = Timesheet::find($id);

		if ($timesheet == null) {
			throw new Exception("Could not find timesheet ".$id);
		}

		if (($timesheet->manager_id != \Auth::user()->id 
			&& !\Auth::user()->hasRole("admin"))
			|| !\Auth::user()->can("approve timesheets")) {

			throw new Exception("Unable to approve timesheet.");

		}

		$timesheet->status = "Approved";
		$timesheet->save();
		return $timesheet;
	}

	private function getTimesheets($employeeId = null) {
		$params = array();
		if (!\Auth::user()->hasRole('Admin') || $employeeId == null) {
			$params["me"]= \Auth::user()->id;
			$params["meToo"] = \Auth::user()->id;
		}
		if ($employeeId != null) {
			$params["employeeId"] = $employeeId;
		}

		$query = "SELECT timesheets.*, DATE_FORMAT(timesheets.start_date, '%Y-%m-%d') as periodStart, employee.uuid as employeeId, date_format(start_date, '%m/%d/%Y') as start_date, date_format(end_date, '%m/%d/%Y') as end_date, 
		employeeProfile.first_name, employeeProfile.last_name,
		(select ifnull(sum(hours),0) from timesheet_entries where timesheet_id = timesheets.id) as hours 
		FROM timesheets
		INNER JOIN employees employee on employee.id = timesheets.employee_id
		INNER JOIN profiles employeeProfile on employeeProfile.id = employee.profile_id
		LEFT JOIN users employeeUser on employeeUser.profile_id = employeeProfile.id 
		LEFT JOIN employees manager on manager.id = employee.manager_id
		LEFT JOIN profiles managerProfile on managerProfile.id = manager.profile_id
		LEFT JOIN users managerUser on managerUser.profile_id = managerProfile.id
		WHERE 1=1 ".
			// If the user is an admin, they can view anyone's timesheets
		(\Auth::user()->hasRole('Admin') && $employeeId != null ? "" : "AND (employeeUser.id = :me OR managerUser.id = :meToo) ") .
			// Optionally limit by employeeId, if one was provided as a function parameter
		($employeeId == null ? "" : " AND employee.id = :employeeId ") . 
		"ORDER BY employeeProfile.last_name, employeeProfile.first_name, timesheets.start_date DESC";

		return \DB::select(\DB::raw($query), $params);
	}

	public function beforeView(&$timesheet) {
		$timesheet->timesheetEntries;
		$timesheet->employee;
		foreach ($timesheet->timesheetEntries as $entry) {
			$entry->project;
		}
		return $timesheet;
	}

	public function getTimesheet($uuid, $date) {
		$employee = \Employee::where('uuid','=',$uuid)->firstOrFail();

		$today = time();

		$date = $date ?: strtotime(" -1 week", time());

		$date = \Timesheet::findPreviousSunday($date);

		$timesheet = \Timesheet::where('start_date','=',date('Y-m-d',$date))->where('employee_id','=',$employee->id)->first();
		if ($timesheet == null) {
			$timesheet = $this->createProposedTimesheet($employee, $date);
			if ($today > strtotime($timesheet->start_date . " +9 days")) {
				$timesheet->status = "Closed";
			}
		}
		$timesheet->closeDate = date('Y-m-d', strtotime($timesheet->start_date . " +8 days"));
		foreach ($timesheet->timesheetEntries as $entry) {
			$entry->project;
		}
		$timesheet->employee;
		$timesheet->employee->profile;
		return $timesheet;
	}

	private function createProposedTimesheet($employee, $date) {
        // Get all worklogs from Jira after that date range.
		$sqlDate = date('Y-m-d', $date);
		$worklogs = \Jira::getWorklogsForEmployee($employee->external_id, $sqlDate);
		$timesheet = new \Timesheet(array(
			"start_date" => $sqlDate,
			"end_date" => date('Y-m-d', strtotime($sqlDate . " +6 days")),
			"employee_id" => $employee->id,
			"status" => "Open"));

		foreach ($worklogs as $log) {
			$project = \Project::where('external_id','=',$log->ProjectID)->firstOrFail();
			$entry = new \TimesheetEntry(array(
				'hours' => floatval($log->hours),
				'day' => $log->datestamp,
				'rate' => \Employee::getEmployeeRateForClient($employee->id, $project->company_id)[0]->Rate,
				'project_id' => $project->id
				));
			$timesheet->timesheetEntries->add($entry);
		}
		return $timesheet;
	}


}



