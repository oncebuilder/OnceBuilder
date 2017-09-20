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

# XAMPP fix without turning error info off -------------------
$_GET['x'] = isset($_GET['x']) ? $_GET['x'] : '';
$_GET['route'] = isset($_GET['route']) ? $_GET['route'] : '';

# SET DATA -------------------
$once->set_data(array(
	"x" => $once->filter_string($_GET['x'])
));

# PREPARE HEADER TEMPLATE
$header='
<h1>Libraries
	<form class="btn-group form-inline margin">
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; dir</button>
			<button class="btn btn-default btn-sm item-upload" type="button"><i class="fa fa-plus"></i>&nbsp; upload</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-file-image-o"></i> Home</a></li>
	<li class="active">libraries</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE	
$str='
<div class="row">
	<div id="content-body" class="col-md-12">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'libraries':$_GET['route']).'&o='.($_GET['v']==''?'dir':$_GET['v']).'", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: libraries"); });
			});
		</script>
	</div>
</div><!-- /.row -->
<script src="js/once.libraries.js" type="text/javascript"></script>';
$obj['html']=$str;
	
# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>