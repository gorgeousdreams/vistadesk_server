<?php
class Pto extends AppModel
{
    protected $table = 'pay_time_off';
    protected $fillable = ['employee_id','start_date','end_date','city','hours','comment','status'];
    public $timestamps = true;
    
    public function employee() {
        return $this->belongsTo('Employee')->with('profile');
    }

    public function manager() {
        return $this->hasOne('User', "id", "manager_id")->with('profile');
    }
    
    static public function RESTQueryBuilder($id = null, $pageSize = 1000) {
        $type = \Input::get('type', 'All');
        
        $x = Pto::with('employee',"manager");
         if(\Auth::user()->hasRole('manager') || \Auth::user()->hasRole('Admin')) {
            $accessConditions = "";//show all users of sistem
        } else {
            $x->where('employee_id', '=', $currentEmployeeId = \Auth::user()->profile->employee->id);
        }
        
	$isUUID = (strpos($id, "-") !== false);
        
	if ($id != null) {
            if ($isUUID) {
                return $x->where('id','=',$id)->get(['pay_time_off.*'])->first();
            }
            else {
                return $x->get(['pay_time_off.*'])->find($id);	// FIXME: Not sure I like this, could be a security issue since it allows lookups by sequential id
            }
	}
	else {
            if($type != 'All') {
                $x->where('status', '=', $type);
            }
            $x->orderBy('id', 'DESC');
            return $x->paginate($pageSize, ['pay_time_off.*']);
        }
	
    }
}

