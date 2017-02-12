<?php

/*
             ;
               .<!
              ;!!
            ;!!!!>
           !!!!!!!;                      .,,,,,,.
          !!!!!!!!!                ,ce$$$$$$$$$$$$P%=
         `!!!!'!!!!>           ,cP???$$$$$$PPPPP" ;<!;, .
         `!!!!  <!!>        ,cP" ,;;;, .;;;;;!  <!! C`'> "c
          <!!! >'!!       ,$P" ;!!'` !!>!!!!(,;!!',d$$b,\. .
           `!!>  !>     ,'".> <!',c$b.`!!!!!!!!' z$"""3P !!!!!;.
  `>,       `'!; !      ;<!!;!!'z$"  ")`'``      ^"   ,F !!!!;,`'
   <!!;;,      ` ;<' ;!';<!!!!! ?$.   ".cc$$$$$$$$bc,." `!!(`''!-
   `!!!!!!;;;;; ;!!;' <!!!'!!!!!."".zd$$$$$$$$$$$$$$$$$c. ``''!;,
    <!!!;!!!!!> !!!!! `,;<!'''`` z$$$$$$$$$$$$$$$$$$$$$$$$c,c,.  `
     !`!!!!!!;`  ``  >'` .,ce$"z$$$$$$$$$$$$$$$$$$$$$$$$$$$$d$$$$c.
      `<;;;;<;`      ,cd$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$P""""?$$$c.
        ```       z$$$$$$$$$$$??$$$$$$$$$$$$$$$$$$$$$$$P".,r<MM  `$$$$b.
                z$$$$$$$$P",n.nmn,""???$$$$$$$$PPP"" .,nMMP ?P" " $$$$$$.
               d$$$$$$$$"4>?ML`NMMT4beeuueuueuueedMMMMMCLnn."MMP 4$$$$$$$
              4$$$$$$$$$, C,um. "MMMMPP"""""`````""""""TTTTT "".z$$$$$$$$
     .,,;;;!> `$$$$$$$$$$."?4MMb`" .,,ce$$$$$$$$$$$$$$$$eee$$$$$$$$$$$$$$
,;!!!!!!!!!!!  ?$$$$$$$$$$b,,,,ce$$$$$$$$$$$$$?????????$$$$$$$$$$$$$$$$P'
!!!!!!!!!''```  "$$$$$$$$$$$$$$$$$$$$$$$$$bhbhU$$$$$$$$$$$$$$$$$$$$$$$"
!!!'``.=ce$$$$$c. "$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$P"
cece$cecc,. ""??""               `""""??$$$$$$$$$$$$$$$$$$$$$$$$P"  ,<!!;,
$$$PP"" ,cecececec,,.  ""??$$$$$$$%=-     `"""""""???$$$$$PP""  ..``<!!!!!
P" .c$$$$$$$$$$$$$$$$$$$c,. `"?".,cd$$$$$$$$$$$$ecc,.      .cd$$$$$c. `!''
.d$$$$$$$$$$$$$$$$$$$$$$$$$$".c$$$$$$$$$$$$$$$$$$$$$$$$$c,.  ""?$$$$P" .,c
$$$$$$$$$$$$$$$$$$$$$$$$$$".d$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ec   "".c$$$$$
$$$$$$$$$$$$$$$$$$$$$$$$$".$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$% .$$$$$$$$
$$$$$$$$$$$$$$$$$$$$$$$$".$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$P" d$$$$$$$$$
$$$$$$$$$$$$$$$$$$$$$$$F.$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$" .$$$$$$$$$$$
$$$$$$$$$$$$$$$$$$$$$$P $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$" d$$$$$$$$$$$$

Some fuckin SQL ninja shit right here, if I do say so myself!

 */
class BillingEntry extends AppModel
{

  protected $fillable = array('amount','company_id','account_id','description','invoice_id','created_at','void');
	public $adminSettings = array(
				      'formats'=>array('amount'=>'currency'),
				      'edit'=>array(
						    'fields'=>array('amount', 'company_id', 'account_id', 'created_at', 'description', 'void')
						    )
		);


	public function account() {
		return $this->belongsTo('Account');
	}

	public function company() {
		return $this->belongsTo('Company');
	}

	static public function getBillingEntriesForMonth($month, $companyId) {
		return DB::select(DB::raw("SELECT Account.company_id, Account.name as account_name, Account.id as account_id, floor(sum(te.hours*te.rate)) as amount
			FROM timesheet_entries te
			LEFT JOIN projects p on p.id = te.project_id
			LEFT JOIN accounts Account on Account.id = p.account_id
			WHERE p.company_id = :companyId
			AND month(te.day) = :month
			GROUP BY Account.id"), array('companyId' => $companyId, 'month' => $month));
	}

	static public function getCompanyBillingEntries($companyId, $startDate, $endDate) {
		return DB::select(DB::raw("
			SELECT * from (
				SELECT
				'memo' as entry_type, 
				created_at as entry_date, 
				account_id,
				accounts.name as account_name,
				amount, 
				description, 
				null as groupcol 
				FROM billing_entries 
				LEFT JOIN accounts on accounts.id = billing_entries.account_id
				WHERE billing_entries.company_id = :memoCompanyId 
                                AND billing_entries.void = 0 
				AND created_at >= :memoStartDate
				AND created_at < DATE_ADD(:memoEndDate, INTERVAL 1 DAY)

				UNION

				SELECT 
				'invoice' as entry_type,
				DATE_ADD(day, INTERVAL (9-DAYOFWEEK(day)) DAY) as entry_date,
				a.id as account_id,
				a.name as account_name,
				floor(sum(te.hours*te.rate))*-1 as amount,
				concat(
					DATE_FORMAT( GREATEST ( :rangeStartDate, DATE_ADD(day, INTERVAL (1-DAYOFWEEK(day)) DAY) ) , '%b %e' ),' - ',
					DATE_FORMAT( LEAST ( :rangeEndDate, DATE_ADD(day, INTERVAL (7-DAYOFWEEK(day)) DAY) ) , '%b %e')
					) as description,
		yearweek(day) as groupcol
		FROM timesheet_entries te
		LEFT JOIN projects p on p.id = te.project_id
		LEFT JOIN accounts a on a.id = p.account_id
		WHERE p.company_id = :invoiceCompanyId
		AND te.day >= :invoiceStartDate and te.day < DATE_ADD(:invoiceEndDate, INTERVAL 1 DAY)
		GROUP BY groupcol, a.id 
		) as Entry 
ORDER BY Entry.entry_date, Entry.account_name           
"), array('memoCompanyId'=>$companyId, 'memoStartDate'=>$startDate, 'memoEndDate'=>$endDate, 'rangeStartDate'=>$startDate, 'rangeEndDate'=>$endDate, 'invoiceCompanyId'=>$companyId, 'invoiceStartDate'=>$startDate, 'invoiceEndDate'=>$endDate));
}


static public function getInvoiceBillingEntries($companyId, $startDate, $endDate) {
	return DB::select(DB::raw("
		SELECT 
		Account.name as accountName,
		Resource.name as resource,
		Profile.first_name,
		Profile.last_name,
		sum(TimesheetEntry.hours) as hours,
		min(TimesheetEntry.rate) as minRate,
		max(TimesheetEntry.rate) as maxRate,
		TimesheetEntry.rate as rate,
		(floor(sum(TimesheetEntry.hours*TimesheetEntry.rate))) as total
		FROM timesheet_entries TimesheetEntry
		INNER JOIN timesheets Timesheet on Timesheet.id = TimesheetEntry.timesheet_id
		INNER JOIN employees Employee on Employee.id = Timesheet.employee_id
                INNER JOIN profiles Profile on Profile.id = Employee.profile_id
		INNER JOIN resources Resource on Resource.id = Employee.resource_id
		INNER JOIN projects Project on Project.id = TimesheetEntry.project_id
		LEFT JOIN accounts Account on Account.id = Project.account_id
		WHERE Project.company_id = :companyId
		AND TimesheetEntry.day >= :startDate AND TimesheetEntry.day < DATE_ADD(:endDate, INTERVAL 1 DAY)
		GROUP BY Account.id, Resource.id, Employee.id"), array('companyId' => $companyId, 'startDate' => $startDate, 'endDate' => $endDate));

}


}