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
	<div class="heading" style="background:#039BE5;color:#FFF;font-weight:bold;height:48px;line-height:48px;font-size:20px;font-weight:normal;padding-left:50px">
		Welcome to <span style="font-weight:bold">VistaDesk</span>
	</div>
	<div class="content" style="padding:20px;padding:20px;max-width:1020px">
		<div style="padding-left:30px">
			<p>
				Welcome <?=$recipient?>,
			</p>
			<p>
				A VistaDesk account for <?=$tenant->name?> has been created, you are just a click away from accelerating your
				consulting business! To complete your registration, simply click the link below:
			</p>
			<p>
				<b><a href="<?php echo $link; ?>">Click here to complete account activation</a></b>
			</p>
			<p> If you have any questions or require assistance setting up your account, call us at 888-910-1101 or email <a href="mailto:info@vistadesk.com">info@vistadesk.com</a> for support.
			</p>
			From all of us here, we thank you for the opportunity to help your business and we wish you the best success!<br/><br/>
			- The VistaDesk Team
			<br/>
		</div>
	</div>
</body>
</html>



