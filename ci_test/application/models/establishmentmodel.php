<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class EstablishmentModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getEstablishmentByID($id) {
		$query = '
			SELECT
				e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.latitude
				, e.longitude
				, e.phone
				, e.url
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
			FROM establishment e
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				e.id = ' . $id
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function checkForRatingByUserIDEstablishmentID($userID, $establishmentID) {
		$query = '
			SELECT
				id				
				, dateVisited
				, drink
                , service
                , atmosphere
                , pricing
                , accessibility
				, comments
				, visitAgain
				, price
			FROM rating_establishment
			WHERE
				userID = ' . $userID . '
				AND establishmentID = ' . $establishmentID
		;
		 
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getRecentReviews() {
		// create the query
		$query = '
			SELECT
				e.id
				, e.name
				, e.city
				, e.stateID
				, st.stateAbbr
				, st.stateFull
				, ROUND((re.drink * (' . PERCENT_DRINK . ' / 100)) + (re.service * (' . PERCENT_SERVICE . ' / 100)) + (re.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (re.pricing * (' . PERCENT_PRICING . ' / 100)) + (re.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100)), 1) AS rating
				, re.comments
			FROM rating_establishment re
			INNER JOIN establishment e ON e.id = re.establishmentID
			INNER JOIN state st ON st.id = e.stateID
			WHERE
				e.active = "1"
				AND re.active = "1"
			ORDER BY 
				re.dateAdded DESC
			LIMIT 5
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
	
	public function getNewestAdditions() {
		// create the query
		$query = '
			SELECT
				e.id
				, e.name
				, e.city
				, e.stateID
				, st.stateFull
			FROM establishment e
			INNER JOIN state st ON st.id = e.stateID
			WHERE
				e.active = "1"
			ORDER BY 
				dateAdded DESC
			LIMIT 5
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
	
	public function getEstablishmentTypes() {
		// create the query
		$query = '
			SELECT
				name
				, description
			FROM establishment_categories
			ORDER BY 
				id ASC
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
	
	public function getHighestRatedEstablishments() {
		// create the query
		$query = '
			SELECT
				e.id
				, e.name
				, e.city
				, e.stateID
				, st.stateAbbr
				, st.stateFull
				, COUNT(e.id) AS totalRatings
				, (SUM((re.drink * (' . PERCENT_DRINK . ' / 100)) + (re.service * (' . PERCENT_SERVICE . ' / 100)) + (re.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (re.pricing * (' . PERCENT_PRICING . ' / 100)) + (re.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) / COUNT(e.id)) AS avgRating
			FROM establishment e
			INNER JOIN rating_establishment re ON re.establishmentID = e.id
			INNER JOIN establishment_categories ec ON ec.id = e.categoryID
			INNER JOIN state st ON st.id = e.stateID
			WHERE
				e.active = "1"
				AND re.active = "1"
			GROUP BY
				e.id
			HAVING
				COUNT(e.id) > ' . TOP_RATED_ESTABLISHMENTS_LIMIT . '
			ORDER BY 
				avgRating DESC
			LIMIT ' . TOP_RATED_ESTABLISHMENTS
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
	
	public function getEstablishmentsByCategoryState($state, $categoryID) {
		// create the query
		$query = '
			SELECT
				e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
				, e.zip
				, e.phone
				, e.url
				, ec.name AS category
				, e.categoryID
			FROM establishment e
			INNER JOIN state st ON st.id = e.stateID
			INNER JOIN establishment_categories ec ON ec.id = e.categoryID
			WHERE
				e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $state) . '
				AND ec.id = ' . mysqli_real_escape_string($this->db->conn_id, $categoryID) . '
				AND e.active = 1
			ORDER BY
				e.name ASC
		';//echo $query;exit;
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
	
	public function getEstablishmentsByState($state) {
		// create the query
		$query = '
			SELECT
				e.city
				, st.id AS stateID
				, st.stateFull
				, COUNT(DISTINCT e.id) as totalPerCity
			FROM establishment e
			LEFT JOIN state st ON st.id = e.stateID
			WHERE
				e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $state) . '
				AND e.active = 1
			GROUP BY
				st.id
				, e.city
			ORDER BY
				e.city
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

	public function getEstablishmentsByCity($state, $city) {
		// create the query
		$query = '
			SELECT
				e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, st.id AS stateID
				, st.stateAbbr
				, st.stateFull
				, e.zip
				, e.phone
				, e.url
				, ec.name AS category
			FROM establishment e
			INNER JOIN state st ON st.id = e.stateID
			INNER JOIN establishment_categories ec ON ec.id = e.categoryID
			WHERE
				e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $state) . '
				AND e.city = "' . mysqli_real_escape_string($this->db->conn_id, $city) . '"
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
	
	public function getEstablishmentRating($establishmentID) {
		// create the query
		$query = '
			SELECT
				AVG((er.drink * (' . PERCENT_DRINK . ' / 100)) + (er.service * (' . PERCENT_SERVICE . ' / 100)) + (er.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (er.pricing * (' . PERCENT_PRICING . ' / 100)) + (er.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS averageRating
				, COUNT(er.id) as totalRatings
			FROM establishment e
			LEFT OUTER 
				JOIN rating_establishment er 
				ON er.establishmentID = e.id
				AND e.id = ' . $establishmentID . '
				AND e.active = 1
		';//echo '<pre>'; print_r($query); echo '</pre>';
		// get the record set
		$rs = $this->db->query($query);
		// temporary holder for results
		$array = array();
		// check if there are any results in the record set
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}//echo '<pre>'; print_r($array);
		// return results
		return $array;
	}
	
	public function getEstablishmentsByCategory($stateID) {
		// create the query
		$query = '
			SELECT
				ec.id
				, ec.name
				, COUNT(e.categoryID) AS totalPerCategory
			FROM establishment_categories ec
			LEFT OUTER 
				JOIN establishment e 
					ON e.categoryID = ec.id
					AND e.stateID = ' . mysqli_real_escape_string($this->db->conn_id, $stateID) . '
					AND e.active = 1
			GROUP BY
				ec.id
				, ec.name
			ORDER BY
				IF(COUNT(e.categoryID) > 0, 1, 0) DESC
				, ec.id ASC
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
	
	public function getEstablishmentInfoByID($establishmentID) {
		// create the query
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
				, e.picture
				, e.pictureApproval
                , e.twitter
                , e.closed
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
				, bh.id AS breweryhopsID
			FROM establishment e
			LEFT OUTER JOIN breweryhops bh ON bh.establishmentID = e.id
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $establishmentID) . '
				AND e.active = 1
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
	
	public function getEstblishmentRatingsByID($establishmentID) {
		$query = '
			SELECT
				e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.phone
				, e.url
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
				, er.id
				, DATE_FORMAT(er.dateVisited, "%M %d, %Y") AS formatDateVisited
				, DATE_FORMAT(er.dateAdded, "%W, %M %d, %Y at %T") AS formatDateAdded
				, ROUND((er.drink * (' . PERCENT_DRINK . ' / 100)) + (er.service * (' . PERCENT_SERVICE . ' / 100)) + (er.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (er.pricing * (' . PERCENT_PRICING . ' / 100)) + (er.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100)), 1) AS rating
				, er.comments
				, er.price
                , er.drink
                , er.service
                , er.atmosphere
                , er.pricing
                , er.accessibility
                , er.visitAgain
				, er.active
				, u.id AS userID
				, u.username
				, u.firstName
				, DATE_FORMAT(u.joindate, "%W, %M %d, %Y at %T") AS formatJoinDate
				, u.city AS userCity
				, u.state AS userState
				, u.avatar
				, u.avatarImage
				, bh.id AS breweryhopsID
			FROM  establishment e
			INNER JOIN state s ON s.id = e.stateID
			INNER 
				JOIN rating_establishment er 
					ON er.establishmentID = e.id 
					AND er.active = "1"
			LEFT OUTER 
				JOIN users u 
					ON u.id = er.userID
			LEFT OUTER 
				JOIN breweryhops bh 
					ON bh.establishmentID = e.id
			WHERE
				e.id = ' . $establishmentID . '
			ORDER BY
				er.dateAdded DESC
		';

		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
    
    public function getEstblishmentRatingsByRatingsID($ratingID) {
        $query = '
            SELECT
                e.id AS establishmentID
                , e.name
                , e.address
                , e.city
                , e.zip
                , e.phone
                , e.url
                , s.id AS stateID
                , s.stateFull
                , s.stateAbbr
                , er.id
                , DATE_FORMAT(er.dateVisited, "%M %d, %Y") AS formatDateVisited
                , DATE_FORMAT(er.dateAdded, "%W, %M %d, %Y at %T") AS formatDateAdded
                , (er.drink * (' . PERCENT_DRINK . ' / 100)) + (er.service * (' . PERCENT_SERVICE . ' / 100)) + (er.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (er.pricing * (' . PERCENT_PRICING . ' / 100)) + (er.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100)) AS rating
                , er.comments
                , er.price
                , er.active
                , u.id AS userID
                , u.username
                , u.firstName
                , DATE_FORMAT(u.joindate, "%W, %M %d, %Y at %T") AS formatJoinDate
                , u.city AS userCity
                , u.state AS userState
                , u.avatar
                , u.avatarImage
                , bh.id AS breweryhopsID
            FROM  establishment e
            INNER JOIN state s ON s.id = e.stateID
            INNER 
                JOIN rating_establishment er 
                    ON er.establishmentID = e.id 
                    AND er.active = "1"
            LEFT OUTER 
                JOIN users u 
                    ON u.id = er.userID
            LEFT OUTER 
                JOIN breweryhops bh 
                    ON bh.establishmentID = e.id
            WHERE
                er.id = ' . $ratingID . '
            ORDER BY
                er.dateAdded DESC
        ';

        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;
    }
	
	public function getNumEstablishmentsAndAverageByUserID($userID) {
		$query = '
			SELECT
				COUNT(DISTINCT e.id) AS totalRatings
				, AVG((re.drink * (' . PERCENT_DRINK . ' / 100)) + (re.service * (' . PERCENT_SERVICE . ' / 100)) + (re.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (re.pricing * (' . PERCENT_PRICING . ' / 100)) + (re.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) as avergeRating
			FROM establishment e
			INNER JOIN rating_establishment re ON re.establishmentID = e.id
			INNER JOIN users u ON u.id = re.userID
			WHERE
				u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID)
		;
		// run the query
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getEstablishmentExistAndHasBeer($establishmentID) {
		$query = '
			SELECT
				id
				, name
				, categoryID
			FROM establishment
			WHERE
				active = 1
				AND id = ' . mysqli_real_escape_string($this->db->conn_id, $establishmentID) . '
		';
		// run the query
		$rs = $this->db->query($query);
		// result holder
		$array = false;
		// check the number of results
		if($rs->num_rows() == 1) {
			// set the results
			$array = $rs->row_array();
		}
		// return the results
		return $array;
	}
	
	public function getEstablishmentReviewCount($userID) {
		// create the query
		$query = '
			SELECT
				COUNT(DISTINCT re.id) AS establishmentsReviewed
			FROM rating_establishment re
			INNER JOIN establishment e ON e.id = re.establishmentID
			INNER JOIN users u ON u.id = re.userID
			WHERE
				u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID)
		;
		// run the query
		$rs = $this->db->query($query);
		$num = 0;
		if($rs->num_rows() == 1) {
			$row = $rs->row_array();
			$num = $row['establishmentsReviewed'];
		}
		return $num;
	}
	
	/**
	 * Stores the data for a new Rating
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createRating($data) {
		// create the query for creating a new record
		$query = '
			INSERT INTO rating_establishment (
				id
				, establishmentID
				, userID
				, dateVisited
				, dateAdded
				, drink
                , service
                , atmosphere
                , pricing
                , accessibility
				, comments
				, visitAgain
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['userID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['dateVisited']) . '"
				, NOW()
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['drink']) . '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['service']) . '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['atmosphere']) . '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['pricing']) . '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['accessibility']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['visitAgain']) . '"
				, "1"
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
	
	public function updateRatingByID($data) {
		$query = '
			UPDATE rating_establishment SET
				dateVisited = "' . mysqli_real_escape_string($this->db->conn_id, $data['dateVisited']) . '"
				, dateEdit = NOW()
				, drink = ' . mysqli_real_escape_string($this->db->conn_id, $data['drink']) . '
                , service = ' . mysqli_real_escape_string($this->db->conn_id, $data['service']) . '
                , atmosphere = ' . mysqli_real_escape_string($this->db->conn_id, $data['atmosphere']) . '
                , pricing = ' . mysqli_real_escape_string($this->db->conn_id, $data['pricing']) . '
                , accessibility = ' . mysqli_real_escape_string($this->db->conn_id, $data['accessibility']) . '
				, comments = "' . mysqli_real_escape_string($this->db->conn_id, htmlentities($data['comments'])) . '"	
				, visitAgain = "' . mysqli_real_escape_string($this->db->conn_id, $data['visitAgain']) . '"			
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';
		$rs = $this->db->query($query);
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
	
	public function getRatingsForEstablishmentByID($id) {
		$query = '
			SELECT
				AVG((re.drink * (' . PERCENT_DRINK . ' / 100)) + (re.service * (' . PERCENT_SERVICE . ' / 100)) + (re.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (re.pricing * (' . PERCENT_PRICING . ' / 100)) + (re.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS averagereview
				, COUNT(DISTINCT re.id) AS reviews
				, AVG(CASE re.pricing WHEN 10 THEN 1 WHEN 9 THEN 1 WHEN 8 THEN 2 WHEN 7 THEN 2 WHEN 6 THEN 3 WHEN 5 THEN 3 WHEN 4 THEN 4 WHEN 3 THEN 4 WHEN 2 THEN 5 WHEN 1 THEN 5 ELSE 1 END) AS averageprice
				, AVG(CASE re.visitAgain WHEN 2 THEN 1 ELSE 0 END) AS averagevisitagain
			FROM establishment e
			INNER JOIN rating_establishment re 
				ON re.establishmentID = e.id
				AND re.active = "1"
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = "1"		
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getRatingsForEstablishmentByIDTwoBeerDudes($id) {
		$query = '
			SELECT
				AVG((re.drink * (' . PERCENT_DRINK . ' / 100)) + (re.service * (' . PERCENT_SERVICE . ' / 100)) + (re.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (re.pricing * (' . PERCENT_PRICING . ' / 100)) + (re.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100))) AS averagereview
			FROM establishment e
			INNER JOIN rating_establishment re 
				ON re.establishmentID = e.id
				AND re.active = "1"
			INNER JOIN users u ON u.id = re.userID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = "1"
				AND u.id IN (1, 2)
		';
		// run the query
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function setLatitudeAndLongitude($config = array()) {
		// create the query
		$query = '
			UPDATE establishment SET
				latitude = ' . $config['lat'] . '
				, longitude = ' . $config['long'] . '
			WHERE
				id = ' . $config['id']
		;
		// run the query
		$this->db->query($query);
	}
	
	public function determineDistance($config = array()) {
		// latitude
		$lat = $config['latitude'];
		// longitude
		$lon = $config['longitude'];
		
		$lon1 = $lon - (RADIUS_SEARCH/(cos(deg2rad($lon))*69));
		$lon2 = $lon + (RADIUS_SEARCH/(cos(deg2rad($lon))*69));
		$lat1 = $lat - (RADIUS_SEARCH/69);
		$lat2 = $lat + (RADIUS_SEARCH/69);
        
        if ($lon1 > $lon2)
        {
            $tmp = $lon1;
            $lon1 = $lon2;
            $lon2 = $tmp;
        }
        
        if ($lat1 > $lat2)
        {
            $tmp = $lat1;
            $lat1 = $lat2;
            $lat2 = $tmp;
        }
		
		// create the query
		$query = '
			SELECT
				establishment.id AS establishmentID
				, establishment.name
				, establishment.address
				, establishment.city
				, establishment.zip
				, establishment.phone
                , establishment.latitude
                , establishment.longitude
                , establishment.url
				, establishment.stateID
				, 3956 * 2 * ASIN(SQRT(POWER(SIN((' . $lat . ' - establishment.latitude) * PI()/180/2), 2) + COS(' . $lat . ' * PI()/180) * COS(establishment.latitude * PI()/180) * POWER(SIN((' . $lon . ' - establishment.longitude) * PI()/180/2), 2))) AS distance
				, state.stateFull
				, state.stateAbbr
			FROM establishment
			INNER JOIN state ON state.id = establishment.stateID
			WHERE
				establishment.latitude IS NOT NULL
                AND establishment.latitude >= ' . $lat1 . '
                AND establishment.latitude <= ' . $lat2 . '
				AND establishment.longitude IS NOT NULL
                AND establishment.longitude <= ' . $lon2 . '
                AND establishment.longitude >= ' . $lon1 . '
				#AND longitude BETWEEN ' . $lon1 . ' AND ' . $lon2 . '
				#AND latitude BETWEEN ' . $lat1 . ' AND ' . $lat2 . '
				AND establishment.id != ' . $config['id'] . '
			HAVING distance <= ' . RADIUS_SEARCH . '
			ORDER BY distance
			LIMIT 10
		'; //'<pre>'; print_r($query); echo '</pre>';
		// get the record set
		$rs = $this->db->query($query);
		// holder array
		$array = array();
		// check for results
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
		//$qry = "SELECT *,(((acos(sin((".$latitude."*pi()/180)) * sin((`Latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`Latitude`*pi()/180)) * cos(((".$longitude."- `Longitude`)*pi()/180))))*180/pi())*60*1.1515) as distance FROM `MyTable` WHERE distance <= ".$distance."
	}
    
    public function getEstablishmentsRatingUserID($userID) {
        // query to get the random id to start with
        $query = '
            SELECT 
                COUNT(er.id) AS totalRatings
            FROM rating_establishment er
            INNER 
                JOIN users u 
                    ON u.id = er.userID
                    AND u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID)
        ;
        $rs = $this->db->query($query);
        $cnt = 0;
        if($rs->num_rows() > 0) {
            $row = $rs->row();
            $cnt = $row->totalRatings;
        }
        
        // holder for the results
        $array = array(); 
        // check that we have at least one result to return
        if($cnt > 0) {
            // check that value compared to are establishment limit
            $limit = $cnt > ESTABLISHMENT_COUNT ? ESTABLISHMENT_COUNT : $cnt;
            // determine the number of values that can be taken and from where
            $start = $cnt > ESTABLISHMENT_COUNT ? ($cnt - ESTABLISHMENT_COUNT - 1) : ($limit - 1);
            // get the random starting point
            $rand = mt_rand(0, $start);

            $query = '
                SELECT
                    e.id AS establishmentID
                    , e.name
                    , (er.drink * (' . PERCENT_DRINK . ' / 100)) + (er.service * (' . PERCENT_SERVICE . ' / 100)) + (er.atmosphere * (' . PERCENT_ATMOSPHERE . ' / 100)) + (er.pricing * (' . PERCENT_PRICING . ' / 100)) + (er.accessibility * (' . PERCENT_ACCESSIBILITY . ' / 100)) AS rating
                FROM establishment e 
                INNER 
                    JOIN rating_establishment er 
                        ON er.establishmentID = e.id
                        AND er.active = "1"
                INNER 
                    JOIN users u 
                        ON u.id = er.userID
                        AND u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID) . '
                ORDER BY
                    e.name ASC
                LIMIT ' . $rand . ', ' . $limit
            ;
            
            $rs = $this->db->query($query);
            
            if($rs->num_rows() > 0) {
                $array = $rs->result_array();
            }
        }
        // return the result set
        return $array;
    }
    
    public function auto_complete_search($term) {
        $query = '
            SELECT
                establishment.id
                , establishment.name
            FROM establishment
            WHERE
                establishment.name LIKE "' . mysqli_real_escape_string($this->db->conn_id, $term) . '%"
            ORDER BY
               establishment.name DESC
            LIMIT 8
        ';
        // run the query
        $rs = $this->db->query($query);
        // holder for results
        $array = false;
        // check for results
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;
    }
}
?>