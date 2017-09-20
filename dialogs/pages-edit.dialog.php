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
$data=$once->once_select_item('pages');

# GET DATA -------------------
$layers=$once->once_select_items('layers','project_id');

# GET DATA -------------------
$routes=$once->once_select_items('routes','project_id');
if($routes['items']){
	foreach($routes['items'] as $key => $val){
		$routes_a[$routes['items'][$key]['page_id']]=$routes['items'][$key]['id'];
	}
}

# GET DATA -------------------
$pagesGrid=$once->get_grid_data();
$gridPlugins=$pagesGrid['gridPlugins'];
$gridPluginsz=$pagesGrid['gridPluginsz'];
$gridPluginsx=$pagesGrid['gridPluginsx'];

# GET DATA -------------------
$pluginsList=$once->once_plugin_list();

if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div id="page-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'" data-plugins="'.count($gridPluginsx).'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-files-o"></i> Edit page item
						<button class="btn btn-default btn-sm pull-right item-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>
						<button class="btn btn-default btn-sm pull-right item-grid-new" style="<?php echo ($_GET['tab']==''?'display: none;':'');?>" type="button"><i class="glyphicon glyphicon-pencil"></i>&nbsp; New layer</button>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="<?php echo ($_GET['tab']==''?'active':'');?>"><a href="#edit_settings" aria-controls="edit_settings" role="tab" data-toggle="tab">Settings & Seo</a></li>
								<li role="presentation" class="<?php echo ($_GET['tab']=='edit_grid'?'active':'');?>"><a href="#edit_grid" aria-controls="edit_grid" role="tab" data-toggle="tab">Page plugins</a></li>
								<li role="presentation" class="ui <?php echo (count($gridPlugins)==1?'':'hidden');?>">
									<?php echo '<a href="#edit_plugin" aria-controls="edit_plugin" role="tab" data-ajax="plugins.php?c=ui&plugin_id='.$gridPlugins[0].'&layer_id=0&page_id='.$_GET['id'].'&grid_id='.$gridPluginsx[0].'" data-toggle="tab">Plugin UI</a>';?>
								</li>
								<li role="presentation" class="dropdown uis <?php echo (count($gridPlugins)>1?'':'hidden');?>">
									<a aria-controls="myTabDrop1-contents" data-toggle="dropdown" class="dropdown-toggle" id="myTabDrop1" href="#" aria-expanded="false">Plugin UI's<span class="caret"></span></a>
									<ul id="myTabDrop1-contents" aria-labelledby="myTabDrop1" role="menu" class="dropdown-menu">
									<?php
									if(count($gridPlugins)){
										foreach($gridPlugins as $key => $val){
											echo '
											<li class="ui_'.$gridPluginsx[$key].'">
												<a href="#edit_plugin" aria-controls="edit_plugin" role="tab" data-ajax="plugins.php?c=ui&plugin_id='.$pluginsList['plugins'][$gridPlugins[$key]]['id'].'&layer_id=0&page_id='.$_GET['id'].'&grid_id='.$gridPluginsz[$key]['id'].'" data-toggle="tab" tabindex="-1" >'.$pluginsList['plugins'][$gridPlugins[$key]]['name'].'</a>
											</li>';
										}
									}else{
										echo '
											<li class="ui_'.$gridPluginsx[0].'">
												<a href="#edit_plugin" aria-controls="edit_plugin" role="tab" data-ajax="plugins.php?c=ui&plugin_id='.$pluginsList['plugins'][$gridPlugins[$key]]['id'].'&layer_id=0&page_id='.$_GET['id'].'&grid_id='.$gridPluginsx[0].'" data-toggle="tab" tabindex="-1" >Plugin UI</a>
											</li>';
									}
									?>
									</ul>
								</li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane <?php echo ($_GET['tab']==''?'active':'');?>" id="edit_settings">
									<div class="row margin">
										<div class="col-md-12">
											<form id="editForm" method="post">
												<div class="row">
													<div class="col-md-12">
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="title">Name</label>
																	<input type="text" value="<?php echo $data['item']['name'];?>" class="form-control" name="name" placeholder="Enter name">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="layer_id">Layer set</label>
																	<select class="form-control" name="layer_id">
																		<?php
																			foreach($layers['items'] as $key => $val){
																				echo '<option data-name="'.$layers['items'][$key]['name'].'" value="'.$layers['items'][$key]['id'].'" '.($layers['items'][$key]['id']==$data['item']['layer_id']?'selected':'').'>'.$layers['items'][$key]['name'].'</option>';
																			}
																		?>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="title">Title</label>
																	<input type="text" value="<?php echo $data['item']['title'];?>" class="form-control" name="title" placeholder="Enter title">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="route">Route variable</label>
																	<select class="form-control" name="route_id">
																		<option value="0">Select route (unset)</option>
																		<?php
																			foreach($routes['items'] as $key => $val){
																				echo '<option data-name="'.$routes['items'][$key]['name'].''.$routes['items'][$key]['name_id'].'" value="'.$routes['items'][$key]['id'].'" '.($routes['items'][$key]['id']==$routes_a[$_GET['id']]?'selected':'').'>$_ROUTE['.$routes['items'][$key]['name'].''.$routes['items'][$key]['name_id'].']</option>';
																			}
																		?>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label for="keywords">Keywords</label>
																	<input type="text" value="<?php echo $data['item']['keywords'];?>" class="form-control" name="keywords" placeholder="Enter keywords">
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="description">Description</label>
															<input type="text" value="<?php echo $data['item']['description'];?>" class="form-control" name="description" placeholder="Enter description">
														</div>
													</div>
												</div>

												<!--
												<div class="form-group">
													<label>Group permissions</label>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="admins" <?php echo ($data['item']['admins']==1?'checked':'');?>> Admins 
														</label>
													</div>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="moderators" <?php echo ($data['item']['moderators']==1?'checked':'');?>> Moderators 
														</label>
													</div>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="users" <?php echo ($data['item']['users']==1?'checked':'');?>> Users 
														</label>
													</div>
													
													<label>Display settings</label>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="private" <?php echo ($data['item']['private']==1?'checked':'');?>> Keep page private?
														</label>
													</div>
													<?php
													if($data['item']['private']==1){
														echo '<input type="text" value="'.$data['item']['password'].'" class="form-control" name="password" placeholder="Enter password">';
													}
													?>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="logged" <?php echo ($data['item']['logged']==1?'checked':'');?>> Login reguired?
														</label>
													</div>
													<div class="checkbox">
														<label>
															<input class="item-checkbox" type="checkbox" name="adult" <?php echo ($data['item']['adult']==1?'checked':'');?>> Page +18?
														</label>
													</div>
												</div>
												-->
												<input type="submit" class="hidden">
											</form>
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo ($_GET['tab']=='edit_grid'?'active':'');?>" id="edit_grid">
									<div class="row margin">
										<div class="col-md-12 page-grid">
											<div class="grid-stack">
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_plugin">
									<div class="row margin">
										<div id="ajax-plugin" class="col-md-12">
											Page plugins not set.
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button class="btn btn-primary pull-left item-save" type="submit"><i class="fa fa-check"></i> Save</button>
					<button type="button" class="btn btn-danger item-close" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div>
	</div><!-- /.modal -->
<?php
if(!isset($_GET['nomodal'])){
echo '
</div><!-- /.modal -->';
}
?>
<script type="text/javascript">
    $(document).ready(function () {
		// Set grid data for Gridstack
		once.pages.setPluginsData(<?php echo json_encode($pluginsList['items']);?>);
		once.pages.setLayersData(<?php echo json_encode($pagesGrid['grid'])?>);
		
		// Initialize actions
		once.pages.actions.editInit($(this));
		
		// Initialize tab if set
		once.pages.setTab('<?php echo $_GET['tab'];?>');
	});
</script>