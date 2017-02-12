<?php
class Timesheet extends AppModel
{
	public $timestamps = true;
	public function employee() {
		return $this->belongsTo('Employee');
	}

	public function timesheetEntries() {
		return $this->hasMany('TimesheetEntry');
	}

	public static function boot()
	{
		parent::boot();

		static::creating(function($timesheet)
		{
			if (empty($timesheet->uuid)) $timesheet->uuid = generateUUID();
		});
	}

	public static function findPreviousSunday($date) {
		if ($date == null)
			$date = time();

		if (!is_numeric($date))
			$date = strtotime($date);
		if (date('w', $date) == 0)
			return $date;
		else
			return strtotime(
				'last sunday', $date
				);
	}

	/** Creates an empty, open timesheet */

	public static function createProposedTimesheet($employee, $date) {
		$sqlDate = date('Y-m-d', $date);
		$timesheet = new Timesheet();
		$timesheet->start_date = $sqlDate;
		$timesheet->end_date = date('Y-m-d', strtotime($sqlDate . " +6 days"));
		$timesheet->employee_id = $employee->id;
		$timesheet->status = "Open";
		return $timesheet;
	}



}