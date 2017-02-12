<div class="row">
    <div class="col-md-4 col-vlg-3 col-sm-6">
        <h3>Last 30 Days</h3>
    </div>
</div>
<div class="row">    

    <div class="col-md-4 col-vlg-3 col-sm-6">

        <div class="tiles green added-margin  m-b-20">
            <div class="tiles-body">
                <div class="controller"> <a class="reload" href="javascript:;"></a> <a class="remove" href="javascript:;"></a> </div>
                <div class="tiles-title text-black">INCOME</div>
                <div class="widget-stats">
                    <div class="wrapper transparent"> 
                        <span class="item-title">Revenue</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->revenue->grossRevenue)}}</span>
                    </div>
                </div>
                <div class="widget-stats">
                    <div class="wrapper transparent">
                        <span class="item-title">COGS</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->cogs->cogs)}}</span> 
                    </div>
                </div>
                <div class="widget-stats ">
   <div class="wrapper last">
                        <span class="item-title">Retainers</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->retainerBalance->balance)}}</span> 
                    </div>
                </div>
                <!--
                <div style="width:90%" class="progress transparent progress-small no-radius m-t-20">
                    <div data-percentage="64.8%" class="progress-bar progress-bar-white animate-progress-bar" style="width: 64.8%;"></div>
                </div>
                <div class="description"> <span class="text-white mini-description ">4% higher <span class="blend">than last month</span></span></div>
            -->
        </div>			
    </div>	
</div>

<div class="col-md-4 col-vlg-3 col-sm-6">
    <div class="tiles blue added-margin  m-b-20">
        <div class="tiles-body">
            <div class="controller"> <a class="reload" href="javascript:;"></a> <a class="remove" href="javascript:;"></a> </div>
            <div class="tiles-title text-black">EXPENSES </div>
            <div class="widget-stats">
                <div class="wrapper transparent"> 
                    <span class="item-title">Non-Billables</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->nonbilledButPaidAmount)}}</span>
                </div>
            </div>
            <div class="widget-stats">
                <div class="wrapper transparent">
                    <span class="item-title">Unbilled Hours</span> <span class="item-count semi-bold">{{intval($lastThirty->nonbilledButPaidHours)}}</span>
                </div>
            </div>
            <div class="widget-stats ">
                <div class="wrapper last"> 
                    <span class="item-title">SG&amp;A</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->overhead)}}</span>
                </div>
            </div>
<!--
                <div style="width:90%" class="progress transparent progress-small no-radius m-t-20">
                    <div data-percentage="54%" class="progress-bar progress-bar-white animate-progress-bar" style="width: 54%;"></div>
                </div>
                <div class="description"> <span class="text-white mini-description ">4% higher <span class="blend">than last month</span></span></div>
            -->
        </div>          
    </div>  
</div>
<div class="col-md-4 col-vlg-3 col-sm-6">
    <div class="tiles red added-margin  m-b-20">
        <div class="tiles-body">
            <div class="controller"> <a class="reload" href="javascript:;"></a> <a class="remove" href="javascript:;"></a> </div>
            <div class="tiles-title text-black">PROFIT &amp; LOSS </div>
            <div class="widget-stats">
                <div class="wrapper transparent"> 
                    <span class="item-title">Gross Margin</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->grossMargin)}}</span>
                </div>
            </div>
            <div class="widget-stats">
                <div class="wrapper transparent">
                    <span class="item-title">Expenses</span> <span class="item-count semi-bold">{{formatCurrency( $lastThirty->nonbilledAmount+$lastThirty->overhead   )}}</span>
                </div>
            </div>
            <div class="widget-stats ">
                <div class="wrapper last"> 
                    <span class="item-title">Net Profit</span> <span class="item-count semi-bold">{{formatCurrency($lastThirty->revenue->grossRevenue - ($lastThirty->cogs->cogs+$lastThirty->nonbilledAmount+$lastThirty->overhead))}}</span>
                </div>
            </div>
<!--                <div style="width:90%" class="progress transparent progress-small no-radius m-t-20">
                    <div data-percentage="90%" class="progress-bar progress-bar-white animate-progress-bar" style="width: 90%;"></div>
                </div>
                <div class="description"> <span class="text-white mini-description ">4% higher <span class="blend">than last month</span></span></div>
            -->
        </div>          
    </div>  
</div>  
</div>

<div class="row">
    <div class="col-md-6 col-vlg-6 m-b-20 ">
        <div class="tiles white added-margin">
            <div class="row">
                <div class="p-t-15 p-l-45">
                    <div class="col-md-12 no-padding"><h5 class="text-black semi-bold">STAFF PROFITABILITY</h5></div>
                </div>
            </div>

            <div class="row ">
                <div class="p-t-35 p-l-45">
                    <div class="col-md-5 col-sm-5 no-padding">
                      <h5 class="no-margin">Billable Hours</h5>
                      <h4><span class="item-count semi-bold">{{intval($lastThirty->billedHours)}}</span> / {{$lastThirty->possibleBillableHours}}</h4>
                  </div>
                  <div class="col-md-7 col-sm-7 no-padding">
                      <p class="semi-bold">MISSED PROFIT</p>
                      <h4><span class="item-count semi-bold">{{formatCurrency(intval($lastThirty->missedBillableHours)*2000)}}</span></h4>
                  </div>
<!--                  <div class="col-md-3 col-sm-3 no-padding">
                      <p class="semi-bold">THIS MONTH</p>
                      <h4><span data-animation-duration="700" data-value="8514" class="item-count animate-number semi-bold">8,514</span> USD</h4>
                  </div>
              -->
              <div class="clearfix"></div>
          </div>
      </div>
      <!--<h5 class="semi-bold m-t-30 m-l-30">EMPLOYEES</h5>-->
      <table class="table no-more-tables m-t-20 m-l-20 m-b-30">
          <thead style="">
              <tr>
                <th style="width:53%">Employee</th>
                <th style="width:30%">Hours</th>
                <th style="width:15%">Billed</th>
                <th style="width:15%">Net</th>
                <th style="width:1%"> </th>
            </tr>
        </thead>
        <tbody>
            <?
            $billedHours = 0;
            $possibleBillableHours = 0;
            $billed = 0;
            $netProfit = 0;

            foreach ($lastThirty->employees as $emp) {
                $textClass = "";
                if (($emp->possibleBillableHours - $emp->billedHours) > 16) $textClass = "text-error";
                ?>
                <tr>
                    <td class="v-align-middle bold">{{$emp->first_name}} {{$emp->last_name}}</td>
                    <td class="v-align-middle  {{$textClass}}"><span class="muted  {{$textClass}}">{{intval($emp->billedHours)}}</span> / {{intval($emp->possibleBillableHours)}}</span> </td>
                    <td><span class="muted bold text-success">{{formatCurrency($emp->billedAmount)}}</span> </td>
                    <td><span class="muted bold text-success">{{formatCurrency($emp->netProfit)}}</span> </td>
                    <td class="v-align-middle"></td>
                </tr>  
                <? 
                $billedHours += $emp->billedHours;
                $possibleBillableHours += $emp->possibleBillableHours;
                $billed += $emp->billedAmount;
                $netProfit += $emp->netProfit;
            } ?>

        </tbody>
        <tfoot style="">
          <tr>
            <td style="width:53%">&nbsp;</td>
            <td style="width:15%">{{intval($billedHours)}} / {{intval($possibleBillableHours)}}</td>
            <td style="width:15%">{{formatCurrency($billed)}}</td>
            <td style="width:15%">{{formatCurrency($netProfit)}}</td>
            <td style="width:1%"> </td>
        </tr>
    </tfoot>
</table>

</div>
</div>




<div class="col-md-6 col-vlg-6 m-b-20 ">
    <div class="tiles white added-margin">
        <div class="row">
            <div class="p-t-15 p-l-45">
                <div class="col-md-12 no-padding"><h5 class="text-black semi-bold">PROJECT PROFITABILITY</h5></div>
            </div>
        </div>

        <!--<h5 class="semi-bold m-t-30 m-l-30">EMPLOYEES</h5>-->
        <table class="table no-more-tables m-t-20 m-l-20 m-b-30">
          <thead style="">
              <tr>
                <th style="width:43%">Project</th>
                <th style="width:15%">Billed</th>
                <th style="width:15%">Cost</th>
                <th style="width:15%">Net</th>
                <th style="width:1%">%</th>
                <th style="width:1%"> </th>
            </tr>
        </thead>
        <tbody>
            <?
            $billed = 0;
            $cost = 0;
            $net = 0;
            foreach ($projects->projects as $project) {
                $textClass = "";
                ?>
                <tr>
                    <td class="v-align-middle bold">{{$project->name}}</td>
                    <td class="v-align-middle  {{$textClass}}"><span class="muted  {{$textClass}}">{{formatCurrency($project->billedAmount)}}</span> </td>
                    <td><span class="muted bold">{{formatCurrency($project->employeeCost)}}</span> </td>
                    <td><span class="muted bold text-success">{{formatCurrency($project->billedAmount - $project->employeeCost)}}</span> </td>
                    <td class="v-align-middle">{{intval( ( ($project->billedAmount - $project->employeeCost) /  $project->billedAmount ) * 100)}}%</td>
                    <td class="v-align-middle"></td>
                </tr>  
                <? 
                $billed += $project->billedAmount;
                $cost += $project->employeeCost;
                $net += ($project->billedAmount - $project->employeeCost);
            } ?>

        </tbody>
        <tfoot style="">
          <tr>
            <td>&nbsp;</td>
            <td>{{formatCurrency($billed)}}</td>
            <td>{{formatCurrency($cost)}}</td>
            <td>{{formatCurrency($net)}}</td>
            <td>
                @if ( $billed!=0)
                {{ intval(($net / $billed)*100)}}%
                @endif
            </td>
            <td style="border:none !important"> </td>
        </tr>
    </tfoot>
</table>

</div>
</div>




</div>
