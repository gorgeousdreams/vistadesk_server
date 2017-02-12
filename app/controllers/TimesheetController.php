<?php

class TimesheetController extends \BaseController {

	var $testing = false;

	public function getView($uuid, $date=null) {
        MultiTenantScope::$tenantId = "all";
        $employee = Employee::where('uuid','=',$uuid)->firstOrFail();

        if (!Auth::check() && $employee->profile->user != null) {
            Auth::login($employee->profile->user);
        }

        $today = time();

        $date = $date ?: strtotime(" -1 week", time());

        $date = Timesheet::findPreviousSunday($date);

        $timesheet = Timesheet::where('start_date','=',date('Y-m-d',$date))->where('employee_id','=',$employee->id)->first();
        if ($timesheet == null) {
            $timesheet = $this->createProposedTimesheet($employee, $date);
            if ($today > strtotime($timesheet->start_date . " +9 days")) {
                $timesheet->status = "Closed";
            }
        }

        return View::make('timesheets.view')
        ->with('employee', $employee)
        ->with('timesheet', $timesheet);

    }

    public function createPendingTimesheets() {
        $lastWeek = strtotime(" -1 week", time());
        // If reminders were already sent this week, don't re-send (in case this method gets called more than once)
        $lastReminderSent = deNull(Setting::getValue("lastTimesheetReminder"), "2001-01-01");
        $monday = date('Y-m-d', strtotime("last sunday +1 day"));

        if ($lastReminderSent < $monday) {
            $emps = Employee::where('status','!=','Inactive')->get();

            foreach ($emps as $employee) {
                $timesheet = $this->getOrCreateThisWeeksTimesheet($employee, $lastWeek);
                if ($timesheet->status == "Open") {
                    $this->sendTimesheetNotification($employee, $timesheet);
                }
            }
            Setting::setValue("lastTimesheetReminder", $monday);
        }
        else {
            return false;
        }
        return true;
    }

    /** Sends timesheet reminders to all employees */
    public function getSendTimesheetReminders() {
        $lastWeek = strtotime(" -1 week", time());
        // If reminders were already sent this week, don't re-send (in case this method gets called more than once)
        $lastReminderSent = deNull(Setting::getValue("lastTimesheetReminder"), "2001-01-01");
        $monday = date('Y-m-d', strtotime("last sunday +1 day"));

        if ($lastReminderSent < $monday) {
            Setting::setValue("lastTimesheetReminder", $monday);

	    $coreController = new \CoreController();
	    $coreController->getImport();

            $emps = Employee::all();

            foreach ($emps as $employee) {
                $timesheet = $this->getOrCreateThisWeeksTimesheet($employee, $lastWeek);
                if ($timesheet->status != "Closed") {
                         $this->sendTimesheetNotification($employee, $timesheet);
                }
            }
        }
        else {
            dd("Already sent");
        }
        dd("OK");
    }

    /** Generate approved / closed timesheets for new employees that forgot previous week timesheets */
    public function getBackdateTimesheets($date = null) {
        MultiTenantScope::$tenantId = "all";
      if ($date == null) {
        $date = strtotime(" -1 week", time());
    } else {
        $date = strtotime($date);
    }
    $periodStartSunday = Timesheet::findPreviousSunday($date);
    foreach (Employee::all() as $employee) {

      if ($employee->profile->first_name != "Dimi") continue;

        Worklog::copyWorklogs($employee, date('Y-m-d', $periodStartSunday));
        $timesheet = $this->getOrCreateThisWeeksTimesheet($employee, $date);
        $timesheet->status = "Closed";
			$timesheet->saveAll();		// save model and relationships
			//			$this->sendTimesheetNotification($employee, $timesheet);
		}

		$this->layout = false;
		dd($timesheet);
    }


    public function getCreateTimesheets($date = null) {
      if ($date == null) {
        $date = strtotime(" -1 week", time());
    } else {
        $date = strtotime($date);
    }
    $periodStartSunday = Timesheet::findPreviousSunday($date);
	    $coreController = new \CoreController();
	    $coreController->getImport();

    foreach (Employee::all() as $employee) {
      //       if ($employee->id != 255) continue;    // Alex kokit
        Worklog::copyWorklogs($employee, date('Y-m-d', $periodStartSunday));
        $timesheet = $this->getOrCreateThisWeeksTimesheet($employee, $date);
        $timesheet->status = "Closed";
			$timesheet->saveAll();		// save model and relationships
			$this->sendTimesheetNotification($employee, $timesheet);
		}

		$this->layout = false;
		dd("OK");
    }

    /* For creating timesheets prior to September for a client. If you uncomment this remember to modify JiraTimesheet.php since
       it will grab all worklogs (not just for a client or project)
    */
    public function getPastTimesheets($date = null) {
      for ($wk = 0; $wk < 22; $wk++) {
        $date = strtotime("2014-08-30 -".$wk." week");
	$periodStartSunday = Timesheet::findPreviousSunday($date);
	foreach (Employee::all() as $employee) {
	  if ($employee->id != 176 && $employee->id != 164 && $employee->id != 174 && $employee->id != 166 && $employee->id != 169) continue;

	  Worklog::copyWorklogsForProject($employee, date('Y-m-d', $periodStartSunday), 43);

	  $timesheet = $this->getOrCreateThisWeeksTimesheet($employee, $date);
	  if ($timesheet->timesheetEntries->count() > 0) {
	    $timesheet->status = "Closed";
	    $timesheet->saveAll();		// save model and relationships
	  }
	  //	  $this->sendTimesheetNotification($employee, $timesheet);
	}
      }

		$this->layout = false;
		dd("OK");
    }
    

    /** Create a NEW / OPEN timesheet for an employee */
    private function getOrCreateThisWeeksTimesheet($employee, $date) {
        date_default_timezone_set("EST");
        $today = time();
        $periodStartSunday = Timesheet::findPreviousSunday($date);    // Find the start date for last week.
        $timesheet = Timesheet::where('start_date','=',date('Y-m-d', $periodStartSunday))->where('employee_id','=',$employee->id)->first();


        if ($timesheet == null) {
        	$timesheet = JiraTimesheet::createOpenTimesheet($employee, $periodStartSunday);
		if ($timesheet->status == "Closed") {
		  $timesheet->saveAll();
		}
	}


	/* Uncomment this section to add support for adding new projects to existing timesheets */
	/*
        } else {
	        $timesheet = JiraTimesheet::addToOpenTimesheet($timesheet, $employee, $periodStartSunday);
		if ($timesheet->status == "Closed") {
		  $timesheet->saveAll();
		}
	}
	*/
        return $timesheet;
    }


    private function createProposedTimesheet($employee, $date) {
        // Get all worklogs from Jira after that date range.
    	$sqlDate = date('Y-m-d', $date);
    	$worklogs = Jira::getWorklogsForEmployee($employee->external_id, $sqlDate);
    	$timesheet = new Timesheet(array(
    		"start_date" => $sqlDate,
    		"end_date" => date('Y-m-d', strtotime($sqlDate . " +6 days")),
    		"employee_id" => $employee->id,
    		"status" => "Open"));

    	foreach ($worklogs as $log) {
    		$project = Project::where('external_id','=',$log->ProjectID)->firstOrFail();
    		$entry = new TimesheetEntry(array(
    			'hours' => floatval($log->hours),
    			'day' => $log->datestamp,
    			'rate' => Employee::getEmployeeRateForClient($employee->id, $project->company_id)[0]->Rate,
    			'project_id' => $project->id
    			));
    		$timesheet->timesheetEntries->add($entry);
      }
      return $timesheet;
  }

  private function sendTimesheetNotification($employee, $timesheet) {
   // Don't send notifications for empty timesheets.
    if ($timesheet == null || !isset($timesheet->timesheetEntries) || sizeof($timesheet->timesheetEntries) == 0) {
        return;
    }
    $subject = "Pending timesheet for " . $employee->profile->first_name . " " . $employee->profile->last_name . ": " . date("M d", strtotime($timesheet->start_date)) . " - " . date("M d, Y", strtotime($timesheet->end_date));
    if ($timesheet->status == "Closed") {
        $subject = "Finalized timesheet for " . $employee->profile->first_name . " " . $employee->profile->last_name . ": " . date("M d", strtotime($timesheet->start_date)) . " - " . date("M d, Y", strtotime($timesheet->end_date));
    }
    if (empty($employee->profile->email)) {
        $recips = "sjaslow@ngdcorp.com";
        $subject = "(NO EMPLOYEE EMAIL) ".$subject;
    } else {
        $recips = $this->testing ? "sjaslow@ngdcorp.com" : $employee->profile->email;
    }

    $cc = null;
    $bcc = null;
		// CC the supervisor(s), only in prod mode and if a supervisor email is on file
    if (!$this->testing && !isNullOrEmpty($employee->supervisor_email)) {
        $cc = explode(",",$employee->supervisor_email);
    }

        // Also send approved timesheets to Seth and Julie in prod mode
    if (!$this->testing) {
        if ($timesheet->status == "Closed") {
            $bcc = array("sjaslow@ngdcorp.com", "cweick@ngdcorp.com");
        } else {
            $bcc = "sjaslow@ngdcorp.com";
        }
    }

    Mail::send('emails.timesheets.pending', 
        array('employee' => $employee, 'timesheet' => $timesheet), 
        function($message) use ($recips, $cc, $bcc, $subject) {
            $message->to($recips)->from("info@ngdcorp.com", "Northgate Digital")->subject($subject);
            if ($cc != null) $message->cc($cc);
            if ($bcc != null) $message->bcc($bcc);
        });
}

} 