<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Seasons {
	private $ci;
	private $title = 'Seasons';

	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function showSeasonFrontPage() {	
		// get the array of pics for the passed in event		
		$items = $this->ci->SeasonModel->getSeasonalForFrontPage();
		// begin the output
		$str = '
		<div id="season">
			<h3 class="brown ' . $items[0]['className'] . '">Seasonal Indicator</h3>
		';
		
		foreach($items as $item) {
			$str .= '
			<ul>
				<li class="bold">' . $item['season'] . ' (' . $this->getMonthNames($item['monthrange']) . ')</li>
				<li>' . $item['beerstyles'] . '</li>
			</ul>			
			';
		}
		// end the output
		$str .= '
		</div>
		';	
		// return the output
		return $str;
	}
	
	private function getMonthNames($months) {
		$str = '';
		$parts = explode(',', $months);
		foreach($parts as $month) {
			$month = (int) $month;
			$str .= !empty($str) ? ', ' : '';
			$str .= date('M', mktime(0, 0, 0, $month, 1, 2009));
		}
		return $str;
	}
}
?>