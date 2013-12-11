<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class BreweriesModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	public function getAll() {
		$query = '
			SELECT
				e.id
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
			FROM establishment e
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				categoryID IN (1, 4)
			ORDER BY
				name ASC
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
				, name
			FROM establishment
			WHERE
				categoryID IN (1, 4)
			ORDER BY
				name ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getBreweryByID($id) {
		$query = '
			SELECT
				e.id
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
			FROM establishment e
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				e.id = ' . $id . '
				AND categoryID IN (1, 4)
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function updateBreweryByID($data) {
		// holder for the phone number
		$phone = $data['phone'];
		// check if phone number is empty
		if(empty($phone)) {
			$phone = 'NULL';
		}
		
		$query = '
			UPDATE establishment SET
				name = "' . mysqli_real_escape_string($this->db->conn_id, $data['name']) . '"
				, categoryID = ' . mysqli_real_escape_string($this->db->conn_id, $data['categoryID']) . '
				, address = "' . mysqli_real_escape_string($this->db->conn_id, $data['address']) . '"
				, city = "' . mysqli_real_escape_string($this->db->conn_id, $data['city']) . '"
				, stateID = ' . mysqli_real_escape_string($this->db->conn_id, $data['stateID']) . '
				, zip = "' . mysqli_real_escape_string($this->db->conn_id, $data['zip']) . '"
				, phone = ' . mysqli_real_escape_string($this->db->conn_id, $phone) . '
				, url = "' . mysqli_real_escape_string($this->db->conn_id, $data['url']) . '"				
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';
		$rs = $this->db->query($query);
	}
	
	/**
	 * Stores the data for a new Brewery
	 * only to be used by the admin as it has
	 * forced activation
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createBrewery($data) {
		// create the query for creating a new record
		$query = '
			INSERT INTO establishment (
				id
				, userID
				, categoryID
				, name
				, address
				, city
				, stateID
				, zip
				, phone
				, url
				, active
				, dateAdded
			) VALUES (
				NULL
				, ' . $data['userID'] . '
				, 1
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['name']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['address']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['city']) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['stateID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['zip']) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['phone']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['url']) . '"			
				, "1"	
				, NOW()
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
	
	/**
	 * create an establishment
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createEstablishment($data) {
		//echo '<pre>'; print_r($data); exit;
		// create the query for creating a new record
		$query = '
			INSERT INTO establishment (
				id
				, userID
				, categoryID
				, name
		';
		$query .= !empty($data['address']) ? ', address' : '';
		$query .= !empty($data['city']) ? ', city' : '';
		$query .= '
				, stateID
		';
		$query .= !empty($data['zip']) ? ', zip' : '';
		$query .= !empty($data['phone']) ? ', phone' : '';
		$query .= !empty($data['url']) ? ', url' : '';
		$query .= !empty($data['twitter']) ? ', twitter' : '';
		$query .= '
				, active
				, dateAdded
			) VALUES (
				NULL
				, ' . $data['userID'] . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['categoryID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['name']) . '"
		';
		$query .= !empty($data['address']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['address']) . '"' : '';
		$query .= !empty($data['city']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['city']) . '"' : '';
		$query .= '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['state'])
		;
		$query .= !empty($data['zip']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['zip']) . '"' : '';
		$query .= !empty($data['phone']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['phone']) . '"' : '';
		$query .= !empty($data['url']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['url']) . '"' : '';
		$query .= !empty($data['twitter']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['twitter']) . '"' : '';
		$query .= '
				, "1"
				, NOW()
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
	
	public function getBreweryInfoByID($id) {
		$query = '
			SELECT
				e.id
				, e.categoryID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
                                , e.twitter
				, e.picture
				, e.pictureApproval
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
				, bh.id AS breweryhopsID
			FROM establishment e
			LEFT OUTER JOIN breweryhops bh ON bh.establishmentID = e.id
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getAllRatingsForBreweryByID($id) {
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.seasonal
				, be.retired
				, st.id AS styleID
				, st.style
				, AVG((r.aroma * .' . PERCENT_AROMA . ') + (r.taste * .' . PERCENT_TASTE . ') + (r.look * .' . PERCENT_LOOK . ') + (r.drinkability * .' . PERCENT_DRINKABILITY . ')) AS averagereview
				, COUNT(DISTINCT r.id) AS reviews
			FROM establishment e
			INNER JOIN beers be 
				ON be.establishmentID = e.id
			INNER JOIN styles st 
				ON st.id = be.styleID
			LEFT OUTER JOIN ratings r 
				ON r.beerID = be.id
				AND r.active = "1"
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = "1"				
			GROUP BY
				be.retired
				, be.id
				, be.beerName
				, st.id
				, st.style
			ORDER BY
				be.retired DESC
				, be.beerName ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getAllBrewreyHops($limit = 8) {
		$query = '
			SELECT
				bh.id
				, DATE_FORMAT(bh.hopDate, "%W, %M %d, %Y") AS hopDate
				, bh.article
				, bh.brewerypic
				, bh.shorttext
				, bh.author
				, e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
			FROM breweryhops AS bh
			INNER JOIN establishment e ON e.id = bh.establishmentID
			INNER JOIN state st ON st.id = e.stateID
		';
		if($limit == 1) {
			$query .= '
			ORDER BY
				RAND()
			';
		} else {
			$query .= '
			ORDER BY
				bh.hopDate DESC
				, bh.id DESC
			';
		}
		$query .= $limit > 0 ? 'LIMIT ' . $limit : '';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getBreweryHopByID($id) {
		$query = '
			SELECT
				bh.id
				, DATE_FORMAT(bh.hopDate, "%W, %M %d, %Y") AS hopDate
				, bh.article
				, e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
			FROM breweryhops AS bh
			INNER JOIN establishment e ON e.id = bh.establishmentID
			INNER JOIN state st ON st.id = e.stateID
			WHERE
				bh.id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getTotalEachBeer($id) {
		// create the query
		$query = '
			SELECT
				COUNT(be.id) AS totalBeers
				, SUM(CASE WHEN r.rating IS NOT NULL THEN r.rating ELSE ((r.aroma * .' . PERCENT_AROMA . ') + (r.taste * .' . PERCENT_TASTE . ') + (r.look * .' . PERCENT_LOOK . ') + (r.drinkability * .' . PERCENT_DRINKABILITY . ')) END) AS totalPoints
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = 1
				AND r.active = 1
		';
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
	
	public function getHighestRatedBreweries() {
		// create the query
		$query = '
			SELECT
				e.id
				, e.name
				, COUNT(be.id) AS beerTotal
				, AVG(CASE WHEN r.rating IS NOT NULL THEN r.rating ELSE ((r.aroma * .' . PERCENT_AROMA . ') + (r.taste * .' . PERCENT_TASTE . ') + (r.look * .' . PERCENT_LOOK . ') + (r.drinkability * .' . PERCENT_DRINKABILITY . ')) END) as avgRating
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			WHERE				
				e.active = 1
				AND r.active = 1
			GROUP BY
				e.id
			HAVING
				COUNT(be.id) > ' . TOP_RATED_LIMIT . '
			ORDER BY 
				avgRating DESC
			LIMIT ' . TOP_RATED_BREWERIES
		;
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function getDistinctBeerCount($id) {
		// create the query
		$query = '
			SELECT
				COUNT(DISTINCT be.id) AS totalBeers
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
		';
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
	
	public function getAvgCostPerPackage($id) {
		// create the query
		$query = '
			SELECT
				be.id
				, p.package
				, COUNT(r.id) AS totalServings
				, ROUND(AVG(CASE WHEN r.price > 0.00 THEN r.price END), 2) AS averagePrice
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN package p ON p.id = r.packageID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
                                AND r.price > 0
			GROUP BY
				be.id
				, p.package
			ORDER BY
				averagePrice ASC			
		';
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function getOverallAverageCostOfBeerByEstablishmentID($id) {
		// create the query
		$query = '
			SELECT
				ROUND(AVG(CASE WHEN r.price > 0.00 THEN r.price END), 2) AS averagePrice
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
		';
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
	
	public function getOverallAverageCostOfBeer($order = '') {
		// determine the order of the query
		$order = empty($order) ? 'ASC' : $order;
		// create the query
		$query = '
			SELECT
				e.name
				, e.id
				, COUNT(r.id) AS totalServings
				, ROUND(AVG(CASE WHEN r.price > 0.00 THEN r.price END), 2) AS averagePrice
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			GROUP BY
				e.id
				, e.name
			HAVING
				averagePrice > 0.00
			ORDER BY
				averagePrice ' . $order . '
			LIMIT ' . AVERAGE_COST_FOR_BEER
		;
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function getHaveAnotherPercent($id) {
		// create the query
		$query = '
			SELECT
				be.id
				, AVG(CASE r.haveAnother WHEN 2 THEN 1 ELSE 0 END) AS percentHaveAnother
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
			GROUP BY
				be.id
			ORDER BY
				be.id			
		';
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function getHaveAnotherPercentByEstablishment($id) {
		// create the query
		$query = '
			SELECT
				e.id
				, AVG(CASE r.haveAnother WHEN 2 THEN 1 ELSE 0 END) AS percentHaveAnother
				, COUNT(DISTINCT be.id) AS totalBeers
				, COUNT(r.id) AS totalDrank
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = 1
				AND r.active = 1
			GROUP BY
				e.id
			ORDER BY
				e.id			
		';
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
	
	public function getBreweryByCity($state, $city) {
		// create the query
		$query = '
			SELECT
				e.id AS establishmentID
				, e.categoryID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.picture
				, e.pictureApproval
				, e.url
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
			FROM establishment e
			INNER JOIN state st ON st.id = e.stateID
			WHERE
				e.city = "' . mysqli_real_escape_string($this->db->conn_id, $city) . '"
				AND e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $state) . '
				AND e.active = 1
			ORDER BY
				e.city ASC
				, e.name ASC
		';
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function getBreweryByState($state) {
		// create the query
		$query = '
			SELECT
				e.id AS establishmentID
				, e.categoryID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.picture
				, e.pictureApproval
				, e.url
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
			FROM establishment e
			LEFT JOIN state st ON st.id = e.stateID
			WHERE
				e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $state) . '
				AND e.active = 1
			ORDER BY
				e.name		
		';
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		// return results
		return $array;
	}
	
	public function updateImageByID($id, $picture) {
		// create the query
		$query = '
			UPDATE establishment
			SET
				picture = "' . mysqli_real_escape_string($this->db->conn_id, $picture) . '"
			WHERE
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		// run the query 
		$this->db->query($query);
	}
	
	public function getAllCategoriesForDropDown() {
		$query = '
			SELECT
				id
				, name
			FROM establishment_categories
			ORDER BY
				id ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getCategoryCheck($categoryID) {
		$query = '
			SELECT
				id
			FROM establishment_categories
			WHERE
				id = ' . $categoryID
		;
		
		$rs = $this->db->query($query);
		$bool = false;
		if($rs->num_rows() > 0) {
			$bool = true;
		}
		return $bool;
	}
}