<?php

namespace API;

// REST services for employees
class CompanyController extends \API\APIController {

	public function save(&$company) {
		$ary = \Input::json()->all();
		if (empty($ary['address'])) { 
     return \Response::json(['message' => ['error' => 'Address not set']], 400);	
   }    
   $address = \Address::findOrNew($company->address_id);
   $address->fill(\Input::json()->all()['address']);
   $addressValidation = \Validator::make($address->toArray(),\Address::getAddValidation());
   if ($addressValidation->fails() ) {
     return \Response::json(['message' => $addressValidation->messages()], 400);	
   } 
   $address->save();
   $company->address_id = $address->id;
   $company->save();
   return $company;
 }

 public function beforeView(&$company) {
  $qbCompany = $company;
  $company = \Company::find($company->id);
  if ($company == null) return \Response::json(['meta' => ['message' => "Not Found"]], 404);
  $company->funds = $qbCompany->funds;
		$company->address;	// load it. Funky syntax, I know.
		$company->billingEntries;
		$company->accounts;
		$company->projects;
//		$company->resourceRates;
    $company->resourceAssignments;
    // Slow. sucks. but it works for now...
    foreach($company->resourceAssignments->all() as $r) {
      $r->employee->profile;
    }
    return $company;
  }
  
  public function beforeList(&$companies) {
   return $companies;
 }

 public function getStatement($uuid, $mode = 'weekly', $date = null) {
		if ($date == null) $date = time();
		else $date = strtotime($date);
		$company = \Company::queryBuilderWithFunds()->where('uuid','=',$uuid)->first();
		// Laravel, you kind of suck for this.
		if ($company == null)
			throw new \Exception("Unable to load company.");
		$eqcompany = \Company::find($company->id);
		$eqcompany->funds = $company->funds;
		$balance = 0;
		$startingBalance = 0;
		$currentMonth = null;
		$endDate = date('Y-m-d', $date);
		if ($mode == 'monthly') {
			$startDate = date('Y-m-01', $date);
			$currentMonth = $startDate;
            $endDate = date('Y-m-t', $date);       // t = # of days in a month
          } else {
           $startDate = date('Y-m-d', strtotime($endDate . " -6 weeks"));
         }
         $startingBalance = \Company::getBalanceForCompany($company->id, null, date('Y-m-d', strtotime($startDate." -1 day")));
         $billingEntries = \BillingEntry::getCompanyBillingEntries($company->id, $startDate, $endDate);

         $statementStartDate = strtotime($startDate);
         $statementEndDate = strtotime($endDate);

         foreach ($billingEntries as &$entry) {
          $entry->invoiceStart = max(date('Y-m-d', $statementStartDate), date('Y-m-d', strtotime($entry->entry_date . " -1 week last sunday")));
          $entry->invoiceEnd = min(date('Y-m-d', $statementEndDate), date('Y-m-d', strtotime($entry->entry_date . "-1 week next saturday")));
          if ($entry->amount < 0) {
            $entry->description = $entry->description . " Services";
          }
        }

        $ret = new \stdClass;
        $ret->billingEntries = $billingEntries;
        $ret->statementEndDate = $endDate;
        $ret->statementStartDate = $startDate;
        $ret->company = $eqcompany;
        $ret->company->address;
        $ret->currentMonth = $currentMonth;
        $ret->invoiceMonths = $this->getInvoiceMonths($company);
        $ret->startingBalance = $startingBalance;
        $ret->lastSaturday = date('F d, Y', strtotime($endDate . " last saturday"));

        return \Response::json($ret);
      }


      public function getInvoice($uuid, $startDate = null, $endDate = null) {
		// Convert inputs to dates
       $startDate = ($startDate != null) ? strtotime($startDate) : time();
       $endDate = ($endDate != null) ? strtotime($endDate) : time();

       if ($endDate < $startDate) $endDate = $startDate;

       $startDate = date('Y-m-d', $startDate);
       $endDate = date('Y-m-d', $endDate);
       $endDatePlusOne = date('Y-m-d', strtotime($endDate." +1 day"));

       $company = \Company::queryBuilderWithFunds()->where('uuid','=',$uuid)->first();
       if ($company == null)
        throw new \Exception("Unable to load company.");
      $eqcompany = \Company::find($company->id);
      $eqcompany->funds = $company->funds;
      $billingEntries = \BillingEntry::getInvoiceBillingEntries($company->id, $startDate, $endDate);

      $worklogs = \Worklog::whereHas('project', function ($q) use ($eqcompany) {
        $q->where('company_id','=',$eqcompany->id);
      })->where('date_worked', '>=', $startDate)->where('date_worked', '<', $endDatePlusOne)->orderBy('task', 'asc')->orderBy('date_worked')->get();

      foreach ($worklogs as $log) {
        $log->employee->profile;
      }

      $eqcompany->address;

      $ret = new \stdClass;
      $ret->startDate = $startDate;
      $ret->endDate = $endDate;
      $ret->company = $eqcompany;
      $ret->billingEntries = $billingEntries;
      $ret->worklogs = $worklogs;
      $ret->invoiceMonths = $this->getInvoiceMonths($company);
      return \Response::json($ret);
    }


    private function getInvoiceMonths($company) {
    	$invoiceMonths = array();
    	$months = \Company::findInvoiceMonths($company->id);
    	foreach($months as $month) {
        $m = new \stdClass;
        $m->month = date("F, Y", strtotime($month->month));
        $m->date = $month->month;
        $invoiceMonths[] = $m;
      }
      return $invoiceMonths;
    }


  }



