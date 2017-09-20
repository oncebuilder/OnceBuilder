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

# SET DATA -------------------
$once->set_data(array(
	"x" => $once->filter_html($_GET['x'])
));

# GET DATA -------------------
$types=$once->once_select_items('posts_types');

# PREPARE HEADER TEMPLATE
$header='
<!-- Content Header (Post header) posts.php -->
<h1>Posts
	<form class="btn-group form-inline margin">
		<div class="form-group">
			<button class="btn btn-default btn-sm item-new" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</form>
</h1>
<ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	<li class="active">posts</li>
</ol>';
$obj['header']=$header;

# PREPARE BODY TEMPLATE		
$str.='
<div class="row">
	<div id="types-data" class="col-md-3">
		<ul class="list-group nav nav-pills nav-stacked">
			<li class="header">Post types</li>
				<li class="list-group-item current" data-id="0">
					<div class="list-group-header"><i class="fa fa-bars"></i><span>All posts</span></div>
				</li>';
			if(isset($types['items'])){
				foreach($types['items'] as $key => $val){
					$str.='
					<li id="type_'.$types['items'][$key]['id'].'" class="list-group-item" data-id="'.$types['items'][$key]['id'].'">
						<div class="list-group-header"><i class="'.$types['items'][$key]['ico'].'"></i><span>'.$types['items'][$key]['name'].'</span></div>';
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
				$.get("view.php?c='.($_GET['route']==''?'posts':$_GET['route']).'&o='.($_GET['v']==''?'list':$_GET['v']).'&type_id=0", function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("View Error: routes"); });
			});
		</script>
	</div>
</div><!-- /.row -->
<script src="js/once.posts.js" type="text/javascript"></script>';
$obj['html']=$str;
	
# RETURN HEADER/BODY TEMPLATE
echo json_encode($obj);
?>