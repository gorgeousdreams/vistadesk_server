<?php

class CoreController extends \BaseController {

  public function getSanitize() {
    $idx = 0;
    $names = DB::select(DB::raw("select companyname from sanitize_company_names"));
    foreach(Company::all() as $c) {
      $c->name = $names[$idx]->companyname;
      $c->save();
      $idx++;
    }

    DB::statement(DB::raw("update projects, companies set projects.name = concat(companies.name,' ',(select project from sanitize_projectnames order by rand() limit 1)) where companies.id = projects.company_id;"));
    /*    $projects = DB::select(DB::raw("select project from sanitize_projectnames"));
    foreach (Project:all() as $p) {
      $p->name = $p->company->name." ".$projects
    }*/
    dd("OK");
  }

  public function getProjectedSpend($startDate = null, $endDate = null) {
    if ($startDate == null) $startDate = date('Y-m-d');
    if ($endDate == null) $endDate = date('Y-m-d', strtotime($startDate." +2 weeks"));
    $companies = DB::select(DB::raw("
      select distinct(Assignment.company_id) as id from resource_assignments Assignment
      WHERE Assignment.start_date <= now() and Assignment.end_date >= now()
      AND Assignment.allocation > 0
      "));

    $list = array();

    foreach($companies as $company) {
      $c = \Company::find($company->id);
      $c->projectedSpend = \Company::getProjectedSpendForCompany($company->id, $startDate, $endDate);
      $list[] = $c;
    }
    dd($list);
  }

  public function getImport() {
   MultiTenantScope::$tenantId = "all";
   $projects = Jira::getProjectData();
   $companies = Jira::getCompanyList();
   $employees = Jira::getEmployees();

        // Load the companies, and create a default account for each.

   foreach ($companies as $jiraId => $name) {
     $check = Company::where('external_id','=',$jiraId)->first();
     if ($check == null) {
      $company = new Company();
      $company->name = $name;
      $company->tenant_id = 1;
      $company->external_id = $jiraId;
      $company->save();
      $account = new Account();
      $account->company_id = $company->id;
      $account->name = "Support and Maintenance";
      $account->save();
    }
  }

        // Load the projects

  foreach ($projects as $p) {

   $check = Project::where('external_id','=',$p->projectID)->first();
   if ($check == null) {
    $company = Company::where('external_id','=',$p->companyID)->first();
    if ($company == null) {
     throw new Exception("Could not find company for project ".$p->pname);
   }                
   if ($company->accounts->count() < 1) {
     throw new Exception("No account available for company");
   }
   $account = $company->accounts->first();
   $project = new Project();
   $project->name = $p->pname;
   $project->external_id = $p->projectID;
   $project->company_id = $company->id;
   $project->account_id = $account->id;
   $project->save();
 }
}

        // Load employees

foreach ($employees as $e) {
 $check = Employee::where('external_id','=',$e->ID)->first();
 if ($check == null) {
  if ($e->first_name == "" || strpos(strtolower($e->last_name), "user") !== false || strpos(strtolower($e->email_address), "disabled") !== false) {
   continue;
 }

 $profile = new Profile();
 $profile->first_name = $e->first_name;
 $profile->last_name = $e->last_name;
 $profile->email = $e->email_address;
 $profile->save();

 $employee = new Employee();
 $employee->profile_id = $profile->id;
 $employee->external_id= $e->ID;
 $employee->tenant_id = 1;
 $employee->status = 'Active';
 $employee->save();
}
}

return Redirect::to("/");

}
}