<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class SearchModel extends CI_Model {	
	public function __construct() {
		parent::__construct();
	}	
	
	public function doSearch($search, $which)
    {
		// holder for return
		$array = FALSE;
        
        if (!empty($search['original']))
        {
            // holder for the result set
    		$rs = '';
    		// creat the query based on type
    		switch($which)
            {
    			case 'beer':
    			default:
    				$rs = $this->beer_search($search);
    				break;
    			case 'establishment':
    				$rs = $this->establishment_search($search);
    				break;
    			case 'user':
    				$rs = $this->user_search($search);
                    break;			
    		}
    		
    		// check for results
    		if ($rs->num_rows() > 0)
            {
    			// get the results
    			$array = $rs->result_array();
    		}
        }
		
        // return the results
		return $array;
	}
    
    public function beer_search($search)
    {
        $this->db->select(
            'beers.id,
			beers.establishmentID,
			beers.beerName,
			beers.retired,
			establishment.name,
			establishment.stateID,
			establishment.city,
			state.stateFull,'
        );
        $this->db->from('beers');
        $this->db->join('establishment', 'establishment.id = beers.establishmentID', 'inner');
        $this->db->join('state', 'state.id = establishment.stateID', 'inner');
        $this->db->where('beers.active', '1');
        $this->db->where('establishment.active', '1');
                
        /*$query = '
			SELECT
				beers.id
				, beers.establishmentID
				, beers.beerName
				, beers.retired
				, establishment.name
				, establishment.stateID
				, establishment.city
				, state.stateFull
			FROM beers
			INNER JOIN establishment ON establishment.id = beers.establishmentID
			INNER JOIN state ON state.id = establishment.stateID
			WHERE
				beers.active = "1"
				AND establishment.active = "1"
				
		';*/
		// create a like statement based on values
		$like = $this->createLikeSearch($search['wildCards'], 'beers.beerName');
        // make sure there is a query to add
		if (!empty($like))
        {
			/*$query .= '
				AND (
					beers.beerName = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
					OR ' . $like . '
				)
			';*/
            $this->db->where('(beers.beerName = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '" OR ' . $like . ')', NULL, TRUE);
		}
        else
        {
			/*$query .= '
				AND beers.beerName = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
			';*/
            $this->db->where('beers.beerName', $search['original']);
		}
        
        return $this->db->get();
        //var_dump($this->db->last_query()); exit;
        
        // get the record set
        //return $this->db->query($query);
    }
    
    public function establishment_search($search)
    {
        $this->db->select(
            'establishment.id,
			establishment.name,
			establishment.stateID,
			establishment.city,
			state.stateFull,'
        );
        $this->db->from('establishment');
        $this->db->join('state', 'state.id = establishment.stateID', 'inner');
        $this->db->where('establishment.active', '1');

        /*$query = '
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
				
		';*/
		// create a like statement based on values
		$like = $this->createLikeSearch($search['wildCards'], 'establishment.name');
		// make sure there is a query to add
		if (!empty($like))
        {
			/*$query .= '
				AND (
					e.name = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
					OR ' . $like . '
				)
			';*/
			$this->db->where('(establisment.name = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '" OR ' . $like . ')', NULL, TRUE);
		}
        else
        {
			/*$query .= '
				AND e.name = "' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '"
			';*/
			$this->db->where('establishment.name', $search['original']);
		}
        
        // get the record set
        return $this->db->get();
    }
    
    public function user_search($search)
    {
    	$this->db->select(
    		'id,
    		username'
    	);
    	$this->db->from('users');
    	$this->db->where('active', '1');
    	//$this->db->where('username LIKE "%' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '%"', NULL, TRUE);
    	$this->db->where('username',  $search['original']);
        /*$query = '
			SELECT
				id
				, username
			FROM
				users
			WHERE
				active = "1"
				AND username LIKE "%' . mysqli_real_escape_string($this->db->conn_id, $search['original']) . '%"
		';
        
        // get the record set
        return $this->db->query($query);*/
        return $this->db->get();
    }
	
	private function createLikeSearch($search, $field)
	{
		$like = '';
		
		if (is_array($search) && count($search) > 0)
		{
			foreach ($search as $word)
			{
				$like .= (empty($like) ? '' : ' OR ') . $field .' LIKE "%' . $word . '%"';
			}
			$like = '(' . $like . ')';
		}
		
		return $like;
	}
}
?>