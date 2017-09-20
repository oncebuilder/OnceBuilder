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
$data=$once->once_select_item('themes');

# GET DATA -------------------
$message=$once->once_select_item_key('themes_reports','theme_id');

# GET DATA -------------------
$once->category_get('themes');

# GET DATA -------------------
$categories=$once->category_get_roots();

if(!isset($_GET['nomodal'])){
echo '
<div id="theme-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-files-o"></i> Edit theme
						<?php
						if($_SESSION['project_id']!=$_GET['id']){
							echo '<button class="btn btn-default btn-sm pull-right item-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>';
						}
						if($_SERVER['HTTP_HOST']=='oncebuilder.com'){
							if($data['item']['published']==1){
								echo '<button class="btn btn-default btn-sm pull-right item-approved" type="button"><i class="fa fa-thumbs-up"></i> Approved</button>';
							}else{
								echo '<button class="btn btn-success btn-sm pull-right item-approve" type="button"><i class="fa fa-thumbs-o-up"></i> Approve</button>';
							}
						}
						if($data['item']['stared']==1){
							echo '<button class="btn btn-default btn-sm pull-right item-stared" type="button"><i class="fa fa-star"></i> Stared</button>';
						}else{
							echo '<button class="btn btn-success btn-sm pull-right item-star" type="button"><i class="fa fa-star-o"></i> Star</button>';
						}
						?>
						<button class="btn btn-default btn-sm pull-right item-export" type="button"><i class="fa fa-download"></i> Export</button>
						<?php
						if($_SESSION['project_id']==$_GET['id']){
							echo '<button class="btn btn-default btn-sm pull-right" type="button"><i class="fa fa-check-square-o"></i> Current</button>';
						}
						?>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#edit_settings" aria-controls="edit_settings" role="tab" data-toggle="tab">General settings</a></li>
								<li role="presentation"><a href="#edit_images" aria-controls="edit_images" role="tab" data-toggle="tab">Images</a></li>
								<li role="presentation" class="<?php echo ($_SERVER['HTTP_HOST']=='oncebuilder.com'?'':'hidden');?>"><a href="#edit_message" aria-controls="edit_message" role="tab" data-toggle="tab">Messages</a></li>
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
															<div class="col-md-4">
																<div class="form-group">
																	<label for="fw_js">Framework PHP</label>
																	<select class="form-control" name="fw_js">
																		<option value="0" <?php echo ($data['item']['fw_php']==0?'selected':'');?>></option>
																	</select>
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label for="fw_css">Framework CSS</label>
																	<select class="form-control" name="fw_js">
																		<option value="0" <?php echo ($data['item']['fw_css']==0?'selected':'');?>></option>
																	</select>
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label for="fw_js">Framework JS</label>
																	<select class="form-control" name="fw_js">
																		<option value="0" <?php echo ($data['item']['fw_js']==0?'selected':'');?>></option>
																	</select>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label for="author_url">Author URL</label>
																	<input type="text" value="<?php echo ($data['item']['author_url']==''?'':$data['item']['author_url']);?>" class="form-control" name="author_url" placeholder="If you made that leave it blank">
																</div>
															</div>
															<div class="col-md-6">
																<label for="price">Points</label>
																<div class="input-group">
																	<input type="text" value="<?php echo $data['item']['price']==0?'':$data['item']['price'];?>" class="form-control"name="price" placeholder="Enter price if you want to earn some" aria-label="Amount (to the nearest dollar)">
																	<span class="input-group-addon">$</span>
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
																	<img id="item-thumbnail" src="themes/<?php echo $_GET['id'];?>/thumbnail.png?<?php echo time();?>" onerror="this.src='img/theme.png'">
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
								<div class="tab-pane" id="edit_images">
									<div class="row margin">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-12">
															<label for="title">Presentation Images</label>
														</div>
													</div>
													<div class="row">
														<div id="item_add" class="col-md-3">
															<div class="thumbnail image">
																<a class="btn btn-default btn-sm item-image"> <i class="fa fa-upload"></i> Upload</a>
															</div>
														</div>
														<?php
															if($_SESSION['project_id']==$_GET['id']){
																$dir='../images/';
															}else{
																$dir='themes/'.$_GET['id'].'/images/';
															}
															for($i=1;$i<10;$i++){
																echo '
																<div id="image_'.$i.'" class="col-md-3 images '.(file_exists($dir."ss".$i.".png")==true?'':'hidden').'" data-id="'.$i.'">
																	<div class="thumbnail image">
																		<div class="caption">
																			<h4>Edit image</h4>
																			<p>&nbsp;</p>
																			<p>
																				<a href="#" class="label label-default item-image-change" title="Change image"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Change</a>
																				<a href="#" class="label label-warning item-image-delete" title="Delete image">×</a>
																			</p>
																		</div>
																		<img data-src="'.$dir.'ss'.$i.'.png" src="'.$dir.'ss'.$i.'.png?'.time().'">
																	</div>
																</div>';
															}
														?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo ($_SERVER['SERVER_ADDR']!='127.0.0.1'?'':'hidden');?>" id="edit_message">
									<div class="row margin">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="message">Approval message</label>
														<textarea disabled class="form-control" name="message" placeholder="Approval message" cols="10" rows="10"><?php echo $message['item']['message'];?></textarea>
													</div>
												</div>
											</div>
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
		once.themes.actions.editInit($(this));
	});
</script>