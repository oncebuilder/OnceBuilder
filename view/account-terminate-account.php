<?php
/**
 * Version: 1.0, 04.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Account plugin (once.account)
 *
*/
?>
<div class="row">
<div class="col-md-12 step">
	<p>You can delete account anytime, your personal data in the account will be permanently deleted. All your resources under MIT licence that have been published will be archived on this site.</p>
	<br><button type="button" class="btn btn-default btn-xs terminate-account">Continiue</button>
</div>
<div class="col-md-12 step step2">
	<p>To delete account you must verify this action by provide password and confirmation email.</p>
	<form id="terminateForm" method="post">
		<div class="form-group">
			<label for="blankRadio">Reason of terminiation?</label>
			<div class="radio">
				<label> 
					<input class="item-radio" type="radio" name="blankRadio" id="blankRadio1" value="option1" aria-label="..."> I don't need it anymore.
				</label>
				<label> 
					<input class="item-radio" type="radio" name="blankRadio" id="blankRadio1" value="option2" aria-label="..."> Other reason.
				</label>
			</div>
			<textarea name="reason" class="form-control" rows="3" placeholder="Please tell us reason"></textarea>
		</div>
		<div class="alert alert-danger message message-error">
			<p>There are serious errors in your form submission.</p>
			<ol></ol>
		</div>
		<div class="form-group">
			<label for="password">Current password</label>
			<input id="password" class="form-control" type="text" value="" name="password" placeholder="Current password">
		</div>
		<button type="submit" class="btn btn-default btn-xs terminate-account">Delete me</button>
	</form>
</div>
<div class="col-md-12 step step3">
	<div class="alert alert-success">
		<p>We have send you deletion confirmation on your email. We are sad that you are deleting your account :( But we always wish you the best, take care <?php echo $_SESSION['user_username'];?>!</p>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		$("#accountPluginContent .terminate-account").click(function () {
			once.account.itemTerminate($(this));
		});
		$("#accountPluginContent .item-radio").click(function () {
			if($(this).val()=='option2'){
				$("#terminateForm textarea").show();
			}else{
				$("#terminateForm textarea").hide();
			}
		});
		// Initialize terminateForm
		once.account.form.terminateForm($(this));
	});
</script>
