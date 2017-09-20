<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is view template
 *
*/

# XAMPP fix without turning error info off -------------------
$_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';

# SET DATA -------------------
$once->set_data(array(
	"id" => intval($_GET['id'])
));

# GET DATA -------------------
$layers=$once->once_select_items('layers','all');

# GET DATA -------------------
$layersGrid=$once->get_grid_data();

# GET DATA -------------------
$pluginsList=$once->once_plugin_list();

if(isset($layers['items'])){
?>
<div id="layers-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-id="<?php echo $_GET['id'];?>" data-switcher="<?php echo $layersGrid['switcher'];?>">
	<div class="row box-header">
		<div class="col-md-5">
			<select class="form-control item-change">
				<?php
				if(count($layers['items'])>0){
					foreach($layers['items'] as $key => $val){
						echo '<option value="'.$layers['items'][$key]['id'].'" '.($layers['items'][$key]['id']==$_GET['id']?'selected':'').'>'.$layers['items'][$key]['name'].'</option>';
					}
				}
				?>
			</select>
		</div>
		<div class="col-md-7">
			<div class="list-inline pull-right">
				<ul style="list-style: none; display: inline-block;">
					<li class="dropdown" role="presentation">
						<a data-toggle="dropdown" class="btn btn-default dropdown-toggle" id="myTabDrop1" aria-expanded="false"><i class="fa fa-chevron-down"></i> Copy</a>
						<ul aria-labelledby="myTabDrop1" role="menu" class="dropdown-menu">
							<li class="header" style="font-size: 10px; cursor: default; color: grey; background: #fefefe;"><a>Copy all elements to:</a></li>
							<?php
							if($layers['items']){
								foreach($layers['items'] as $key => $val){
									if($layers['items'][$key]['id']!=$_GET['id']){
										echo '<li class="item-copy-to" data-id="'.$layers['items'][$key]['id'].'"><a>'.$layers['items'][$key]['name'].'</a></li>';
									}
								}
							}
							?>
						</ul>
					</li>
				</ul>
				<button type="button" class="btn btn-success item-new"><i class="glyphicon glyphicon-pencil"></i> New block</button>
				<button type="button" class="btn btn-success item-save"><i class="glyphicon glyphicon-floppy-disk"></i> Save grid</button>
				<button type="button" class="btn btn-success item-download"><i class="fa fa-download"></i> Download grid</button>
				<button type="button" class="btn btn-default item-edit"><i class="glyphicon glyphicon-cog"></i></button>
			</div>
		</div>
	</div>
	<div class="row box-body layers-grid">
		<div class="col-md-12">
			<div class="grid-stack">
			</div>
		</div>
	</div>
<?php
}else{
	echo '<div class="alert alert-info" role="alert">Please create grid of layers to make first page of your website.</div>';
}
?>
<script type="text/javascript">
	$(document).ready(function () {
		// Set grid data for Gridstack
		once.layers.setPluginsData(<?php echo json_encode($pluginsList['items']);?>);
		once.layers.setLayersData(<?php echo json_encode($layersGrid['grid'])?>);
		
		// Initialize actions
		once.layers.actions.mainInit($(this));
	});
</script>