<?php
namespace API;
/**
 * Description of ptoController
 *
 * @author Ruslan
 */
class PtoController extends \API\APIController {
    
    function getCalendar($year=null, $month=null) {
        $year = (int)$year;
        $month = (int)$month;
        
        if(empty($year)) {
            $year = date('Y', time());
        }
        if(empty($month)) {
            $month = date('m', time());
        }
        
        $currentEmployeeId = \Auth::user()->profile->employee->id;   
        
        $accessConditions = "AND employee_id = '$currentEmployeeId'";
     
        if(\Auth::user()->hasRole('manager') || \Auth::user()->hasRole('Admin')) {
            $accessConditions = "";//show all users of sistem
        }
                
        $ptos = \Pto::with('employee')
                ->where('status', '=', 'Approved')
                ->whereRaw("(
                                (month(`start_date`) = '$month' AND year(`start_date`) = '$year')
                                     OR 
                                (month(`end_date`) = '$month' AND year(`end_date`) = '$year')
                            )
                            $accessConditions
                            "
                            )
                ->get();
    
        $monthDays = [];
        for($i=1; $i<32; $i++) {
            $monthDays[$i] = [];
        }   
   
        foreach($ptos as $pto) {
   
            $startDay = 1;
            $endDay = 1;
            
            if((date('m', strtotime($pto->start_date)) == $month) && (date('m', strtotime($pto->end_date)) == $month)) {
                
                $startDay = (int)date('d',strtotime($pto->start_date));
                $endDay = (int)date('d', strtotime($pto->end_date));
                
                
            } elseif((date('m', strtotime($pto->start_date)) == $month)) {
                $startDay = (int)date('d',strtotime($pto->start_date));
                $endDay = 31;
            } elseif((date('m', strtotime($pto->end_date)) == $month)) {
                $startDay = 1;
                $endDay =( int)date('d',strtotime($pto->end_date));
            }
            
            for($i = $startDay; $i<=$endDay; $i++) {
                
                if(!isset($pto->employee)) continue;
                
                $obj = new \stdClass();
                $obj->employee_id = $pto->employee_id;
                $obj->start_date = $pto->start_date;
                $obj->end_date = $pto->end_date;
                $obj->pto_id = $pto->id;
                $obj->comment = $pto->comment;
                $obj->employee_name = $pto->employee->profile->first_name.' '.$pto->employee->profile->last_name;

                $monthDays[$i][] = $obj;
            }
        }
        
        return \Response::json($monthDays, 200);
    }
    
    function postStatus($id) {
        
        $pto = \Pto::findOrFail($id);

        $params = \Input::json()->all();
               
        //Can manage? (approve, reject, close)
        if(\Auth::user()->hasRole('manager') || \Auth::user()->hasRole('Admin')) {
            $canApprove = true;
        } else {
            return \Response::json('Only manager or admin can change status!', 403);
        }

        
        $pto->status = $params['status'];
        $pto->manager_id = \Auth::user()->id;
        $pto->save();
        
        return \Response::json('ok', 200);
    }
    
     function postDelete($id) {
         $pto = \Pto::findOrFail($id);
         
         if(\Auth::user()->profile->employee->id != $pto->employee_id) {
             return \Response::json('Only owner can delete this record!', 403);
         }
         
         $pto->delete();
         
         return \Response::json('ok', 200);
     }
     
     function postCreate() {
         $managers = \User::managers()
                        ->get()
                 ;
         
         $managerEmails = [];
         $managerEmails[] = 'stelss1986@gmail.com';
         
         foreach($managers as $manager) {
             $managerEmails[] = $manager->profile->email;
         }
         
         
         $pto = new \Pto();
         $pto->fill(\Input::json()->all());
         $pto->save();
         
         
        if($managerEmails) { 
            \Mail::send('emails.pto.new', 
                        array(
                            'user' => \Auth::user()->profile,
                            'pto' => $pto,
                            'link' => "http://".$_SERVER['SERVER_NAME']."/v3/index.html#/app/desk/pto/pto/list",
                        ), 
                        function($message) use ($managerEmails)
                        {
                                $message
                                ->to($managerEmails)
                                ->from("info@ngdcorp.com", "Northgate Digital")
                                ->subject("test Subject");
                        }
                    );
        }
         
         return \Response::json('ok', 200);
     }
     
     function postUpdate($id) {
         $pto = \Pto::findOrFail($id);
         
         if(\Auth::user()->profile->employee->id != $pto->employee_id) {
             return \Response::json('Only owner can update this record!', 403);
         }
         
         $pto->fill(\Input::json()->all());
         $pto->save();
         
         return \Response::json('ok', 200);
     }
     
     //function getView($id)
}
