<?php

namespace API;

class TimesheetReminderController extends \API\APIController {
    
    public function save(&$timesheetReminder) { 
        $timesheetReminder->tenant_id = \Auth::user()->tenant->id;
        $timesheetReminder->user_id = \Auth::user()->id;
        $timesheetValidation = \Validator::make($timesheetReminder->toArray(), \TimesheetReminder::getAddValidation(), \TimesheetReminder::$validationMessages);
        if ($timesheetValidation->fails() ) {
            return \Response::json(['messages' => $timesheetValidation->messages()], 400);	
        }
        return parent::save($timesheetReminder);
    }
    
}

