<?php
/**
 * Version: 1.0, 01.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Register plugin (once.register)
 *
*/

// After Submit registerForm
if(isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])){
	# CLASS -----------------
	require_once('./once/class/register.class.php');
 	$once = new once($_CONFIG);

	# SET DATA -------------------
	$once->set_data(array(
		"email" => $once->filter_string($_POST['email']),
		"username" => $once->filter_string($_POST['username']),
		"password" => $once->filter_string($_POST['password'])
	));

	$result=$once->item_register();

	if($result['status']=='ok'){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-error").hide();
			$("#registerPlugin .message-registred").show();
			$("#registerForm").hide();
		});
	},1000);
</script>';
	}else{
		if($result['error']>0){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-sent").hide();
			$("#registerPlugin .message-error").show();
			var str=\'\';';

			foreach($result['errors'] as $k => $v){
				$str.='
				str+=\'<li>'.$result['errors'][$k].'</li>\';';
			}
			$str.='
			$("#registerPlugin .message-error").find("ol").show();
			$("#registerPlugin .message-error").find("ol").html(str);
		});
	},1000);
</script>';		
		}
	}
}

// After click to link confirmation
if(isset($_GET['o']) && $_GET['o']=='active'){
	# CLASS -----------------
	require_once('./once/class/register.class.php');
 	$once = new once($_CONFIG);

	# SET DATA -------------------
	$once->set_data(array(
		"user_id" => intval($_GET['uid']),
		"hash" => $once->filter_string($_GET['hash'])
	));

	$result=$once->check_activiation();
	
	if($result['status']=='ok'){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-error").hide();
			$("#registerPlugin .message-active").show();
			$("#registerForm").hide();
		});
	},1000);
</script>';
	}else{
		if($result['error']>0){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-sent").hide();
			$("#registerPlugin .message-error").show();
			$("#registerPlugin .message-notactived").show();
			var str=\'\';';

			foreach($result['errors'] as $k => $v){
				$str.='
				str+=\'<li>'.$result['errors'][$k].'</li>\';';
			}
			$str.='
			$("#registerPlugin .message-error").find("ol").show();
			$("#registerPlugin .message-error").find("ol").html(str);
		});
	},1000);
</script>';		
		}
	}
}

// After click to deletion confirmation
if(isset($_GET['o']) && $_GET['o']=='delete'){
	# CLASS -----------------
	require_once('./once/class/register.class.php');
 	$once = new once($_CONFIG);

	# SET DATA -------------------
	$once->set_data(array(
		"user_id" => intval($_GET['uid']),
		"hash" => $once->filter_string($_GET['hash'])
	));

	$result=$once->check_deletion();

	if($result['status']=='ok'){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-error").hide();
			$("#registerPlugin .message-deletion").show();
			$("#registerForm").hide();
		});
	},1000);
</script>';
	}else{
		if($result['error']>0){
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {
			$("#registerPlugin .message-sent").hide();
			$("#registerPlugin .message-error").show();
			$("#registerPlugin .message-notdeleted").show();
			var str=\'\';';

			foreach($result['errors'] as $k => $v){
				$str.='
				str+=\'<li>'.$result['errors'][$k].'</li>\';';
			}
			$str.='
			$("#registerPlugin .message-error").find("ol").show();
			$("#registerPlugin .message-error").find("ol").html(str);
		});
	},1000);
</script>';		
		}
	}
}
echo $str;

/*
// After click to deletion confirmation
if(isset($_GET['o']) && $_GET['o']=='delete'){
	# CLASS -----------------
	require_once('./once/class/register.class.php');
 	$once = new once($_CONFIG);

	# SET DATA -------------------
	$once->set_data(array(
		"user_id" => intval($_GET['uid']),
		"hash" => $once->filter_string($_GET['hash'])
	));

	$result=$once->check_deletion();

	
$str='
<script type="text/javascript">
	setTimeout(function(){
		$(document).ready(function () {';
		if($result['status']=='ok'){
			$str.='
			$("#registerPlugin .message-error").hide();
			$("#registerPlugin .message-deletion").show();
			$("#registerForm").hide();';
		}else{
			if($result['error']>0){
			$str.='
				$("#registerPlugin .message-sent").hide();
				$("#registerPlugin .message-error").show();
				$("#registerPlugin .message-notdeleted").show();
				var str=\'\';';
				foreach($result['errors'] as $k => $v){
					$str.='
					str+=\'<li>'.$result['errors'][$k].'</li>\';';
				}
				$str.='
				$("#registerPlugin .message-error").find("ol").show();
				$("#registerPlugin .message-error").find("ol").html(str);';
			}
		}
		$str.='});
	},1000);
</script>';
}
*/

?>
<div class="row" id="registerPlugin" data-require="/once/js/once.register.js" data-o=<?php echo $_GET['o'];?> data-error="<?php echo $result['errors'][0];?>" data-status="<?php echo $result['status'];?>">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
		<img src="/once/images/blacklogo.png" alt="company logo">
		<p class="text-center">Sign up in 30 seconds. No credit card required.</p>
		<p class="text-center">Already have a OnceBuilder account? Log in <a href="/login">here.</a></p>
		<hr class="colorgraph">
		<div class="alert alert-danger message message-error">
			<p>There are serious errors in your form submission.</p>
			<ol></ol>
		</div>
		<div class="alert alert-success message message-registred">
			<p>Your account has been created. We have sent confirmation email to you. Thank you for cooperation!</p>
		</div>
		<div class="alert alert-warning message message-notactived">
			<p>If you have problem with activation, please let use know <a href="/contact">contact</a>.</p>
		</div>
		<div class="alert alert-success message message-deletion">
			<p>You have deleted account :(</p>
		</div>
		<div class="alert alert-warning message message-notdeleted">
			<p>There is problem with deletion. please let use know <a href="/contact">contact</a>.</p>
		</div>
		<div class="alert alert-success message message-active">
			<p>Your account has been actived. Please <a href="/login">login here</a></p>
		</div>
		<form id="registerForm" role="form" method="post">
			<div class="form-group">
				<label for="email">Email</label>
				<input title="Please write your email." type="email" name="email" id="email" class="form-control input-xs" value="<?php echo $_POST['email'];?>" placeholder="email" required>
			</div>
			<div class="form-group">
				<label for="username">Username</label>
				<input title="Please write your username." type="text" name="username" id="username" class="form-control input-xs" value="<?php echo $_POST['username'];?>" placeholder="username" required>
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<div class="input-group">
					<input title="Please write your password." type="password" name="password" id="password" class="form-control" placeholder="password" aria-describedby="password-toggle" required>
					<span class="input-group-addon" id="password-toggle"><i class="glyphicon glyphicon-eye-open"></i></span>
				</div>
			</div>
			<div class="form-group">
				<input type="submit" value="Create my account" class="btn btn-primary btn-block btn-lg" tabindex="7">
			</div>
			<!--<p class="text-center">or<br><br></p>
			<div class="form-group">
				<input type="submit" value="Create via Facebook" class="btn btn-primary btn-block btn-lg">
			</div>-->
			<p>By clicking button, you agree to OnceBuilder's <a href="/terms" target="_blank">Terms of Use.</a></p>
		</form>
	</div>
</div>