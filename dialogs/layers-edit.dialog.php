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
	"id" => $once->filter_string($_GET['id'])
));

# GET DATA -------------------
$data=$once->once_select_item('layers','all');
if(!isset($_GET['nomodal'])){
echo '
<!-- COMPOSE MESSAGE MODAL -->
<div id="layer-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'">';
}
?>
	<div class="container">
		<div class="container">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-th"></i> Edit layer set
						<button class="btn btn-default btn-sm pull-right item-delete" type="button" <?php echo($data['item']['default']==1?'disabled':'')?>><i class="fa fa-trash-o"></i> Delete</button>
					</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<form id="editForm" method="post">
									<div class="form-group">
										<div class="input-group">
											<label>Name:</label>
											<input name="name" type="text" class="form-control" value="<?php echo $data['item']['name'];?>">
										</div>
									</div>
									<div class="form-group">
										<div class="input-group">
											<label>Default:</label>
											<select class="form-control" name="default">
												<option value="0" <?php echo($data['item']['default']==0?'selected':''); ?> <?php echo($data['item']['default']==1?'disabled':''); ?>>No</option>
												<option value="1" <?php echo($data['item']['default']==1?'selected':''); ?>>Yes</option>
											</select>
										</div>
									</div>
									<input type="submit" class="hidden">
								</form>
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
	</div>
<?php
if(!isset($_GET['nomodal'])){
echo '
</div><!-- /.modal -->';
}
?>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.layers.actions.editInit($(this));
	});
</script>