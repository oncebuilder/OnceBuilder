<?php
/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Login plugin (once.login)
 *
*/

if(isset($_POST['email']) && isset($_POST['password'])){ // Login to service
	# CLASS -----------------
	require_once('./once/class/login.class.php');
 	$once = new once($_CONFIG);
  
	# SET DATA -------------------
	$once->set_data(array(
		"email" => $once->filter_string($_POST['email']),
		"password" => $once->filter_string($_POST['password'])
	));

	# GET DATA -------------------
	$result=$once->item_login();
	
	if($result['status']=='ok'){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			document.location.href=$("#loginPlugin").data("redirect");
		});
	},1000);
</script>';
	}else{
		if($result['error']>0){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#loginPlugin .message-sent").hide();
			$("#loginPlugin .message-error").show();
			var str=\'\';';

			foreach($result['errors'] as $k => $v){
				$str.='
				str+=\'<li>'.$result['errors'][$k].'</li>\';';
			}
			$str.='
			$("#loginPlugin .message-error").find("ol").show();
			$("#loginPlugin .message-error").find("ol").html(str);
		});
	},1000);
</script>';		
		}
	}
	
}else if(isset($_POST['email'])){ // Remind password
	# CLASS -----------------
	require_once('./once/class/login.class.php');
 	$once = new once($_CONFIG);
  
	# SET DATA -------------------
	$once->set_data(array(
		"email" => $once->filter_string($_POST['email'])
	));

	# GET DATA -------------------
	$result=$once->item_remind();

	$str='
	<script type="text/javascript">
		setTimeout(function(){
			$(document).ready(function () {
				$("#loginForm").hide();
				$("#remindForm").show();
				$("#changeForm").hide();';
				if($result['status']=='ok'){
					$str.='
					$("#remindForm .message-error").hide();
					$("#remindForm .message-success").show();';
				}else{
					if($result['error']>0){
					$str.='
						$("#remindForm .message-error").show();
						$("#remindForm .message-success").hide();';
					}
				}
			$str.='
			});
		},1000);
	</script>';
}else if(isset($_GET['uid']) && isset($_GET['hash']) && isset($_POST['password'])){ // Change password
	# CLASS -----------------
	require_once('./once/class/login.class.php');
 	$once = new once($_CONFIG);
  
	# SET DATA -------------------
	$once->set_data(array(
		"user_id" => intval($_GET['uid']),
		"hash" => $once->filter_string($_GET['hash']),
		"password" => $once->filter_string($_POST['password'])
	));

	# GET DATA -------------------
	$result=$once->item_change();;

	$str='
	<script type="text/javascript">
		setTimeout(function(){
			$(document).ready(function () {
				$("#loginForm").hide();
				$("#remindForm").hide();
				$("#changeForm").show();';
				if($result['status']=='ok'){
					$str.='
					$("#changeForm .message-success").show();
					$("#changeForm .message-error").hide();
					$("#changeForm .form-group").hide();';
				}else{
					if($result['error']>0){
					$str.='
						$("#changeForm .message-sent").hide();
						$("#changeForm .message-error").show();';
					}
				}
			$str.='
			});
		},1000);
	</script>';
}else if(isset($_GET['o']) && $_GET['o']=='remind'){ // Remind
	# CLASS -----------------
	require_once('./once/class/login.class.php');
 	$once = new once($_CONFIG);

	# SET DATA -------------------
	$once->set_data(array(
		"user_id" => intval($_GET['uid']),
		"hash" => $once->filter_string($_GET['hash'])
	));

	$result=$once->check_remind();

	$str='
	<script type="text/javascript">
		setTimeout(function(){
			$(document).ready(function () {
				$("#loginForm").hide();
				$("#remindForm").hide();
				$("#changeForm").show();';
				if($result['status']=='ok'){
					$str.='
					$("#changeForm .message-error").hide();
					$("#changeForm .message-deletion").hide();';
				}else{
					if($result['error']>0){
					$str.='
						$("#changeForm .message-sent").hide();
						$("#changeForm .message-error").show();';
					}
				}
			$str.='
			});
		},1000);
	</script>';
}
echo $str;
?>
<div class="row" id="loginPlugin" data-redirect="/account" data-require="/once/js/once.login.js" data-o=<?php echo $_GET['o'];?> data-error="<?php echo $result['errors'][0];?>" data-status="<?php echo $result['status'];?>">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
		<img src="/once/images/blacklogo.png" alt="oncebuilder company logo">
		<form id="loginForm" role="form" method="post">
			<p class="text-center">Don't have OnceBuilder account? signup <a href="/signup">here</a></p>
			<hr class="colorgraph">
			<div class="alert alert-danger message message-error">
				<p>There are serious errors in your form submission.</p>
				<ol></ol>
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" class="form-control input-xs" value="<?php echo $_POST['email'];?>"  placeholder="email">
			</div>
			<div class="form-group">
				<label for="password">Password<span class="pull-right"> <a class="item-forgot">Forgot password</a></span></label>
				<input type="password" name="password" id="password" class="form-control input-xs" placeholder="password">
			</div>
			<div class="checkbox">
				<label>
			  		<input name="remember" type="checkbox" value="Remember Me"> Remember Me
			  	</label>
			</div>
			<div class="form-group">
				<input type="submit" value="Log In" class="btn btn-primary btn-block btn-lg">
			</div>
			<!--<p class="text-center">or<br><br></p>
			<div class="form-group">
				<input type="submit" value="Log In via Facebook" class="btn btn-primary btn-block btn-lg">
			</div>-->
			<p>By clicking this button, you agree to OnceBuilder's <a href="/terms">Terms of Use.</a></p>
		</form>
		<form id="remindForm" role="form" method="post">
			<p class="text-center">We'll email you instructions on how to reset your password.</p>
			<hr class="colorgraph">
			<div class="message message-success">
				<div class="alert alert-success" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>
					Success!<br><br>

					We have sent an email with reset instructions.
				</div>
				<p>If the email does not arrive soon, check your spam folder. It was sent from confirm@oncebuilder.com.</p>
			</div>
			<div class="message message-error">
				<div class="alert alert-danger" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>
					Wrong email.
				</div>
			</div>
			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" name="email" id="email" class="form-control input-xs" placeholder="email">
			</div>
			<div class="row form-group">
				<div class="col-md-6">
					<input type="submit" value="Reset password" class="btn btn-primary btn-block btn-lg">
				</div>
				<div class="col-md-6">
					<input type="button" value="Return to login" class="btn btn-default btn-block btn-lg item-back">
				</div>
			</div>
		</form>
		<form id="changeForm" role="form" method="post">
			<input type="hidden" name="uid" value="<?php echo $_GET['uid'];?>">
			<input type="hidden" name="hash" value="<?php echo $_GET['hash'];?>">
			<p class="text-center">Enter your new password.</p>
			<hr class="colorgraph">
			<div class="message message-success">
				<div class="alert alert-success" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>
					Success!<br><br>

					Your password has been updated.
					
					<p class="text-center">Login <a href="/login">here</a></p>
				</div>
			</div>
			<div class="message message-error">
				<div class="alert alert-danger" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only"></span>
					Password can contain only a-z, A-Z, 0-9, _ and should be between 6 to 20 chars // Or it's just a wrong change link...
				</div>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<div class="input-group">
					<input title="Please write your password." type="password" name="password" id="password" class="form-control" placeholder="password" aria-describedby="password-toggle" required>
					<span class="input-group-addon" id="password-toggle"><i class="glyphicon glyphicon-eye-open"></i></span>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-md-6 button button-change">
					<input type="submit" value="Change password" class="btn btn-primary btn-block btn-lg">
				</div>
			</div>
		</form>
	</div>
</div>