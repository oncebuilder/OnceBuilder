<?php
# SECURE -----------------
if(!$home) exit;

# GLOBAL CONFIG -------------------
{config}

# LANGUAGE SETTINGS -------------------
{langs}

# GLOBAL SETTINGS -------------------
$_LANGS['en']='English';
$main=false;
$found=false;

# FUNNY FUNCTION
foreach ($_LANGS as $index => $value) {
	if($main==false) $main=$index;
	if($_GET['route']==$index) $found=$index;
}
if($found) $_SESSION['user_lang']=$index;
else $_SESSION['user_lang']=$main;

if(!function_exists('a')){
	function a($a){
		print_r($a);
	}
}
?>