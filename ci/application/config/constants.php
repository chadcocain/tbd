<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


// the url for the blog rss
define('BLOG_RSS_URL', 'http://blog.twobeerdudes.com/feed/');
// default number of blog entries to show
define('BLOG_RSS_COUNT', 6);
// default number for how many reviews have to be done to 
// create a beer
define('MIN_REVIEW_COUNT', 0);
// default number for how many reviews have to be done to 
// create an establishment
define('MIN_REVIEW_COUNT_FOR_ESTABLISHMENT', 0);
// whether or not to send an email for creattion of an
// establishment or beer
define('SEND_CREATION_NOTICE', true);

// maximum lenght of a private message
define('PRIVATE_MESSAGE_MAX_LENGTH', 2000);

// color for normal state form field
define('DEFAULT_FORMFIELD', '#ffffff');
// color for mouse over of form
define('MOUSEOVER_FORMFIELD', '#a1a1a1');

// whether or not to send an email for creation of a new beer
define('SEND_NEWBEER_NOTICE', true);

// whether or not to send an email for creation of a new establishment
define('SEND_NEWBREWERY_NOTICE', true);

// number of beers to show on front page 
define('BEER_REVIEW_HOME', 10);
// number of beers to show on review front page
define('BEER_REVIEWS', 12);
// number of beers to show on the top rated beers
define('HIGHEST_RATED_LIMIT', 50);
// minimum number of times a beer should have been reviewed
// in order for it to be considered in the highest rated
define('HIGHEST_RATED_LIMIT_RATINGS', 0);
// number of beers to show per style of highest rated beers
define('HIGHEST_RATED_BY_STYLE_LIMIT', 12);
// minimum number of times a style could have been reviewed
// in order for it to be considered in the highest rated
define('HIGHEST_RATED_BY_STYLE_LIMIT_RATINGS', 0);

// number of beers to show on styles page
define('BEER_STYLE_PAGINATION', 12);

// number of header images to sort through
define('HEADER_IMAGE_COUNT', 12);

// number of highest rated breweries to show in right hand column
define('TOP_RATED_BREWERIES', 8);
// number of ratings that has to be done for a brewery to be considered
define('TOP_RATED_LIMIT', 1);

// number of highest rated establishments to show in right hand column
define('TOP_RATED_ESTABLISHMENTS', 8);
// number of ratings that has to be done for an establishment to be considered
define('TOP_RATED_ESTABLISHMENTS_LIMIT', 0);

// number of similar beer ratings to show per user by their review writeup
define('SIMILAR_BEER_RATINGS', 6);

// number of average cost to show in the right column
define('AVERAGE_COST_FOR_BEER', 4);

// start year for the copyright information
define('START_YEAR', 2009);

// how long a captcha should be stored in seconds
define('CAPTCHA_STORE', 600);

// how many malted mail messages can be stored total for a user
define('MESSAGE_LIMIT', 10);

// number of brewery hops to show in right column
define('NUMBER_HOPS_TO_SHOW', 20);

// percentage that aroma makes up on a short review
define('PERCENT_AROMA', 25);
// percentage that taste makes up on a short review
define('PERCENT_TASTE', 25);
// percentage that look makes up on a short review
define('PERCENT_LOOK', 15);
// percentage that drinkability makes up on a short review
define('PERCENT_DRINKABILITY', 35);

// percentage that drink makes up on an establishment review
define('PERCENT_DRINK', 30);
// percentage that service makes up on an establishment review
define('PERCENT_SERVICE', 25);
// percentage that atmosphere makes up on an establishment review
define('PERCENT_ATMOSPHERE', 15);
// percentage that pricing makes up on an establishment review
define('PERCENT_PRICING', 15);
// percentage that accessibility makes up on an establishment review
define('PERCENT_ACCESSIBILITY', 15);

// the url to geocoder
define('GEOCODER', 'http://geocoder.us/service/csv/geocode?address=');
// google url for maps
define('MAPS_HOST', 'http://maps.google.com/');
// google xml path
define('GOOGLE_XML_PATH', 'maps/geo?output=xml&key=');
// google api key
define('GOOGLEAPI', 'ABQIAAAAUkbxy1rBZ7YdIl2C-ckxvBRDXtlp_hZRoOI_0qCoAppPOPmpYBQOjy3rCPVQVc0rb3qBBtMKhm25Bw');
// number of miles to search radius
define('RADIUS_SEARCH', 15);

// number of establishment ratings to select
define('ESTABLISHMENT_COUNT', 5);

// number of swap reviews to display per page for pagination
define('PER_PAGE_SWAP_FEEDBACK', 4);

// number of abuse items reported to lose membership
define('ABUSE_TOLERATION_LEVEL', 3);

// TWITTER constants
// boolean to show twitter on beer reviews
define('SHOW_TWITTER_BEER_REVIEWS', true);
// boolean to show twitter on establisment pages
define('SHOW_TWITTER_ESTABLISHMENT', true);
// FACEBOOK constants
// boolean to show facebook on beer reviews
define('SHOW_FACEBOOK_BEER_REVIEWS', true);
// boolean to show facebook on establisment pages
define('SHOW_FACEBOOK_ESTABLISHMENT', true);

// the number of ratings to query for the trending data
define('TREND_BEER_RATING_LIMIT', 5);

// number of reviews for a user to display per page
define('USER_BEER_REVIEWS_PAGINATION', 20);

// number of styles for a user to display per page
define('USER_STYLE_REVIEWS_PAGINATION', 20);

// email addresses to send contact us information to
define('EMAIL_CONTACT_US', 'scot@twobeerdudes.com, rich@twobeerdudes.com');
/* End of file constants.php */
/* Location: ./system/application/config/constants.php */
?>