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
            Northgate Digital <span class="semi-bold">Estimates</span> for <span class="semi-bold"><?=  $company['name']; ?></span>
        </div>
        <div class="content">
            <div class="grid simple ">
                <div class="grid-body">
					<?php foreach ($projects as $project) { ?>
					  <h3>Project:  <span class="semi-bold"><?=  $project['name']; ?></span></h3>
					  <div class="table-responsive">
						<table class="table no-more-tables">
						  <thead>
							<tr>
							  <th>ID</th>
							  <th>Summary</th>
							  <th>Est. time</th>
							  <th>Est. cost</th>
							  <th>Entered by</th>
							  <th>Approve</th>
							  <th>Reject</th>
							</tr>
						  </thead>
						  <tbody >
							<?php foreach ($project['issueHistories'] as $issue) { ?>
							<tr>
							  <td><?=  $issue['issue']['pkey'] ; ?></td>
							  <td><?=  $issue['issue']['summary'] ; ?></td>
							  <td><?=  $issue['estimate'] ; ?></td>
							  <td>$0</td>
							  <td><?=  $issue['created_by'] ; ?></td>
							  <td><a href="http://<?= $_SERVER['SERVER_NAME']; ?>/v3/index.html#/company/estimates/approve?token=<?=  $token ?>&issuehistoryid=<?=  $issue['id'] ?>">Approve</a></td>
							  <td><a href="http://<?= $_SERVER['SERVER_NAME']; ?>/v3/index.html#/company/estimates/reject?token=<?=  $token ?>&issuehistoryid=<?=  $issue['id'] ?>">Reject</a></td>
							</tr>
							<tr>
								<td colspan="7">
									<?=  $issue['issue']['description'] ; ?>
								</td>
							</tr>
							<?php } ?>
						  </tbody>
						</table>
					  </div>
					<?php } ?>
                </div>
            </div>
			<p><a href="http://<?= $_SERVER['SERVER_NAME']; ?>/v3/index.html#/company/estimates/view?token=<?=  $token ?>">View all estimates</a></p>


            <p><h3>Thank you!</h3></p>
            <p>The Northgate Digital Team</p>
        </div>
    </body>
</html>
