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
$data_sources=array('','source_ui','source_php','source_css','source_js');
$data_sources_titles=array('','Plugin UI','PHP','CSS','JS','DEPENDENCIES');
$data_sources_count=count($data_sources_titles);

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
					<h4 class="modal-title"><i class="fa fa-cube"></i> Plugin preview
					<?php 
					if($data['item']['price']==0 || $data['item']['bought']){
						echo '<button class="btn btn-success btn-sm pull-right item-download" type="button"><i class="fa fa-plus"></i> Download</button>';
					}
					?>
					<a href="https://oncebuilder.com/plugin/<?php echo $_GET['id'];?>" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> More info</a>
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
								<?php
									if($data['item']['price']==0 || $data['item']['bought']){
										for($i=1;$i<$data_sources_count;$i++){
											echo '<li role="presentation"><a href="#preview_source" aria-controls="preview_source" role="tab" data-toggle="tab" data-source="'.$i.'" data-editor="'.$i.'">'.$data_sources_titles[$i].'</a></li>';
										}
									}
								?>
								<li role="presentation"><a href="#preview_screenshots" aria-controls="preview_screenshots" role="tab" data-toggle="tab">Screenshots</a></li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane active" id="preview_settings">
									<div class="row margin">
										<div class="col-md-12">
											<div class="plugin-item">
												<div class="row plugin-content">
													<div class="col-md-2">
														<img src="https://oncebuilder.com/once/plugins/<?php echo $_GET['id'];?>/thumbnail.png" onerror="this.src='img/plugin.png'">
													</div>
													<div class="col-md-10">
														<h3><?php echo $data['item']['name'];?> <span><!--Updated: <?php echo $data['item']['created'];?> --><span style="display: inline; cursor: pointer;">by <a href="<?php echo $data['item']['author_url'];?>" target="_blank"><?php echo $data['item']['author'];?></a></span></span></h3>
														<div style="min-height: 135px;">
															<?php echo $data['item']['description'];?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="preview_source">
									<div class="row margin">
										<div class="col-md-12">
											<textarea id="ajax-playground" name="ajax-playground"></textarea>
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
														<img id="item-image" src="http://www.oncebuilder.com/once/plugins/'.$_GET['id'].'/images/'.$v.'" onerror="this.src=\'img/plugin.png\'; $(this).parent().parent().addClass(\'hidden\');">
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
<?php
if($data['item']['price']==0){
	for($i=1;$i<$data_sources_count;$i++){
		echo '<code id="source_'.$i.'" class="hidden">'.rawurlencode($data['item'][$data_sources[$i]]).'</code>';
	}
}
?>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.plugins.actions.previewInit($(this));
	});
</script>