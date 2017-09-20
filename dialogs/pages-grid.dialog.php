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

# DECLARE SORT ARRAY -------------------
$data_sources_files=array('','head.php','global.css','main.js','file.php','style.css','script.js');
$data_sources_titles=array('','PHP Head','Global CSS','Main JS','PHP','CSS','JS');
$data_sources_count=count($data_sources_files);

if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div id="grid-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'" data-page_id="'.$data['page_id'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-files-o"></i> Edit grid item <i style="font-size: 10px;">page_<?php echo $data['page_id'];?>_<?php echo $_GET['id'];?>.php</i>
						<button class="btn btn-warning btn-sm pull-right grid-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>
						<button class="btn btn-default btn-sm pull-right grid-preview" style="margin-right: 5px;" type="button"><i class="fa fa-chevron-down"></i> Preview</button>
						<a href="preview.php?path=pages&file=page_<?php echo $data['page_id'];?>_<?php echo $_GET['id'];?>.php" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> Preview</a>
						<ul class="pull-right" style="margin-right: 5px; list-style: none;">
							<li class="dropdown" role="presentation">
								<a data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" id="myTabDrop1" aria-expanded="false"><i class="fa fa-chevron-down"></i> Save</a>
								<ul aria-labelledby="myTabDrop1" role="menu" class="dropdown-menu">
									<li class="header" style="font-size: 10px; cursor: default; color: grey; background: #fefefe;"><a>Save element as:</a></li>
									<?php if($data['plugin_id']>1){ ?>
										<li class="grid-save-as" data-type="plugins-theme"><a>Plugin theme</a></li>
									<?php
									}else if($data['plugin_id']<=1){
									?>
										<li class="grid-save-as" data-type="plugins"><a>Plain plugin</a></li>
										<li class="grid-save-as" data-type="snippets"><a>Plain snippet</a></li>
									<?php
										}
									?>
								</ul>
							</li>
						</ul>
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
										echo '<li role="presentation"><a href="#edit_grid_plugin" aria-controls="edit_grid_plugin" role="tab" data-toggle="tab" data-ajax="plugins.php?c=ui&plugin_id='.$data['plugin_id'].'&layer_id=0&page_id='.$data['page_id'].'&grid_id='.$_GET['id'].'">Plugin UI</a></li>';
									}
									if($data['plugin_id']!=-1){
										for($i=1;$i<$data_sources_count;$i++){
											echo '<li role="presentation"><a href="#edit_grid_source" aria-controls="edit_grid_source" role="tab" data-toggle="tab" data-file="'.$data_sources_files[$i].'" data-editor="'.$i.'" data-tab="'.strtolower($data_sources_titles[$i]).'">'.$data_sources_titles[$i].'</a></li>';
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
											<form id="editGridForm" action="ajax.php?c=pages&o=item_grid_edit&id=<?php echo $_GET['id'];?>" method="post">
												<div class="form-group">
													<label for="item_id">Col id</label>
													<input type="text" value="<?php echo $data['item_id'];?>" class="form-control" name="item_id" placeholder="Enter keywords">
												</div>
												<div class="form-group">
													<label for="item_class">Col class</label>
													<input type="text" value="<?php echo $data['item_class'];?>" class="form-control" name="item_class" placeholder="Enter description">
												</div>
												<div class="form-group">
													<label for="row_id">Row id</label>
													<input type="text" value="<?php echo $data['row_id'];?>" class="form-control" name="row_id" placeholder="Enter description">
												</div>
												<div class="form-group">
													<label for="row_class">Row class</label>
													<input type="text" value="<?php echo $data['row_class'];?>" class="form-control" name="row_class" placeholder="Enter description">
												</div>
												<input type="submit" class="hidden">
											</form>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_grid_plugin">
									<div class="row margin">
										<div id="ajax-grid-plugin" class="col-md-12">
											Page plugins already not set.
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
								<div class="tab-pane" id="edit_preview">
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
					<button class="btn btn-primary pull-left grid-save" type="submit"><i class="fa fa-check"></i> Save</button>
					<button type="button" class="btn btn-danger hidden grid-close" data-dismiss="modal"><i class="fa fa-times"></i> Anuluj</button>
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
		once.pages.actions.gridInit($(this));
		
		// Initialize tab if set
		once.pages.setTab('<?php echo $_GET['tab'];?>');
	});
</script>