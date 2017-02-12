<?php

class PayrollController extends \BaseController {

	var $testing = true;

    public function getTest() {
        $this->createPayrollPeriods();
    }

    public function createPayrollPeriods() {
        foreach (Tenant::all() as $tenant) $this->createPayrollPeriodForTenant($tenant, date('Y-m-d', strtotime('today')));
    }

    public function createPayrollPeriodForTenant($tenant, $runDate) {
        $period = $this->createNextPayrollPeriod($tenant->id, $runDate);
        dd($period);
        if ($period == null) {
            dd("Payroll period not due yet.");
            return;
        }
    }

    public function createNextPayrollPeriod($tenantId, $runDate) {
        MultiTenantScope::$tenantId = $tenantId;
        // Find the most recent payroll period, and see if a new one is needed.
        $period = PayrollPeriod::getLatestExistingPayrollPeriod($tenantId);        
        $frequency = Setting::getValue('payroll.frequency', $tenantId, 'Weekly'); // Default = Weekly

        // If no periods have yet been created, then create one based on the start date setting
        if ($period == null) {
            $period = PayrollPeriod::getPayrollPeriod($runDate);
            $period->tenant_id = $tenantId;
            $period->saveAll();
        }
        else {
            if (date('w', strtotime($runDate)) == 0) $lastSunday = $runDate;       // If today is sunday, use today
            else $lastSunday = date('Y-m-d', strtotime($runDate . " last sunday"));     // Otherwise use last sunday.

            if ($frequency == "Weekly") $weeks = 1;
            else $weeks = 2;

            $periodStart = date('Y-m-d', strtotime($lastSunday . "-".$weeks." weeks"));

        // Do not create a payroll period if one already exists that overlaps this date range.
            if ($period->end_date >= $periodStart) return null;

            return $period;
        }
    }


} 