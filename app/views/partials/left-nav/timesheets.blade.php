<?
$active = Request::is('timesheets*');
$emp = null;
if (!empty($employee)) $emp = $employee;
else if (!empty(Auth::user()->employee)) {
  $emp = Auth::user()->employee;
}
if ($emp != null || isset($timesheet)) {?>
<ul>
	<li {{ $active ? 'class="active open"' : ''}}> <a href="javascript:;"> <i class="icon-custom-form"></i> <span class="title">Timesheets</span> 
	<span class="arrow {{ $active ? "open" : ""}}"></span> </a>
		<ul class="sub-menu" style="background:none">
			<?
			if (date('w', time()) == 0)
				$lastSunday = date('Y-m-d', time());
			else
				$lastSunday = date('Y-m-d', strtotime('last sunday', time()));

			for ($i = 0; $i < 12; $i++) {
				$startDateObj = strtotime($lastSunday . " -" . $i . " weeks");
				$startDate = date('Y-m-d', $startDateObj);
				$endDateObj = strtotime($startDate . " +6 days");
				$period = date("M d", $startDateObj) . " - " . date("M d", $endDateObj);
				$uuid = $emp->uuid;
				if (isset($timesheet)) $uuid = $timesheet->employee->uuid;
				?>
				<li> 
					<a <?= ((isset($timesheet) && $startDate == $timesheet->start_date)) ? "style=\"font-weight:bold\"" : "" ?> href="/timesheets/view/{{ $uuid }}/{{ $startDate }}"> {{ $period }} </a> 
				</li>
				<? 
			} 
			?>
		</ul>
	</li>
</ul>
<?}?>