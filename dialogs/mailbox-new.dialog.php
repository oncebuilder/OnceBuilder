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

if(!isset($_GET['nomodal'])){
echo '
<div id="mailbox-data" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-id="'.$_GET['id'].'">';
}
?>
	<div class="container">
		<div class="modal-dialog" style="width: 100%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fa fa-envelope-o"></i> New Message</h4>
				</div>
				<form id="newForm" method="post">
					<div class="modal-body">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">Recipient:</span>
								<input name="email_to" type="text" class="form-control" placeholder="Email">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">Title:</span>
								<input name="title" type="text" class="form-control" placeholder="Title">
							</div>
						</div>
						<div class="form-group">
							<textarea name="message" id="email_message" class="form-control" placeholder="Message" style="height: 120px;"></textarea>
						</div>
					</div>
					<div class="modal-footer clearfix">
						<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
						<button type="submit" class="btn btn-primary pull-left"><i class="fa fa-envelope"></i> Send Message</button>
					</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
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
		once.mailbox.actions.editInit($(this));
	});
</script>