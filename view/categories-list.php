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
	"id" => intval($_GET['id'])
));

# GET DATA -------------------
$once->category_get(strtolower($_GET['module']));

# GET DATA -------------------
$categories=$once->category_get_roots();

?>
<ul class="list-group nav nav-pills nav-stacked">
	<li class="header"><?php echo ucfirst($_GET['module']);?> categories
		<div class="btn-group margin">
			<button id="category-new" class="btn btn-default btn-xs" type="button"><i class="fa fa-plus"></i>&nbsp; new</button>
		</div>
	</li>
	<li class="list-group-item <?php echo ($_GET['id']>0?'':'current');?>" data-id="0">
		<div class="list-group-header"><i class="fa fa-bars"></i><span>All <?php echo strtolower($_GET['module']);?></span></div>
	</li>
	<?php
	if($categories['items']){
		$str='';
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
		echo  $str;
	}
?>
</ul>
<script type="text/javascript">
	$(document).ready(function () {
		once.categories.actions.categoryInit();
	});
</script>