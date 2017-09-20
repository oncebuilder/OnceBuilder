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
$_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
$_GET['x'] = isset($_GET['x']) ? $_GET['x'] : '';
$_GET['route'] = isset($_GET['route']) ? $_GET['route'] : '';

# SET DATA -------------------
$once->set_data(array(
	"x" => $once->filter_string($_GET['x'])
));

# GET DATA -------------------
$variables=$once->variables_get('configs');

# PREPARE HEADER TEMPLATE
$header='
<h1>Configs
	<form id="addForm" method="post" class="btn-group form-inline margin">
		<div class="form-group">
			<input name="key" type="text" class="form-control input-sm" placeholder="Config variable key">
		</div>
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">configs</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE
$str.='
<div class="row">
	<div id="variables-data" class="col-md-3">
		<ul class="list-group nav nav-pills nav-stacked">
			<li class="header">Config variables</li>
				<li class="list-group-item current" data-key="">
					<div class="list-group-header"><span>All variables</span></div>
				</li>';
			if($variables['keys']){
				foreach($variables['keys'] as $key => $val){
					$str.='
					<li class="list-group-item" data-key="'.$val.'">
						<div class="list-group-header">
							<span>'.$val.'</span>
						</div>
					</li>';
				}
			}
			$str.='
		</ul>
	</div>
	<div id="content-body" class="col-md-9">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'configs':$_GET['route']).'&o=list", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: configs"); });
			});
		</script>
	</div>
</div><!-- /.row -->
<script src="js/once.configs.js" type="text/javascript"></script>';
$obj['html']=$str;

# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>