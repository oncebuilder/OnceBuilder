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
//$about=$once->get_about();

$header='
<!-- Content Header (Page header) about.class.php -->
<h1>About</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-about"></i> Home</a></li>
	<li class="active">about</li>
</ol>
';

$obj['header']=$header;
		
$str='
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div id="info-header" class="box-body margin"></div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div id="content-body" class="box-body margin">
				<p>Thank you for downloading OnceBuilder CMS Beta</p>
				</br>
				<p>Now you can try to build your first site with OnceBuilder. It\'s like build from Lego! Idea about building site with HTML blocks appread in 2012!</p>
				<p>Visit our fanpage and let your friends know about new way of building websites! <a target="_blank" href="http://facebook.com/oncebuilder">http://facebook.com/oncebuilder</a></p>
				</br>
				<iframe width="560" height="315" src="https://www.youtube.com/embed/tBEQgegLGOQ" frameborder="0" allowfullscreen></iframe>
				<p>Follow our YouTube channel to recive more information about this software</p>
				</br>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<script src="js/once.about.js" type="text/javascript"></script>';
	
$obj['html']=$str;

if(true){
	$obj['status']='ok';
}else{
	$obj['error']='view not found';
}

echo json_encode($obj);
?>