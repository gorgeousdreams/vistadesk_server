<?php
class ResourceAssignment extends AppModel
{

	public $adminSettings = array(
		// Field-level authorization, array of fields and the roles that can view/edit those fields.
		'acl' => array('rate' => 'finance,admin'),
		// Formatting for fields. Possible formats include date, currency, number
		'formats'=>array('rate'=>'currency')

		);

	protected $fillable = array('company_id', 'project_id', 'employee_id','rate','allocation','start_date','end_date','description');

	public function company() {
		return $this->belongsTo('Company');
	}

	public function employee() {
		return $this->belongsTo('Employee');
	}

	public function project() {
		return $this->belongsTo('Project');
	}

	public static function validation() {
		$validation = array(
				'company_id'              => 'required|integer',
				'project_id'              => 'required|integer',
				'employee_id'			  => 'required|integer',
				'rate'			  		  => 'required|integer',
				'allocation'			  => 'required|integer|between:0,100',
				'start_date'			  => 'required|date',
				'end_date'			 	  => 'required|date',
				'description'			  => 'required',
		);
		return $validation;
	}

	/**
	* Returns an array of company ID and current max billable rate for each, eg:
  	* 4 => int 9500
  	* 8 => int 8500
  	* 9 => int 8500
  	*/
	public static function getMaxRates() {
		$returnArray = array();
		$rates = DB::select(DB::raw("select company_id, max(rate) as rate from resource_assignments where start_date < now() and end_date > now() group by company_id"));
		foreach($rates as $rate) {
			$returnArray[$rate->company_id] = intval($rate->rate);
		}
		return $returnArray;
	}

}