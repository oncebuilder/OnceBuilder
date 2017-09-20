<?php

// Report simple running errors except notices
error_reporting(E_ERROR | E_WARNING | E_PARSE);

ob_start("ob_gzhandler");
$home=true;

# SESSION -----------------
{session}
# XAMPP fix without turning error info off -------------------
$_GET['route'] = isset($_GET['route']) ? $_GET['route'] : '';

# CONFIG -----------------
{config}

# ROUTES -------------------
{routes}

# LANGS -------------------
{langs}

# CLASS -------------------
{classes}

# PAGE START -------------------
echo '
<!DOCTYPE html>
<html>
	<head>
		';
		# PAGE HEADER -------------------
		{headers}
		echo '
		<meta content="'.$once->once_csrf_token().'" name="csrf_token">

		<!-- Latest compiled and minified global styles from layers -->
		<link rel="stylesheet" href="/css/global.css" type="text/css">
		
		<!-- Latest compiled and minified styles from layers -->
		<link rel="stylesheet" href="/css/style.css" type="text/css">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div id="body">';
		# PAGE GRIDS -------------------
		{grids}
		echo '
		</div>
		<!-- Latest compiled and minified jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		
		<!-- Latest compiled and minified modernizr -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>

        <!-- Latest compiled and minified once -->
		<script type="text/javascript" src="/once/js/once.js"></script>

		<!-- Latest dependencies -->
        {dependencies}
		
        <!-- Latest compiled and minified script of all layers -->
		<script type="text/javascript" src="/js/script.js"></script>
	</body>
</html>';
ob_end_flush(); 
?>