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

# INITIALIZE DATA -------------------
if(!isset($_GET['page'])){
	$_GET['page']=1;
}

# SET DATA -------------------
$once->set_data(array(
	"id" => intval($_GET['id']),
	"page" => intval($_GET['page'])
));

# GET DATA -------------------
if(isset($_GET['id'])){
	$data=$once->once_select_item('libraries');
}else{	
	$data=$once->once_select_items_page('libraries');
}

#GET DATA -------------------
$once->category_get('libraries');

# GET DATA -------------------
$categories=$once->category_get_roots();

if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">';
}
?>
	<div class="container" id="librarie-data" data-id="<?php echo $_GET['id'];?>">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-cube"></i> Plugin publish</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="<?php echo (isset($_GET['id'])==true?'hidden':'active');?>"><a href="#publish_select" aria-controls="publish_select" role="tab" data-toggle="tab">Select librarie</a></li>
								<li role="presentation" class="<?php echo (isset($_GET['id'])==true?'active':'hidden');?>"><a href="#publish_settings" aria-controls="publish_settings" role="tab" data-toggle="tab">General info</a></li>
								<li role="presentation" class="<?php echo (isset($_GET['id'])==true?'show':'hidden');?>"><a href="#publish_images" aria-controls="publish_images" role="tab" data-toggle="tab">Screenshots</a></li>
								<li role="presentation" class="<?php echo (isset($_GET['id'])==true?'show':'hidden');?>"><a href="#publish_message" aria-controls="publish_message" role="tab" data-toggle="tab">Message for approval</a></li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane <?php echo (isset($_GET['id'])==true?'hidden':'active');?>" id="publish_select">
									<div class="row mailbox margin">
										<div class="col-md-12">
											<div class="table-responsive">
												<table id="tablelist" class="table table-bordered table-striped table-mailbox">
													<?php
													if($data['items']){
													echo '
													<thead>
														<tr>
															<th>Name</th>
															<th style="width: 60px;"></th>
														</tr>
													</thead>
													<tbody>';
														foreach($data['items'] as $key => $val){
															echo '
															<tr id="item_'.$data['items'][$key]['id'].'" data-id="'.$data['items'][$key]['id'].'">
																<td>'.$data['items'][$key]['name'].'</td>
																<td>
																	<button class="btn btn-success btn-xs pull-left item-select" type="submit">Select <i class="fa fa-caret-right"></i></button>
																</td>
															</tr>';
														}
													echo '
													</tbody>';
													}else{
														echo '
														<thead>
															<th>Not found any library here, be first and create it once!</th>
														</thead>';
													}
													?>
												</table>
												<div class="box-footer clearfix">
													<div class="pull-left" style="margin-top: 10px">
														<label style="padding-right: 10px;">Results on page</label>
														<select class="display-limit">
															<option <?php echo ''.($data['limit']==10?'selected':'').''?>>10</option>
															<option <?php echo ''.($data['limit']==20?'selected':'').''?>>20</option>
															<option <?php echo ''.($data['limit']==50?'selected':'').''?>>50</option>
															<option <?php echo ''.($data['limit']==100?'selected':'').''?>>100</option>
														</select>
													</div>
													<div class="pull-right">
														<?php
														if($data['page']){
															echo '
															<ul class="pagination">';
																for($i=1;$i<=$data['pages'];$i++){
																	echo '<li><a '.($_GET['page']==$i?'class="active"':'').'>'.$i.'</a></li>';
																}
																echo '
															</ul>';
														}
														?>
													</div>
												</div><!-- box-footer -->
											</div><!-- /.col (RIGHT) -->
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo (isset($_GET['id'])==true?'active':'hidden');?>" id="publish_settings">
									<div class="row margin">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-9">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="name">Name</label>
																<input disabled type="text" value="<?php echo $data['item']['name'];?>" class="form-control" name="name" placeholder="Enter name">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="category_id">Category</label>
																<select disabled class="form-control" name="category_id">
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
																<label for="author">Author</label>
																<input disabled type="text" value="<?php echo $data['item']['author'];?>" class="form-control" name="author" placeholder="Enter author">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="version">Version</label>
																<input disabled type="text" value="<?php echo ($data['item']['version']==''?'0.0.1':$data['item']['version']);?>" class="form-control" name="version" placeholder="Enter version">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="author_url">Author URL</label>
																<div class="input-group">
																	<span class="input-group-addon">http://</span>
																	<input disabled type="text" value="<?php echo $data['item']['author_url'];?>" class="form-control" name="author_url" placeholder="Enter author_url">
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<label for="price">Points</label>
															<div class="input-group">
																<input disabled type="text" value="<?php echo $data['item']['price'];?>" class="form-control" name="price" placeholder="Enter price if you want to earn some" aria-label="Amount (to the nearest dollar)">
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
																<img src="libraries/<?php echo $data['item']['id'];?>/thumbnail.png?<?php echo time();?>" onerror="this.src='img/librarie.png'">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="tags">Tags</label>
														<input disabled type="text" value="<?php echo $data['item']['tags'];?>" class="form-control" name="tags" placeholder="Enter tags">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="description">Description</label>
														<textarea disabled class="form-control" name="description" placeholder="Enter description" cols="10" rows="10"><?php echo $data['item']['description'];?></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane <?php echo (isset($_GET['id'])==true?'':'hidden');?>" id="publish_images">
									<div class="row margin">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-12">
															<label for="title">Images</label>
														</div>
													</div>
													<div class="row">
														<?php
															if($_SESSION['project_id']==$_GET['id']){
																$dir='../images/';
															}else{
																$dir='libraries/'.$_GET['id'].'/images/';
															}
															for($i=1;$i<10;$i++){
																echo '
																<div id="image_'.$i.'" class="col-md-3 images '.(file_exists($dir."ss".$i.".png")==true?'':'hidden').'" data-id="'.$i.'">
																	<div class="thumbnail image">
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
								<div class="tab-pane <?php echo (isset($_GET['id'])==true?'':'hidden');?>" id="publish_message">
									<div class="row margin">
										<div class="col-md-12">
											<form id="publishForm" method="post">
												<input type="hidden" class="form-control" name="id">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="message">Approval message</label>
															<textarea value="<?php echo $data['message'];?>" class="form-control" name="message" placeholder="Approval message" cols="10" rows="10"></textarea>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button class="btn btn-primary pull-left item-submit" type="submit"><i class="fa fa-check"></i> Publish</button>
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
		once.libraries.actions.publishInit($(this));
	});
</script>