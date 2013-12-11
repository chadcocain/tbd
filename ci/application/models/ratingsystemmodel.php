<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class RatingSystemModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getRatingSystem() {
		$query = '
			SELECT
				id
				, ratingValue
				, description
			FROM rating_system
			ORDER BY
				ratingValue DESC
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