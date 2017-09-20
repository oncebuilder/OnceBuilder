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
$types=$once->type_get('settings');

# PREPARE HEADER TEMPLATE
$header='
<h1>Settings
	<form class="btn-group form-inline margin">
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-settings"></i> Home</a></li>
	<li class="active">settings</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE
$str='
<div class="row">
	<div id="types-data" class="col-md-3">
		<ul class="list-group nav nav-pills nav-stacked">
			<li class="header">Settings types
				<div class="btn-group margin">
					<button id="type-new" class="btn btn-default btn-xs" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
				</div>
			</li>';
			if(isset($types['items'])){
				foreach($types['items'] as $key => $val){
					$str.='
					<li id="type_'.$types['items'][$key]['id'].'" class="list-group-item" data-id="'.$types['items'][$key]['id'].'">
						<div class="list-group-header"><i class="'.$types['items'][$key]['ico'].'"></i><span>'.$types['items'][$key]['name'].'</span></div>
						<div class="list-group-hover">
							<button class="btn btn-default btn-xs type-delete" type="button"><i class="fa fa-minus"></i></button>
							<button class="btn btn-default btn-xs type-edit" type="button"><i class="fa fa-cog"></i></button>
						</div>';
						$str.='
					</li>';
				}
			}
			$str.='
		</ul>
	</div>
	<div id="content-body" class="col-md-9">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'settings':$_GET['route']).'&o=list&type_id=0", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: settings"); });
			});
		</script>
	</div>
</div>
<script src="js/once.settings.js" type="text/javascript"></script>';
$obj['html']=$str;

# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>