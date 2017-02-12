<?php
class PayrollPeriod extends AppModel
{

	use MultiTenantTrait;
	public $timestamps = true;
	public static $rules = array();	
	
	public static function boot()
	{
		parent::boot();

		static::creating(function($obj)
		{
			if (empty($obj->uuid)) $obj->uuid = generateUUID();
		});
	}

	public function tenant() {
		return $this->belongsTo('Tenant');
	}

	public function payrollEntries() {
		return $this->hasMany('PayrollEntry', 'period_id');
	}

	public static function getLatestExistingPayrollPeriod($tenantId) {
		$res = DB::select(DB::raw("select id from payroll_periods where tenant_id = :tenantId order by end_date desc limit 1"));
		if (sizeof($res) > 0) {
			return PayrollPeriod::find($res[0]->id);
		}
		return null;
	}

	public static function isPayDay($todaySqlFormat) {
		return PayrollPeriod::getLastPayDay($todaySqlFormat) == $todaySqlFormat;
	}

	public static function getLastPayDay($todaySqlFormat) {
		$settings = PayrollPeriod::getTenantSettings();
		$today = strtotime($todaySqlFormat);
		$daysBetween = floor(($today - strtotime($settings->payperiod_start))/(60*60*24));
		$periodLength = ($settings->payperiod_frequency == 'Weekly') ? 7 : 14;
		$payDay = date('Y-m-d', strtotime($settings->payperiod_start." +".(floor($daysBetween/$periodLength)*$periodLength) ." days"));
		return $payDay;
	}

	public static function getPayrollPeriod($todaySqlFormat) {
		$settings = PayrollPeriod::getTenantSettings();
		$periodLength = ($settings->payperiod_frequency == 'Weekly') ? 7 : 14;
		$lastPayrollDate = PayrollPeriod::getLastPayDay($todaySqlFormat);
		$periodStart = date('Y-m-d', \Timesheet::findPreviousSunday($lastPayrollDate . " -".$periodLength." days"));
		$periodEnd = date('Y-m-d', strtotime($periodStart . " +".($periodLength-1)." days"));

		$period = PayrollPeriod::where('start_date','=',$periodStart)->first();
		if ($period == null) {
			$period = new PayrollPeriod(array(
				'start_date' => $periodStart,
				'end_date' => $periodEnd,
				'processed_at' => null				
				));


			// Why this next line no multi-tenant?	
			foreach (Employee::all() as $employee) {
//			foreach (Employee::where('tenant_id','=',\MultiTenantScope::getTenantId())->get() as $employee) {
				if ($employee->termination_date != NULL && $employee->termination_date != '0000-00-00' && $employee->termination_date < $periodStart) continue;
				$amount = 0;
				$quantity = 1;

				// SALARY
				if ($employee->comp_type == "Annual") {
					$amount = ($periodLength / 7) * ($employee->comp_amount / 52);
				} 
				// HOURLY
				else {
					$res = DB::select(DB::raw("
						SELECT 
						sum(te.hours) as hours, t.employee_id from timesheet_entries te
						INNER JOIN timesheets t on t.id = te.timesheet_id
						WHERE te.day between ? AND ?
						AND t.employee_id = ?
						"), array($periodStart, $periodEnd, $employee->id));
					if (sizeof($res) > 0) {
						$amount = ($res[0]->hours * $employee->comp_amount);						
						$quantity = $res[0]->hours;
					}
				}

				if ($amount > 0) {
					$entry = new PayrollEntry(array(
						'employee_id' => $employee->id,
						'period_id' => 0,
						'amount' => $amount,
						'quantity' => $quantity,
						));
					$period->payrollEntries->add($entry);
				}
			}
		} 
		return $period;
	}

	protected static function getTenantSettings() {
		$settings = \TenantSettings::first();
		if ($settings == null) {
			throw new \Exception("No settings available for tenant");
		}
		return $settings;		
	}

}