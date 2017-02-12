<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">    
    <head>
        <Style>
            body {font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;padding:0px;margin:0px}
            th {vertical-align: bottom;text-align:left;color: #6f7b8a;padding:12px;text-transform:uppercase;font-size:11px}
            h3 {font-size:18px;line-height: 30px;color: #282323;font-weight:normal;margin-top:10px;margin-bottom:0px;}
            table {border-collapse: collapse}
            table td {
                color: #576475;
                font-size: 13px;
                padding: 10px 12px !important;
            }
            table tbody tr td {
                border-top:1px solid #ccc;
            }
            th small {font-weight:normal}
            td.total {font-weight:bold;text-align:right}
            tr.footer td {border-top:3px solid #999}
            tfoot td {font-weight:bold;color: #0090d9 !important} /*#0aa699 !important}*/
            th.hours, td.hours {text-align:center}
            td.grand-total {font-size:16px;text-align:right}
            .page-title .next {text-align:right;}
            .page-title .next i {float:right;margin-right:0px;margin-left:12px}
            .period-info {margin-left:30px;}
            .grid-title h4 {line-height:30px;}
            .grid-title .label {position:relative;top:-2px}
            .semi-bold {
                font-weight: 600;
            }
            .heading {
                background:#0090d9;
                color:#FFF;
                font-weight:bold;
                height:48px;
                line-height:48px;
                font-size:20px;
                font-weight:normal;
                padding-left:20px

            }
            .content {padding:20px;}
        </style>
    </head>
    <body>
        <div class="heading">
            Northgate Digital <span class="semi-bold">Timesheets</span>
        </div>
        <div class="content">
            <?
            if ($timesheet->status == "Closed") {
                ?>
                <h2>This timesheet is now final.</h2>
		<p>The time period is closed and time can no longer be modified. This notification does not imply that the hours below have been reviewed or approved.</p>
                <?
            } else {
                ?>
                <h2>This timesheet will be finalized today at 6:00 PM ET.</h2>
                <p>If the hours below are incorrect, please log into Jira immediately to update them. Your hours for the week
                    shown below cannot be altered after 6PM today.
                </p>
                <?
            }
            ?>

            <div class="grid simple ">
                <div class="grid-body">


                    <h3>Time Period:  <span class="semi-bold"><?= date("M d", strtotime($timesheet->start_date)) ?> - <?= date("M d, Y", strtotime($timesheet->end_date)) ?></span></h3>
                    <h3>Employee:  <span class="semi-bold"><?= $employee->profile->first_name ?> <?= $employee->profile->last_name ?></span></h3>
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
                                            echo "<td class=\"hours\">" . $hours . "</td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                        ?>
                                    <? } ?>
                                    <td class="total"><?= $projectTotal ?></td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="text-success footer">
                                <td class="total">TOTAL HOURS</td>
                                <td class="hours"><?= $dayTotals[0] ?></td>
                                <td class="hours"><?= $dayTotals[1] ?></td>
                                <td class="hours"><?= $dayTotals[2] ?></td>
                                <td class="hours"><?= $dayTotals[3] ?></td>
                                <td class="hours"><?= $dayTotals[4] ?></td>
                                <td class="hours"><?= $dayTotals[5] ?></td>
                                <td class="hours"><?= $dayTotals[6] ?></td>
                                <td class="hours grand-total"><?= $totalHours ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <p><h3>Thank you!</h3></p>
            <p>The Northgate Digital Team</p>
        </div>
    </body>
</html>
