<?php
class Dashboard extends AppModel
{

	static public function getProjects($startDate, $endDate) {
		$projects = new stdClass;
		$projects->projects = Dashboard::getProjectData($startDate, $endDate);
		return $projects;
	}

	static public function getEmployeeDataV2($startDate, $endDate, $employee) {	
		$empData = DB::select(DB::raw(
			"select 
			employees.*, profiles.first_name, profiles.last_name,
			parameters.totalHours as totalHoursInPeriod,
			floor(sum(timesheet_entries.hours)*4)/4 as billedHours,			
			sum(timesheet_entries.rate*timesheet_entries.hours) as billedAmount,
			floor((parameters.totalHours - sum(timesheet_entries.hours))*4)/4 as unbilledHours,
			parameters.totalHours as totalHours

			from timesheet_entries
			inner join (select ".(Dashboard::getWorkingDays($startDate, $endDate, array())*8)." as totalHours, ".(Dashboard::getWorkingDays($startDate, $endDate, array())*$employee->daily_billable_hours)." as billableHours) as parameters
			inner join timesheets on timesheets.id = timesheet_entries.timesheet_id
			inner join employees on employees.id = timesheets.employee_id
			inner join profiles on profiles.id = employees.profile_id
			inner join projects on projects.id = timesheet_entries.project_id
			where timesheet_entries.day >= :startDate and timesheet_entries.day < :endDate
			and employees.tenant_id = ".\MultiTenantScope::getTenantId()." 
			and employees.id = :employeeId
			and projects.billable = 1
			group by employees.id
			"
			), array(
			'startDate'=>date('Y-m-d', strtotime($startDate)),
			'endDate'=>date('Y-m-d', strtotime($endDate . '+1 day')),
			'employeeId'=>$employee->id
			));
if (sizeof($empData) == 0) return null;
return $empData[0];
}

static public function getFinancials($startDate, $endDate) {
//	dd(date('Y-m-d', strtotime($startDate)) . " - ". date('Y-m-d', strtotime($endDate)));
	$financials = new stdClass;
	$financials->revenue = Dashboard::getRevenue($startDate, $endDate);	
	$financials->extendedCredit = Dashboard::getExtendedCredit();
	$financials->retainerBalance = Dashboard::getRetainerBalance();
	$financials->employees = Dashboard::getAllEmployeeData($startDate, $endDate);
	$financials->startDate = date('Y-m-d', strtotime($startDate));
	$financials->endDate = date('Y-m-d', strtotime($endDate));
	$financials->cogs = 0;						// What was the cost of hours billed? Does not include the cost of nonbilled hours. Gross, not net.
	$financials->nonbilledButPaidHours = 0;		// How many hours did salaried staff not bill? (below 40)
	$financials->nonbilledActualCost = 0;		// How much did the non-billed hours cost in salary/comp?
	$financials->nonbilledOpportunityCost = 0;	// How much did the non-billed hours cost in missed revenue?
	$financials->nonbilledAndNotPaidHours = 0;	// How many hours did hourly staff not bill? (Theoretically, no actual cost for this)
	$financials->billedHours = 0;				// How many hours were billed total?
	$financials->missedBillableHours = 0;
	$financials->possibleBillableHours = 0;		// How many hours were possible to bill? Should equal nonBilledButPaid + nonBilledAndNotPaid
	$financials->numberOfDays = floor((strtotime($endDate)-strtotime($startDate))/(60*60*24));
	$financials->numberOfBillableDaysInPeriod = Dashboard::getWorkingDays($startDate, $endDate, array());
	$financials->numberOfHoursInPeriod = $financials->numberOfBillableDaysInPeriod*8;
	$sga = Setting::getValue('sga_daily');
	$revs = 0;

	$financials->overhead = intval($sga)*$financials->numberOfDays;

	foreach ($financials->employees as &$emp) {
		$emp->hourly_comp = Employee::getHourlyComp($emp);
		$emp->possibleBillableHours = $emp->totalHours;
		if ($emp->base_rate == null) $emp->base_rate = 0;

		if ($emp->comp_type == 'Monthly' || $emp->comp_type == 'Annual') { 
			$emp->unbilledHours = max(0, ($emp->totalHours - $emp->billedHours));
			$financials->nonbilledButPaidHours += $emp->unbilledHours;
			$emp->nonbilledActualCost = ($emp->unbilledHours * $emp->hourly_comp);
			$financials->nonbilledActualCost += $emp->nonbilledActualCost;
			$emp->employeeCost = $emp->hourly_comp * $emp->totalHours;
			$emp->grossProfit = ($emp->billedAmount - ($emp->billedHours * $emp->hourly_comp));
			$emp->netProfit = ($emp->billedAmount - $emp->employeeCost);
		} else {
			$emp->employeeCost = ($emp->billedHours * $emp->hourly_comp);
			$emp->grossProfit = $emp->netProfit = ($emp->billedAmount - $emp->employeeCost);
			$emp->unbilledHours = 0;
			$emp->nonbilledActualCost = 0;
			$financials->nonbilledAndNotPaidHours += max(0,($emp->possibleBillableHours - $emp->billedHours));
		}
		$emp->cogs = ($emp->billedHours * $emp->hourly_comp);
		$financials->cogs += $emp->cogs;
		$financials->nonbilledOpportunityCost += ($emp->base_rate * max(0,$emp->possibleBillableHours - $emp->billedHours));
		$financials->possibleBillableHours += $emp->possibleBillableHours;
		$financials->missedBillableHours += max(0, ($emp->possibleBillableHours - $emp->billedHours));
		$financials->billedHours += $emp->billedHours;
		$revs += $emp->billedAmount;
	}

	$financials->grossMargin = $financials->revenue->grossRevenue - $financials->cogs;

	return $financials;
}

static public function getProjectData($startDate, $endDate) {
	return DB::select(DB::raw(
		"select 
		projects.*,
		sum(timesheet_entries.rate*timesheet_entries.hours) as billedAmount,
		sum(timesheet_entries.hours *  (CASE employees.comp_type WHEN 'Annual' THEN (employees.comp_amount / 2080) WHEN 'Monthly' THEN (employees.comp_amount / 173.3) ELSE employees.comp_amount END)) as employeeCost
		from timesheet_entries
		inner join timesheets on timesheets.id = timesheet_entries.timesheet_id
		inner join projects on projects.id = timesheet_entries.project_id
		inner join companies on companies.id = projects.company_id
		inner join employees on employees.id = timesheets.employee_id
		where timesheet_entries.day >= :startDate and timesheet_entries.day < :endDate
		and companies.tenant_id = ".\MultiTenantScope::getTenantId()."
		group by projects.id
		"
		), array(
		'startDate'=>date('Y-m-d', strtotime($startDate)),
		'endDate'=>date('Y-m-d', strtotime($endDate . '+1 day'))
		));
}

static public function getExtendedCredit() {
	$rows = DB::select(DB::raw("select c.name, p.company_id as companyId, (floor(sum(te.hours*te.rate))*-1) + (SELECT
		ifnull(floor(sum(amount)),0) from billing_entries where company_id = p.company_id AND void = 0) as funds                                                                     
	FROM timesheet_entries te                                                                                                                                                                                                                                  
	INNER JOIN projects p on p.id = te.project_id
	INNER JOIN companies c on c.id = p.company_id
	LEFT JOIN accounts a on a.id = p.account_id
	where c.active = 1
	and c.tenant_id = ".\MultiTenantScope::getTenantId()."
	group by p.company_id"));
	$credit = 0;
	foreach ($rows as $row) {
		if ($row->funds < 0) $credit += ($row->funds * -1);
	}
	return $credit;
}



static public function getRetainerBalance() {
	return DB::select(DB::raw("select sum(funds) as balance from 
		(select p.company_id as companyId, 
			greatest((floor(sum(te.hours*te.rate))*-1) + (SELECT ifnull(floor(sum(amount)),0) from billing_entries where company_id = p.company_id  AND void = 0),0) as funds                                         
			FROM timesheet_entries te
			INNER JOIN projects p on p.id = te.project_id
			INNER JOIN companies c on c.id = p.company_id
			LEFT JOIN accounts a on a.id = p.account_id
			WHERE c.tenant_id = ".\MultiTenantScope::getTenantId()."
			group by p.company_id) as fundsquery"))[0];
}

static public function getRevenue($startDate, $endDate) {
	return DB::select(DB::raw(
		"select sum(hours*rate) as grossRevenue from 
		timesheet_entries te, projects p, companies c where c.id = p.company_id and p.id = te.project_id and p.billable = 1 and day >= :startDate and day < :endDate and c.tenant_id = ".\MultiTenantScope::getTenantId()
		), array(
		'startDate'=>date('Y-m-d', strtotime($startDate)),
		'endDate'=>date('Y-m-d', strtotime($endDate . '+1 day'))
		))[0];
}

static public function getAllEmployeeData($startDate, $endDate) {
	$employeeData = array();
	$thisStart = $startDate;
	$startDateSQL = date('Y-m-d', strtotime($startDate));
	$endDateSQL = date('Y-m-d', strtotime($endDate));
	foreach(Employee::all() as $emp) {
		$thisEnd = $endDate;
			// Check if employee was active during this date range.
		if ($emp->hire_date > $endDateSQL || ($emp->termination_date != null && $emp->termination_date != '0000-00-00' && $emp->termination_date < $startDateSQL)) continue;
			// If so, only use their active date range for calculation.
		if ($emp->hire_date > $startDateSQL) $thisStart = date('Y-m-d', strtotime($emp->hire_date));
		if ($emp->termination_date != null && $emp->termination_date != '0000-00-00' && $emp->termination_date < $endDateSQL) {
			$thisEnd = date('Y-m-d', strtotime($emp->termination_date));
		}
		$eData = Dashboard::getEmployeeDataV2($thisStart, $thisEnd, $emp);

		if ($eData != null) $employeeData[] = $eData;
	}
	return $employeeData;
}

static public function getEmployeeData($startDate, $endDate, $employee) {
	$billableFTEHours = DB::select(DB::raw(
		"select 
		employees.*, profiles.first_name, profiles.last_name,
		employees.comp_amount*(employees.daily_billable_hours - sum(timesheet_entries.hours)) as unbilledPotential,
		employees.comp_amount*(parameters.totalHours - sum(timesheet_entries.hours)) as unbilledAmount,
		sum(timesheet_entries.rate*timesheet_entries.hours) as billedAmount,
		floor(sum(timesheet_entries.hours)*4)/4 as billedHours,			
		floor((parameters.totalHours - sum(timesheet_entries.hours))*4)/4 as unbilledHours,
		parameters.totalHours as totalHours,
		parameters.totalHours * employees.comp_amount as employeeCost
		from timesheet_entries
		inner join (select ".(Dashboard::getWorkingDays($startDate, $endDate, array())*$employee->daily_billable_hours)." as totalHours) as parameters
		inner join timesheets on timesheets.id = timesheet_entries.timesheet_id
		inner join employees on employees.id = timesheets.employee_id
		inner join profiles on profiles.id = employees.profile_id
		where timesheet_entries.day >= :startDate and timesheet_entries.day < :endDate
		and employees.id = :employeeId
		and employees.tenant_id = ".\MultiTenantScope::getTenantId()."
		group by employees.id
		"
		), array(
		'startDate'=>date('Y-m-d', strtotime($startDate)),
		'endDate'=>date('Y-m-d', strtotime($endDate . '+1 day')),
		'employeeId'=>$employee->id
		));
if (sizeof($billableFTEHours) == 0) return null;
return $billableFTEHours[0];
}


static function getWorkingDays($startDate, $endDate)
{
	$begin = strtotime($startDate);
	$end   = strtotime($endDate);
	if ($begin > $end) {
		echo "startdate '".$startDate."' is after the end date '".$endDate."'! <br />";

		return 0;
	} else {
		$no_days  = 0;
		$weekends = 0;
		while ($begin <= $end) {
            	$no_days++; // no of days in the given interval
            	$what_day = date("N", $begin);
            	if ($what_day > 5) { // 6 and 7 are weekend days
            		$weekends++;
            	}
            	$begin += 86400; // +1 day
            }
            $working_days = $no_days - $weekends;

            return $working_days;
        }
    }

}
