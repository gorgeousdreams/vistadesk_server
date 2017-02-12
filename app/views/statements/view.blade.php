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
            <li><a href="/statements/view/<?=$company->uuid?>/full">All time</a></li>
        </ul>
    </li>
</ul>
<?}?>
@endsection
@section('content')
<div class="row">
    <div class="col-md-11">
        <div class="grid simple">
            <div class="grid-body no-border invoice-body"> <br>
                <div class="pull-left">
                    <img src="/images/logo.png" width="222" class="invoice-logo" alt="">
                    
                </div>
                <div class="pull-right">
                    <?
                    $startDateDisplay = date('F, Y', $statementStartDate);
                    $endDateDisplay = date('F, Y', $statementEndDate);
                    if ($startDateDisplay == $endDateDisplay) $dateDisplay = $endDateDisplay;
                    else {
                        $dateDisplay = "through " . date('F d, Y', strtotime(date('Y-m-d', $statementEndDate) . " last saturday"));
                    }
                    ?>
                    <h2 style="text-align:right">Retainer Statement</h2>
                    <h3 style="text-align:right"><?=$dateDisplay?></h3>
                </div>
                <div class="clearfix"></div>
                <br>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-9">
                        <h4 class="semi-bold">Client</h4>
                        <address>
                            <strong><?= $company->name ?></strong><br>
		      <?if (!empty($company->address)) {?>
                            {{denull($company->address->street1)}} {{denull($company->address->street2)}}<br>
                            {{denull($company->address->city)}}{{denull($company->address->city) != "" ? "," : ""}} {{denull($company->address->state)}} {{denull($company->address->postal)}}<br>
							<?}?>
                        </address>

                    </div>
                    <div class="col-md-3"> <br>
                        <div>
                            <div class="pull-left"> STATEMENT DATE : </div>
                            <div class="pull-right"> <?= date('m/d/Y') ?> </div>
                            <div class="clearfix"></div>
                        </div>
                        <br>
                        <div class="well well-small green">
                            <div class="pull-left"> Available Funds: </div>
                            <div class="pull-right"> <?= formatCurrency($company->funds) ?></div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:60px" class="unseen text-center">DATE</th>
                            <th class="text-left">DESCRIPTION</th>
                            <th class="account-col text-left">ACCOUNT</th>
                            <th style="width:100px" class="text-right">CREDITS</th>
                            <th style="width:100px" class="text-right">DEBITS</th>
                            <th style="width:110px" class="text-right">BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        if ($startingBalance != 0) {
                            ?>
                            <tr>
                                <td class="unseen text-center">
                                    <?=date("m/d/Y", $statementStartDate)?>
                                </td>
                                <td>Starting Balance</td> 
                                <td class="account-col"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"><?= formatCurrency($startingBalance) ?></td>
                            </tr>
                            <?
                        }
                        $runningBalance = $startingBalance;
                        $debitTotal = 0;
                        $creditTotal = 0;
                        ?>
                        <?
			$byProject = array();
                        foreach ($billingEntries as $entry) {
			  if ($entry->amount <= 0 && $entry->account_name != null) {
			    if (!isset($byProject[$entry->account_name])) $byProject[$entry->account_name] = $entry->amount;
			    else $byProject[$entry->account_name] += $entry->amount;
					       }
                            $entry->amount = intval($entry->amount);
                            $runningBalance += $entry->amount;
                            $invoiceStart = max(date('Y-m-d', $statementStartDate), date('Y-m-d', strtotime($entry->entry_date . " -1 week last sunday")));
                            $invoiceEnd = min(date('Y-m-d', $statementEndDate), date('Y-m-d', strtotime($entry->entry_date . "-1 week next saturday")));

                            $invoiceLinkBegin = "";
                            $invoiceLinkEnd = "";
                            if ($entry->amount < 0) $debitTotal += $entry->amount;
                            else $creditTotal += $entry->amount;
                            $description = $entry->description;
                            $entryDate = $entry->entry_date;
                            if ($entry->entry_type == "invoice") {
                                $invoiceLinkBegin = "<a href=\"/statements/invoice/" . $company->uuid . "/" . $invoiceStart . "/" . $invoiceEnd . "\">";
                                $invoiceLinkEnd = "</a>";
                                $description = $description . " Services";
                                $entryDate = $invoiceEnd;
                            }


                            ?>
                            <tr>
                                <td class="unseen text-center"><?=$invoiceLinkBegin?><?= date("m/d/Y", strtotime($entryDate)) ?><?=$invoiceLinkEnd?></td>
                                <td><?=$invoiceLinkBegin?><?= $description ?><?=$invoiceLinkEnd?></td>
                                <td class="account-col"><?=$invoiceLinkBegin?><?if ($entry->amount <= 0 && $entry->account_name != null) {echo $entry->account_name;}?><?=$invoiceLinkEnd?></td>
                                <td class="text-right"><?=$invoiceLinkBegin?><?= ($entry->amount >= 0) ? formatCurrency($entry->amount) : "" ?><?=$invoiceLinkEnd?></td>
                                <td class="text-right"><?=$invoiceLinkBegin?><?= ($entry->amount < 0) ? formatCurrency($entry->amount, true, true, false) : "" ?><?=$invoiceLinkEnd?></td>
                                <td class="text-right"><?=$invoiceLinkBegin?><?= formatCurrency($runningBalance) ?><?=$invoiceLinkEnd?></td>
                            </tr>
                            <? } ?>
                            <tr>
                                <td colspan="2" >
                                </td>
                                <td class="text-right"><div class="well well-small green total"><strong>Totals</strong></div></td>
                                <td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($creditTotal) ?></strong></td>
                                <td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($debitTotal) ?></strong></td>
                                <td class="text-right"><strong style="padding-top:13px;display:block"><?= formatCurrency($runningBalance) ?></strong></td>
                            </tr>

			  <tr style="border:none">
<td style="border:none" colspan="6"><h3>Totals by Account</h3></td>
			  </tr>
<?php
			  foreach($byProject as $account => $amt) {
?>


									<tr class="no-border">
										<td colspan=3>
										</td>
										<td colspan="2" class="text-right"><strong><?=$account?></strong></div></td>
										<td class="text-right"><strong style="display:block"><?= formatCurrency(-1*$amt) ?></strong></td>
									</tr>
<?
			}
?>

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
    <style>
        td .well.green.total {float:right;width:120px !important;}
    </style>
    @endsection