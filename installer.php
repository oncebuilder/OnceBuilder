<?php

// Report simple running errors except notices
error_reporting(E_ERROR | E_WARNING | E_PARSE);

# SESSION -----------------
if(!$home) exit;

# FUNCTION -------------------
if(isset($_POST['install'])){

	$obj['error']=0;
		
	if(isset($_POST['datahost']) && strlen($_POST['datahost'])<=0){
		$obj['errors'][0][]='DATAHOST CAN\'T BE EMPTY';
		$obj['error']++;
	}
	if(!filter_var(gethostbyname($_POST['datahost']), FILTER_VALIDATE_IP)) {
		$obj['errors'][0][]='DATAHOST is not valid';
		$obj['error']++;
	}
	if(isset($_POST['database']) && strlen($_POST['database'])<=0){
		$obj['errors'][1][]='DATABASE CAN\'T BE EMPTY';
		$obj['error']++;
	}
	if(isset($_POST['datauser']) && strlen($_POST['datauser'])<=0){
		$obj['errors'][2][]='DATAUSER CAN\'T BE EMPTY';
		$obj['error']++;
	}
	if(isset($_POST['login']) && strlen($_POST['login'])<=0){
		$obj['errors'][3][]='LOGIN CAN\'T BE EMPTY';
		$obj['error']++;
	}
	if(isset($_POST['password']) && strlen($_POST['password'])<=0){
		$obj['errors'][4][]='PASSWORD CAN\'T BE EMPTY';
		$obj['error']++;
	}

	if($obj['error']==0){
		try{
			$pdo = new PDO('mysql:host='.$_POST['datahost'].'', $_POST['datauser'], $_POST['datapass'], array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
			//PDO::ATTR_PERSISTENT => true, 
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
				
			$pdo->query("CREATE DATABASE IF NOT EXISTS ".$_POST['database']."");
			$pdo->query("use ".$_POST['database']."");
	
			// install db from file
			$database=@file_get_contents('once.sql');

			// execute sql
			$qr = $pdo->exec($database);

			// Building simple config file with mysql connection for Once Builder
			$config="";
			$config.="<?php\n";
			$config.="# SECURE -----------------\n";
			$config.="if(!\$home) exit;\n";
			$config.="\n";
			$config.="# CONFIG API -------------------\n";
			$config.="\$_CONFIG['api_key']='d41d8cd98f00b204e9800998ecf8427e';\n";
			$config.="\n";

			$config.="\$filename = './oconfig.php';\n";
			$config.="\$filename2 = '../oconfig.php';\n";
			$config.="if(file_exists(\$filename)) {\n";
			$config.="		require_once('./oconfig.php');\n";
			$config.="}else if(file_exists(\$filename2)) {\n";
			$config.="		require_once('../oconfig.php');\n";
			$config.="}\n";

			$config.="# CONFIG MYSQL -------------------\n";
			$config.="\$_CONFIG['datahost']='".$_POST['datahost']."';\n";
			$config.="\$_CONFIG['database']='".$_POST['database']."';\n";
			$config.="\$_CONFIG['datauser']='".$_POST['datauser']."';\n";
			$config.="\$_CONFIG['datapass']='".$_POST['datapass']."';\n";
			$config.="\n";
			$config.="if(!function_exists('a')){\n";
			$config.="	function a(\$a){\n";
			$config.="		print_r($a);\n";
			$config.="	}\n";
			$config.="}\n";
			$config.="?>\n";
			file_put_contents('./config.php',$config);

			
			$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
				
			// Prepare statements to get plugin.
			$stmt = $pdo->query("SELECT id FROM edit_users WHERE login='".$_POST['login']."' AND type_id=1 LIMIT 1");
			if($stmt->rowCount()){
				// Get id if creator account exists
				$user['item']=$stmt->fetch(PDO::FETCH_ASSOC);
				$pdo->query("UPDATE `edit_users` SET `password`='".$hash."' WHERE id='".$user['item']['id']."' LIMIT 1");
			}else{
				// Create creator account
				$pdo->query("INSERT INTO `edit_users` (`id`, `login`, `password`, `type_id`) VALUES (NULL, '".$_POST['login']."', '".$hash."', '1');");
				$user['item']['id']=$pdo->lastInsertId();
			}

			// Insert new lags
			$stmt2 = $pdo->query("INSERT INTO edit_themes (user_id, name, description) VALUES('".$user['item']['id']."', 'OnceBuilder Starter', 'Template from scrach')");
			$theme['item']['id']=$pdo->lastInsertId();

			// Make dirs and default if theme added to db
			if($stmt2->rowCount()){
				// Make new dirs and copy default files
				@mkdir('../ajax/', 0777);
				@mkdir('../class/', 0777);
				@mkdir('../css/', 0777);
				@mkdir('../fonts/', 0777);
				@mkdir('../grids/', 0777);
				@mkdir('../images/', 0777);
				@mkdir('../include/', 0777);
				@mkdir('../js/', 0777);
				@mkdir('../langs/', 0777);
				@mkdir('../layers/', 0777);
				@mkdir('../libs/', 0777);
				@mkdir('../pages/', 0777);
				@mkdir('../routes/', 0777);
				@mkdir('../tpl/', 0777);
				
				// Prepare starter teplate
				@file_put_contents('../css/global.css',file_get_contents('../once/default/css/global.css'));
				@file_put_contents('../css/style.css',file_get_contents('../once/default/css/style.css'));
				@file_put_contents('../js/main.js',file_get_contents('../once/default/js/main.js'));
				@file_put_contents('../js/script.js',file_get_contents('../once/default/js/script.js'));
				@file_put_contents('../.htaccess',file_get_contents('../once/default/.htaccess'));
				@file_put_contents('../ajax.php',file_get_contents('../once/default/ajax.php'));
				@file_put_contents('../head.php',file_get_contents('../once/default/head.php'));
				@file_put_contents('../index.php',file_get_contents('../once/default/template.php'));
				@file_put_contents('../oconfig.php',file_get_contents('../once/default/oconfig.php'));
				
				// Unset all project
				$stmt = $pdo->query("UPDATE edit_themes SET `default`='0' WHERE user_id=".$user['item']['id']."");

				// Set fields to update
				$stmt = $pdo->query("UPDATE edit_themes SET `default`='1' WHERE id=".$theme['item']['id']."");
				if($stmt->rowCount()){ $_SESSION['project_id']=$theme['item']['id'];}	
				
				// Insert new lags
				$stmt = $pdo->query("INSERT INTO edit_themes_langs (project_id, type_id) VALUES('".$theme['item']['id']."', '1')");
				
				// Make themes dir
				@mkdir('../once/themes');
				@chmod('../once/themes', 0777);

				// Make dirs for new theme
				@mkdir('../once/themes/'.$theme['item']['id']);
				@chmod('../once/themes/'.$theme['item']['id'], 0777);
				@mkdir('../once/themes/'.$theme['item']['id'].'/images');
				@chmod('../once/themes/'.$theme['item']['id'].'/images', 0777);

				// Set status ok
				$obj['status']='ok';
			}
			
			header("Location: login.php?installed"); /* Redirect browser */
		}catch (Exception $e){
			if($e->getCode()==2002){
				$obj['errors'][0][]='NO RESPONSE FROM HOST';
				$obj['error']++;
			}
			if($e->getCode()==1045){
				$obj['errors'][2][]='ACCESS DENIED FOR USER';
				$obj['error']++;
			}
			if($e->getCode()!=2002 && $e->getCode()!=1045){
				die('Error: '.$e->getMessage().' Code: '.$e->getCode());
			}
		}
	}
}

# PAGE START -------------------
?>
<!DOCTYPE html>
<html class="bg-blue">
    <head>
        <meta charset="UTF-8">
        <title>Once CMS | Installer</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="libs/AdminLTE/AdminLTE.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
	<body class="bg-blue">
        <div class="form-box install-form" id="install-box">
            <a href="login.php">
				<div class="header">OnceBuilder Installer</div>
			</a>
            <form action="login.php" method="post">
                <div class="body bg-gray">
					Database Settings
					<div class="form-group">
						<?php
						if(isset($obj['errors'][0]) && count($obj['errors'][0])>0){
							echo '<label for="datahost">'.$obj['errors'][0][0].'</label>';
						}
						?>
                        <input type="text" name="datahost" value="<?php if(isset($_POST['datahost'])){echo $_POST['datahost'];}?>" class="form-control" placeholder="Database host"/>
                    </div>
                    <div class="form-group">
						<?php
						if(isset($obj['errors'][1]) && count($obj['errors'][1])>0){
							echo '<label for="database">'.$obj['errors'][1][0].'</label>';
						}
						?>
                       <input type="text" name="database" value="<?php if(isset($_POST['database'])){echo $_POST['database'];}?>" class="form-control" placeholder="Database name"/>
                    </div>
					<div class="form-group">
						<?php
						if(isset($obj['errors'][2]) && count($obj['errors'][2])>0){
							echo '<label for="datauser">'.$obj['errors'][2][0].'</label>';
						}
						?>
                       <input type="text" name="datauser" value="<?php if(isset($_POST['datauser'])){echo $_POST['datauser'];}?>" class="form-control" placeholder="Database username"/>
                    </div>
					<div class="form-group">
                       <input type="password" name="datapass" value="<?php if(isset($_POST['datapass'])){echo $_POST['datapass'];}?>" class="form-control" placeholder="Database password"/>
                    </div>
					<hr>
					Creator's Account
					<div class="form-group">
						<?php
						if(isset($obj['errors'][3]) && count($obj['errors'][3])>0){
							echo '<label for="login">'.$obj['errors'][3][0].'</label>';
						}
						?>
						</label>
                        <input type="text" name="login" value="<?php if(isset($_POST['login'])){echo $_POST['login'];}?>" class="form-control" placeholder="Creator login"/>
                    </div>
					<div class="form-group">
						<?php
						if(isset($obj['errors'][4]) && count($obj['errors'][4])>0){
							echo '<label for="password">'.$obj['errors'][4][0].'</label>';
						}
						?>
						</label>
                       <input type="password" name="password" value="<?php if(isset($_POST['password'])){echo $_POST['password'];}?>" class="form-control" placeholder="Creator password"/>
                    </div>
                </div>
                <div class="footer">                                                               
                    <button type="submit" name="install" class="btn bg-olive btn-block">Install OnceBuidler</button>
                </div>
            </form>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script>

    </body>
</html>