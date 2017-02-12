<?php
class CashflowPlanEntry extends AppModel
{
	protected $fillable = array('description', 'item_id', 'amount', 'day', 'entry_type');

	public function planItem() {
		return $this->belongsTo('CashflowPlanItem', 'item_id');
	}

	static public function getEarnedRetainers($startDate, $endDate) {
		$entries = array();
		$retainersEarned = \DB::select(\DB::raw("
			SELECT DATE_FORMAT(e.day, '%Y-%m-%d') as day, sum(e.hours*e.rate) as amount from timesheet_entries e
			INNER JOIN projects p on p.id = e.project_id
			INNER JOIN companies c on c.id = p.company_id
			where (e.day BETWEEN ? AND ?)
			AND c.billing_frequency = 'Retainer'
			and c.tenant_id = ".\MultiTenantScope::getTenantId()."
			GROUP BY e.day
			ORDER BY e.day asc
			"), array($startDate, $endDate));

		foreach ($retainersEarned as $r) {
			$entries[] = new \CashFlowPlanEntry(array('amount' => $r->amount,'description' => 'Earned income','day' => $r->day,'entry_type' => 'Entry'));
		}
		return $entries;
	}

	static public function getFutureRetainers($startDate, $endDate) {
		$entries = array();
		$emps = Employee::where('tenant_id','=',\MultiTenantScope::getTenantId())->where(function($query) use ($startDate) {
			$query->whereNull('termination_date')->orWhere('termination_date', '<', $startDate);
		})->get();
		$today = $startDate;
		while ($today <= $endDate) {

			// Skip weekends
			if (date('N', strtotime($today)) < 6) {
				$amount = 0;
				foreach ($emps as $emp) {
					$amount += CashFlowPlanEntry::projectRevenue($emp, $today);
				}
				$entries[] = new \CashFlowPlanEntry(array('amount' => $amount,'description' => 'Projected income','day' => $today,'entry_type' => 'Entry'));
			}
			$today = date('Y-m-d', strtotime($today."+ 1 day"));
		}

		return $entries;
	}

	static protected function projectRevenue($emp, $today) {
		$totalPercent = 0;
		$revenue = 0;
		foreach ($emp->resourceAssignments as $assignment) {
			// Skip any allocations that won't contribute revenue today...
			if ($today < $assignment->start_date || $today > $assignment->end_date || $assignment->allocation <= 0) continue;
			// Cap out anyone's allocation at 100%
			$pct = min($assignment->allocation, 100-$totalPercent);
			// Calculate revenue based on the capped percentage
			$revenue += floor(8*($pct/100)*$assignment->rate);
			$totalPercent += $pct;
			// And don't bother continuing if we've already hit 100%
			if ($totalPercent >= 100) break;
		}

		return $revenue;
	}


}
