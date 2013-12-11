<?php	
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

function parseWPRSS($config) {
	// check for size value and set a 
	// generic value for size to send back
	$size = key_exists('size', $config) ? (int) $config['size'] : BLOG_RSS_COUNT;
	
	// get the xml for the feed
	$xml = @simplexml_load_file(BLOG_RSS_URL, 'SimpleXMLElement', LIBXML_NOCDATA);
	//echo '<pre>'; print_r($xml); echo '</pre>'; exit;
	if($xml == false) {
		// couldn't get the xml feed
		throw new Exception('<p>Blog RSS currently down.</p>');
	}
	
	// parse the xml
	$result['title'] = $xml->xpath('/rss/channel/item/title');
	$result['link'] = $xml->xpath('/rss/channel/item/link');
	$result['pubdate'] = $xml->xpath('/rss/channel/item/pubDate');
	$result['creator'] = $xml->xpath('/rss/channel/item/dc:creator');
	$result['description'] = $xml->xpath('/rss/channel/item/description');
	
	// get the size of the result array
	$count = count($result['title']);
	// check if it is less than the size
	if($count < $size) {
		// the size is now the count
		$size = $count;
	}
	
	// start the output
	$str = '
		<h2><a class="brown" href="http://blog.twobeerdudes.com">Sips Blog</a></h2>
		<ul>
		';
	// iterate through the results
	for($i = 0; $i < $size; $i++) {
		// add to the output
		$str .= '
			<li>
				<p>
					<span class="blogDate mediumgray">' . date('m/d', strtotime($result['pubdate'][$i])) . '</span>
					<span class="blogLink"><a href="' . $result['link'][$i] . '">' . $result['title'][$i] . '</a></span>
					<span class="mediumgray">by ' . $result['creator'][$i] . '</span>
				</p>
				<p>' . str_replace('[...]', '', $result['description'][$i]) . '</p>
			</li>
		';	
	}//<p>' . date('M d, Y', strtotime($result['pubdate'][$i])) . ' <a href="' . $result['link'][$i] . '"><span class="bold">' . $result['title'][$i] . '</bold></a> by ' . $result['creator'][$i] . '</p>
	// finish the output
	$str .= '
		</ul>
	';
	// return the output
	return $str;
}
?>