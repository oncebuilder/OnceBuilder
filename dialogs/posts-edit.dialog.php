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
$data=$once->once_select_item('posts');

if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div id="post-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'" data-source="true">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-coffee"></i> Edit post
						<button class="btn btn-default btn-sm pull-right item-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="<?php echo ($_GET['tab']=='edit_settings'?'active':'');?>"><a href="#edit_settings" aria-controls="edit_settings" role="tab" data-toggle="tab">General settings</a></li>
								<li role="presentation" class="<?php echo ($_GET['tab']==''?'active':'');?>"><a href="#edit_content" aria-controls="edit_content" role="tab" data-toggle="tab">Content</a></li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane <?php echo ($_GET['tab']=='edit_settings'?'active':'');?>" id="edit_settings">
									<div class="row margin">
										<div class="col-md-12">
											<form id="editForm" action="ajax.php?c=posts&o=item_edit&id=<?php echo $_GET['id'];?>" method="post">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="title">Title</label>
															<input type="text" value="<?php echo $data['item']['title'];?>" class="form-control" name="title" placeholder="Enter title">
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="type_id">Type</label>
															<select class="form-control" name="type_id">
																<option value="0" <?php echo ($data['item']['type_id']==0?'selected':'');?>></option>
																<option value="1" <?php echo ($data['item']['type_id']==1?'selected':'');?>>Published</option>
																<option value="2" <?php echo ($data['item']['type_id']==2?'selected':'');?>>Draft</option>
																<option value="3" <?php echo ($data['item']['type_id']==3?'selected':'');?>>Pedding</option>
																<option value="5" <?php echo ($data['item']['type_id']==5?'selected':'');?>>Junk</option>
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
													<div class="col-md-12">
														<div class="form-group">
															<label for="description">Description</label>
															<input type="text" value="<?php echo $data['item']['description'];?>" class="form-control" name="description" placeholder="Enter description">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="keywords">Publish date</label>
															<div class="input-group">
																<div class="input-group-addon">
																	<i class="fa fa-clock-o"></i>
																</div>
																<input class="form-control data_publish" type="text">
															</div>
														</div>
													</div>
												</div>
												<input type="submit" class="hidden">
											</form>
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo ($_GET['tab']==''?'active':'');?>" id="edit_content">
									<div class="row margin">
										<div class="col-md-12 page-grid">
											<textarea class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $data['item']['source'];?></textarea>
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
		// Initialize actions
		once.posts.actions.editInit($(this));
	});
</script>