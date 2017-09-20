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
$data=$once->once_select_item('mailbox');

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
                        <h4 class="modal-title"><i class="fa fa-envelope-o"></i> <span class="mail-title"><?php echo $data['item']['title'];?></span>
							<button class="btn btn-default btn-sm pull-right item-delete" type="button"><i class="fa fa-trash-o"></i> Delete</button>
							<button class="btn btn-default btn-sm pull-right item-reply" type="button"><i class="fa fa-mail-reply"></i> Reply</button>
							<button class="btn btn-default btn-sm pull-right item-forward" type="button"><i class="fa fa-mail-reply-all"></i> Forward</button>
							<?php
							if($data['item']['stared']==1){
								echo '<button class="btn btn-default btn-sm pull-right item-stared" type="button"><i class="fa fa-star"></i> Stared</button>';
							}else{
								echo '<button class="btn btn-success btn-sm pull-right item-star" type="button"><i class="fa fa-star-o"></i> Star</button>';
							}
							?>
						</h4>
                    </div>
					<form id="newForm" method="post">
						<div id="mail">
							<div class="modal-head">
								<div class="mail-sender"> 
									<a href="#"><span><?php echo $data['item']['author'];?></span> (<span class="mail-reciver"><?php echo $data['item']['email'];?></span>)</a>
								</div> 
								<div class="mail-date">
									<?php echo date("Y-m-d H:i:s", $data['items'][$key]['time']);?>
								</div>
							</div>
							<div class="modal-body">
								<div class="mail-message"><p><?php echo $data['item']['message'];?></div>
							</div>
						</div>
                        <div id="form" class="modal-body hidden">
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
                            <button type="submit" class="btn btn-primary pull-left hidden"><i class="fa fa-envelope"></i> Send Message</button>
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