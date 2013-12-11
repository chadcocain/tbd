<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class SearchModel extends CI_Model {	
	public function __construct() {
		parent::__construct();
	}	
	
	public function doSearch($search, $which) {//echo '<pre>'; print_r($search); exit;
		// holder for the query
		$query = '';
		// creat the query based on type
		switch($which) {
			case 'beer':
			default:
				$query = '
					SELECT
						be.id
						, be.establishmentID
						, be.beerName
						, be.retired
						, e.name
						, e.stateID
						, e.city
						, st.stateFull
					FROM beers be
					INNER JOIN establishment e ON e.id = be.establishmentID
					INNER JOIN state st ON st.id = e.stateID
					WHERE
						be.active = "1"
						AND e.active = "1"
						
				';
				// create a like statement based on values
				$like = $this->createLikeSearch($search['wildCards'], 'be.beerName');
                // make sure there is a query to add
				if(!empty($like)) {
					$query .= '
						AND (
							be.beerName = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
							OR ' . $like . '
						)
					';
				} else {
					$query .= '
						AND be.beerName = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
					';
				}
				break;
			case 'establishment':
				$query = '
					SELECT
						e.id
						, e.name
						, e.stateID
						, e.city
						, st.stateFull
					FROM establishment e
					INNER JOIN state st ON st.id = e.stateID
					WHERE
						e.active = "1"
						
				';
				// create a like statement based on values
				$like = $this->createLikeSearch($search['wildCards'], 'e.name');
				// make sure there is a query to add
				if(!empty($like)) {
					$query .= '
						AND (
							e.name = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
							OR ' . $like . '
						)
					';
				} else {
					$query .= '
						AND e.name = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
					';
				}
				break;
			case 'user':
				$query = '
					SELECT
						id
						, username
					FROM
						users
					WHERE
						active = "1"
						AND username LIKE "%' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '%"
				';			
		}//echo '<pre>'; print_r($query); exit;
		// get the record set
		$rs = $this->db->query($query);
		// holder for return
		$array = false;
		// check for results
		if($rs->num_rows() > 0) {
			// get the results
			$array = $rs->result_array();
		}
		// return the results
		return $array;
	}
	
	private function createLikeSearch($search, $field) {
		$like = '';
		if(is_array($search) && count($search) > 0) {
			foreach($search as $word) {
				$like .= empty($like) ? '' : ' OR ';
				$like .= $field .' LIKE "%' . $word . '%"';
			}
			$like = '(' . $like . ')';
		}
		return $like;
	}
}
?>