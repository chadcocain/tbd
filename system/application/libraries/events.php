<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Events {
	private $ci;
	private $title = 'Events';

	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function showGallery($eventID) {	
		// get the array of pics for the passed in event		
		$pics = $this->ci->EventModel->getEventPicsByID($eventID);
		// holder for the output 
		$str = '';
		// iterate throught he pictures
		foreach($pics as $pic) {
			$title = !empty($pic['pictitle']) ? $pic['pictitle'] : '';
			$description = !empty($pic['picdesc']) ? $pic['picdesc'] : '&nbsp;';
			$original = $pic['original'] == 0 ? '&nbsp;' : '<a href="' . base_url() . 'images/events/' . $pic['shortname'] . '/' . $pic['picurl'] . '_l.jpg">Download Original</a>';
			$str .= '
			<li>	
				<a class="thumb" href="' . base_url() . 'images/events/' . $pic['shortname'] . '/' . $pic['picurl'] . '.jpg" title="' . $title. '">
					<img src="' . base_url() . 'images/events/' . $pic['shortname'] . '/' . $pic['picurl'] . '_s.jpg" alt="' . $title. '" />
				</a>
				<div class="caption">
					<div class="download">' . $original . '</div>
					<div class="image-title">' . $title. '</div>	
					<div class="image-desc">' . $description. '</div>
				</div>
			</li>
			';
		}
		$str .= '<br class="both" />';

		$head = '
		<h2 class="brown" style="margin-top: 1.0em;">' . $pics[0]['name'] . ' in ' . $pics[0]['city'] . ', ' . $pics[0]['stateAbbr'] . ' on ' . $pics[0]['eventdate'] . '</h2>
		<p style="margin-bottom: 0.6em;">' . count($pics) . ' pictures.</p>
		';	
		
		// get configuration values for creating the seo
		$config = array(
			'eventDate' => $pics[0]['eventdate']
			, 'eventName' => $pics[0]['name']
			, 'city' => $pics[0]['city']
			, 'state' => $pics[0]['stateFull']
			, 'seoType' => 'eventPics'
		);
		// set the page information
		$seo = getDynamicSEO($config);
		$array = $seo + array('str' => $str) + array('head' => $head);
		return $array;
	}
}
?>