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
<div class="col-md-6">
	<h3>Change password</h3>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<form id="changepasswordForm" class="col-md-12" method="post">
					<div class="alert alert-danger message message-error">
						<p>There are serious errors in your form submission.</p>
						<ol></ol>
					</div>
					<div class="alert alert-success message message-success">
						<p>Your password has been changed!</p>
					</div>
					<div class="form-group">
						<label for="password">Current password</label>
						<input id="password" class="form-control" type="password" name="password" placeholder="Current password">
					</div>
					<div class="form-group">
						<label for="newpassword">New password</label>
						<input id="newpassword" class="form-control" type="password" name="newpassword" placeholder="New password">
					</div>
					<div class="form-group">
						<label for="confirmpassword">Confirm new password</label>
						<input id="confirmpassword" class="form-control" type="password" name="confirmpassword" placeholder="Confirm new password">
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-default">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize changepasswordForm
		once.account.form.changepasswordForm($(this));
	});
</script>