<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class RatingModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	//, DATE_FORMAT((CASE WHEN TIMEDIFF(r.dateAdded, r.dateEdit) < 0 THEN r.dateEdit ELSE r.dateAdded END), "%W, %M %D, %Y at %T") AS dateAdded
	//CASE WHEN aroma = 0 THEN r.rating ELSE ((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) END AS rating
	public function getAll($limit = 0) {
		$query = '
			SELECT
				r.id
				, DATE_FORMAT(r.dateTasted, "%W, %M %D, %Y") AS dateTasted
				, DATE_FORMAT(r.dateAdded, "%W, %M %D, %Y at %T") AS dateAdded
				, r.color
				, (aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100)) AS rating
				, r.comments
				, r.haveAnother
				, r.price
				, r.shortrating
				, r.aroma
				, r.taste
				, r.look
				, r.drinkability
				, be.id AS beerID
				, be.beerName
				, be.alcoholContent
				, be.malts
				, be.hops
				, be.yeast
				, be.gravity
				, be.ibu
				, be.food
				, be.glassware
				, be.picture
				, be.seasonal
				, be.seasonalPeriod
				, st.id AS styleID
				, st.style
				, p.package
				, e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.stateAbbr
				, u.id AS userID
				, u.username
				, u.firstname
				, u.lastname
			FROM ratings r
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON be.styleID = st.id
			INNER JOIN package p ON p.id = r.packageID
			INNER JOIN state s ON s.id = e.stateID
			INNER JOIN users u ON u.id = r.userID
			
		';
		if($limit > 0) {
			$query .= '
			ORDER BY
				r.dateAdded DESC
				, be.beerName ASC
			LIMIT ' . $limit
			;
		} else {
			$query .= '
			ORDER BY 
				be.beerName
			';
		}
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getAllPagination($limit) {
		// temporary holder for results
		$array = '';
		
		// create the count query
		$query = '
			SELECT
				COUNT(DISTINCT r.id) AS totalRatings
			FROM ratings r
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON be.styleID = st.id
			INNER JOIN package p ON p.id = r.packageID
			INNER JOIN state s ON s.id = e.stateID
			INNER JOIN users u ON u.id = r.userID
		';		
		// get the record set
		$rs = $this->db->query($query);
		// get the number of rows
		$row = $rs->row_array();
		// get the total number of ratings
		$total = $row['totalRatings'];
		
		// see if there are any results
		if($total > 0) {
			//CASE WHEN aroma = 0 THEN r.rating ELSE ((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) END AS rating
			// create the query to grab the rating information
			$query = '
				SELECT
					r.id
					, DATE_FORMAT(r.dateTasted, "%W, %M %D, %Y") AS dateTasted
					, DATE_FORMAT(r.dateAdded, "%W, %M %D, %Y at %T") AS dateAdded
					, r.color
					, (aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100)) AS rating
					, r.comments
					, r.haveAnother
					, r.price
					, r.shortrating
					, r.aroma
					, r.taste
					, r.look
					, r.drinkability
					, be.id AS beerID
					, be.beerName
					, be.alcoholContent
					, be.malts
					, be.hops
					, be.yeast
					, be.gravity
					, be.ibu
					, be.food
					, be.glassware
					, be.picture
					, be.seasonal
					, be.seasonalPeriod
					, st.id AS styleID
					, st.style
					, p.package
					, e.id AS establishmentID
					, e.name
					, e.address
					, e.city
					, e.zip
					, e.phone
					, e.url
					, s.stateAbbr
					, u.id AS userID
					, u.username
					, u.firstname
					, u.lastname
				FROM ratings r
				INNER JOIN beers be ON be.id = r.beerID
				INNER JOIN establishment e ON e.id = be.establishmentID
				INNER JOIN styles st ON be.styleID = st.id
				INNER JOIN package p ON p.id = r.packageID
				INNER JOIN state s ON s.id = e.stateID
				INNER JOIN users u ON u.id = r.userID
				ORDER BY
					r.dateAdded DESC
					, be.beerName ASC
				LIMIT ' . $limit . ', ' . BEER_REVIEWS
			;
			/*if($limit > 0) {
				$query .= '
				ORDER BY
					r.dateAdded DESC
					, be.beerName ASC
				LIMIT ' . $limit
				;
			} else {
				$query .= '
				ORDER BY 
					be.beerName
				';
			}*/			
			// get the record set
			$rs = $this->db->query($query);
			// check that informtion was actually passed
			if($rs->num_rows() > 0) {
				// set the return array
				$array = array('total' => $total, 'rs' => $rs->result_array());
			} else {
				$array = false;
			}
		} else {
			$array = false;
		}
		return $array;
	}
	
	public function getNonRatedBeersForDropDown() {
		/*$query = '
			SELECT	
				be.id AS id
				, CONCAT(be.beerName, " - ", b.name) AS name
			FROM beers be
			INNER JOIN breweries b ON b.id = be.breweryID
			INNER JOIN ratings r ON r.beerID = be.id
			ORDER BY 
				be.beerName ASC
		';*/
		
		$query = '
			SELECT	
				be.id AS id
				, CONCAT(be.beerName, " - ", e.name) AS name
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			ORDER BY 
				be.beerName ASC
		';
		
		/*$query = '
			SELECT	
				be.id AS id
				, CONCAT(be.beerName, " - ", b.name) AS name
			FROM beers be
			INNER JOIN breweries b ON b.id = be.breweryID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID			
			WHERE
				u.id <> ' . $this->session->userdata('id') . '
			ORDER BY 
				be.beerName ASC
		';*/
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getRatingsByUserID($id) {
		$query = '
			SELECT
				r.id
				, DATE_FORMAT(r.dateTasted, "%W, %M %D, %Y") AS dateTasted
				, r.color
				, r.rating
				, r.comments
				, r.haveAnother
				, r.price
				, be.beerName
				, be.alcoholContent
				, be.malts
				, be.hops
				, be.yeast
				, be.gravity
				, be.ibu
				, be.food
				, be.glassware
				, be.picture
				, be.seasonal
				, be.seasonalPeriod
				, st.style
				, p.package
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.stateAbbr
			FROM ratings r
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON be.styleID = st.id
			INNER JOIN package p ON p.id = r.packageID
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				r.userID = ' . $id . '
				AND r.active = "1"
			ORDER BY 
				be.beerName
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
    
    public function getBeerRatingByUserIDStatistics($id) {
        $query = '
            SELECT
                COUNT(users.id) AS rated_beers
                , AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) AS rated_beer_average
                , COUNT(DISTINCT styles.id) AS rated_styles
                , MAX((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) AS rated_beer_max
                , MIN((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) AS rated_beer_min
            FROM users
            LEFT OUTER JOIN ratings
                ON ratings.userID = users.id
            INNER JOIN beers
                ON beers.id = ratings.beerID
            INNER JOIN styles
                ON styles.id = beers.styleID
            WHERE
                users.id = ' . $id . '
                AND users.active = "1"
                AND users.banned = "0"
        ';
        
        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;    
    }
    
    public function getEstablishmentRatingByUserIDStatistics($id) {
        $query = '
            SELECT
                COUNT(users.id) AS rated_establishments
                , AVG((rating_establishment.drink * (' . PERCENT_DRINK . ' / 100)) + (rating_establishment.service * (' . PERCENT_SERVICE . ' / 100)) + (rating_establishment.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (rating_establishment.pricing * (' . PERCENT_PRICING . ' / 100)) + (rating_establishment.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS rated_establishment_average
                , establishment_categories.name
                , MAX((rating_establishment.drink * (' . PERCENT_DRINK . ' / 100)) + (rating_establishment.service * (' . PERCENT_SERVICE . ' / 100)) + (rating_establishment.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (rating_establishment.pricing * (' . PERCENT_PRICING . ' / 100)) + (rating_establishment.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS rated_establishment_max
                , MIN((rating_establishment.drink * (' . PERCENT_DRINK . ' / 100)) + (rating_establishment.service * (' . PERCENT_SERVICE . ' / 100)) + (rating_establishment.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (rating_establishment.pricing * (' . PERCENT_PRICING . ' / 100)) + (rating_establishment.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS rated_establishment_min
            FROM users
            LEFT OUTER JOIN rating_establishment
                ON rating_establishment.userID = users.id
            INNER JOIN establishment
                ON establishment.id = rating_establishment.establishmentID
            INNER JOIN establishment_categories
                ON establishment_categories.id = establishment.categoryID
            WHERE
                users.id = ' . $id . '
                AND users.active = "1"
                AND users.banned = "0"
           GROUP BY
                establishment_categories.name
           ORDER BY
                rated_establishments DESC
        ';
        
        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;
    }
	
	public function getRatingByID($ratingID) {
		$query = '
			SELECT
				r.id
				, DATE_FORMAT(r.dateTasted, "%W, %M %D, %Y") AS dateTasted
				, dateTasted AS mdate
				, r.color
				, r.rating
				, r.comments
				, r.haveAnother
				, r.price
				, r.packageID
				, r.shortrating
				, r.aroma
				, r.taste
				, r.look
				, r.drinkability
				, be.beerName
				, be.alcoholContent
				, be.malts
				, be.hops
				, be.yeast
				, be.gravity
				, be.ibu
				, be.food
				, be.glassware
				, be.picture
				, be.seasonal
				, be.seasonalPeriod
				, st.style
				, p.package
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.stateAbbr
				, u.firstname
				, u.lastname
			FROM ratings r
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON be.styleID = st.id
			INNER JOIN package p ON p.id = r.packageID
			INNER JOIN state s ON s.id = e.stateID
			INNER JOIN users u ON u.id = r.userID
			WHERE
				r.id = ' . $ratingID . '
				AND r.active = "1"
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getRatingsByUserIDEstablishmentID($userID, $establishmentID, $beerID = '') {
		$query = '
			SELECT
				r.id				
		';
		if(!empty($beerID)) {
			$query .= '
				, DATE_FORMAT(r.dateTasted, "%W, %M %D, %Y") AS dateTasted
				, r.color
				, r.rating
				, r.comments
				, r.haveAnother
				, r.price
				, be.beerName
				, be.alcoholContent
				, be.malts
				, be.hops
				, be.yeast
				, be.gravity
				, be.ibu
				, be.food
				, be.glassware
				, be.picture
				, be.seasonal
				, be.seasonalPeriod
				, st.style
				, p.package
			';
		}
		$query .= '				
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.stateAbbr
			FROM ratings r
		';
		if(!empty($beerID)) {
			$query .= '
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN styles st ON be.styleID = st.id
			INNER JOIN package p ON p.id = r.packageID
			';
		}
		$query .= '
			INNER JOIN establishment e ON e.id = r.establishmentID			
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				r.userID = ' . $userID . '
				AND r.active = "1"
				AND r.establishmentID = ' . $establishmentID
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function checkForRatingByUserIDBeerID($userID, $beerID) {
		$query = '
			SELECT
				r.id				
				, r.dateTasted
				, r.color
				, r.rating
				, r.comments
				, r.haveAnother
				, r.price
				, r.packageID
				, r.shortrating
				, r.aroma
				, r.taste
				, r.look
				, r.drinkability
			FROM ratings r
			WHERE
				r.userID = ' . $userID . '
				AND r.beerID = ' . $beerID
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function updateRatingByID($data) {
		/*$query = '
			UPDATE ratings SET
				packageID = ' . mysqli_real_escape_string($this->db->conn_id, $data['packageID']) . '
				, dateTasted = "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, dateEdit = NOW()
				, color = "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['color'])) . '"
				, rating = ' . mysqli_real_escape_string($this->db->conn_id, $data['rating']) . '
				, comments = "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"	
				, haveAnother = "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"			
				, price = ' . mysqli_real_escape_string($this->db->conn_id, $data['price']) . '
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';*/
		$query = '
			UPDATE ratings SET
				packageID = ' . mysqli_real_escape_string($this->db->conn_id, $data['packageID']) . '
				, dateTasted = "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, dateEdit = NOW()
				, color = "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['color'])) . '"
				, aroma = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['aroma'])) . '
				, taste = ' . mysqli_real_escape_string($this->db->conn_id, $data['taste']) . '
				, look = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['look'])) . '	
				, drinkability = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['drinkability'])) . '
				, comments = "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"	
				, haveAnother = "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"			
				, price = ' . mysqli_real_escape_string($this->db->conn_id, $data['price']) . '
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';
		$rs = $this->db->query($query);
	}
	
	public function updateShortRatingByID($data) {
		$query = '
			UPDATE ratings SET
				dateTasted = "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, dateEdit = NOW()
				, aroma = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['aroma'])) . '
				, taste = ' . mysqli_real_escape_string($this->db->conn_id, $data['taste']) . '
				, look = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['look'])) . '	
				, drinkability = ' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['drinkability'])) . '	
				, haveAnother = "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"			
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';
		$rs = $this->db->query($query);
	}
	
	/**
	 * Stores the data for a new Rating
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createRating($data) {
		// create the query for creating a new record
		/*$query = '
			INSERT INTO ratings (
				id
				, establishmentID
				, beerID
				, userID
				, packageID
				, dateTasted
				, dateAdded
				, color
				, rating
				, comments
				, haveAnother
				, price
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['beerID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['userID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['packageID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, NOW()
				, "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['color'])) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['rating']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['price']) . '
				, "1"
			)
		';*/
		$query = '
			INSERT INTO ratings (
				id
				, establishmentID
				, beerID
				, userID
				, packageID
				, dateTasted
				, dateAdded
				, color
				, shortRating
				, aroma
				, taste
				, look
				, drinkability
				, comments
				, haveAnother
				, price
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['beerID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['userID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['packageID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, NOW()
				, "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['color'])) . '"
				, "0"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['aroma']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['taste']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['look']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['drinkability']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['price']) . '
				, "1"
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
	
	/**
	 * Stores the data for a new Rating
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createShortRating($data) {
		// create the query for creating a new record
		$query = '
			INSERT INTO ratings (
				id
				, establishmentID
				, beerID
				, userID
				, packageID
				, dateTasted
				, dateAdded
				, haveAnother
				, shortRating
				, aroma
				, taste
				, look
				, drinkability
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['beerID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['userID']) . '
				, 1
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['dateTasted']) . '"
				, NOW()
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['haveAnother']) . '"
				, "1"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['aroma']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['taste']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['look']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['drinkability']) . '
				, "1"
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
}