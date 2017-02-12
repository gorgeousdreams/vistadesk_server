<?php

class StatementsController extends \BaseController {

        var $testing = false;	   	   // Set to true to send all email to sjaslow@

	public function getSendInvoices() {
        // If reminders were already sent this week, don't re-send (in case this method gets called more than once)
		$lastInvoicesSent = deNull(Setting::getValue("lastInvoices"), "2001-01-01");

        // Tuesday's invoice day, wednesday's pudding day!
        if (date('w', time()) == 2) $tuesday = date('Y-m-d');       // If today is tuesday, use today
        else $tuesday = date('Y-m-d', strtotime("last tuesday"));   // Otherwise use last tuesday.

        $startDate = date('Y-m-d', strtotime("last sunday -1 week", time()));
        $endDate = date('Y-m-d', strtotime("last saturday", time()));

        if ($lastInvoicesSent < $tuesday) {
	          	Setting::setValue("lastInvoices", $tuesday);


			// When it fails mid-way, for now all we can do is this...
			//	  $invoicedCompanies = [8, 9,26, 14, 27, 32, 13, 28];


	  foreach (Company::queryBuilderWithFunds()->get() as $companyAndFunds) {
	    $company = Company::find($companyAndFunds->id);

	    /*             and this...

	    if (in_array($company->id, $invoicedCompanies)) {
		continue;
	      }
	    */
	    $funds = $companyAndFunds->funds;
	    if ($funds == NULL) $funds = 0;
	    $company->funds = $funds;
		  $entries = BillingEntry::getInvoiceBillingEntries($company->id, $startDate, $endDate);

                // Don't send a statement if there was no activity for this client.
        		if (sizeof($entries) == 0) continue;

        		if (isNullOrEmpty($company->billing_email)) continue;

        		$subject = "Northgate Digital activity statement: " . date('M d', strtotime("last sunday -1 week", time())) . " - " . date('M d, Y', strtotime("last saturday", time()));
        		$recips = $this->testing ? explode(",","sjaslow@ngdcorp.com") : explode(",",$company->billing_email);
        		$cc = null;
			$bcc = null;
        		if (!$this->testing) {
        			$bcc = array("sjaslow@ngdcorp.com", "cweick@ngdcorp.com");
        		}

			$startingBalance = $company->funds;
			foreach($entries as $entry) $startingBalance += $entry->total;

        		Mail::send('emails.statements.statement', 
				   array('billingEntries' => $entries, 'company' => $company, 'startDate' => $startDate, 'endDate' => $endDate, 'startingBalance'=>$startingBalance), 
        			function($message) use ($recips, $cc, $bcc, $subject)
        			{
        				$message
        				->to($recips)
        				->from("info@ngdcorp.com", "Northgate Digital")
        				->subject($subject);
        				if ($cc != null) $message->cc($cc);
        				if ($bcc != null) $message->bcc($bcc);
        			});
        	}
        }
        dd("OK");
    }


    public function getInvoice($uuid, $startDate = null, $endDate = null) {
      MultiTenantScope::$tenantId = "all";
		// Convert inputs to dates
    	$startDate = ($startDate != null) ? strtotime($startDate) : time();
    	$endDate = ($endDate != null) ? strtotime($endDate) : time();

    	if ($endDate < $startDate) $endDate = $startDate;

    	$startDate = date('Y-m-d', $startDate);
    	$endDate = date('Y-m-d', $endDate);
    	$endDatePlusOne = date('Y-m-d', strtotime($endDate." +1 day"));

    	$company = Company::queryBuilderWithFunds()->where('uuid','=',$uuid)->first();
    	if ($company == null)
    		throw new Exception("Unable to load company.");
    	$eqcompany = Company::find($company->id);
    	$eqcompany->funds = $company->funds;
    	$billingEntries = BillingEntry::getInvoiceBillingEntries($company->id, $startDate, $endDate);

    	$worklogs = Worklog::whereHas('project', function ($q) use ($eqcompany) {
    		$q->where('company_id','=',$eqcompany->id);
    	})->where('date_worked', '>=', $startDate)->where('date_worked', '<', $endDatePlusOne)->orderBy('task', 'asc')->orderBy('date_worked')->get();

    	return View::make('statements.invoice')
    	->with('startDate', $startDate)
    	->with('endDate', $endDate)
    	->with('company', $eqcompany)
    	->with('billingEntries', $billingEntries)
    	->with('worklogs', $worklogs)
    	->with('invoiceMonths', $this->getInvoiceMonths($company));

    }

    public function getView($uuid, $mode = 'weekly', $date = null) {
      MultiTenantScope::$tenantId = "all";
    	if ($date == null) $date = time();
    	else $date = strtotime($date);
    	$company = Company::queryBuilderWithFunds()->where('uuid','=',$uuid)->first();
		// Laravel, you kind of suck for this.
    	if ($company == null)
    		throw new Exception("Unable to load company.");
    	$eqcompany = Company::find($company->id);
    	$eqcompany->funds = $company->funds;
    	$balance = 0;
    	$startingBalance = 0;
    	$currentMonth = null;
    	$endDate = date('Y-m-d', $date);
    	if ($mode == 'monthly') {
    		$startDate = date('Y-m-01', $date);
    		$currentMonth = $startDate;
            $endDate = date('Y-m-t', $date);       // t = # of days in a month
        } else if ($mode == 'full') {
    		$startDate = date('2014-01-01', $date);
    		$currentMonth = $startDate;
		$endDate = date('Y-m-t');       // t = # of days in a month
	} else {
        	$startDate = date('Y-m-d', strtotime($endDate . " -6 weeks"));
        }
        $startingBalance = Company::getBalanceForCompany($company->id, null, date('Y-m-d', strtotime($startDate." -1 day")));
        $billingEntries = BillingEntry::getCompanyBillingEntries($company->id, $startDate, $endDate);

        return View::make('statements.view')
        ->with("billingEntries", $billingEntries)
        ->with("statementEndDate", strtotime($endDate))
        ->with("statementStartDate", strtotime($startDate))
        ->with("company", $eqcompany)
	->with("startingBalance", $startingBalance)
        ->with("currentMonth", $currentMonth)
        ->with("invoiceMonths", $this->getInvoiceMonths($company))
        ->with("startingBalance", $startingBalance);
    }


    private function getInvoiceMonths($company) {
    	$invoiceMonths = array();
    	$months = Company::findInvoiceMonths($company->id);
    	foreach($months as $month) {
    		$invoiceMonths[$month->month] = date("F, Y", strtotime($month->month));
    	}
    	return $invoiceMonths;
    }


}