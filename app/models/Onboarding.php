<?php

class Onboarding extends Eloquent {

	protected $table = 'onboarding';
	public $timestamps = false;
	protected $fillable = array('bank_info','basic_info','contact_info','w4','i9', 'g4', 'other_docs');

	public function employee() {
		return $this->belongsTo('Employee','employee_id');
	}

	static public function setValueForEmployee($employee_id, $key = false, $value = false) {
		$ob = Onboarding::firstOrNew(array('employee_id'=>$employee_id));
                if(!empty($key)) {
                    $ob->employee_id = $employee_id;
                    $ob->{$key} = $value;
                    $ob->save();
                }
                
                //For check any fields
                $validStatus = true;
                
                $validFields = [
                    'bank_info' => true,
                    'contact_info' => true,
                    'basic_info' => true,
                    'w4' => true,
                    'i9' => true,
                    'w9' => false,
                    'other_docs' => true,
                ];

		$employee = \Employee::find($employee_id);


		if ($employee == null) return;
                 
                
//		if ($ob->bank_info && $ob->contact_info && $ob->basic_info && $ob->w4 && $ob->i9 && $ob->other_docs) {
//			if ($employee->status == "Onboarding") {
//				$employee->status = "Active";
//				$employee->save();
//			}
//		} else {
//			if ($employee->status == "Active") {
//				$employee->status = "Onboarding";
//				$employee->save();
//			}
//		}
                
                $profile = $employee->profile()->first();
                $address = $profile->address()->first();
                
                if($address && $address->state == 'GA') {
                    $validFields['g4'] = true;
                }
                
                //IF employee is contractor
                if($employee->worker_type == 'Contractor') {
                    $validFields = [
                        'bank_info' => true,
                        'contact_info' => true,
                        'basic_info' => true,
                        'w4' => false,
                        'i9' => false,
                        'g4' => false,
                        'w9' => true,
                        'other_docs' => true,
                    ];
                }
                
                foreach($validFields as $key => $value) {
                    if($value && empty($ob->$key)) {
                        $validStatus = false;
                        break;
                    }
                }
                if ($validStatus) {
			if ($employee->status == "Onboarding") {
				$employee->status = "Active";
				$employee->save();
			}
		} else {
			if ($employee->status == "Active") {
				$employee->status = "Onboarding";
				$employee->save();
			}
		}
	}

}
