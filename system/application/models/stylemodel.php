<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class StyleModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	public function getAll() {
		$query = '
			SELECT
				id
				, style
				, description
			FROM styles
			ORDER BY
				style ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getAllForDropDown() {
		$query = '
			SELECT
				id
				, style AS name
				, origin
				, styleType
			FROM styles
			ORDER BY
				styleType ASC
				, origin ASC
				, style ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getStyleByID($styleID) {
		$query = '
			SELECT
				id
				, style AS name
			FROM styles
			WHERE
				id = ' . $styleID
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
}
?>