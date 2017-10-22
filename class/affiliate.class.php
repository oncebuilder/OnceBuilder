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

class once extends core{
	public $pdo;
	public $data;
	public $time;
	
	//############################ MYSQL & CREATE ##################################################

	function item_check(){
		/* OLD SCRIPT 2007 o.O
			$from=$_SERVER['HTTP_REFERER'];
			if(ereg("dolarek.com", $from)) $blad++;
			if(ereg("banknocik.com", $from)) $blad++;
			if(ereg("autosurf.portaldoaraguaia.tur.br", $from)) $blad++;
			if(ereg("autosurf.webinfinito.com", $from)) $blad++;
			if(ereg("qoooq.eu", $from)) $blad++;
			if(ereg("autohits", $from)) $blad++;
			if(ereg("pay4surf", $from)) $blad++;
			if(ereg("78.154.", $ip)) $blad++;
			if(ereg("keep.pl", $from)) $blad++;
			if(ereg("zdjecia.prv.pl", $from)) $blad++;
			if(ereg("pawel92", $from)) $blad++;
			if(ereg("fotki.prv.pl", $from)) $blad++;
			if(ereg("tnij.org", $from)) $blad++;
			if(ereg("idarionis.com", $from)) $blad++;
			if(ereg("217.197.73.122", $ip)) $blad++;
		*/

		if(isset($_SERVER['HTTP_REFERER'])) $this->data['referer_website']=$_SERVER['HTTP_REFERER'];
		else $this->data['referer_website']='';

		// Prepare statements to get user information.
		$stmt = $this->pdo->prepare("SELECT id, username FROM edit_users WHERE id=:user_id LIMIT 1");
		$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_STR, 50);
		$stmt->execute();

		if($stmt->rowCount()){
			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);
			
			// Prepare statements to get user information.
			$stmt2 = $this->pdo->prepare("SELECT id FROM edit_referers WHERE user_ip=:user_ip LIMIT 1");
			$stmt2->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 50);
			$stmt2->execute();

			if(!$stmt2->rowCount()){
				$stmt = $this->pdo->prepare("
					INSERT INTO edit_referers (referer_id, user_ip, referer_website) 
					VALUES (:referer_id, :user_ip, :referer_website)
				");

				$stmt->bindParam(':referer_id', $this->data['user_id'], PDO::PARAM_INT, 50);
				$stmt->bindParam(':user_ip', $this->data['user_ip'], PDO::PARAM_STR, 50);
				$stmt->bindParam(':referer_website', $this->data['referer_website'], PDO::PARAM_STR, 50);
				$stmt->execute();
				
				$stmt = $this->pdo->prepare("UPDATE edit_users SET balance=balance+1 WHERE id=:user_id");
				$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
				$stmt->execute();
			}
		}
		return $this->once_response();
	}
}
?>