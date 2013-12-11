<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class SEOModel extends Model {	
	public function __construct() {
		parent::Model();		
	}
	
	public function getSEOInfo($uri) {
		$query = '
			SELECT
				pagetitle
				, metadescription
				, metakeywords
			FROM seo
			WHERE
				pageurl = "' . $uri . '"
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}	
}
?>