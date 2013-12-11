<?php  
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if(!function_exists('getSEO')) {
	function getSEO($len = 2) {
		// get the code igniter instance
		$ci =& get_instance();
		
		// holder for the uri string
		$str_uri = '';
		// check the number of segments
		if($ci->uri->total_segments() == 0) {
			// this is probably the home page
			$str_uri = 'page/index/';
		} else {		
			// get the uri string
			$str_uri = $ci->uri->uri_string();
			
			// check the length of the uri segment
			// truncate at that length
			$parts = explode('/', $str_uri);
			// set the string to an empty string to start over
			$str_uri = '';
			// counter
			$cnt = 0;
			// check that there are parts
			if(is_array($parts)) {
				// iterate through the parts
				foreach($parts as $uri) {
					if($cnt < $len && !empty($uri)) {
						$str_uri .= $uri . '/';
						$cnt++;
					}
				}
			}
		}
		// load the seo model
		$ci->load->model('SEOModel', '', true);
		// get the appropriate information for the
		// current page
		$array = $ci->SEOModel->getSEOInfo($str_uri);
		// return the information
		return $array;
	}
}

if(!function_exists('getDynamicSEO')) {
	function getDynamicSEO($config) {
		// get the code igniter instance
		$ci =& get_instance();
		
		$pageTitle = '';
		$metaDesc = '';
		$metaKey = '';
		$metaKey_beer = 'beer reviews, beer ratings, brewery, brewery hops';
		if(key_exists('beerName', $config)) {
			$pageTitle = $config['beerName'] . ' - ' . $config['breweryName'] . ' - Two Beer Dudes';
			$metaDesc = $config['beerName'] . ', ' . $config['beerStyle'] . ' from ' . $config['breweryName'] . ' in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', beer reviews and ratings for ' . $config['beerName'] . ' on Two Beer Dudes';
			$metaKey = $config['beerName'] . ', ' . $config['beerStyle'] . ', ' . $config['breweryName'] . ', ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', ' . $metaKey_beer;
		} else if(key_exists('styleType', $config)) {
			$pageTitle = $config['style'] . ' - ' . $config['origin'] . ' ' . $config['styleType'] . 's - Two Beer Dudes';
			$metaDesc = $config['style'] . ' a ' . $config['origin'] . ' ' . $config['styleType'] . 's, beer reviews and ratings for ' . $config['style'];
			$metaKey = $config['style'] . ', ' . $config['origin'] . ' ' . $config['styleType'] . ', ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'establishmentByCity') {
			$pageTitle .= 'Breweries in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ' - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes breweries in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', beer reviews and ratings in ' . $config['breweryCity'] . ', ' . $config['breweryState'];
			$metaKey = 'breweries in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'establishmentsByState') {
			$pageTitle .= 'Breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryState'] . '  - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryState'] . ', beer reviews and ratings in ' . $config['breweryState'];
			$metaKey = 'breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryState'] . ', ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'establishmentsByCity') {
			$pageTitle .= 'Breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . '  - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', beer reviews and ratings in ' . $config['breweryCity'] . ', ' . $config['breweryState'];
			$metaKey = 'breweries, beer bars, brew pubs, and beer stores in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'establishmentByState') {
			$pageTitle .= 'Breweries in ' . $config['breweryState'] . ' - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes breweries in ' . $config['breweryState'] . ', beer reviews and ratings in ' . $config['breweryState'];
			$metaKey = 'breweries in ' . $config['breweryState'] . ', ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'userProfile') {
			$pageTitle .= $config['username'] . ' User Account - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes beer reviews and ratings for members like ' . $config['username'];
			$metaKey = $config['username'] . ' account, ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'reviewEstablishment') {
			$pageTitle .= 'Create an establishment review of ' . $config['breweryName'] . ' in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ' - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes establishment review for ' . $config['breweryName'] . ' in ' . $config['breweryCity'] . ', ' . $config['breweryState'];
			$metaKey = $config['breweryName'] . ' in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ' establishment review, ' . $metaKey_beer;
		} else if(key_exists('seoType', $config) && $config['seoType'] == 'eventPics') {
			$pageTitle .= $config['eventName'] . ' on ' . $config['eventDate'] . ' - Two Beer Dudes';
			$metaDesc = 'Two Beer Dudes visit to ' . $config['eventName'] . ' in ' . $config['city'] . ', ' . $config['state'];
			$metaKey = $config['eventName'] . ', ' . $config['city'] . ', ' . $config['state'] . $metaKey_beer;
		} else if(array_key_exists('forum_sub_topic', $config)) {
                    $pageTitle = $config['forum_sub_topic'] . ' Forum - Two Beer Dudes';
                    $metaDesc = $config['forum_sub_topic'] . ' - ' . $config['description'];
                    $metaKey = 'beer, news, beer news, American craft beer industry';
                } else if(array_key_exists('forum_thread', $config)) {
                    $pageTitle = $config['forum_thread'] . ' - Two Beer Dudes';
                    $metaDesc = $config['forum_thread'] . ' in ' . $config['sub_topic_name'] . ' Forum';
                    $metaKey = $config['forum_thread'] . ', beer, American craft beer, homebrew, beer forum, home brewing';
                } else {
			$pageTitle .= $config['breweryName'] . ' - Two Beer Dudes';
			$metaDesc = $config['breweryName'] . ' in ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', beer reviews and ratings ' . $config['breweryName'];
			$metaKey = $config['breweryName'] . ', ' . $config['breweryCity'] . ', ' . $config['breweryState'] . ', ' . $metaKey_beer;
		}		
		
		return array('pagetitle' => $pageTitle, 'metadescription' => $metaDesc, 'metakeywords' => $metaKey);
	}
}

if(!function_exists('swapOutURI')) {
	function swapOutURI($config) {
		return str_replace($config['search'], $config['replace'], $config['uri']);
	}
}
?>