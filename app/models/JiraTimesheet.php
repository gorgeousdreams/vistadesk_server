<?php 
class JiraTimesheet extends Timesheet {

	protected $table = 'timesheets';

	public static function createOpenTimesheet($employee, $date) {

		// Create the default empty timesheet
		$timesheet = Timesheet::createProposedTimesheet($employee, $date);

        // Get all worklogs from Jira for the timesheet period
		$worklogs = Jira::getWorklogsForEmployee($employee->external_id, $timesheet->start_date);

    	foreach ($worklogs as $log) {
    		$project = Project::where('external_id','=',$log->ProjectID)->firstOrFail();

    		$entry = new TimesheetEntry(array(
    			'hours' => floatval($log->hours),
    			'day' => $log->datestamp,
    			'rate' => Employee::getEmployeeRateForClient($employee->id, $project->company_id)[0]->Rate,
    			'project_id' => $project->id
    			));
    		$timesheet->timesheetEntries->add($entry);
      }
      return $timesheet;
	}



	public static function addToOpenTimesheet($timesheet, $employee, $date) {

        // Get all worklogs from Jira for the timesheet period
		$worklogs = Jira::getWorklogsForEmployee($employee->external_id, $timesheet->start_date);

    	foreach ($worklogs as $log) {
    		$project = Project::where('external_id','=',$log->ProjectID)->firstOrFail();
		if ($project->id != 43) continue;

    		$entry = new TimesheetEntry(array(
    			'hours' => floatval($log->hours),
    			'day' => $log->datestamp,
    			'rate' => Employee::getEmployeeRateForClient($employee->id, $project->company_id)[0]->Rate,
    			'project_id' => $project->id
    			));
    		$timesheet->timesheetEntries->add($entry);
      }
      return $timesheet;
	}



}