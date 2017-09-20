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
$once->category_get('langs');

# GET DATA -------------------
$categories=$once->category_get_roots();

# PREPARE HEADER TEMPLATE
$header='
<h1>Langs
	<form id="addForm" method="post" class="btn-group form-inline margin">
		<div class="form-group">
			<input name="name" type="text" class="form-control input-sm" placeholder="Lang variable name">
		</div>
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">langs</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE
$str.='
<div class="row">
	<div id="categories-data" class="col-md-3">
		<ul class="list-group nav nav-pills nav-stacked">
			<li class="header">Langs categories
				<div class="btn-group margin">
					<button id="category-new" class="btn btn-default btn-xs" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
				</div>
			</li>
				<li class="list-group-item current" data-id="0">
					<div class="list-group-header"><span>All variables</span></div>
				</li>';
			if($categories['items']){
				foreach($categories['items'] as $key => $val){
					$str.='
					<li class="list-group-item '.($categories['items'][$key]['id']==$_GET['id']?'current':'').'" data-id="'.$categories['items'][$key]['id'].'">
						<div class="list-group-header"><span>'.$categories['items'][$key]['name'].'</span></div>
						<div class="list-group-hover">
							<button class="btn btn-default btn-xs category-delete" type="button"><i class="fa fa-minus"></i></button>
							<button class="btn btn-default btn-xs category-edit" type="button"><i class="fa fa-cog"></i></button>
						</div>';
						//$str.=$once->category_display_ul_simple_tree($categories['items'][$key]['id'], 0);
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
				$.get("view.php?c='.($_GET['route']==''?'langs':$_GET['route']).'&o=list&category_id=0", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: langs"); });
			});
		</script>
	</div>
</div><!-- /.row -->
<script src="js/once.langs.js" type="text/javascript"></script>';
$obj['html']=$str;

# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>