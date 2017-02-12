<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">    
<head>
	<Style>
		table td {
			color: #576475;	font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;
		}


	</style>
</head>
<body style="font-family:Helvetica Neue,Helvetica,Arial,sans-serif;font-size:12px;padding:0px;margin:0px;background-color:#e5e9ec">
	<div class="heading" style="background:#007081;color:#FFF;font-weight:bold;height:48px;line-height:48px;font-size:20px;font-weight:normal;padding-left:50px">
		Northgate Digital <span style="font-weight:bold">Statement</span>
	</div>
	<div class="content" style="padding:20px;padding:20px;max-width:1020px">
		<div style="padding-left:30px">
			<?
			$nameParts = explode(" ", $company->contact_name);
			if ($nameParts == null || sizeof($nameParts) == 0) $displayName = "valued Client";
			else $displayName = $nameParts[0];
			?>
			<p>
				Dear <?=$displayName?>,<br/><br/>
				Below please find a statement of last week's development and operations for <?=$company->name?>. 
				For a full billing statement or to view past invoices and account history, you can now <a style="color:#007081;font-weight:bold;" href="http://webdesk.ngdcorp.com/statements/view/<?=$company->uuid?>">view your account details online</a>.
			</p>
			<p>
				If you should have any questions regarding this statement or the services provided, please contact Cindy Weick at <a href="mailto:cweick@ngdcorp.com">cweick@ngdcorp.com</a> or by phone at 267-250-6460. Thank you!<br/><br/>
				- The Northgate Digital team
			</p>
			<br/>
		</div>
		<div class="row">
			<div class="col-md-11">
				<div class="grid simple">
					<div class="grid-body invoice-body" style="border:none !important;background:#FFF;padding:30px;"> <br>
						<div class="pull-left" style="float: left !important;">
							<img src="http://webdesk.ngdcorp.com/images/logo.png" width="220" height="73" class="invoice-logo" alt="Northgate Digital">

						</div>
						<div class="pull-right" style="float: right !important;">
							<h2 style="text-align:right">Activity Statement</h2>
							<h3 style="font-size:18px;line-height: 30px;color: #282323;font-weight:normal;margin-top:10px;margin-bottom:0px;"><?=date("M d", strtotime($startDate))?> - <?=date("M d, Y", strtotime($endDate))?></h3>
						</div>
						<div class="clearfix" style="clear:both"></div>
						<div class="no-print">
							<br>
							<br>
							<br>
						</div>
						<div class="row">
							<div class="col-md-8 invoice-client" style="float:left;width:60%;">
								<h4 class="semi-bold">Client</h4>
								<address>
									<strong><?= $company->name ?></strong><br>
									<?if (isset($company->address)) {?>
									<?=denull($company->address->street1)?> <?=denull($company->address->street2)?><br>
									<?=denull($company->address->city)?><?=denull($company->address->city) != "" ? "," : ""?> <?=denull($company->address->state)?> <?=denull($company->address->postal)?><br>
									<?}?>
								</address>

							</div>
							<div class="col-md-4 invoice-retainer" style="float:right;max-width:240px;width:40%;"> <br>
								<div>
									<div class="pull-left" style="float: left !important;"> STATEMENT DATE : </div>
									<div class="pull-right" style="float: right !important;"> <?= date('m/d/Y') ?> </div>
									<div class="clearfix" style="clear:both"></div>
								</div>
								<br>
								<div class="well well-small green" style="background-color: #d1dade;background-image: none;border: medium none;border-radius: 3px;box-shadow: none !important;padding: 13px;width: auto;background-color: #00a2bb;border: medium none;color: #ffffff;">
									<div style="text-align:right"><b>RETAINER ACCOUNT</b></div>
									<div class="pull-left" style="float: left !important;"> Available Funds: </div>
									<div class="pull-right" style="float: right !important;"> <?= formatCurrency($company->funds) ?></div>
									<div class="clearfix" style="clear:both"></div>
								</div>
							</div>
						</div>
						<table style="border-collapse:collapse;width:100%;"><tbody>
							<?
							$runningBalance = 0;
							$accountTotal = 0;
							$thisAccount = null;
							foreach ($billingEntries as $entry) {
								if ($thisAccount != $entry->accountName) {

									if ($thisAccount != null) {
										?>
										<tr>
											<td colspan="3" style="text-align:right;color: #576475;	font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;"><div style="background-color: #d1dade;background-image: none;border: medium none;border-radius: 3px;box-shadow: none !important;display:inline-block;padding: 13px;width: auto;background-color: #00a2bb;border: medium none;color: #ffffff;margin-bottom:0px !important;" class="well well-small green"><strong><?=$thisAccount?> Total</strong></div></td>
											<td style="text-align:right;color: #576475;	font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;"><strong style="padding-top:2px;display:block"><?= formatCurrency($accountTotal) ?></strong></td>
										</tr>
										<?
									}
									$accountTotal = 0;
									$thisAccount = $entry->accountName;
									?></tbody></table>
									<h3 style="font-size:18px;line-height: 30px;color: #282323;font-weight:normal;margin-top:10px;margin-bottom:0px;"><?=$thisAccount?></h3>
									<table class="table" style="border-collapse:collapse;width:100%;">
										<thead>
											<tr>
												<th class="unseen text-left" style="vertical-align: bottom;text-align:left;color: #6f7b8a;padding:12px;text-transform:uppercase;font-size:11px;">DESCRIPTION</th>
												<th style="vertical-align: bottom;text-align:left;color: #6f7b8a;padding:12px;text-transform:uppercase;font-size:11px;width:100px;text-align:right" >UNITS</th>
												<th style="vertical-align: bottom;text-align:left;color: #6f7b8a;padding:12px;text-transform:uppercase;font-size:11px;width:100px;text-align:right">PRICE</th>
												<th style="vertical-align: bottom;text-align:left;color: #6f7b8a;padding:12px;text-transform:uppercase;font-size:11px;width:100px;text-align:right">TOTAL</th>
											</tr>
										</thead>
										<tbody>
											<?}

											$runningBalance += $entry->total;
											$accountTotal += $entry->total;

											?>
											<tr>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;"><?= $entry->resource ?>: <?=$entry->first_name?> <?=$entry->last_name?></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right"><?= $entry->hours ?></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right"><?= formatCurrency($entry->rate) ?></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right"><?= formatCurrency($entry->total) ?></td>
											</tr>
											<? } ?>
											<tr>
												<td colspan="3" style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right"><div style="background-color: #d1dade;background-image: none;border: medium none;border-radius: 3px;box-shadow: none !important;display:inline-block;padding: 13px;width: auto;background-color: #00a2bb;border: medium none;color: #ffffff;margin-bottom:0px !important;" class="well well-small green"><strong><?=$thisAccount?> Total</strong></div></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right"><strong style="padding-top:2px;display:block"><?= formatCurrency($accountTotal) ?></strong></td>
											</tr>
											<tr style="border:none !important;">
												<td colspan="3" style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right;border:none !important;"><div style="display:inline-block;padding-top:13px"><strong>Starting Balance</strong></div></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;text-align:right;border:none"><strong style="padding-top:13px;display:block"><?= formatCurrency($startingBalance) ?></strong></td>
											</tr>
											<tr style="border:none !important;">
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;">
												</td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;text-align:right" colspan="2"><strong>Total Services</strong></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;text-align:right"><strong style="display:block"><?= formatCurrency($runningBalance*-1) ?></strong></td>
											</tr>
											<tr style="border:none !important;">
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;">
												</td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;text-align:right" colspan="2"><strong>Ending Balance</strong></td>
												<td style="color: #576475;font-size: 13px;padding: 10px 12px !important;border-top:1px solid #ccc;border:none !important;text-align:right"><strong style="display:block"><?= formatCurrency($company->funds) ?></strong></td>
											</tr>
										</tbody>
									</table>
									<h5 class="text-center semi-bold" style="display:block;font-size:14px;font-weight:bold;text-align:center;"></h5>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
			</html>



