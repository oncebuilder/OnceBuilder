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
	function check_server(){
		// Check type of request $_GET -> local , $_POST -> remote
		if($this->data['id']){
			if($this->once_csrf_token_check($this->data['csrf_token'])){
				$filename = './update.once';
				if(!file_exists($filename)) {
					$this->item = array("version" => 1);
					file_put_contents($filename,serialize($this->item));
				}else{
					$this->item = unserialize(@file_get_contents('./update.once'));
				}
					
				$version=$this->item['version'];

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
					$this->item=$obj['item'];

					if(!$obj['error']){
						$this->item['version']=$version;
						$_SESSION['user_balance']=$this->item['balance'];
						file_put_contents($filename,serialize($this->item));
					}else{
						$this->set_error('API error authorized');
					}
				}
			}
		}else{
			// Check if user exist by api_key
			$stmt = $this->pdo->prepare("SELECT id, balance FROM edit_users WHERE api_key=:api_key LIMIT 1");
			$stmt->bindParam(':api_key', $this->data['api'], PDO::PARAM_STR, 255);
			$stmt->execute();

			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);

				$filename = './update.once';
				if(!file_exists($filename)) {
					file_put_contents($filename,1);
				}
				$version=file_get_contents($filename);
		
				if($version>$this->data['version']){
					$this->item['server_info']='OnceBuilder version is outdated <a href="https://oncebuilder.com/download">Check for update</a>';
				}else{
					$this->item['server_info']='OnceBuilder version: 1.0.0 BETA';
				}
			}else{
				$this->set_error('API not authorized');
			}
		}
		return $this->once_response();
	}
}
?>