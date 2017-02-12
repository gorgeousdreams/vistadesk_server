<?php
class Worklog extends AppModel
{

	// Gets a week's worth of worklogs and inserts them
	public static function copyWorklogs($employee, $sqlDate) {
		$worklogs = Jira::getFullWorklogsForEmployee($employee->external_id, $sqlDate);
		foreach ($worklogs as $worklog) {
			$ins = new Worklog();
			$project = Project::where('external_id','=',$worklog->ProjectID)->first();
			$ins->date_worked = $worklog->datestamp;
			$ins->employee_id = $employee->id;
			$ins->hours = $worklog->hours;
			$ins->description = $worklog->worklogbody;
			$ins->external_id = $worklog->pkey;
			$ins->task = $worklog->summary;
			$ins->project_id = $project->id;
			$ins->save();
		}
	}

	// Gets a week's worth of worklogs and inserts them
	public static function copyWorklogsForProject($employee, $sqlDate, $projectId) {
		$worklogs = Jira::getFullWorklogsForEmployee($employee->external_id, $sqlDate);
		foreach ($worklogs as $worklog) {
			$ins = new Worklog();
			$project = Project::where('external_id','=',$worklog->ProjectID)->first();
			if ($project->id != $projectId) continue;
			$ins->date_worked = $worklog->datestamp;
			$ins->employee_id = $employee->id;
			$ins->hours = $worklog->hours;
			$ins->description = $worklog->worklogbody;
			$ins->external_id = $worklog->pkey;
			$ins->task = $worklog->summary;
			$ins->project_id = $project->id;
			$ins->save();
		}
	}

	public function project() {
		return $this->belongsTo('Project');
	}

	public function employee() {
		return $this->belongsTo('Employee');
	}

}