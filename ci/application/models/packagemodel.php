<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class PackageModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getAll() {
		$query = '
			SELECT
				id
				, package
			FROM package
			ORDER BY
				package ASC
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
				, package AS name
			FROM package
			ORDER BY
				package ASC
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