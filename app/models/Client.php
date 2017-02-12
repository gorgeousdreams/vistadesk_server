<?php
/**
 * Client-facing functionality. Note this class does not represent a table in the database.
 */

class Client extends AppModel
{

	static public function getProjects($companyId) {
		$sql = 
		"select 
		projects.*,
		sum(timesheet_entries.rate * timesheet_entries.hours) as billedAmount,
		(select sum(budget_requests.amount) from budget_requests where project_id = projects.id) as budget
		from timesheet_entries
		inner join timesheets on timesheets.id = timesheet_entries.timesheet_id
		inner join projects on projects.id = timesheet_entries.project_id
		left join budget_requests on budget_requests.project_id = projects.id
		inner join companies on companies.id = projects.company_id
		where companies.id = :companyId
		group by projects.id
		";

		return DB::select(DB::raw($sql), array('companyId'=>$companyId));
	}

}
