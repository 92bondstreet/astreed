<?php
/**
 * Astreed
 *
 * An advanced PHP scripts RSS and Feeds manager.
 * Search - Save feeds from rss (lists).
 *
 * Copyright (c) 2013 - 92 Bond Street, Yassine Azzout
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *	The above copyright notice and this permission notice shall be included in
 *	all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Astreed
 * @version 1.0
 * @copyright 2013 - 92 Bond Street, Yassine Azzout
 * @author Yassine Azzout
 * @link http://www.92bondstreet.com Astreed
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
// SimplePie plugin
// Download on https://github.com/simplepie/simplepie or official website http://simplepie.org/
require_once('autoloader.php');				
// token to database
require_once('token.php');	
// SwissCode plugin
// Download on https://github.com/92bondstreet/swisscode
require_once('swisscode.php');	

//Report all PHP errors
error_reporting(E_ALL);
set_time_limit(0);


/**
 * Trim description
 */
define('DESCRIPTION_LENGTH', 255);


class Feed { 
	public $id = "";
	public $title = "";
	public $link = "";
	public $description = "";
	public $image = "";
	public $blogname = "";
}



class Astreed {
	
	// Database to save
	private  $pdodb;
		
	// file dump to log
	private  $enable_log;
	private  $log_file_name = "astreed.log";
	private  $log_file;
	
	// SimplePie object to load feed
	private $simplepie;
	
	/**
	 * Constructor, used to input the data
	 *
	 * @param bool $log
	 */
	public function __construct($log=false){
	
		if(defined('DB_NAME') && defined('DB_USER') && defined('DB_PWD') ){
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;		
			$this->pdodb = new PDO(DB_NAME, DB_USER, DB_PWD);
			$this->pdodb->exec("SET CHARACTER SET utf8");
		}
		else
			$this->pdodb = null;
		
		$this->simplepie = new SimplePie();
		
		$this->enable_log = $log;
		if($this->enable_log)
			$this->log_file = fopen($this->log_file_name, "w");
		else
			$this->log_file = null;
			
	}
	
	/**
	 * Destructor, free datas
	 *
	 */
	public function __destruct(){
	
		// and now we're done; close it
		$this->pdodb = null;
		if(isset($this->log_file))
			fclose($this->log_file);
	}
	
	/**
	 * Write to log file
	 *
	 * @param $value string to write 
	 *
	 */
	function dump_to_log($value){
		fwrite($this->log_file, $value."\n");
	}
	
	/**
	 * Get RSS url from website url
	 *
	 * @param $url of website 
	 *
	 * @return string|null
	 */
	 
	function get_rssurl_from_url($url){
	 
		$this->simplepie->set_feed_url($url);	
		$this->simplepie->init();
		$this->simplepie->enable_cache(false);
		$this->simplepie->handle_content_type();
		$rss_url = $this->simplepie->subscribe_url();
				
		if(isset($rss_url) && $rss_url != "")
			return $rss_url;
		else
			return NULL;		
	}	
	
	/**
	 * RSS url parser to get feeds
	 *
	 * @param 	$rss_url 	of website
	 * @param 	$nb_feeds 	number feeds to get in array result
	 * @param 	$blogname	website name
	 *
	 * @return array|null
	 */
	 
	function parse_rss_from_url($rss_url, $nb_feeds, $blogname = NULL){
	 
		// Step 0 : process and find feeds from $rss_url
		$this->simplepie->set_feed_url($rss_url);	
		$this->simplepie->init();
		$this->simplepie->handle_content_type();

		// give a name to identify feeds source
		if(!isset($blogname))
			$blogname = $this->simplepie->get_title();
		
		$feeds = array();
		
		// Step 1 : parse feeds and format them to array (of Feed)
		foreach ($this->simplepie->get_items(0,$nb_feeds) as $entry){
			
			// get atrributes 
			$title = $entry->get_title();
			$description = $entry->get_description();
			$link = $entry->get_permalink();
		
			$title = mb_convert_encoding($title, 'HTML-ENTITIES', 'UTF-8');		
			$title = addslashes($title);
			$id = MacG_toAscii($title, "'"); // French accent in title
		
			$description = strip_tags($description);		
			$description = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');	
			$description = str_replace('&nbsp;',"",$description);		
			$description = preg_replace('/\s\s+/', ' ', $description);
			$description = MacG_trim_text(trim($description),DESCRIPTION_LENGTH);
			$description = addslashes($description);
			
			$image = "";
			
			// save it
			$feed = new Feed();
			$feed->id = $id;
			$feed->title = $title;
			$feed->link = $link;
			$feed->description = $description;
			$feed->image = $image;
			$feed->blogname = $blogname;
			
			$feeds[] = $feed;
		}
		
		return $feeds;
	}
	 
	 
	/**
	 * Insert feed from RSS url to Database
	 *
	 * @param	$feeds_table_name 	to store feeds 
	 * @param	$rss_url 			of website
	 * @param 	$nb_feeds 			number feeds to get in array result	 
	 * @param 	$blogname			website name
	 *
	 * @return bool
	 */
	 
	function insert_feeds_from_url($feeds_table_name, $rss_url, $nb_feeds, $blogname = NULL){
	
		// Step 0 : get feeds
		$feeds = $this->parse_rss_from_url($rss_url, $nb_feeds, $blogname);
		
		// Step 1 : save to database
		if(count($feeds)>0){
		
			// insert query prepared statement
			$query = 'INSERT INTO '.$feeds_table_name.' (id, title, link, description, image, blogname) VALUES (?, ?, ?, ?, ?, ?);';			
			$pdodb_stmt = $this->pdodb->prepare($query);
		
			
			foreach ($feeds as $current_feed){
			
				// Continue if id exists in DB
				$id_in_db = 'SELECT * FROM '.$feeds_table_name.' WHERE id=\''.$current_feed->id.'\'';
				$request = $this->pdodb->query($id_in_db);		
				if($request->rowCount()>0){					
					$request->closeCursor(); // end request
					continue;
				}
				
				// step 2 : insert in db	
				$pdodb_stmt->bindValue(1, $current_feed->id);
				$pdodb_stmt->bindValue(2, $current_feed->title);
				$pdodb_stmt->bindValue(3, $current_feed->link);
				$pdodb_stmt->bindValue(4, $current_feed->description);
				$pdodb_stmt->bindValue(5, $current_feed->image);
				$pdodb_stmt->bindValue(6, $current_feed->blogname);				
				$pdodb_stmt->execute();						
			}
		}
		else
			return false;
		
		return true;
	}
	 
	/**
	 * Database (list of url) RSS parser to get feeds 
	 *
	 * @param	$url_table_name 	table name with stored url rss
	 * @param 	$nb_feeds 			number feeds to get in array result	 
	 *
	 * @return array|null
	 */
	 
	function parse_rss_from_db($url_table_name, $nb_feeds){
	
		$feeds = array();
	
		// Step 0 : parse url rss stored in database
		$select = "SELECT * FROM ".$url_table_name;
		$rss_urls = $this->pdodb->query($select);
	
		$rss_urls->setFetchMode(PDO::FETCH_OBJ); 
		while( $rss = $rss_urls->fetch() ) {
			// get rss url
			$rss_url = $rss->rss_url;		
			$blogname = $rss->blogname;		
			
			// Step 1 : return array of feeds
			$current_feeds = $this->parse_rss_from_url($rss_url, $nb_feeds, $blogname);
			if(isset($current_feeds))
					$feeds = array_merge($feeds, $current_feeds);
			
		}
		$rss_urls->closeCursor();		
		
		return $feeds;
	}
	 
	/**
	 * Insert feeds from Database url rss to Database 
	 *
	 * @param	$url_table_name 	table name with stored url rss
	 * @param	$feeds_table_name 	to store feeds 
	 * @param 	$nb_feeds 			number feeds to get in array result	 
	 *
	 * @bool
	 */
	 
	function insert_feeds_from_db($url_table_name, $feeds_table_name, $nb_feeds){
	
		// Step 0 : get feeds
		$feeds = $this->parse_rss_from_db($url_table_name, $nb_feeds);
		
		// Step 1 : save to database
		if(count($feeds)>0){
		
			// insert query prepared statement
			$query = 'INSERT INTO '.$feeds_table_name.' (id, title, link, description, image, blogname) VALUES (?, ?, ?, ?, ?, ?);';			
			$pdodb_stmt = $this->pdodb->prepare($query);
		
			
			foreach ($feeds as $current_feed){
			
				// Continue if id exists in DB
				$id_in_db = 'SELECT * FROM '.$feeds_table_name.' WHERE id=\''.$current_feed->id.'\'';
				$request = $this->pdodb->query($id_in_db);		
				if($request->rowCount()>0){					
					$request->closeCursor(); // end request
					continue;
				}
				
				// step 2 : insert in db	
				$pdodb_stmt->bindValue(1, $current_feed->id);
				$pdodb_stmt->bindValue(2, $current_feed->title);
				$pdodb_stmt->bindValue(3, $current_feed->link);
				$pdodb_stmt->bindValue(4, $current_feed->description);
				$pdodb_stmt->bindValue(5, $current_feed->image);
				$pdodb_stmt->bindValue(6, $current_feed->blogname);				
				$pdodb_stmt->execute();						
			}
		}
		else
			return false;
		
		return true;
	
	}
}

?>