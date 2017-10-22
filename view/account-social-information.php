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
# INITIALIZE -------------------
$once=new once($_CONFIG);

# GET DATA -------------------
$data=$once->get_social_data();
?>
<div class="row">
<div class="col-md-12">
	<h3>Social information</h3>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<form id="socialForm" method="post">
					<div class="alert alert-danger message message-error">
						<p>There are serious errors in your form submission.</p>
						<ol></ol>
					</div>
					<div class="alert alert-success message message-success">
						<p>Your information has been changed!</p>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="facebook">Facebook</label>
							<input id="facebook" class="form-control" type="text" value="<?php echo $data['item']['facebook'];?>" name="facebook" tabindex="1" placeholder="Facebook">
						</div>
						<div class="form-group">
							<label for="twitter">Twitter</label>
							<input id="twitter" class="form-control" type="text" value="<?php echo $data['item']['twitter'];?>" name="twitter" tabindex="3" placeholder="Twitter">
						</div>
						<div class="form-group">
							<label for="youtube">Youtube</label>
							<input id="youtube" class="form-control" type="text" value="<?php echo $data['item']['youtube'];?>" name="youtube" tabindex="3" placeholder="Youtube">
						</div>
						<div class="form-group">
							<label for="linkedin">Linkedin</label>
							<input id="linkedin" class="form-control" type="text" value="<?php echo $data['item']['linkedin'];?>" name="linkedin" tabindex="5" placeholder="Linkedin">
						</div>
						<div class="form-group">
							<label for="dribbble">Dribbble</label>
							<input id="dribbble" class="form-control" type="text" value="<?php echo $data['item']['dribbble'];?>" name="dribbble" tabindex="7" placeholder="Dribbble">
						</div>
						<div class="form-group">
							<label for="github">GitHub</label>
							<input id="github" class="form-control" type="text" value="<?php echo $data['item']['github'];?>" name="github" tabindex="9" placeholder="GitHub">
						</div>
						<div class="form-group">
							<label for="google">Google+</label>
							<input id="google" class="form-control" type="text" value="<?php echo $data['item']['google'];?>" name="google" tabindex="11" placeholder="Google+">
						</div>
						<div class="form-group">
							<label for="behance">Behance</label>
							<input id="behance" class="form-control" type="text" value="<?php echo $data['item']['behance'];?>" name="behance" tabindex="11" placeholder="Behance">
						</div>
						<div class="form-group">
							<label for="codepen">CodePen</label>
							<input id="codepen" class="form-control" type="text" value="<?php echo $data['item']['codepen'];?>" name="codepen" tabindex="11" placeholder="CodePen">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-default" tabindex="13">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize socialForm
		once.account.form.socialForm($(this));
	});
</script>