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
$data=$once->once_select_items('layers','all');

if(isset($data['items'][0]['id'])){
	$id=intval($data['items'][0]['id']);
	foreach($data['items'] as $key => $val){
		if($data['items'][$key]['default']==1){
			$id=$data['items'][$key]['id'];
		}
	}
}else{
	$id=0;
}

# PREPARE HEADER TEMPLATE
$header='
<h1>Layers
	<form class="btn-group form-inline margin">
		<div class="form-group">
			<button id="item-set-new" class="btn btn-default btn-sm" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">layers</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE
$str='
<div class="row">
	<div id="content-body" class="col-md-12">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'layers':$_GET['route']).'&o=grid&id='.$id.'", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: layers"); });
			});
		</script>
	</div>
</div>
<script src="js/once.layers.js" type="text/javascript"></script>';
$obj['html']=$str;

# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>