<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class SwapModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function getSwapInsByUserID($id) {
        /*$query = '
                SELECT
                        be.id AS beerID
                        , be.beerName
                        , e.id AS establishmentID
                        , e.name
                        , e.url
                        , si.insDate
                FROM swapins AS si
                INNER JOIN beers be ON be.id = si.beerID
                INNER JOIN establishment e ON e.id = be.establishmentID
                WHERE
                        si.userID = ' . $id . '
                GROUP BY
                        e.id
                ORDER BY
                        be.beerName ASC
        ';*/
        $query = '
            SELECT
                beers.id AS beerID
                , beers.beerName
                , establishment.id AS establishmentID
                , establishment.name
                , establishment.url
                , swapins.insDate
                , users.username
            FROM users
            LEFT OUTER JOIN swapins
                ON swapins.userID = users.id
            LEFT OUTER JOIN beers 
                ON beers.id = swapins.beerID
            LEFT OUTER JOIN establishment
                ON establishment.id = beers.establishmentID
            WHERE
                users.id = ' . $id . '
            GROUP BY
                establishment.id
            ORDER BY
                beers.beerName ASC    
        ';

        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;
    }
	
	public function getSwapOutsByUserID($id) {
		/*$query = '
			SELECT
				be.id AS beerID
				, be.beerName
				, e.id AS establishmentID
				, e.name
				, e.url
				, so.outsDate
                                , users.username
                        FROM users
                        LEFT OUTER JOIN swapouts
                            ON swapouts.userID = users.id
			FROM swapouts AS so
			INNER JOIN beers be ON be.id = so.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
                        INNER JOIN users
                            ON users.id = so.userID
			WHERE
				so.userID = ' . $id . '
			GROUP BY
				e.id
			ORDER BY
				be.beerName ASC
		';*/
            
            $query = '
                SELECT
                    beers.id AS beerID
                    , beers.beerName
                    , establishment.id AS establishmentID
                    , establishment.name
                    , establishment.url
                    , swapouts.outsDate
                    , users.username
                FROM users
                LEFT OUTER JOIN swapouts
                    ON swapouts.userID = users.id
                LEFT OUTER JOIN beers 
                    ON beers.id = swapouts.beerID
                LEFT OUTER JOIN establishment
                    ON establishment.id = beers.establishmentID
                WHERE
                    users.id = ' . $id . '
                GROUP BY
                    establishment.id
                ORDER BY
                    beers.beerName ASC
            ';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getSwapInsByBeerID($id) {
		$query = '
			SELECT
				be.id AS beerID
				, be.beerName
				, e.id AS establishmentID
				, e.name
				, e.url
				, u.id AS userID
				, u.username
				, u.city
				, u.state
			FROM swapins AS si
			INNER JOIN beers be ON be.id = si.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN users u ON u.id = si.userID
			WHERE
				si.beerID = ' . $id . '
			ORDER BY
				u.username ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getSwapOutsByBeerID($id) {
		$query = '
			SELECT
				be.id AS beerID
				, be.beerName
				, e.id AS establishmentID
				, e.name
				, e.url
				, u.id AS userID
				, u.username
				, u.city
				, u.state
			FROM swapouts AS so
			INNER JOIN beers be ON be.id = so.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN users u ON u.id = so.userID
			WHERE
				so.beerID = ' . $id . '
			ORDER BY
				u.username ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getSwapFeedbackCountByFeedbackUserID($id) {
		$query = '
			SELECT
				COUNT(id) as feedbackCount
			FROM swapfeedback
			WHERE
				feedbackUserID = ' . $id . '
				AND active = "1"
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
    
    public function getSwapFeedbackByFeedbackID($id) {
        $query = '
            SELECT
                swf.id
                , swf.writerUserID
                , swf.feedback
                , DATE_FORMAT(swf.feedbackDate, "%W, %M %d, %Y at %T") AS feedbackDate
                , u.username
            FROM swapfeedback swf
            INNER JOIN users u ON u.id = swf.writerUserID
            WHERE
                swf.id = ' . $id . '
                AND swf.active = "1"
            LIMIT 1
        ';
        
        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->row();
        }
        return $array;    
    }
	
	public function getSwapFeedbackByFeedbackUserID($id, $limit = 0) {
		$query = '
			SELECT
				swf.id
				, swf.writerUserID
				, swf.feedback
				, DATE_FORMAT(swf.feedbackDate, "%W, %M %d, %Y at %T") AS feedbackDate
				, u.username
				, u.city
				, u.state
				, u.avatar
				, u.avatarImage
				, DATE_FORMAT(u.joindate, "%M, %Y") AS joindate
				, u.active
			FROM swapfeedback swf
			INNER JOIN users u ON u.id = swf.writerUserID
			WHERE
				swf.feedbackUserID = ' . $id . '
				AND swf.active = "1"
			ORDER BY
				swf.feedbackDate DESC
            LIMIT ' . $limit . ', ' . PER_PAGE_SWAP_FEEDBACK
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function numberSwapOutsByBeerID($beerID) {
		$query = '
			SELECT
				COUNT(beerID) as totalCount
			FROM swapouts
			WHERE
				beerID = ' . $beerID
		;
		
		$rs = $this->db->query($query);
		$row = $rs->row_array();
		return $row['totalCount'];
	}
	
	public function numberSwapInsByBeerID($beerID) {
		$query = '
			SELECT
				COUNT(beerID) as totalCount
			FROM swapins
			WHERE
				beerID = ' . $beerID
		;
		
		$rs = $this->db->query($query);
		$row = $rs->row_array();
		return $row['totalCount'];
	}
	
	public function insertSwapOut($userID, $beerID) {
		// create the query
		$query = '
			INSERT INTO swapouts (
				userID
				, beerID
				, outsDate
			) VALUES (
				' . $userID . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
				, NOW()
			);
		';
		// run the query
		$rs = $this->db->query($query);
	}
	
	public function insertSwapIn($userID, $beerID) {
		// create the query
		$query = '
			INSERT INTO swapins (
				userID
				, beerID
				, insDate
			) VALUES (
				' . $userID . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
				, NOW()
			);
		';
		// run the query
		$rs = $this->db->query($query);
	}
	
	public function removeSwapOut($userID, $beerID) {
		// create the query
		$query = '
			DELETE FROM swapouts 
			WHERE
				userID = ' . mysqli_real_escape_string($this->db->conn_id, $userID) . '
				AND beerID = ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
			LIMIT 1
		';
		// run the query
		$rs = $this->db->query($query);
		// clean up
		$query = 'OPTIMIZE TABLE swapouts';
		// run the query
		$this->db->query($query);
	}
	
	public function removeSwapIn($userID, $beerID) {
		// create the query
		$query = '
			DELETE FROM swapins 
			WHERE
				userID = ' . mysqli_real_escape_string($this->db->conn_id, $userID) . '
				AND beerID = ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
			LIMIT 1
		';
		// run the query
		$this->db->query($query);
		// clean up
		$query = 'OPTIMIZE TABLE swapins';
		// run the query
		$this->db->query($query);
	}
	
	public function saveFeedback($config) {
		// create the query
		$query = '
			INSERT INTO swapfeedback (
				id
				, writerUserID
				, feedbackUserID
				, feedback
				, feedbackDate
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $config['hdn_writerUserID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $config['hdn_feedbackUserID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['ttr_swapFeedback']) . '"
				, NOW()
				, "1"
			)
		';
		// run the query
		$this->db->query($query);
	}
	
	public function getInsAndOutsByBeerID($beerID) {
		$query = '
			SELECT
				COUNT(beerID) as totalCount
			FROM swapouts
			WHERE
				beerID = ' . $beerID
		;
		$array = array();
		$rs = $this->db->query($query);
		$row = $rs->row_array();
		$array['outs'] = $row['totalCount'];
		
		$query = '
			SELECT
				COUNT(beerID) as totalCount
			FROM swapins
			WHERE
				beerID = ' . $beerID
		;
		$rs = $this->db->query($query);
		$row = $rs->row_array();
		$array['ins'] = $row['totalCount'];
		
		return $array;
	}
	
	public function determineInSwapOuts($beerID, $userID) {
		$query = '
			SELECT
				beerID
			FROM swapouts
			WHERE
				beerID = ' . $beerID . '
				AND userID = ' . $userID
		;
		$boolean = false;
		$rs = $this->db->query($query);
		if($rs->num_rows() > 0) {
			$boolean = true;
		}
		return $boolean;
	}
	
	public function determineInSwapIns($beerID, $userID) {
		$query = '
			SELECT
				beerID
			FROM swapins
			WHERE
				beerID = ' . $beerID . '
				AND userID = ' . $userID
		;
		$boolean = false;
		$rs = $this->db->query($query);
		if($rs->num_rows() > 0) {
			$boolean = true;
		}
		return $boolean;
	}
}
?>