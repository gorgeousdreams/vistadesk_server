@extends('layouts.default')
@section('nav-top')
<?if (isset($invoiceMonths)) {?>
<p class="menu-title">STATEMENTS</p>
<ul>
	<li class="active open">
		<ul class="sub-menu" style="background:none">
			<li><a <?=(!isset($currentMonth)) ? "style=\"font-weight:bold\"" : ""?> href="/statements/view/<?=$company->uuid?>/">Last Six Weeks</a></li>
			<?foreach($invoiceMonths as $month=>$desc) {?>
			<li><a <?=(isset($currentMonth) && $currentMonth == $month)? "style=\"font-weight:bold\"" : ""?> href="/statements/view/<?=$company->uuid?>/monthly/<?=$month?>"><?=$desc?></a></li>
			<?}?>
		</ul>
	</li>
</ul>
<?}?>
@endsection
@section('content')
<div class="row print-page">
	<div class="col-md-11">
		<div class="grid simple">
			<div class="grid-body no-border invoice-body"> <br>
				<div class="pull-left">
					<img src="/images/logo.png" width="222" class="invoice-logo" alt="">

				</div>
				<div class="pull-right" style="text-align:right">
					<h2 style="text-align:right">Services Rendered</h2>
					<h3><?=date("M d", strtotime($startDate))?> - <?=date("M d, Y", strtotime($endDate))?></h3>
				</div>
				<div class="clearfix"></div>
				<div class="no-print">
					<br>
					<br>
					<br>
				</div>
				<div class="row">
					<div class="col-md-8 invoice-client">
						<h4 class="semi-bold">Client</h4>
						<address>
							<strong><?= $company->name ?></strong><br>
  <?if (!empty($company->address)) {?>
							{{denull($company->address->street1)}} {{denull($company->address->street2)}}<br>
							{{denull($company->address->city)}}{{denull($company->address->city) != "" ? "," : ""}} {{denull($company->address->state)}} {{denull($company->address->postal)}}<br>
				    <?}?>
						</address>

					</div>
					<div class="col-md-4 invoice-retainer"> <br>
						<div>
							<div class="pull-left"> STATEMENT DATE : </div>
							<div class="pull-right"> {{ date('m/d/Y') }} </div>
							<div class="clearfix"></div>
						</div>
						<br>
						<div class="well well-small green">
							<div style="text-align:right"><b>RETAINER ACCOUNT</b></div>
							<div class="pull-left"> Available Funds: </div>
							<div class="pull-right"> {{ formatCurrency($company->funds) }} </div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<table style="width:100%">
					<?
					$runningBalance = 0;
					$accountTotal = 0;
					$thisAccount = null;
					if (sizeof($billingEntries) == 0) {
						?>
						<tr>
							<td colspan="3" style="text-align:center">No activity</td>
							<td style="width:100px">&nbsp;</td>
						</tr>
						<?
					} else 
					foreach ($billingEntries as $entry) {
						if ($thisAccount != $entry->accountName) {

							if ($thisAccount != null) {
								?>
								<tr>
									<td colspan="3" class="text-right"><div style="display:inline-block" class="well well-small green"><strong><?=$thisAccount?> Total</strong></div></td>
									<td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($accountTotal) ?></strong></td>
								</tr>
								<?
							}
							$accountTotal = 0;
							$thisAccount = $entry->accountName;
							?></table>
							<h3><?=$thisAccount?></h3>
							<table class="table">
								<thead>
									<tr>
										<th class="unseen text-left">DESCRIPTION</th>
										<th style="width:100px" class="text-right">UNITS</th>
										<th style="width:200px" class="text-right">PRICE</th>
										<th style="width:100px" class="text-right">TOTAL</th>
									</tr>
								</thead>
								<tbody>
									<?}

									$runningBalance += $entry->total;
									$accountTotal += $entry->total;
									$displayRate = formatCurrency($entry->rate);
									if ($entry->minRate != $entry->maxRate) {
									  $displayRate = formatCurrency($entry->minRate) . " - ".formatCurrency($entry->maxRate);
									}
									?>
									<tr>
										<td><?= $entry->resource ?>: {{$entry->first_name}} {{$entry->last_name}}</td>
										<td class="text-right"><?= $entry->hours ?></td>
										<td class="text-right"><?= $displayRate ?></td>
										<td class="text-right"><?= formatCurrency($entry->total) ?></td>
									</tr>
									<? } ?>
									<tr>
										<td colspan="3" class="text-right"><div style="display:inline-block" class="well well-small green"><strong><?=$thisAccount?> Total</strong></div></td>
										<td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($accountTotal) ?></strong></td>
									</tr>
									<tr class="no-border">
										<td colspan="3" class="text-right"><div style="display:inline-block;padding-top:13px"><strong>Total Services</strong></div></td>
										<td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($runningBalance) ?></strong></td>
									</tr>
									<tr class="no-border">
										<td>
										</td>
										<td colspan="2" class="text-right"><strong>Paid by Retainer</strong></div></td>
										<td class="text-right"><strong style="display:block"><?= formatCurrency($runningBalance*-1) ?></strong></td>
									</tr>
									<tr class="no-border">
										<td>
										</td>
										<td colspan="2" class="text-right"><strong>Amount Due</strong></div></td>
										<td class="text-right"><strong style="display:block"><?= formatCurrency(0) ?></strong></td>
									</tr>
								</tbody>
							</table>
							<h5 class="text-center semi-bold">Thank you for your business!</h5>
						</div>
					</div>
				</div>
				<div class="col-md-1">
					<div class="invoice-button-action-set">
						<p>
							<button class="btn btn-primary" type="button" onclick="window.print()"><i class="fa fa-print"></i></button>
						</p>
					</div>
				</div>
			</div>

			<?if (!empty($worklogs)) {?>

			<div class="row">
				<div class="col-md-11">
					<div class="grid simple">
						<div class="grid-body no-border invoice-body" style="width:100%"> <br>
							<div class="pull-left">
							&nbsp;
							</div>
							<div class="pull-right" style="text-align:right">
								<h2 style="text-align:right">Hourly Report</h2>
								<h3><?=date("M d", strtotime($startDate))?> - <?=date("M d, Y", strtotime($endDate))?></h3>
							</div>
							<div class="clearfix"></div>
							<div class="no-print">
								<br>
								<br>
								<br>
							</div>
							<div class="row">
								<div class="col-md-8 invoice-client">
									<h4 class="semi-bold">Client</h4>
									<address>
										<strong><?= $company->name ?></strong><br>
						  <?if (!empty($company->address)) {?>
										{{denull($company->address->street1)}} {{denull($company->address->street2)}}<br>
										{{denull($company->address->city)}}{{denull($company->address->city) != "" ? "," : ""}} {{denull($company->address->state)}} {{denull($company->address->postal)}}<br>
										    <? }?>
									</address>

								</div>
								<div class="col-md-4 invoice-retainer"> <br>
									<div>
										<div class="pull-left"> REPORT DATE : </div>
										<div class="pull-right"> {{ date('m/d/Y') }} </div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
							<table style="width:100%">
								<?
								$thisID = null;
								foreach ($worklogs as $log) {
									if ($thisID != $log->external_id) {
										$thisID = $log->external_id;
										?></table>
										<h3 style="margin-top:30px">{{$log->external_id}}: {{$log->task}}</h3>
										<table class="table">
											<thead>
												<tr>
													<th style="width:150px">DATE</th>
													<th style="width:150px">RESOURCE</th>
													<th class="unseen text-left">DESCRIPTION</th>
													<th style="width:100px" class="text-right">HOURS</th>
												</tr>
											</thead>
											<tbody>
												<? } ?>
												<tr>
													<td>{{ date('m/d/Y', strtotime($log->date_worked)) }}</td>
													<td>{{ $log->employee->profile->first_name }} {{ $log->employee->profile->last_name }}</td>
													<td>{{ ucfirst($log->description) }}</td>
													<td class="text-right">{{ $log->hours }}</td>
												</tr>
												<? } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-md-1">
								<div class="invoice-button-action-set">
									<p>
										<button class="btn btn-primary" type="button" onclick="window.print()"><i class="fa fa-print"></i></button>
									</p>
								</div>
							</div>
						</div>




						<? } ?>



						<style>
							tr.no-border td {border:none !important;}                
							@media print {
								.no-print {display:none !important}
								h4,h5 {margin:0px !important;}
								.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
									padding:4px !important;
								}
								.invoice-client, .invoice-retainer {width:45% !important; float:left !important;}
								.invoice-retainer {float:right !important;}
								.invoice-retainer > .well { padding:0px !important;}
								table td.text-right > div.well-small.green {margin-bottom:0px !important;}
							}
						</style>



						@endsection