<?php
class PayrollEntry extends AppModel
{
	public $timestamps = true;
	
	public function employee() {
		return $this->belongsTo('Employee');
	}

	public function payrollPeriod() {
		return $this->belongsTo('PayrollPeriod', 'period_id');
	}

}
