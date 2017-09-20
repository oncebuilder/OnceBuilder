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

# DECLARE SORT ARRAY -------------------
$data_sources_path=array('','snippets/'.$_GET['id'],'snippets/'.$_GET['id'],'snippets/'.$_GET['id']);
$data_sources_files=array('','snippet.html','snippet.css','snippet.js');
$data_sources_titles=array('','HTML','CSS','JS');
$data_sources_count=count($data_sources_files);

# GET DATA -------------------
$categories=$once->once_select_items('snippets_categories');

# GET DATA -------------------
$data=$once->once_select_item('snippets');

if(!isset($_GET['nomodal'])){
echo '
<div id="snippet-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-bars"></i> Edit snippet
						<button class="btn btn-default btn-sm pull-right item-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>
						<button class="btn btn-default btn-sm pull-right item-preview" type="button"><i class="fa fa-chevron-down"></i> Preview</button>
						<a href="snippets/<?php echo $_GET['id'];?>/index.php" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> Preview</a>
						<?php
						if($_SERVER['HTTP_HOST']=='oncebuilder.com'){
							if($data['item']['published']==1){
								echo '<button class="btn btn-default btn-sm pull-right item-approved" type="button"><i class="fa fa-thumbs-up"></i> Approved</button>';
							}else{
								echo '<button class="btn btn-success btn-sm pull-right item-approve" type="button"><i class="fa fa-thumbs-o-up"></i> Approve</button>';
							}
							echo '<button class="btn btn-default btn-sm pull-right item-insights-image" type="button"><i class="fa fa-google-plus"></i> Insights Image</button>';
						}
						if($data['item']['stared']==1){
							echo '<button class="btn btn-default btn-sm pull-right item-stared" type="button"><i class="fa fa-star"></i> Stared</button>';
						}else{
							echo '<button class="btn btn-success btn-sm pull-right item-star" type="button"><i class="fa fa-star-o"></i> Star</button>';
						}
						?>
						<button class="btn btn-default btn-sm pull-right item-publish" type="button"><i class="fa fa-share"></i> Publish</button>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#edit_settings" aria-controls="edit_settings" role="tab" data-toggle="tab">General information</a></li>
								<?php
									for($i=1;$i<$data_sources_count;$i++){
										echo '<li role="presentation"><a href="#edit_source" aria-controls="edit_source" role="tab" data-toggle="tab" data-path="'.$data_sources_path[$i].'" data-file="'.$data_sources_files[$i].'" data-editor="'.$i.'">'.$data_sources_titles[$i].'</a></li>';
									}
								?>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane active" id="edit_settings">
									<div class="row margin">
										<div class="col-md-12">
											<form id="editForm" method="post">
												<div class="row">
													<div class="col-md-9">
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="name">Name</label>
																	<input type="text" value="<?php echo $data['item']['name'];?>" class="form-control" name="name" placeholder="Enter name">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="category_id">Category</label>
																	<select class="form-control" name="category_id">
																		<option value="0" <?php echo ($data['item']['category_id']==0?'selected':'');?>>Uncategorized</option>
																		<?php
																			if($categories['items']){
																				foreach($categories['items'] as $key => $val){
																					echo '<option value="'.$categories['items'][$key]['id'].'" '.($categories['items'][$key]['id']==$data['item']['category_id']?'selected':'').'>'.$categories['items'][$key]['name'].'</option>';
																				}
																			}
																		?>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fw_css">Framework CSS</label>
																	<select class="form-control" name="fw_css">
																		<option value="1" <?php echo ($data['item']['fw_css']==0?'selected':'');?>>Bootstrap</option>
																	</select>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label for="fw_js">Framework JS</label>
																	<select class="form-control" name="fw_js">
																		<option value="0" <?php echo ($data['item']['fw_js']==0?'selected':'');?>></option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label for="author_url">Author URL</label>
																	<input type="text" value="<?php echo ($data['item']['author_url']==''?'':$data['item']['author_url']);?>" class="form-control" name="author_url" placeholder="If you made that leave it blank">
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="row">
															<div class="col-md-12">
																<label for="title">Thumbnail</label>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="thumbnail">
																	<div class="caption">
																		<h4>Edit thumbnail</h4>
																		<p>&nbsp;</p>
																		<p>
																			<a href="#" class="label label-default item-thumbnail" title="Change thumbnail">Change thumbnail</a>
																		</p>
																	</div>
																	<img id="item-thumbnail" src="snippets/<?php echo $_GET['id'];?>/thumbnail.png?<?php echo time();?>" onerror="this.src='img/snippet.png'">
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="tags">Tags</label>
															<input type="text" value="<?php echo $data['item']['tags'];?>" class="form-control" name="tags" placeholder="Enter tags">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="description">Description</label>
															<textarea class="form-control" name="description" placeholder="Enter description" cols="10" rows="10"><?php echo $data['item']['description'];?></textarea>
														</div>
													</div>
												</div>
												<input type="submit" class="hidden">
											</form>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="edit_source">
									<div class="row margin">
										<div class="col-md-12">
											<textarea id="ajax-playground" name="ajax-playground"></textarea>
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
					<button class="btn btn-primary pull-left item-save" type="submit"><i class="fa fa-check"></i> Save</button>
					<button type="button" class="btn btn-danger item-close" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div>
	</div><!-- /.modal -->
<?php
if(!isset($_GET['nomodal'])){
echo '
</div>';
}
?>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.snippets.actions.editInit($(this));
	});
</script>