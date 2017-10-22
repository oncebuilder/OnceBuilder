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
	public $scanned;
	
	function rel2abs($rel, $base) {
		if(strpos($rel,"//") === 0) {
			return "http:".$rel;
		}
		/* return if  already absolute URL */
		if  (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
		$first_char = substr ($rel, 0, 1);
		/* queries and  anchors */
		if ($first_char == '#'  || $first_char == '?') return $base.$rel;
		/* parse base URL  and convert to local variables:
		$scheme, $host,  $path */
		extract(parse_url($base));
		/* remove  non-directory element from path */
		$path = preg_replace('#/[^/]*$#',  '', $path);
		/* destroy path if  relative url points to root */
		if ($first_char ==  '/') $path = '';
		/* dirty absolute  URL */
		$abs =  "$host$path/$rel";
		/* replace '//' or  '/./' or '/foo/../' with '/' */
		$re =  array('#(/.?/)#', '#/(?!..)[^/]+/../#');
		for($n=1; $n>0;  $abs=preg_replace($re, '/', $abs, -1, $n)) {}
		/* absolute URL is  ready! */
		return  $scheme.'://'.$abs;
	}
	
	function scan_url($url){
		$str='';
		$url = filter_var ($url, FILTER_SANITIZE_URL);

		if (!filter_var ($url, FILTER_VALIDATE_URL) || in_array ($url, $this->scanned)) {
			return;
		}

		array_push ($this->scanned, $url);
		$html = str_get_html ($this->get_page($url));
		$a1   = $html->find('a');
		a($a1 );

		if(isset($a1)){
			foreach ($a1 as $val) {
				$next_url = $val->href or "";

				$fragment_split = explode ("#", $next_url);
				$next_url       = $fragment_split[0];

				if ((substr ($next_url, 0, 7) != "http://")  && 
					(substr ($next_url, 0, 8) != "https://") &&
					(substr ($next_url, 0, 6) != "ftp://")   &&
					(substr ($next_url, 0, 7) != "mailto:"))
				{
					$next_url = @rel2abs ($next_url, $url);
				}

				$next_url = filter_var ($next_url, FILTER_SANITIZE_URL);

				if (substr ($next_url, 0, strlen ($start_url)) == $start_url) {
					$ignore = false;

					if (!filter_var ($next_url, FILTER_VALIDATE_URL)) {
						$ignore = true;
					}

					if (in_array ($next_url, $this->scanned)) {
						$ignore = true;
					}

					if (isset ($skip) && !$ignore) {
						foreach ($skip as $v) {
							if (substr ($next_url, 0, strlen ($v)) == $v)
							{
								$ignore = true;
							}
						}
					}
					
					if (!$ignore) {
						foreach ($extension as $ext) {
							if (strpos ($next_url, $ext) > 0) {
								$pr = number_format ( round ( $priority / count ( explode( "/", trim ( str_ireplace ( array ("http://", "https://"), "", $next_url ), "/" ) ) ) + 0.5, 3 ), 1 );
								$str.="<url>\n" .
								$str.="<loc>" . htmlentities ($next_url) ."</loc>\n" .
								$str.="		<changefreq>$freq</changefreq>\n" .
								$str.="		<priority>$pr</priority>\n" .
								$str.="</url>\n";
							}
						}
					}
				}
			}
		}
		
		echo $str;
	}
	function item_seo(){
		
		// Scan frequency
		$priority = "1.0";
		$str="";
		$i=1;
		
		$str='<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<!-- created with OnceBuilder Sitemap Generator www.oncebuilder.com -->
<url>
	<loc>https://oncebuilder.com/</loc>
	<changefreq>daily</changefreq>
	<priority>1</priority>
</url>
';
			
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT source_en FROM edit_pages INNER JOIN edit_routes ON edit_pages.id=edit_routes.page_id WHERE page_id>0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$str.="<url>
	<loc>https://oncebuilder.com/".$row['source_en']."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.9</priority>
</url>
";$i++;
		}
		
		
		
		if(file_exists('../langs/seo.php')) require('../langs/seo.php');
		if(isset($_SEO['plugin'])){
			foreach($_SEO['plugin'] as $k => $v){
				if(isset($_SEO['plugin'][$k]['title']) && strlen($_SEO['plugin'][$k]['title'])>0){
				$str.="<url>
	<loc>https://oncebuilder.com/plugins/".$k."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.8</priority>
</url>
";$i++;
				}
			}
		}
		if(isset($_SEO['snippet'])){
			foreach($_SEO['snippet'] as $k => $v){
				if(isset($_SEO['snippet'][$k]['title']) && strlen($_SEO['snippet'][$k]['title'])>0){
				$str.="<url>
	<loc>https://oncebuilder.com/snippets/".$k."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.8</priority>
</url>
	";$i++;
				}
			}
		}
		if(isset($_SEO['theme'])){
			foreach($_SEO['theme'] as $k => $v){
				if(isset($_SEO['theme'][$k]['title']) && strlen($_SEO['theme'][$k]['title'])>0){
				$str.="<url>
	<loc>https://oncebuilder.com/themes/".$k."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.8</priority>
</url>
";$i++;
				}
			}
		}
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT source_en FROM edit_pages INNER JOIN edit_routes ON edit_pages.id=edit_routes.page_id WHERE page_id>0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			
		}
		
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT id, name FROM edit_snippets WHERE published>0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$str.="<url>
	<loc>https://oncebuilder.com/snippet/".$row['id']."/".$this->url_slug($row['name'])."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.7</priority>
</url>
";$i++;
		}
		
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT id, name FROM edit_plugins WHERE published>0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$str.="<url>
	<loc>https://oncebuilder.com/plugin/".$row['id']."/".$this->url_slug($row['name'])."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.7</priority>
</url>
";$i++;
		}
		
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT id, name FROM edit_themes WHERE published>0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$str.="<url>
	<loc>https://oncebuilder.com/themes/".$row['id']."/".$this->url_slug($row['name'])."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.7</priority>
</url>
";$i++;
		}
		
		// Prepare statements to get selected data
		$stmt = $this->pdo->prepare("SELECT id, username FROM edit_users WHERE username!='' AND type_id=0");
		$stmt->execute();

		// Return result in table
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$str.="<url>
	<loc>https://oncebuilder.com/user/".$row['id']."/".$this->url_slug($row['username'])."</loc>
	<changefreq>weekly</changefreq>
	<priority>0.7</priority>
</url>
";$i++;
		}
		$str.="<!-- ".$i." pages generated by OnceBuilder SEO - Free XML sitemap generator -->
</urlset>";
		
		file_put_contents('../sitemapa.xml',$str);
		
		//$this->scan_url('https://www.oncebuilder.com');
	}
}
?>