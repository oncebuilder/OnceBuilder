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
$data_sources=array('','source_html','source_css','source_js');
$data_sources_titles=array('','HTML','CSS','JS');
$data_sources_count=count($data_sources);

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
					<h4 class="modal-title"><i class="fa fa-bars"></i> Snippet preview
						<button class="btn btn-success btn-sm pull-right item-download" type="button"><i class="fa fa-plus"></i> Download</button>
						<a href="http://oncebuilder.com/snippet/<?php echo $_GET['id'];?>" target="_blank" class="btn btn-default btn-sm pull-right item-link" type="button"><i class="fa fa-link"></i> More info</a>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#preview_settings" aria-controls="preview_settings" role="tab" data-toggle="tab">General info</a></li>
								<?php
									for($i=1;$i<$data_sources_count;$i++){
										echo '<li role="presentation"><a href="#preview_source" aria-controls="preview_source" role="tab" data-toggle="tab" data-source="'.$data_sources[$i].'" data-editor="'.$i.'">'.$data_sources_titles[$i].'</a></li>';
									}
									echo '<li role="presentation"><a href="#preview_preview" aria-controls="preview_preview" role="tab" data-toggle="tab">Preview</a></li>';
								?>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div class="tab-pane active" id="preview_settings">
									<div class="row margin">
										<div class="col-md-12">
											<div class="snippet-item">
												<div class="row snippet-content">
													<div class="col-md-2">
														<img src="http://oncebuilder.com/once/snippets/<?php echo $_GET['id'];?>/thumbnail.png" onerror="this.src='/once/img/snippet.png'">
													</div>
													<div class="col-md-10">
														<h3><?php echo $data['item']['name'];?> 
														<span>
															<span style="display: inline; cursor: pointer;">by 
																<a href="<?php echo $data['item']['author_url'];?>" target="_blank">
																	<?php echo $data['item']['author'];?>
																</a>
															</span>
														</span>
														</h3>
														<div>
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
								<div class="tab-pane" id="preview_preview">
									<div class="row margin">
										<div class="col-md-12">
											<iframe src="http://oncebuilder.com/once/snippets/<?php echo $_GET['id'];?>/index.php"></iframe>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div>
	</div><!-- /.modal -->
</div><!-- /.modal -->
<?php 
if($data['item']['price']==0){
	for($i=1;$i<$data_sources_count;$i++){
		echo '<code id="source_'.$i.'">'.$data['item'][$data_sources[$i]].'</code>';
	}
}
?>
<script type="text/javascript">
    $(document).ready(function () {
		// Initialize actions
		once.snippets.actions.previewInit($(this));
	});
</script>