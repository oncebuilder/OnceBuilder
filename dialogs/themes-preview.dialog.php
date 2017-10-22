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

# GET REMOTE DATA -------------------
$data=$once->item_preview();
?>
<!-- COMPOSE MESSAGE MODAL -->
<div id="preview-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="<?php echo $_GET['id'];?>">
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-chain-broken"></i> Theme preview
					<?php 
					if($data['item']['price']==0 || $data['item']['bought']){
						echo '<button class="btn btn-success btn-sm pull-right item-download" type="button"><i class="fa fa-plus"></i> Download</button>';
					}
					?>
					<a href="https://oncebuilder.com/theme/<?php echo $_GET['id'];?>" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> More info</a>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<?php
								//If bought on premium
								if($data['item']['price']!=0){
									if(!$data['item']['bought']){
										echo '<div class="alert alert-warning" role="alert">This is premium service, theme will be unlocked after succefull purchase.</div>';
									}else{
										echo '<div class="alert alert-success" role="alert">This is premium service, you have already unlocked this theme.</div>';
									}
								}
							?>
							<div class="alert alert-danger hidden" role="alert">Bad response.</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#preview_settings" aria-controls="preview_settings" role="tab" data-toggle="tab">General info</a></li>
								<li role="presentation"><a href="#preview_screenshots" aria-controls="preview_screenshots" role="tab" data-toggle="tab">Screenshots</a></li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane active" id="preview_settings">
									<div class="row margin">
										<div class="col-md-3">
											<img src="https://oncebuilder.com/once/themes/<?php echo $_GET['id'];?>/thumbnail.png" onerror="this.src='img/theme.png'">
										</div>
										<div class="col-md-9">
											<h1><?php echo $data['item']['name'];?></h1>
											<h4>by: <?php echo $data['item']['author'];?></h4>
											<?php
											if(isset($update)){// for futures
											echo 
											'<div class="box box-info">
												<div class="box-header">
													<h3 class="box-title">Update Available</h3>
													<div class="box-tools pull-right">
														<div class="label bg-aqua">ver: 1.12</div>
													</div>
												</div>
												<div class="box-body">
													<p><strong><a title="Test" class="thickbox" href="">View version 1.0.1 details</a> before you overwrite theme</strong></p>
												</div><!-- /.box-body -->
											</div>';
											}
											?>
											<div class="row">
												<div class="col-md-12">
													<p><?php echo $data['item']['description'];?></p>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<p>Tags: <?php echo $data['item']['tags'];?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="preview_screenshots">
									<div class="row margin">
										<div class="col-md-12">
											<label for="title">Images</label>
										</div>
										<?php
										if(isset($data['item']['images'])){
											foreach($data['item']['images'] as $k => $v){
												echo '
												<div id="image_'.$k.'" class="col-md-3">
													<div class="thumbnail image">
														<img id="item-image" src="http://www.oncebuilder.com/once/themes/'.$_GET['id'].'/images/'.$v.'" onerror="this.src=\'img/theme.png\'; $(this).parent().parent().addClass(\'hidden\');">
													</div>
												</div>';
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-mail-reply"></i> Go back</button>
				</div>
			</div><!-- /.modal-content -->
		</div>
	</div><!-- /.modal -->
</div><!-- /.modal -->
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.themes.actions.previewInit($(this));
	});
</script>