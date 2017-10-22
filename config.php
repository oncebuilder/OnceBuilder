<?php 
# SECURE -----------------
if(!$home) exit;

$_CONFIG['api_key']='d41d8cd98f00b204e9800998ecf8427e';

$filename = './oconfig.php';
$filename2 = '../oconfig.php';
if(file_exists($filename)) {
	require_once('./oconfig.php');
}else if(file_exists($filename2)) {
	require_once('../oconfig.php');
}

# CONFIG MYSQL -------------------
$_CONFIG['datahost']='localhost';
$_CONFIG['database']='once';
$_CONFIG['datauser']='root';
$_CONFIG['datapass']='';

if(!function_exists('a')){
	function a($a){
		print_r($a);
	}
}
?>
