<?php
class Company extends AppModel
{
	use MultiTenantTrait;

	public $adminSettings = array(
		'list'=>array(
			'fields'=>array('id', 'name','funds'),
			'actions'=>array('add','view','edit')
			),
		'view'=>array(		
			'actions'=>array('edit')
			),
		'edit'=>array(
			'fields'=>array('id','name','external_id','billing_email','contact_name','billing_frequency', 'billing_payable_days')
			),
		'formats'=>array('funds'=>'currency'),
		// Field-level authorization, array of fields and the roles that can view/edit those fields.
		'acl' => array('funds'=>'finance'),
		'displayField' => 'name'
		);

	public function projects() {
		return $this->hasMany('Project');
	}

	public function address() {
		return $this->belongsTo('Address');
	}

	public function billingEntries() {
		return $this->hasMany('BillingEntry')->orderBy('created_at');
	}

	public function accounts() {
		return $this->hasMany('Account');
	}

	public function resourceRates() {
		return $this->hasMany('ResourceRate');
	}

	public function resourceAssignments() {
		return $this->hasMany('ResourceAssignment');
	}

	public function users() {
		return $this->belongsToMany('User');
	}

	public static function validation() {
		$validation = array(
				'name'              => 'required|unique:companies',
		);
		return $validation;
	}

	public static function boot()
	{
		parent::boot();

		static::creating(function($obj)
		{
			if (empty($obj->uuid)) $obj->uuid = generateUUID();
		});
	}

	static public function getBalanceForCompany($companyId, $startDate = null, $endDate = null) {
		if ($startDate == null) $startDate = '2000-01-01';
		if ($endDate == null) $endDate = date('Y-m-d', time());
		$res = DB::select(DB::raw("
			SELECT ifnull((floor(sum(te.hours*te.rate))*-1),0) + (SELECT ifnull(floor(sum(amount)),0) from billing_entries where company_id = p.company_id AND void = 0 and billing_entries.created_at >= :balance_start and DATE_SUB(billing_entries.created_at, INTERVAL 1 DAY) < :balance_end) as balance
			FROM timesheet_entries te
			INNER JOIN projects p on p.id = te.project_id
			LEFT JOIN accounts a on a.id = p.account_id
			WHERE p.company_id = :companyId
			AND te.day >= :timesheet_start and DATE_SUB(te.day, INTERVAL 1 DAY) < :timesheet_end"), array("balance_start"=>$startDate, "balance_end"=>$endDate, "companyId"=>$companyId, "timesheet_start"=>$startDate, "timesheet_end" => $endDate));

		return intval($res[0]->balance);
	}

	static public function RESTQueryBuilder($id = null, $pageSize = 1000) {
		$x = Company::queryBuilderWithFunds();
		if ($id != null) return $x->find($id);
		else return $x->paginate($pageSize);
	}

	static public function queryBuilderWithFunds() {
		return DB::table('companies')
		->leftJoin(DB::raw('(select p.company_id as companyId, (floor(sum(ifnull(te.hours,0)*ifnull(te.rate,0)))*-1) + (SELECT ifnull(floor(sum(amount)),0) from billing_entries where company_id = p.company_id AND void = 0) as funds
			FROM projects p
			LEFT JOIN timesheet_entries te on p.id = te.project_id
			LEFT JOIN accounts a on a.id = p.account_id
			group by p.company_id 			
			) as fundsquery'), 'fundsquery.companyId', '=', 'companies.id')->where('active','=',1)->where('tenant_id','=',\MultiTenantScope::getTenantId());		
	}

	static public function findInvoiceMonths($companyId) {
		return DB::select(DB::raw("
			SELECT distinct(concat(YEAR(day),'-',LPAD(MONTH(day),2,0),'-01')) as month 
			FROM timesheet_entries e, projects p 
			WHERE p.id = e.project_id and p.company_id = :companyId order by month desc"), array('companyId'=>$companyId));

	}

	/* Gets projected spend for a company based on resourece assignments for the time period.
	** Date parameters are in SQL 'Y-m-d' format
	*/
	static public function getProjectedSpendForCompany($companyId, $startDate, $endDate) {
		if ($startDate == null) $startDate = date('Y-m-d');
		if ($endDate == null) $endDate = date('Y-m-d', strtotime($startDate." +2 weeks"));
		$spend = 0;
		$assignments = DB::select(DB::raw("
			select Assignment.*, Profile.first_name, Profile.last_name, Company.name from resource_assignments Assignment
			LEFT JOIN employees Employee on Employee.id = Assignment.employee_id
			LEFT JOIN profiles Profile on Profile.id = Employee.profile_id
			LEFT JOIN companies Company on Company.id = Assignment.company_id
			WHERE Assignment.company_id = :companyId
			AND Assignment.start_date <= :endDate AND Assignment.end_date >= :startDate
			AND Assignment.allocation > 0
			ORDER BY Assignment.company_id
			"), array("companyId" => $companyId, "startDate"=>$startDate, "endDate"=>$endDate));

		foreach ($assignments as $a) {
			// Adjust the date range in case the assignment starts or ends during the period we're interested in
			$assignmentStart = ($a->start_date > $startDate) ? $a->start_date : $startDate;
			$assignmentEnd = ($a->end_date < $endDate) ? $a->end_date : $endDate;
			
			// Now find the number of working days for the adjusted range. empty array 3rd param == no holidays
			$workingDaysInRange = \Employee::getWorkingDays($assignmentStart, $assignmentEnd, array());

			$spend += (8 * $workingDaysInRange * $a->rate * ($a->allocation / 100));			
		}
		return $spend;
	}

}