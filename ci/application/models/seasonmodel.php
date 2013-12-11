<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class SeasonModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getSeasonalForFrontPage() {
		$query = '
			SELECT
				season
				, className
				, monthrange
				, beerstyles
			FROM season
			WHERE
				FIND_IN_SET(MONTH(CURDATE()), monthrange)
			ORDER BY
				monthrange ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
}
?>