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
	"id" => intval($_GET['id']),
	"path" => $once->filter_string($_GET['path'])
));

$data=$once->get_file_info();

if(!isset($_GET['nomodal'])){
echo '
<div id="image-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-path="'.$_GET['path'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-bars"></i> Edit <?php echo $data['item']['file']===true?'file':'dir';?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<form id="editForm" method="post">
								<div class="form-group">
									<label for="name">Name</label>
									<input type="text" value="<?php echo $data['item']['name'];?>" class="form-control" name="name" placeholder="Enter name">
								</div>
								<input type="submit" class="hidden">
							</form>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?php echo $data['item']['file']===true?'<img src="/images'.$_GET['path'].'">':'';?>
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
		once.images.actions.editInit($(this));
	});
</script>