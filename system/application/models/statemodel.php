<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class StateModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	public function getAllStates() {
		$query = '
			SELECT
				id
				, stateFull
				, stateAbbr
			FROM state
			ORDER BY
				stateFull ASC
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
				, stateFull AS name
			FROM state
			ORDER BY
				stateFull ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getStateByID($stateID) {
		$query = '
			SELECT
				id
				, stateFull
				, stateAbbr
			FROM state
			WHERE
				id = ' . $stateID
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getStateCheck($stateID) {
		$query = '
			SELECT
				id
			FROM state
			WHERE
				id = ' . $stateID
		;
		
		$rs = $this->db->query($query);
		$bool = false;
		if($rs->num_rows() > 0) {
			$bool = true;
		}
		return $bool;
	}
}
?>