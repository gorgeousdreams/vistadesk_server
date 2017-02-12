<?php
class Employee extends AppModel
{

	use MultiTenantTrait;

  public $timestamps = true;
	public static $rules = array();	
	protected $fillable = array('comp_amount', 'base_rate', 'external_id', 'resource_id','supervisor_email','full_time',
		'daily_billable_hours','department','manager_id','worker_type','comp_type','job_title','location','hire_date',
		'personal_email','status');    

	public static function getAddValidation() {
		$validation = array(
			'hire_date' => 'required|date',
			'worker_type' => 'required',
			'comp_type'=> 'required'
			);
		return $validation;
	}



	public $adminSettings = array(
		// Fields shown on list pages
		'list'=>array(
			'fields' => array('id', 'comp_amount'),
			'actions' => array('add', 'view', 'edit', 'delete')
			),
		// Fields shown on details / view pages
		'view'=>array(
			'fields' => array('id', 'comp_amount', 'base_rate','resource_id'),
			'actions' => array('edit'),
			'tabs' => array('Custom Data')
			),
		// How this object should be 'named' in simple lists and hyperlinks 
		'displayField' => 'name',
		// Field-level authorization, array of fields and the roles that can view/edit those fields.
		'acl' => array('comp_amount' => 'finance', 'base_rate'=>'finance','comp_type'=>'finance'),
		// Formatting for fields. Possible formats include date, currency, number
		'formats'=>array('base_rate'=>'currency', 'comp_amount' => 'currency')

		);


    public static function boot()
    {
            parent::boot();

            static::creating(function($obj)
            {
                    if (empty($obj->uuid)) $obj->uuid = generateUUID();
            });
    }

    public function company() {
            return $this->belongsTo('Company');
    }

    public function resource() {
            return $this->belongsTo('Resource');
    }

    public function resourceRates() {
            return $this->hasMany('ResourceRate');
    }

    public function manager() {
            return $this->belongsTo('Employee', 'manager_id');
    }


    public function profile() {
            return $this->belongsTo('Profile');
    }

    public function documentFieldValue() {
            return $this->hasMany('DocumentFieldValue');
    }

    public function employeeDocuments() {
            return $this->hasMany('EmployeeDocument');
    }

    public function onboarding() {
        return $this->hasOne('Onboarding');
    }

    public function secureInfo() {
    	return $this->hasOne('SecureInfo');
    }

    public function resourceAssignments() {
    	return $this->hasMany('ResourceAssignment');
    }


public static function getEmployeeRateForClient($employeeId, $companyId) {
	return DB::connection()->select(DB::raw("
		SELECT Employee.id, Profile.last_name, COALESCE( NULLIF(ResourceRate.rate,0), NULLIF(Resource.base_rate,0), Employee.base_rate) as Rate
		FROM employees Employee 
		LEFT JOIN profiles Profile on Profile.id = Employee.profile_id
		LEFT JOIN resources Resource on Resource.id = Employee.resource_id 
		LEFT JOIN resource_assignments ResourceRate on ResourceRate.employee_id = Employee.id and company_id = :companyId
		WHERE Employee.id = :employeeId"), array($companyId, $employeeId));
}

static public function getEmployeeLocations($tenantId) {
	return DB::select(DB::raw("SELECT distinct(location) from employees where location is not null and tenant_id = :tenantId ORDER BY location"), array('tenantId' => $tenantId));
}

static public function getWorkingDays($startDate,$endDate,$holidays){
  // do strtotime calculations just once
  $endDate = strtotime($endDate);
  $startDate = strtotime($startDate);


  //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
  //We add one to inlude both dates in the interval.
  $days = ($endDate - $startDate) / 86400 + 1;

  $no_full_weeks = floor($days / 7);
  $no_remaining_days = fmod($days, 7);

  //It will return 1 if it's Monday,.. ,7 for Sunday
  $the_first_day_of_week = date("N", $startDate);
  $the_last_day_of_week = date("N", $endDate);

  //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
  //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
  if ($the_first_day_of_week <= $the_last_day_of_week) {
    if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
    if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
  }
  else {
    // (edit by Tokes to fix an edge case where the start day was a Sunday
    // and the end day was NOT a Saturday)

    // the day of the week for start is later than the day of the week for end
    if ($the_first_day_of_week == 7) {
      // if the start date is a Sunday, then we definitely subtract 1 day
      $no_remaining_days--;

      if ($the_last_day_of_week == 6) {
	// if the end date is a Saturday, then we subtract another day
	$no_remaining_days--;
      }
    }
    else {
      // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
      // so we skip an entire weekend and subtract 2 days
      $no_remaining_days -= 2;
    }
  }

  //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
  //---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
  $workingDays = $no_full_weeks * 5;
  if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

  //We subtract the holidays
  foreach($holidays as $holiday){
    $time_stamp=strtotime($holiday);
    //If the holiday doesn't fall in weekend
    if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
      $workingDays--;
  }

  return $workingDays;
}


// returns the number of hours (including fractional hours) of PTO taken by the employee
static public function getPTOHours($employee) {
  $workingDaysThisYear = \Employee::getWorkingDays(date('Y-01-01'), date('Y-m-d'), array());
	$paidNonBillables = DB::select(DB::raw("select ifnull(sum(hours),0) as hours from worklogs where external_id like 'INT-%' and external_id not in ('INT-1', 'INT-2', 'INT-20') and employee_id = :employeeId and year(date_worked) = year(now())"), array("employeeId" => $employee->id))[0]->hours;
	$billable = DB::select(DB::raw("select sum(te.hours) as billableHours from timesheet_entries te, timesheets t, projects p where t.id = te.timesheet_id and p.id = te.project_id and p.billable = 1 and year(te.day) = year(now()) and t.employee_id = :employeeId"), array("employeeId" => $employee->id))[0]->billableHours;	
	return max(0, (($workingDaysThisYear * 8) - $billable) - $paidNonBillables);
}

/*
	Gets an employee's time worked and accrued PTO. Notes:

	1. Time logged on non-billable projects is counted as PTO - this should only be INT-* such as vacation, sick, etc.
	2.a. There are 260 (52 * 5) total workdays per year
	2.b. There are 13 PTO days provided: 2 weeks vacation and 3 sick days
	2.c. There are 7 paid holiday / office closing days provided
	2.d. That leaves 240 days that should be worked per year, these are used to calculate accrual. SO:
	3. Accrued PTO days = 13 / 240

*/

static public function getTimeData($employee) {
	$timedata = new stdClass;
	$billable = DB::select(DB::raw("select sum(te.hours) as billableHours from timesheet_entries te, timesheets t, projects p where t.id = te.timesheet_id and p.id = te.project_id and p.billable = 1 and year(te.day) = year(now()) and t.employee_id = :employeeId"), array("employeeId" => $employee->id));

	$timedata->daysWorked = floor($billable[0]->billableHours / 8);	
	$timedata->accruedPTODays = floor($timedata->daysWorked * 0.08333333); // (20 PTO days / 240 worked days)
	$timedata->usedPTODays = floor(\Employee::getPTOHours($employee) / 8);



	return $timedata;
}

const HOURS_PER_MONTH = 173.3;
const HOURS_PER_YEAR = 2080;

public static function getHourlyComp($employee) {
	$compDivisor = 1;
	if ($employee->comp_type == 'Monthly') $compDivisor = 173.33;	// Hours per month
	else if ($employee->comp_type == 'Annual') $compDivisor = 2080;	// Hours per year
	return intval(floor($employee->comp_amount / $compDivisor));
}

static public function RESTQueryBuilder($id = null, $pageSize = 1000) {
    	// Note the trick here... Using join() and with(), since orderBy can onlybe used with join() apparently.
	$x = Employee::join('profiles','profiles.id','=','employees.profile_id')->orderBy('profiles.last_name')->orderBy('profiles.first_name')->with('profile')->where('tenant_id','=',\MultiTenantScope::getTenantId());
	$isUUID = (strpos($id, "-") !== false);
	if ($id != null) {
		if ($isUUID) {
			return $x->where('uuid','=',$id)->get(['employees.*'])->first();
		}
		else {
			return $x->get(['employees.*'])->find($id);	// FIXME: Not sure I like this, could be a security issue since it allows lookups by sequential id
		}
	}
	else return $x->paginate($pageSize, ['employees.*']);
	
}

static public function queryBuilderWithFunds() {
}


}