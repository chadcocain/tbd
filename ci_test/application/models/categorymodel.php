<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class CategoryModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getCategoryInfoByID($categoryID) {
		// create the query
		$query = '
			SELECT
				id
				, name
				, description
			FROM establishment_categories
			WHERE
				id = ' . $categoryID
		;
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		// return results
		return $array;
	}
}
?>