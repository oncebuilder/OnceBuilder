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
$_GET['tab'] = isset($_GET['tab']) ? $_GET['tab'] : '';

# SET DATA -------------------
$once->set_data(array(
	"id" => intval($_GET['id'])
));

# GET DATA -------------------
$data=$once->get_edit_data();

# GET DATA -------------------
$layers=$once->once_select_items('layers','all');

# DECLARE SORT ARRAY -------------------
$data_sources_files=array('','head.php','global.css','main.js','file.php','style.css','script.js');
$data_sources_titles=array('','PHP Head','Global CSS','Main JS','PHP','CSS','JS');
$data_sources_count=count($data_sources_files);

if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div id="grid-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-th"></i> Edit grid item <i style="font-size: 10px;">layer_<?php echo $_GET['id'];?>.php</i>
						<button class="btn btn-default btn-sm pull-right item-delete" type="button">Delete</button>
						<?php if($data['plugin_id']!=-1){ ?>
						<button class="btn btn-default btn-sm pull-right item-preview" style="margin-right: 5px;" type="button"><i class="fa fa-chevron-down"></i> Preview</button>
						<a href="preview.php?path=layers&file=layer_<?php echo $_GET['id'];?>.php" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> Preview</a>
						<ul class="pull-right" style="margin-right: 5px; list-style: none; padding-left: 0;">
							<li class="dropdown" role="presentation">
								<a data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" id="myTabDrop1" aria-expanded="false"><i class="fa fa-chevron-down"></i> Copy</a>
								<ul aria-labelledby="myTabDrop1" role="menu" class="dropdown-menu">
									<li class="header" style="font-size: 10px; cursor: default; color: grey; background: #fefefe;"><a>Copy element to:</a></li>
									<?php
										if($layers['items']){
											foreach($layers['items'] as $key => $val){
												echo '
												<li class="item-copy" data-id="'.$layers['items'][$key]['id'].'">
													<a>'.$layers['items'][$key]['name'].'</a>
												</li>';
											}
										}
									?>
								</ul>
							</li>
						</ul>
						<ul class="pull-right" style="margin-right: 5px; list-style: none;">
							<li class="dropdown" role="presentation">
								<a data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" id="myTabDrop1" aria-expanded="false"><i class="fa fa-chevron-down"></i> Save</a>
								<ul aria-labelledby="myTabDrop1" role="menu" class="dropdown-menu">
									<li class="header" style="font-size: 10px; cursor: default; color: grey; background: #fefefe;"><a>Save element as:</a></li>
									<?php if($data['plugin_id']>1){ ?>
										<li class="item-save-as" data-type="plugins-theme"><a>Plugin theme</a></li>
									<?php
									}else if($data['plugin_id']<=1){
									?>
										<li class="item-save-as" data-type="plugins"><a>Plain plugin</a></li>
										<li class="item-save-as" data-type="snippets"><a>Plain snippet</a></li>
									<?php
										}
									?>
								</ul>
							</li>
						</ul>
						<?php } ?>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#edit_grid_settings" aria-controls="edit_grid_settings" role="tab" data-toggle="tab">General settings</a></li>
								<?php 
									if($data['plugin_id']>0){
										echo '<li role="presentation"><a href="#edit_grid_plugin" aria-controls="edit_grid_plugin" role="tab" data-toggle="tab" data-ajax="plugins.php?c=ui&plugin_id='.$data['plugin_id'].'&layer_id='.$_GET['id'].'&page_id=0&grid_id='.$data['id'].'">Plugin UI</a></li>';
									}
									if($data['plugin_id']!=-1){
										for($i=1;$i<$data_sources_count;$i++){
											echo '<li role="presentation"><a href="#edit_grid_source" aria-controls="edit_grid_source" role="tab" data-toggle="tab" data-file="'.$data_sources_files[$i].'" data-editor="'.$i.'" data-tab="'.strtolower($data_sources_titles[$i]).'">'.$data_sources_titles[$i].'</a></li>';
										}
									}else{
										for($i=1;$i<$data_sources_count;$i++){
											if($data_sources_titles[$i]!='PHP'){
												echo '<li role="presentation"><a href="#edit_grid_source" aria-controls="edit_grid_source" role="tab" data-toggle="tab" data-file="'.$data_sources_files[$i].'" data-editor="'.$i.'" data-tab="'.strtolower($data_sources_titles[$i]).'">'.$data_sources_titles[$i].'</a></li>';
											}
										}
									}
								?>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane active" id="edit_grid_settings">
									<div class="row margin">
										<div class="col-md-12">
											<form id="editGridForm" action="ajax.php?c=layers&o=item_edit&id=<?php echo $_GET['id'];?>" method="post">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="row_id">Row id</label>
															<input type="text" value="<?php echo $data['row_id'];?>" class="form-control" name="row_id" placeholder="Enter id">
														</div>
														<div class="form-group">
															<label for="row_class">Row class</label>
															<input type="text" value="<?php echo $data['row_class'];?>" class="form-control" name="row_class" placeholder="Enter class">
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="item_id">Layer id</label>
															<input type="text" value="<?php echo $data['item_id'];?>" class="form-control" name="item_id" placeholder="Enter id">
														</div>
														<div class="form-group">
															<label for="item_class">Layer class</label>
															<input type="text" value="<?php echo $data['item_class'];?>" class="form-control" name="item_class" placeholder="Enter class">
														</div>
													</div>
													<hr>
													<div class="col-md-<?php echo ($data['plugin_id']!=-1?"6":"12");?>">
														<div class="form-group">
															<label for="container">Keep in container</label>
															<select class="form-control" name="container">
																<option value="0" <?php echo ($data['container']==0?"selected":"");?>>Fluid container</option>
																<option value="1" <?php echo ($data['container']==1?"selected":"");?>>1170px container</option>
															</select>
														</div>
													</div>
													<?php if($data['plugin_id']!=-1){ ?>
													<div class="col-md-6">
														<div class="form-group">
															<label for="namespace">Layer namespace</label>
															<input type="text" value="<?php echo $data['namespace'];?>" class="form-control" name="namespace" placeholder="Layer namespace">
														</div>
													</div>
													<?php } ?>
												</div>
												<input type="submit" class="hidden">
											</form>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_grid_plugin">
									<div class="row margin">
										<div id="ajax-grid-plugin" class="col-md-12">
											Plugin not set.
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_grid_theme">
									<div class="row margin">
										<div id="ajax-grid-theme" class="col-md-12">
											Plugin not set.
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_grid_source">
									<div class="row margin">
										<div class="col-md-12">
											<textarea id="code-playground" name="code-playground"></textarea>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_grid_preview">
									<div class="row margin">
										<div class="col-md-12">
											<iframe></iframe>
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
			</div>
		</div>
	</div>
<?php
if(!isset($_GET['nomodal'])){
echo '
</div>';
}
?>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.layers.actions.gridInit($(this));
		
		// Initialize tab if set
		once.layers.setTab('<?php echo $_GET['tab'];?>');
	});
</script>