@extends('layouts.default')
@section('content')
<ul class="breadcrumb">
    <li>
        <p>TIMESHEETS</p>
    </li> 
  <li><a href="#" class="active">Review Timesheet: {{$employee->profile->first_name}} {{$employee->profile->last_name}}</a>

    </li>
</ul>
<?
?>
<div class="page-title">
    <div class="row">
        <div class="col-md-6 prev">
            <a href="/timesheets/view/{{ $employee->uuid }}/{{ date("Y-m-d", strtotime($timesheet->start_date . " -7 days")) }}">
                <i class="icon-custom-left"></i>
                <h3>Prev: <span class="semi-bold">    <?= date("M d", strtotime($timesheet->start_date . " -7 days")) ?></span>
                </h3>
            </a>
        </div>
        <div class="col-md-6 next">
        <a href="/timesheets/view/<?= $employee->uuid ?>/<?= date("Y-m-d", strtotime($timesheet->start_date . " +7 days")) ?>">
                <h3>Next: <span class="semi-bold">    <?= date("M d", strtotime($timesheet->start_date . " +7 days")) ?></span>
                </h3>
                <i class="icon-custom-right"></i>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="grid simple ">
            <div class="grid-title">
                <h4>
									       Status: <span class="semi-bold"><?= $timesheet->status == "Closed" ? "Approved" : $timesheet->status?></span>
                    <?
                    $today = date('Y-m-d', time());
                    $closingDay = date('Y-m-d', strtotime($timesheet->start_date . " +8 days"));
                    if ($timesheet->status == "Open") {
                        if ($today == $closingDay) {
                            ?><span class="period-info label label-important">Time entry closes today.</span>
                            <?
                        } else {
                            ?><span class="period-info label label-info">Time entry closes <?= date("l, M d", strtotime($timesheet->start_date . " +8 days")) ?> EOB.</span>
                            <?
                        }
                    }
                    ?>
                </h4>
                <?
                if ($today == $closingDay && $timesheet->status == "Open") {
                    ?>
                    <div class = "alert alert-error">
                        <button data-dismiss = "alert" class = "close"></button>
                        This timesheet period closes at the end of business today. If the hours shown below are incorrect they must be updated in JIRA today, before the sun goes down and vampires come out.
                        If the hours are correct, you do not need to do anything further.
                    </div>
                    <? 
                }
                ?>
            </div>
            <div class="grid-body">


                <h3>Period:  <span class="semi-bold"><?= date("M d", strtotime($timesheet->start_date)) ?> - <?= date("M d", strtotime($timesheet->end_date)) ?></span></h3>
                <table class="table no-more-tables">
                    <thead>
                        <tr>
                            <th valign="top">Project</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date)) ?></small><br/>Sun</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +1 day")) ?></small><br/>Mon</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +2 days")) ?></small><br/>Tue</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +3 days")) ?></small><br/>Wed</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +4 days")) ?></small><br/>Thu</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +5 days")) ?></small><br/>Fri</th>
                            <th class="hours" style="width:8%"><small><?= date("m/d", strtotime($timesheet->start_date . " +6 days")) ?></small><br/>Sat</th>
                            <th style="text-align:right;width:6%">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?
                        // Create an array representing the timesheet grid
                        $currentProject = null;
                        $projects = array();
                        foreach ($timesheet->timesheetEntries as $entry) {
                            $dayDateString = date('Y-m-d', strtotime($entry->day));
                            if ($currentProject != $entry->project->name) {
                                $currentProject = $entry->project->name;
                                $projects[$currentProject] = array();
                            }
                            $projects[$currentProject][$dayDateString] = $entry->hours;
                        }
                        $dayTotals = array(0, 0, 0, 0, 0, 0, 0);
                        $totalHours = 0;
                        foreach ($projects as $projectName => $entries) {
                            $projectTotal = 0;
                            ?>
                            <tr>
                                <td class="v-align-middle"><?= $projectName ?></td>
                                <?
                                for ($i = 0; $i < 7; $i++) {
                                    $dayDate = date('Y-m-d', strtotime($timesheet->start_date . " +" . $i . " days"));
                                    if (isset($projects[$projectName][$dayDate])) {
                                        $hours = $projects[$projectName][$dayDate];
                                        $dayTotals[$i] += $hours;
                                        $projectTotal += $hours;
                                        $totalHours += $hours;
                                        echo "<td class=\"hours\">" . intval($hours*4)/4 . "</td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                } ?>
                                <td class="total">{{ $projectTotal }}</td>
                            </tr>
                            <?
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="text-success">
                            <td class="total">TOTAL HOURS</td>
                            <td class="hours">{{ $dayTotals[0] }}</td>
                            <td class="hours">{{ $dayTotals[1] }}</td>
                            <td class="hours">{{ $dayTotals[2] }}</td>
                            <td class="hours">{{ $dayTotals[3] }}</td>
                            <td class="hours">{{ $dayTotals[4] }}</td>
                            <td class="hours">{{ $dayTotals[5] }}</td>
                            <td class="hours">{{ $dayTotals[6] }}</td>
                            <td class="hours grand-total">{{ $totalHours }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<Style>
    th {vertical-align: top}
    th small {font-weight:normal}
    td.total {font-weight:bold;text-align:right}
    tfoot tr {border-top:3px solid #999}
    tfoot td {font-weight:bold;color: #0090d9 !important} /*#0aa699 !important}*/
    th.hours, td.hours {text-align:center}
    td.grand-total {font-size:16px;text-align:right}
    .page-title .next {text-align:right;}
    .page-title .next i {float:right;margin-right:0px;margin-left:12px}
    .period-info {margin-left:30px;}
    .grid-title h4 {line-height:30px;}
    .grid-title .label {position:relative;top:-2px}
</style>
@endsection