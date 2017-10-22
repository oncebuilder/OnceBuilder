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
$data=$once->get_profile_data();
?>
<div class="row">
<div class="col-md-12">
	<h3>Contact information</h3>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<form id="informationForm" method="post">
					<div class="alert alert-danger message message-error">
						<p>There are serious errors in your form submission.</p>
						<ol></ol>
					</div>
					<div class="alert alert-success message message-success">
						<p>Your information has been changed!</p>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="firstname">First name</label>
							<input id="firstname" class="form-control" type="text" value="<?php echo $data['item']['firstname'];?>" name="firstname" tabindex="1" placeholder="First name">
						</div>
						<div class="form-group">
							<label for="email">E-mail</label>
							<input id="email" class="form-control" type="text" value="<?php echo $data['item']['email'];?>" name="email" tabindex="3" placeholder="E-mail">
						</div>
						<div class="form-group">
							<label for="company">Company name</label>
							<input id="company" class="form-control" type="text" value="<?php echo $data['item']['company'];?>" name="company" tabindex="5" placeholder="Company name">
						</div>
						<div class="form-group">
							<label for="address2">Address 2</label>
							<input id="address2" class="form-control" type="text" value="<?php echo $data['item']['address2'];?>" name="address2" tabindex="7" placeholder="Address 2">
						</div>
						<div class="form-group">
							<label for="phone">Phone</label>
							<input id="phone" class="form-control" type="text" value="<?php echo $data['item']['phone'];?>" name="phone" tabindex="9" placeholder="Phone">
						</div>
						<div class="form-group">
							<label for="province">State / province</label>
							<input id="province" class="form-control" type="text" value="<?php echo $data['item']['province'];?>" name="province" tabindex="11" placeholder="State / province">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-default" tabindex="13">Save</button>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="lastname">Last name</label>
							<input id="lastname" class="form-control" type="text" value="<?php echo $data['item']['lastname'];?>" name="lastname" tabindex="2" placeholder="Last name">
						</div>
						<div class="form-group">
							<label for="website">Website url</label>
							<input id="website" class="form-control" type="text" value="<?php echo $data['item']['website'];?>" name="website" tabindex="4" placeholder="Website url">
						</div>
						<div class="form-group">
							<label for="address">Address</label>
							<input id="address" class="form-control" type="text" value="<?php echo $data['item']['address'];?>" name="address" tabindex="6" placeholder="Address">
						</div>
						<div class="form-group">
							<label for="city">City</label>
							<input id="city" class="form-control" type="text" value="<?php echo $data['item']['city'];?>" name="city" tabindex="8" placeholder="City">
						</div>
						<div class="form-group">
							<label for="zip">Zip / postal code</label>
							<input id="zip" class="form-control" type="text" value="<?php echo $data['item']['zip'];?>" name="zip" tabindex="10" placeholder="Zip / postal code">
						</div>
						<div class="form-group">
							<label for="country">Country</label>
							<input id="country" class="form-control" type="text" value="<?php echo $data['item']['country'];?>" name="country" tabindex="12" placeholder="Country">
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
		// Initialize informationForm
		once.account.form.informationForm($(this));
	});
</script>