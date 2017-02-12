<?php

namespace API;

// REST services for tenants
class PayrollController extends \BaseController {

	public function getReport($startDate = null, $endDate = null) {

		if ($startDate == null || $endDate == null) {
			$startDate = date('Y-m-d', strtotime('last sunday - 2 weeks', time()));
			$endDate = date('Y-m-d', strtotime('last sunday'));
		}

		$endDatePlusOne = date('Y-m-d', strtotime($endDate." +1 day"));

		$rows = \DB::select(\DB::raw("
			SELECT 
			sum(TimesheetEntry.hours) as hours,
			sum(TimesheetEntry.hours * TimesheetEntry.rate) as billed,
			Employee.comp_amount,
			Employee.id as employee_id,
			Profile.first_name, Profile.last_name
			FROM
			timesheet_entries TimesheetEntry
			INNER JOIN timesheets Timesheet on Timesheet.id = TimesheetEntry.timesheet_id
			INNER JOIN employees Employee on Employee.id = Timesheet.employee_id
			INNER JOIN profiles Profile on Profile.id = Employee.profile_id
			WHERE TimesheetEntry.day >= :startDate AND TimesheetEntry.day < :endDate
			GROUP BY Employee.id
			ORDER BY Profile.last_name, Profile.first_name
			"), array("startDate"=>$startDate, "endDate"=>$endDatePlusOne));
		$result = array();
		foreach($rows as $row) {
			$entry = new \stdclass;
			$entry->employee = \Employee::findOrFail($row->employee_id);
			$entry->employee->profile;
			if ($entry->employee->comp_type == '') $entry->employee->comp_type = 'Hourly';
			$entry->payroll = new \stdclass;
			$entry->payroll->hours = $row->hours;
			if (\Auth::user()->hasRole('Admin')) {
			   $entry->payroll->billed = $row->billed;
			} else {
			  $entry->payroll->billed = 0;
			  }
			if ($entry->employee->comp_type == "Annual") {
				$entry->payroll->total = $entry->employee->comp_amount / 26;
			} else {
				$entry->payroll->total = $row->hours * $entry->employee->comp_amount;
			}
			$result[] = $entry;
		}

		return \Response::json($result);
	}
	
}


