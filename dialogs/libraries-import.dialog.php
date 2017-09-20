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
?>
<!-- COMPOSE MESSAGE MODAL -->
<div id="import-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-cube"></i> Plugin import</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<!-- Tabs -->
							<ul class="nav nav-tabs">
								<li role="presentation" class="active"><a href="#import_file" aria-controls="import_file" role="tab" data-toggle="tab">Import via file</a></li>
								<li role="presentation"><a href="#import_url" aria-controls="import_url" role="tab" data-toggle="tab">Import by URL</a></li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<form id="importForm" method="post" enctype="multipart/form-data">
								<div class="tab-content">
									<div class="tab-pane active" id="import_file">
										<div class="row margin">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12">
														<div class="alert alert-info" role="alert">Uploaded file should be with .zip extension & include once.config</div>
														<div class="form-group">
															<label for="name">Source File</label>
															<input type="file" name="file">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="import_url">
										<div class="row margin">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12">
														<div class="alert alert-info" role="alert">Imported file should be with .zip extension & include once.config</div>
														<div class="form-group">
															<label for="name">Source URL</label>
															<input type="text" class="form-control" name="url" placeholder="Enter url">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="submit" class="hidden">
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer clearfix">
					<button class="btn btn-primary pull-left item-import-save" type="submit"><i class="fa fa-check"></i> Import</button>
					<button type="button" class="btn btn-danger item-close" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div>
	</div><!-- /.modal -->
</div><!-- /.modal -->
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.libraries.actions.importInit($(this));
	});
</script>