<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>New PTO Request</h2>

        <div>
            <p>
                Dear Manager!
            </p>
            <p>
                The employee <b><?php echo $user->first_name?> <?php echo $user->last_name?></b> is requesting paid time off from <b><?php echo date('d/m/Y', strtotime($pto->start_date))?></b>  to <b><?php echo date('d/m/Y', strtotime($pto->end_date))?></b>. 
                Total <b><?php echo $pto->hours?> hours</b>. Please log into the system by the <a href='<?php echo $link;?>'>link</a> and approve or reject the PTO in the corresponding dashboard section. 
            </p>
            <p>
                Thank you!
            </p>
        </div>
    </body>
</html>

