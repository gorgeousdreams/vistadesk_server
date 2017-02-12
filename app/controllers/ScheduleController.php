<?php

class ScheduleController extends \BaseController {

	public function getTest() {
		$this->createTimesheetsFromJira();
		dd("OK");
	}

	public function createTimesheetsFromJira() {
		dd("FIXME: DISABLED");
		$tenants = Tenant::all();
		foreach($tenants as $tenant) {
			MultiTenantScope::$tenantId = $tenant->id;
			$c = new TimesheetController();
			$c->createPendingTimesheets();
		}
	}

}