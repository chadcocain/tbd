<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class ChangetypeModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function selectForDropdown() {
		// create the query
		$query = '
			SELECT
				id
				, changetype AS name
			FROM changetype
			ORDER BY
				changetype ASC
		';
		// get the record set
		$rs = $this->db->query($query);
		// holder for the return
		$array = array();
		// check that there are records
		if($rs->num_rows() > 0) {
			// get the array of results
			$array = $rs->result_array();
		}
		// return the results 
		return $array;
	}
	
	public function checkExistsByID($id) {
		// create the query
		$query = '
			SELECT
				id
			FROM changetype
			WHERE 
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		// get the record set
		$rs = $this->db->query($query);
		// holder for the return
		$boolean = false;
		// check that there are records
		if($rs->num_rows() == 1) {
			$boolean = true;
		}
		// return the results 
		return $boolean;
	}
	
	public function getByID($id) {
		// create the query
		$query = '
			SELECT
				id
				, changetype
			FROM changetype
			WHERE 
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		// get the record set
		$rs = $this->db->query($query);
		// holder for the return
		$array = array();
		// check that there are records
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		// return the results 
		return $array;
	}
}
?>