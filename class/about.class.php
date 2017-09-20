<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is core connector class
 *
*/

class once extends core{
	function check_server(){//ok
		// used varibles
		$obj['errors'] =  array();
		$obj['error'] = 0 ;
		
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			
			$filename = './update.once';
			$version=file_get_contents($filename);
			
			if(file_exists($filename)) {
				if((filemtime($filename)+3600)>$this->data['time']){
					// check if last check was one day ago
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=about&o=check_server");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$this->data['api_key']."&version=".$version); //dane do wyslania
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					$response = curl_exec($ch);
					curl_close($ch);
					// Decode jsons
					$obj=json_decode($response, true);
					a($response);

					$_SESSION['user_balance']=$obj['item']['balance'];
				}
			}

			return $obj;
		}else{
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id, balance FROM edit_users WHERE api_key=:api_key LIMIT 1");
			$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$obj['item']=$stmt->fetch(PDO::FETCH_ASSOC);

				$filename = './update.once';
				if(!file_exists($filename)) {
					file_put_contents($filename,1);
				}

				$version=file_get_contents($filename);
	
				if($version>$this->data['version']){
					$obj['server_status']='1';
					$obj['server_info']='OnceBuilder version: 1.0.0 is outdated';
					$obj['status']='ok';
				}else{
					$obj['server_status']='0';
					$obj['server_info']='OnceBuilder version: 1.0.0 BETA';
					$obj['status']='ok';
				}
			}else{
				$obj['errors'][]='API not authorized';
				$obj['error']++;
			}
		}
		// Return depends on type
		if($this->data['ajax']){
			// Print JSON object
			echo json_encode($obj);
		}else{
			// Return JSON object
			return $obj;
		}
	}
}
?>