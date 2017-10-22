<?php

// Report simple running errors except notices
error_reporting(E_ERROR | E_WARNING | E_PARSE);

ob_start("ob_gzhandler");

# SESSION -----------------
session_start();
$home=true;

# XAMPP FIXING -----------------
$_GET['route'] = isset($_GET['route']) ? $_GET['route'] : 'about';
$_GET['v'] = isset($_GET['v']) ? $_GET['v'] : '';

# LOGIN -----------------
if(!$_SESSION['user_logged']){// || $_SESSION['user_type_id']!=1
	session_unset();
	session_destroy();
	header("Location: login.php"); /* Redirect browser */
}

# LOGOUT ----------------
if($_GET['route']=='logout'){
	session_unset();
	session_destroy();
	header("Location: login.php"); /* Redirect browser */
}

# SECURE -----------------
if(!$home) exit;

# CONFIG -----------------
require_once('./config.php');

# CLASS -------------------
require_once('./class/core.class.php');
$core = new core($_CONFIG);

# GET DATA -------------------
//$data=$once->once_mailbox_notification();

# PAGE START -------------------
?>
<!DOCTYPE html>
<html>
    <head>
		<!-- some meta tags / oncebuilder.com -->
		<title>Once CMS | Dashboard</title>
		 
		<meta charset="UTF-8">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<meta content="<?php echo $core->once_csrf_token(true);?>" name="csrf_token">

		<!-- Boostrap style -->
		<link href="libs/bootstrap-3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

		<!-- Animate css -->
		<link href="libs/animateCSS/animate.css" rel="stylesheet" type="text/css" />
		
		<!-- Font awesome -->
		<link href="libs/font-awesome-4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

        <!-- Ionicons -->
        <link href="libs/ionicons-2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />

		<!-- Theme AdminLTE style -->
        <link href="libs/AdminLTE/AdminLTE.css" rel="stylesheet" type="text/css" />
		
		<!-- iCheck for checkboxes and radio inputs -->
        <link href="libs/icheck-1.0.2/skins/minimal/blue.css" rel="stylesheet" type="text/css" />

		<!-- Gridstack.js style -->
		<link rel="stylesheet" href="libs/gridstack/dist/gridstack.css"/>
		
		<!-- code mirror -->
		<link href="libs/codemirror/lib/codemirror.css" type="text/css" rel="stylesheet">
		<link href="libs/codemirror/addon/fold/foldgutter.css" type="text/css" rel="stylesheet">
		<link href="libs/codemirror/addon/dialog/dialog.css" type="text/css" rel="stylesheet">
		<link href="libs/codemirror/theme/monokai.css" type="text/css" rel="stylesheet">
		
		<!-- Oncebudiler style -->
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		
		<!-- Running thanks to jQuery & Bootstrap -->
		<script src="libs/jquery/jquery.min.js"></script>
		<script src="libs/bootstrap-3.3.6/js/bootstrap.min.js" type="text/javascript"></script>

		<!-- jQuery UI -->
        <script src="libs/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>

		<!-- some cdns -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/3.5.0/lodash.min.js"></script>
		
		<!-- Gridstack.js -->
		<script src="libs/gridstack/dist/gridstack.js"></script>
		
		<!-- jQuery Ajax From -->
		<script src="libs/jquery-form/jquery.form.js" type="text/javascript"></script>

		<!-- code mirror -->
		<script type="text/javascript" src="libs/codemirror/lib/codemirror.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/xml/xml.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/javascript/javascript.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/css/css.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/vbscript/vbscript.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/htmlmixed/htmlmixed.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/clike/clike.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/php/php.js"></script>
		<script type="text/javascript" src="libs/codemirror/mode/markdown/markdown.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/comment/comment.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/dialog/dialog.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/edit/matchtags.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/edit/matchbrackets.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/foldcode.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/foldgutter.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/brace-fold.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/xml-fold.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/markdown-fold.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/fold/comment-fold.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/hint/show-hint.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon//tern/tern.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/search/match-highlighter.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/search/search.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/search/searchcursor.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/selection/active-line.js"></script>
		<script type="text/javascript" src="libs/codemirror/addon/wrap/hardwrap.js"></script>
		<script type="text/javascript" src="libs/codemirror/keymap/sublime.js"></script>
		
		<!-- code mirror & acron -->
		<script src="libs/acorn/acorn.js"></script>
		<script src="libs/acorn/acorn_loose.js"></script>
		<script src="libs/acorn/walk.js"></script>
				
		<!-- code mirror & tern -->
		<script src="libs/tern/polyfill.js"></script>
		<script src="libs/tern/signal.js"></script>
		<script src="libs/tern/tern.js"></script>
		<script src="libs/tern/def.js"></script>
		<script src="libs/tern/comment.js"></script>
		<script src="libs/tern/infer.js"></script>
		<script src="libs/tern/doc_comment.js"></script>
		
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue">
		<!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="index.php" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                OnceBuilder
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
						<!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="/">
                                <i class="fa fa-mail-forward"></i>
                                <span class="label label-success"></span>
                            </a>
                        </li>
						<!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="index.php?route=mailbox">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-success"></span>
                            </a>
                        </li>
						<!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo $core->get_data('user_name');?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header bg-light-blue">
                                    <img src="/once/users/<?php echo $core->get_data('user_id');?>/thumbnail.png" onerror="this.src='img/user.png'" class="img-circle" alt="User Image" />
                                    <p>
                                        Web Developer Mode
                                        <small>Level - <?php echo $core->get_data('user_level');?></small>
										<small><?php echo $core->get_data('user_balance');?> Points</small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <!--<div class="pull-left">
                                        <a href="index.php?route=settings" class="btn btn-default btn-flat">Settings</a>
                                    </div>-->
									<div class="pull-right">
                                        <a href="index.php?route=logout" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <?php
					echo '
					<ul class="sidebar-menu">
						<li '.($_GET['route']=='layers'?'class="active"':'').'>
                             <a href="index.php?route=layers">
								<i class="fa fa-th"></i> <span>Layers</span>
                            </a>
                        </li>
						<li '.($_GET['route']=='pages'?'class="active"':'').'>
                             <a href="index.php?route=pages">
								<i class="fa fa-files-o"></i> <span>Pages</span>
                            </a>
                        </li>
						<li '.($_GET['route']=='posts'?'class="active"':'').'>
                             <a href="index.php?route=posts">
								<i class="fa fa-coffee"></i> <span>Posts</span>
                            </a>
                        </li>
						<li '.($_GET['route']=='images'?'class="active"':'').'>
                             <a href="index.php?route=images">
								<i class="fa fa-file-image-o"></i> <span>Images</span>
                            </a>
                        </li>
						<li '.($_GET['route']=='libraries'?'class="active"':'').'>
                             <a href="index.php?route=libraries">
								<i class="fa fa-book"></i> <span>Libraries</span>
                            </a>
                        </li>
						<li class="treeview">
                            <a href="#">
                                <i class="fa fa-cubes"></i> <span>Plugins</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                           <ul class="treeview-menu" style="display: block;">
                                <li '.($_GET['route']=='plugins'?''.($_GET['v']=='installed'?'class="active"':'').'':'').'>
									<a href="index.php?route=plugins&v=installed">
										<i class="fa fa-angle-double-right"></i> Available plugins
									</a>
								</li>
								<li '.($_GET['route']=='plugins'?''.($_GET['v']=='search'?'class="active"':'').'':'').'>
									<a href="index.php?route=plugins&v=search">
										<i class="fa fa-angle-double-right"></i> Search plugins 
									</a>
								</li>
                            </ul>
                        </li>
						<li class="treeview">
                            <a href="#">
                                <i class="fa fa-bars"></i> <span>Snippets</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu" style="display: block;">
                                <li '.($_GET['route']=='snippets'?''.($_GET['v']=='installed'?'class="active"':'').'':'').'>
									<a href="index.php?route=snippets&v=installed">
										<i class="fa fa-angle-double-right"></i> Available snippets
									</a>
								</li>
								<li '.($_GET['route']=='snippets'?''.($_GET['v']=='search'?'class="active"':'').'':'').'>
									<a href="index.php?route=snippets&v=search">
										<i class="fa fa-angle-double-right"></i> Search snippets 
									</a>
								</li>
                            </ul>
                        </li>
						<li class="treeview">
                            <a href="#">
                                <i class="fa fa-archive"></i> <span>Themes</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu" style="display: block;">
                                <li '.($_GET['route']=='themes'?''.($_GET['v']=='installed'?'class="active"':'').'':'').'>
									<a href="index.php?route=themes&v=installed">
										<i class="fa fa-angle-double-right"></i> Available themes
									</a>
								</li>
								<li '.($_GET['route']=='themes'?''.($_GET['v']=='search'?'class="active"':'').'':'').'>
									<a href="index.php?route=themes&v=search">
										<i class="fa fa-angle-double-right"></i> Search themes 
									</a>
								</li>
                            </ul>
                        </li>
						<li '.($_GET['route']=='users'?'class="active"':'').'>
                             <a href="index.php?route=users">
								<i class="fa fa-users"></i> <span>Users</span>
                            </a>
                        </li>
						<li class="treeview">
                            <a href="#">
                                <i class="fa fa-code"></i> <span>Variables</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu" style="display: block;">
								<li '.($_GET['route']=='configs'?'class="active"':'').'>
									 <a href="index.php?route=configs">
										<i class="fa fa-cog"></i> <span>Configs</span>
									</a>
								</li>
								<li '.($_GET['route']=='langs'?'class="active"':'').'>
									 <a href="index.php?route=langs">
										<i class="fa fa-font"></i> <span>Langs</span>
									</a>
								</li>
								<li '.($_GET['route']=='routes'?'class="active"':'').'>
									 <a href="index.php?route=routes">
										<i class="fa fa-sitemap"></i> <span>Routes</span>
									</a>
								</li>
                            </ul>
                        </li>
                    </ul>';
					?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section id="header" class="content-header"> </section>
                <!-- Main content -->
                <section id="content" class="content">
					<script type="text/javascript">
						$(function() {
							$.getJSON("view.php?c=<?php echo ($_GET['route']==''?'about':$_GET['route']);?>&v=<?php echo $_GET['v'];?>", function(data) {
								$('#header').html(data.header);
								$('#content').html(data.html);
							})
							.error(function() { alert("couldn't load: get_<?php echo $_GET['route'];?>"); });
						});
					</script>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

		<!-- iCheck -->
        <script src="libs/icheck-1.0.2/icheck.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="libs/AdminLTE/app.js" type="text/javascript"></script>

		<!-- Once App -->
        <script src="js/once.js?<?php echo time();?>" type="text/javascript"></script>
		
		<!-- Once Configuration -->
        <script type="text/javascript">
			once.api = true;
			once.api_key = '<?php echo $_CONFIG['api_key'];?>';
			once.cms = true;
			once.admin = true;
			once.creator = true;
			once.path = '/once';
		</script>

		<!-- Once Script -->
        <script src="js/script.js" type="text/javascript"></script>
    </body>
</html>
<?php
ob_end_flush(); 
?>