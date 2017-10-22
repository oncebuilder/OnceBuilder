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
<div class="col-md-6">
	<h3>Hi <?php echo $_SESSION['user_username'];?>!</h3>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="col-md-2">
					<?php 
					if($data['item']['photo_url']){
						echo '<img id="item-photo" width="100" height="100" src="/images/'.$data['item']['photo_url'].'.png">';
					}else{
						echo '<img id="item-photo" src="/once/users/'.$_SESSION['user_id'].'/thumbnail.png"  width="100" height="100" alt="user logo" onerror="this.src=\'/images/emptyphoto.png\'" />';
					}
					?>
				</div>
				<div class="col-md-10">
					<h4>Upload your photo …</h4>
					<p>Photo should be at least 256px × 256px </p>
					<button type="button" class="btn btn-default item-photo">Upload photo</button>
				</div>
			</div>
			<div class="row">
				<form id="editForm" class="col-md-12" method="post">
					<div class="alert alert-danger message message-error">
						<p>There are serious errors in your form submission.</p>
						<ol></ol>
					</div>
					<div class="alert alert-success message message-success">
						<p>Your information has been changed!</p>
					</div>
					<div class="form-group">
						<label for="username">Username</label>
						<input title="Please write your username." id="username" class="form-control" type="text" value="<?php echo $_SESSION['user_username'];?>" name="username" disabled placeholder="Username">
					</div>
					<div class="form-group">
						<label for="firstname">First name</label>
						<input title="Please write your first name." id="firstname" class="form-control" type="text" value="<?php echo $data['item']['firstname'];?>" name="firstname" placeholder="First name">
					</div>
					<div class="form-group">
						<label for="lastname">Last name</label>
						<input title="Please write your last name." id="lastname" class="form-control" type="text" value="<?php echo $data['item']['lastname'];?>" name="lastname" placeholder="Last name">
					</div>
					<div class="form-group">
						<label for="position">Position</label>
						<input title="Please write your last name." id="position" class="form-control" type="text" value="<?php echo $data['item']['position'];?>" name="position" placeholder="Position">
					</div>
					<div class="form-group">
						<label for="location">Location</label>
						<input title="Please write your last name." id="location" class="form-control" type="text" value="<?php echo $data['item']['location'];?>" name="location" placeholder="Location">
					</div>
					<div class="form-group">
						<label for="email">E-mail</label>
						<input id="email" class="form-control" type="text" value="<?php echo $_SESSION['user_email'];?>" name="email" disabled placeholder="E-mail">
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
<form id="uploadImage" action="/ajax.php?c=account&o=upload_image"" method="post" enctype="multipart/form-data" class="hidden">
	<input type="file" size="60" name="myImage" id="myImage">
	<input type="submit" value="Ajax File Upload">
</form>
<script type="text/javascript">
	$(document).ready(function () {
		// Change logo
		$("#accountPluginContent .item-photo").click(function () {
			once.account.itemEditPhoto($(this));
		});
		
		// Initialize editForm & uploadImage
		once.account.form.editForm($(this));
		once.account.form.uploadImage($(this));
	});
</script>