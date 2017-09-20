<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is core connector class
 *
*/

# FIX XAMPP AND UNDEFINED -------------------
$_GET['t'] = isset($_GET['t']) ? $_GET['t'] : '';
$_POST['query'] = isset($_POST['query']) ? $_POST['query'] : '';

# SET DATA -------------------
$once->set_data(array(
	"t" => $once->filter_string($_GET['t']),
	"query" => $once->filter_string($_POST['query'])
));
//$dashboard=$once->get_dashboard();

$header='
<!-- Content Header (Page header) dashboard.class.php -->
<h1>Dashboard</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">dashboard</li>
</ol>
';

$obj['header']=$header;
		
$str='
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div id="content-body" class="box-body margin">
				<p>Thank you for downloading OnceBuilder CMS Beta</p>
				</br>
				<p>Now you can try to build your first site with OnceBuilder. It\'s like build from Lego! Idea about building site with HTML blocks appread in 2012!</p>
				<p>Visit our fanpage and let your friends know about new way of building websites! <a target="_blank" href="http://facebook.com/oncebuilder">http://facebook.com/oncebuilder</a></p>
				</br>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="MD28E5PXFCY5N">
					<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<script src="js/once.dashboard.js" type="text/javascript"></script>';
	
$obj['html']=$str;

if(true){
	$obj['status']='ok';
}else{
	$obj['error']='view not found';
}

echo json_encode($obj);
?>