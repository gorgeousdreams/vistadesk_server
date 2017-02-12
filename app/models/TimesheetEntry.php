<?php
class TimesheetEntry extends AppModel
{
	public function project() {
		return $this->belongsTo('Project');
	}

	public function timesheet() {
		return $this->belongsTo('Timesheet');
	}

}
