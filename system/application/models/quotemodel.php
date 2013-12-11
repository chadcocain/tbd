<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class QuoteModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	public function getRandom() {
		$query = '
			SELECT
				id
				, quote
				, person
			FROM quotes
			ORDER BY
				RAND()
			LIMIT 1
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
}
?>