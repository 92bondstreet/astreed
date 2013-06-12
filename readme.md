Astreed
=========

Astreed is an Advanced PHP scripts for RSS url, RSS Feeds management. 

Retreive feeds from url, from url(s) stored in datasase, save them in database... 


Requirements
------------
* PHP 5.2.0 or newer
* <a href="https://github.com/simplepie/simplepie" target="_blank">SimplePie</a>
* <a href="https://github.com/92bondstreet/swisscode" target="_blank">SwissCode</a>


What comes in the package?
--------------------------
1. `astreed.php` - The Astreed class functions to get, save... feeds from urls/database to database...
2. `example.php` - All Adtreed functions call
3. `token.php` - Token file with Database parameters
4. `sql/`- Directory with SQL schema to save feeds (and datas example) 


Example.php
-----------

	$AstreedParser = new Astreed();
	// Get RSS url from website url
	$rss_url = $AstreedParser->get_rssurl_from_url("http://www.logodesignlove.com/");
	var_dump($rss_url);

	// RSS url parser to get feeds 	 
	$feeds = $AstreedParser->parse_rss_from_url($rss_url, 5);
	print_r($feeds);
	
	//Insert feed from RSS url to Database
	$feeds_in_db = $AstreedParser->insert_feeds_from_url("rssfeeds",$rss_url, 5, "LogoDesignLove");
	var_dump($feeds_in_db);
	
	// Database (list of url) RSS parser to get feeds 
	$feeds = $AstreedParser->parse_rss_from_db("urlandrss", 2);
	print_r($feeds);
	
	// Insert feeds from Database url rss to Database 
	$feeds_in_db = $AstreedParser->insert_feeds_from_db("urlandrss","rssfeeds",5);
	var_dump($feeds_in_db);


To start the demo
-----------------
1. Upload this package to your webserver.
2. In your database manager, browse sql directory import `rssfeeds.sql` and `urlandrss.sql` then `example.sql` files.
3. Update the `token.php` file with database host, name, user and password  
4. Open `example.php` in your web browser and check screen output and database. 
5. Enjoy !


Project status
--------------
Astreed is currently maintained by Yassine Azzout.


Authors and contributors
------------------------
### Current
* [Yassine Azzout][] (Creator, Building keeper)

[Yassine Azzout]: http://www.92bondstreet.com


License
-------
[MIT license](http://www.opensource.org/licenses/Mit)

