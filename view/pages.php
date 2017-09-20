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

# GET DATA -------------------
$types=$once->type_get('pages');

# PREPARE HEADER TEMPLATE
$header='
<h1>Pages
	<form class="btn-group form-inline margin">
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">pages</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE
$str='
<div class="row">
	<div id="content-body" class="col-md-12">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'pages':$_GET['page']).'&o=list&type_id=0", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: pages"); });
			});
		</script>
	</div>
</div>
<script src="js/once.pages.js" type="text/javascript"></script>';
$obj['html']=$str;

# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>