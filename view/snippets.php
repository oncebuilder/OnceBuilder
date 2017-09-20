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
$once->category_get('snippets');

# GET DATA -------------------
$categories=$once->category_get_roots();

# PREPARE HEADER TEMPLATE
$header='
<h1>Snippets
	<form class="btn-group form-inline margin">
		<div class="form-group">';
			if($_GET['v']=='installed'){
				$header.='
				<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>';
			}else{
				$header.='
				<button class="btn btn-default btn-sm item-publish" type="button"><i class="fa fa-plus"></i>&nbsp; publish</button>';
			}
			$header.='
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">snippets</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE	
$str='
<div class="row">
	<div id="categories-data" class="col-md-'.($_GET['v']=='installed'?'3':'12 hidden').'">
		<ul class="list-group nav nav-pills nav-stacked">
			<li class="header">Snippets categories
				<div class="btn-group margin">
					<button id="category-new" class="btn btn-default btn-xs" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
				</div>
			</li>
				<li class="list-group-item current" data-id="0">
					<div class="list-group-header"><i class="fa fa-bars"></i><span>All snippets</span></div>
				</li>';
			if(isset($categories['items'])){
				foreach($categories['items'] as $key => $val){
					$str.='
					<li id="category_'.$categories['items'][$key]['id'].'" class="list-group-item '.($categories['items'][$key]['id']==$_GET['id']?'current':'').'" data-id="'.$categories['items'][$key]['id'].'">
						<div class="list-group-header"><i class="'.$categories['items'][$key]['ico'].'"></i><span>'.$categories['items'][$key]['name'].'</span></div>
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
	<div id="content-body" class="col-md-'.($_GET['v']=='installed'?'9':'12').'">
		<script type="text/javascript">
			$(function() {
				$.get("view.php?c='.($_GET['route']==''?'snippets':$_GET['route']).'&o='.($_GET['v']==''?'installed':$_GET['v']).'", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: routes"); });
			});
		</script>
	</div>
</div><!-- /.row -->
<script src="js/once.snippets.js" type="text/javascript"></script>';
$obj['html']=$str;
	
# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>