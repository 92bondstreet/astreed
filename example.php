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
 
require_once('astreed.php');


/**
 * Init parser with false value: no dump file
 *
 */

$AstreedParser = new Astreed();


/**
 * Get RSS url from website url
 *
 */

$rss_url = $AstreedParser->get_rssurl_from_url("http://www.logodesignlove.com/");
var_dump($rss_url);

/**
 * RSS url parser to get feeds
 *
 */
	 
$feeds = $AstreedParser->parse_rss_from_url($rss_url, 5);
print_r($feeds);

/**
 * Insert feed from RSS url to Database
 *
 */
 
$feeds_in_db = $AstreedParser->insert_feeds_from_url("rssfeeds",$rss_url, 5, "LogoDesignLove");
var_dump($feeds_in_db);

/**
 * Database (list of url) RSS parser to get feeds 
 *
 */ 
 
$feeds = $AstreedParser->parse_rss_from_db("urlandrss", 2);
print_r($feeds);

/**
 * Insert feeds from Database url rss to Database 
 *
 */
$feeds_in_db = $AstreedParser->insert_feeds_from_db("urlandrss","rssfeeds",5);
var_dump($feeds_in_db);
?>