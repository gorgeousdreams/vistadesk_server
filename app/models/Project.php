<?php
class Project extends AppModel
{
	public $adminSettings = array(
		'list'=>array(
			'fields' => array('id', 'name', 'company'),
			'actions' => array('add', 'view', 'edit', 'delete')
			),
		'view'=>array(
			'fields' => array('id', 'name', 'company'),
			'actions' => array('edit'),
			'tabs' => array('Budget Requests', 'Company', 'Custom Data')
			),
		'displayField' => 'name'
		);

	public function company() {
		return $this->belongsTo('Company');
	}

	public function budgetRequests() {
		return $this->hasMany('BudgetRequest');
	}
	public function account() {
		return $this->belongsTo('Account');
	}

	static public function findAmountBilled($pkey, $datestamp, $userId) {
		$ret = DB::select(DB::raw("
			SELECT 
			Worklog.external_id,
			Project.name, 
			TimesheetEntry.rate,
			Worklog.hours,
			Worklog.hours * TimesheetEntry.rate as total, 
			concat(Profile.first_name,' ',Profile.last_name), 
			Worklog.description
			FROM
			worklogs Worklog
			INNER JOIN timesheet_entries TimesheetEntry on TimesheetEntry.project_id = Worklog.project_id and TimesheetEntry.day = Worklog.date_worked  
			INNER JOIN timesheets Timesheet on Timesheet.id = TimesheetEntry.timesheet_id
			INNER JOIN employees Employee on Employee.id = Timesheet.employee_id
			INNER JOIN profiles Profile on Profile.id = Employee.profile_id
			INNER JOIN projects Project on Project.id = Worklog.project_id
			WHERE
			Timesheet.employee_id = Worklog.employee_id
			AND Worklog.external_id = :pkey
			AND Employee.external_id = :userId
			AND Worklog.date_worked = :dateWorked
			ORDER BY
			Worklog.date_worked
			"), array('pkey'=>$pkey, 'userId'=>$userId, 'dateWorked'=>$datestamp));
		
		if (sizeof($ret) > 0) return $ret[0];
	}

	static public function getProjectOverageEntries($projectId) {

		$overageHours = \Jira::getOverageHours($projectId);
		$overages = array();
		$overage = null;
		$issue = null;
		$totalReduction = 0;
		foreach ($overageHours as $o) {	    
			if ($issue == null || $issue->pkey != $o->pkey) {
				if ($issue != null) {
	    		// Calculate cost of this overage
					$worklog = \Project::findAmountBilled($issue->pkey, $issue->datestamp, $issue->user_id);

					$hoursToCredit = 0;
					if ($issue->pname == 'Bug') {
						$hoursToCredit = $issue->hours;
					} else {
						$hoursToCredit = $issue->hours - $issue->estHours;
					}

					if ($worklog != null) {
						$issue->totalCost = ($worklog->rate * $issue->hours)/100;
						$issue->rate = $worklog->rate;
						$issue->totalReduction = floor(0.15 * $worklog->rate * $hoursToCredit)/100;
					} else {
						$issue->totalCost = $issue->totalReduction = 0;
					}
					$totalReduction += $issue->totalReduction;
				}

				$issue = $o;	      
				$overages[] = $issue;
			} else {
				$issue->timeworked += $o->timeworked;
				$issue->hours += $o->hours;
			}
		}

		$returnValue = new \stdClass;
		$returnValue->totalCredit = $totalReduction;
		$returnValue->entries = $overages;

		return $returnValue;

	}

}