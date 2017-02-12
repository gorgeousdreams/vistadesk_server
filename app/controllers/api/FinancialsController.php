<?php

namespace API;


/*

 TODO: Projected future retainers.
 Process: Get all employees
 			For each employee, get all allocations
 			For each weekday, check allocations

 TODO: Get non-retainer client payments

*/


// REST services for tenants
 class FinancialsController extends \API\APIController {

       public function getProjectOverage($projectId) {
 		if(!\Auth::user()->hasRole('finance')) {
 			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
 		}

		return \Response::json(\Project::getProjectOverageEntries($projectId));

          }

 	public function getCashFlows($mode = 'monthly', $date = null) {
 		if(!\Auth::user()->hasRole('finance')) {
 			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
 		}
 		if ($date == null) $date = date('Y-m-d', time());

 		$startDate = date('Y-m-01', strtotime($date));
 		$endDate = date('Y-m-t', strtotime($date));
 		$entries = array();

 		foreach(\CashflowPlanEntry::join('cashflow_plan_items', 'cashflow_plan_items.id','=','cashflow_plan_entries.item_id')->where('tenant_id','=',\MultiTenantScope::getTenantId())->whereBetween('day',array($startDate,$endDate))->get() as $entry) $entries[] = $entry;

		// Add daily earned retainers
 		$startOfPlannedIncome = $startDate;
 		foreach(\CashflowPlanEntry::getEarnedRetainers($startDate, $endDate) as $entry) {
 			$entries[] = $entry;
 			if ($entry->day > $startOfPlannedIncome) $startOfPlannedIncome = $entry->day;
 		}

 		$startOfPlannedIncome = date('Y-m-d', strtotime($startOfPlannedIncome."+ 1 day"));
		// Add future anticipated retainers
 		foreach(\CashflowPlanEntry::getFutureRetainers($startOfPlannedIncome, $endDate) as $entry) $entries[] = $entry;


 		$settings = \Auth::user()->tenant->tenantSettings;
 		$today = $startDate;

 		$rollupPayroll = true;

		// Go over every day of the time span
 		while ($today <= $endDate) {			
 			if (\PayrollPeriod::isPayDay($today)) {
 				$payrollPeriod = \PayrollPeriod::getPayrollPeriod($today);				
 				$totalPayroll = 0;
 				$totalOutsourced = 0;

 				if (!$rollupPayroll) {
 					foreach($payrollPeriod->payrollEntries as $entry) {
 						array_push($entries, new \CashflowPlanEntry(array(
 							'amount'=> -1 * $entry->amount,
 							'description'=>'Payroll: '. $entry->employee->profile->first_name . " " . $entry->employee->profile->last_name,
 							'day'=>$today,
 							'entry_type'=>'Entry'
 							)));
 					}

 				} else {

 					foreach($payrollPeriod->payrollEntries as $entry) {
 						if ($entry->employee->worker_type == "Contractor") {
 							$totalOutsourced += $entry->amount;
 						} else {
 							$totalPayroll += $entry->amount;
 						}
 					}

 					if ($totalPayroll > 0) {
 						array_push($entries, new \CashflowPlanEntry(array(
							'amount'=> -1.08 * $totalPayroll,			// -1.08 = make the number negative, and add 8% for employer taxes
							'description'=>'Payroll',
							'day'=>$today,
							'entry_type'=>'Entry'
							)));
 					}
 					if ($totalOutsourced > 0) {
 						array_push($entries, new \CashflowPlanEntry(array(
 							'amount'=> -1 * $totalOutsourced,
 							'description'=>'Outsourced Labor',
 							'day'=>$today,
 							'entry_type'=>'Entry'
 							)));
 					}
 				}
 			}
 			$today = date('Y-m-d', strtotime($today."+ 1 day"));
 		}

		// Sort by date

 		usort($entries,function($a,$b){
 			return $a->day >= $b->day;
 		});

		// Calculate running balance
 		$startingBalance = 0;
 		$runningBalance = $startingBalance;
 		foreach ($entries as &$e) {
 			$runningBalance = $runningBalance + $e->amount;
 			$e->balance = $runningBalance;
 		}

 		return \Response::json($entries);
 	}

 	public function getTestInsert() {
 		if(!\Auth::user()->hasRole('finance')) {
 			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
 		}
 		$this->createExpense("Rent", "Rent", 600000, '2015-04-01', '2016-12-31', 'monthly');
 		$this->createExpense("Health Insurance", "Benefits", 300000, '2015-04-02', '2016-12-31', 'monthly');
 		$this->createExpense("Autos", "Other", 150000, '2015-04-05', '2016-12-31', 'monthly');
 		$this->createExpense("Amex", "Credit", 900000, '2015-04-15', '2016-12-31', 'monthly');
 		$this->createExpense("Vanguard", "Benefits", 350000, '2015-04-27', '2016-12-31', 'monthly');
 		$this->createExpense("Legal", "Outsourced Labor", 200000, '2015-04-28', '2016-12-31', 'monthly');

 	}

 	private function createExpense($description, $category, $amount, $startDate, $endDate, $recurrence) {
 		if(!\Auth::user()->hasRole('finance')) {
 			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
 		}
 		$planItem = new \CashflowPlanItem(array('description'=>$description,'amount'=> -1 * $amount,'category'=>$category,'start_date'=>$startDate,'end_date'=>$endDate,'recurrence'=>$recurrence));
 		$planItem->tenant_id = 1;
 		$planItem->save();
 		$planItem->createEntries();		
 	}

 	public function getRetainerReport($startDate = null, $endDate = null) {

 		if ($startDate == null || $endDate == null) {
 			$startDate = date('Y-m-d', strtotime('- 6 months', time()));
 			$endDate = date('Y-m-d', strtotime('last sunday'));
 		}

 		$currentDate = date('Y-m-t', strtotime($startDate));

 		$months = array();
 		while ($currentDate <= $endDate) {
 			$endOfMonth = date('Y-m-t', strtotime($currentDate));
 			$endDatePlusOne = date('Y-m-d', strtotime($endOfMonth." +1 day"));
 			$companiesAndBalances = \DB::select(\DB::raw("
 				SELECT c.name, c.id,  
 				GREATEST(0,ifnull((floor(sum(te.hours*te.rate))*-1),0) + (SELECT ifnull(floor(sum(amount)),0) from billing_entries where company_id = p.company_id  AND void = 0 and DATE_SUB(billing_entries.created_at, INTERVAL 1 DAY) < :balance_end)) as balance
 				FROM timesheet_entries te
 				INNER JOIN projects p on p.id = te.project_id
 				INNER JOIN companies c on c.id = p.company_id
 				LEFT JOIN accounts a on a.id = p.account_id
 				WHERE DATE_SUB(te.day, INTERVAL 1 DAY) < :timesheet_end
 				AND c.tenant_id = ".\MultiTenantScope::getTenantId()."
 				GROUP BY p.company_id
 				"), array('balance_end'=>$endDatePlusOne, 'timesheet_end' => $endDatePlusOne));
 			$month = new \stdclass;
 			$months[] = $month;		// add to array
 			$month->name = date('F', strtotime($endOfMonth));
 			// Include year for december/january
 			if ($month->name == 'January' || $month->name == 'December') $month->name .= ' ' . date('Y', strtotime($endOfMonth));
 			$month->entries = array();
 			// Add all non-zero balance companies to the list for this month
 			foreach ($companiesAndBalances as $c) {
 				if ($c->balance > 0) {
 					$month->entries[] = $c;
 				}
 			} 			
 			$currentDate = date('Y-m-d', strtotime($endOfMonth . " +1 day"));

 		}
 		return \Response::json($months);
 	}

 }



