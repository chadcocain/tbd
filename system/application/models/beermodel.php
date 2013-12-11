<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class BeerModel extends Model {
	public function __construct() {
		parent::Model();
	}
	
	public function getAll() {
		$query = '
			SELECT
				be.id
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
				, e.id AS establishmentID
				, e.name
				, e.url
				, st.id as styleID
				, st.style
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON st.id = be.styleID
			ORDER BY
				be.beerName ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getBeerByID($id) {
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.alcoholContent
                , be.beerNotes
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
				, e.id AS establishmentID
				, e.name
				, e.address
				, e.city
				, e.zip
				, e.url
				, st.id as styleID
				, st.style
				, s.id AS stateID
				, s.stateFull
				, s.stateAbbr
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON st.id = be.styleID
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				be.id = ' . $id
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getAllForDropDownByBrewery($id) {
		$query = '
			SELECT
				be.id
				, be.beerName AS name
			FROM establishment e
			INNER JOIN beers be ON be.establishmentID = e.id
			WHERE
				e.id = ' . $id . '
				AND categoryID = 1
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
	
	public function getAllForDropDownSwaps($userID) {
		$query = '
			SELECT
				be.id
				, CONCAT(be.beerName, " - ", e.name) AS name
			FROM establishment e
			INNER JOIN beers be ON be.establishmentID = e.id
			WHERE
				e.categoryID = 1
				AND be.id NOT IN (
					SELECT
						beerID
					FROM swapins						
					WHERE
						userID = ' . $userID . '
				)
				AND be.id NOT IN (
					SELECT
						beerID
					FROM swapouts						
					WHERE
						userID = ' . $userID . '
				)
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
	
	public function updateBeerByID($data) {
		$query = '
			UPDATE beers SET
				establishmentID = ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, beerName = "' . mysqli_real_escape_string($this->db->conn_id, $data['beerName']) . '"
				, styleID = ' . mysqli_real_escape_string($this->db->conn_id, $data['styleID'])
		;
		$query .= !empty($data['alcoholContent']) ? ', alcoholContent = ' . mysqli_real_escape_string($this->db->conn_id, $data['alcoholContent']) : '';
		$query .= !empty($data['malts']) ? ', malts = "' . mysqli_real_escape_string($this->db->conn_id, $data['malts']) . '"' : '';
		$query .= !empty($data['hops']) ? ', hops = "' . mysqli_real_escape_string($this->db->conn_id, $data['hops']) . '"' : '';
		$query .= !empty($data['yeast']) ? ', yeast = "' . mysqli_real_escape_string($this->db->conn_id, $data['yeast']) . '"' : '';
		$query .= !empty($data['gravity']) ? ', gravity = ' . mysqli_real_escape_string($this->db->conn_id, $data['gravity']) : '';
		$query .= !empty($data['ibu']) ? ', ibu = ' . mysqli_real_escape_string($this->db->conn_id, $data['ibu']) : '';
		$query .= !empty($data['food']) ? ', food = "' . mysqli_real_escape_string($this->db->conn_id, $data['food']) . '"' : '';
		$query .= !empty($data['glassware']) ? ', glassware = "' . mysqli_real_escape_string($this->db->conn_id, $data['glassware']) . '"' : '';
		$query .= !empty($data['picture']) ? ', picture = "' . mysqli_real_escape_string($this->db->conn_id, $data['picture']) . '"' : '';
		$query .= '
				, seasonal = "' . mysqli_real_escape_string($this->db->conn_id, $data['seasonal']) . '"
		';
		$query .= $data['seasonal'] == 1 ? ', seasonalPeriod = "' . mysqli_real_escape_string($this->db->conn_id, $data['seasonalPeriod']) . '"' : ', seasonalPeriod = NULL';
		$query .= '
			WHERE
				id = ' . $data['id'] . '
			LIMIT 1
		';
		$rs = $this->db->query($query);
	}
	
	/**
	 * Stores the data for a new Beer
	 *
	 * @param array $data
	 * @return integer
	 */
	public function createBeer($data) {
		// create the query for creating a new record
		$query = '
			INSERT INTO beers (
				id
				, establishmentID
				, userID
				, beerName
				, styleID
		';
		$query .= !empty($data['alcoholContent']) ? ', alcoholContent' : '';
        $query .= !empty($data['beerNotes']) ? ', beerNotes' : '';
		$query .= !empty($data['malts']) ? ', malts' : '';
		$query .= !empty($data['hops']) ? ', hops' : '';
		$query .= !empty($data['yeast']) ? ', yeast' : '';
		$query .= !empty($data['gravity']) ? ', gravity' : '';
		$query .= !empty($data['ibu']) ? ', ibu' : '';
		$query .= !empty($data['food']) ? ', food' : '';
		$query .= !empty($data['glassware']) ? ', glassware' : '';
		$query .= !empty($data['picture']) ? ', picture' : '';
		$query .= '
				, seasonal
		';
		$query .= $data['seasonal'] == 1 ?	', seasonalPeriod' : '';
		$query .= '
				, dateAdded
				, active
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['establishmentID']) . '
				, ' . $data['userID'] . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['beerName']) . '"
				, ' . mysqli_real_escape_string($this->db->conn_id, $data['styleID']) . '
		';
		$query .= !empty($data['alcoholContent']) ? ', ' . mysqli_real_escape_string($this->db->conn_id, $data['alcoholContent']) : '';
        $query .= !empty($data['beerNotes']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['beerNotes']) . '"': '';
		$query .= !empty($data['malts']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['malts']) . '"' : '';
		$query .= !empty($data['hops']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['hops']) . '"' : '';
		$query .= !empty($data['yeast']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['yeast']) . '"' : '';
		$query .= !empty($data['gravity']) ? ', ' . mysqli_real_escape_string($this->db->conn_id, $data['gravity']) : '';
		$query .= !empty($data['ibu']) ? ', ' . mysqli_real_escape_string($this->db->conn_id, $data['ibu']) : '';
		$query .= !empty($data['food']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['food']) . '"' : '';
		$query .= !empty($data['glassware']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['glassware']) . '"' : '';
		$query .= !empty($data['picture']) ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['picture']) . '"' : '';
		$query .= '
				, "' . mysqli_real_escape_string($this->db->conn_id, $data['seasonal']) . '"
		';
		$query .= $data['seasonal'] == 1 ? ', "' . mysqli_real_escape_string($this->db->conn_id, $data['seasonalPeriod']) . '"' : '';
		$query .= '
				, NOW()
				, "1"
			)
		';
		// run the query
		$this->db->query($query);
		// return the id of the data that was just inserted
		return $this->db->conn_id->insert_id;
	}
	
	public function getBeerRatingsByID($id) {
		$query = '
                    SELECT
                        be.id
                        , be.beerName
                        , be.alcoholContent
                        , be.beerNotes
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
                        , be.retired
                        , e.id AS establishmentID
                        , e.name
                        , e.address
                        , e.city
                        , e.zip
                        , e.phone
                        , e.url
                        , e.twitter
                        , s.id AS stateID
                        , s.stateFull
                        , s.stateAbbr
                        , st.id AS styleID
                        , st.style
                        , DATE_FORMAT(r.dateTasted, "%M %d, %Y") AS formatDateTasted
                        , DATE_FORMAT(r.dateAdded, "%W, %M %d, %Y at %T") AS formatDateAdded
                        , r.color
                        , r.rating
                        , r.comments
                        , r.haveAnother
                        , r.shortrating
                        , r.aroma
                        , r.taste
                        , r.look
                        , r.drinkability
                        , r.price
                        , p.package
                        , u.id AS userID
                        , u.username
                        , u.firstName
                        , DATE_FORMAT(u.joindate, "%W, %M %d, %Y at %T") AS formatJoinDate
                        , u.city AS userCity
                        , u.state AS userState
                        , u.avatar
                        , u.avatarImage
                        , bh.id AS breweryhopsID
                    FROM beers be
                    INNER JOIN establishment e ON e.id = be.establishmentID
                    INNER JOIN state s ON s.id = e.stateID
                    INNER JOIN styles st ON st.id = be.styleID
                    LEFT OUTER JOIN ratings r ON r.beerID = be.id
                    LEFT OUTER JOIN package p ON p.id = r.packageID
                    LEFT OUTER JOIN users u ON u.id = r.userID
                    LEFT OUTER JOIN breweryhops bh ON bh.establishmentID = e.id
                    WHERE
                        be.id = ' . $id . '
                    ORDER BY
                        r.dateAdded DESC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getBeerRating($id) {
		//AVG(CASE WHEN aroma = 0 THEN r.rating ELSE ((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) END) AS averagerating
		$query = '
			SELECT
				COUNT(r.id) AS timesrated
				, SUM(CASE WHEN aroma = 0 THEN 1 ELSE 0 END) AS totaltimerated
				, AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) AS averagerating
			FROM beers be
			LEFT OUTER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				be.id = ' . $id . '
				AND u.active = "1"
				AND u.banned = "0"
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function getBestWorstBeers($config) {
		// set the order by clause
		$ob = $config['type'] == 'low' ? 'ASC' : 'DESC';
		// , AVG(r.rating) AS averagerating
		// set the query
		$query = '
			SELECT
				be.id
				, be.beerName
				, e.id AS establishmentID
				, e.name
				, s.id AS styleID
				, s.style
				, COUNT(r.id) AS totalratings
				, AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))) AS averagerating
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles s ON s.id = be.styleID
			LEFT OUTER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				u.active = "1"
				AND u.banned = "0"
			GROUP BY
				be.beerName
			HAVING
				totalratings > ' . HIGHEST_RATED_LIMIT_RATINGS . '
			ORDER BY
				averagerating ' . $ob . '
				, totalratings ' . $ob . '
				, be.beerName
			LIMIT ' . HIGHEST_RATED_LIMIT	
		;
        //echo '<pre>'; print_r($query); echo '</pre>'; exit;
		// get the record set
		$rs = $this->db->query($query);
		// holder for the results
		$array = array();
		// make sure there were result
		if($rs->num_rows() > 0) {
			// store the results
			$array = $rs->result_array();
		}
		// return the results
		return $array;
	}
	
	public function getBestStyles() {
		// , ROUND(AVG(r.rating), 1) AS avgRating
		// create the query
		$query = '
			SELECT
				st.id
				, st.style				
				, ROUND(AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))), 1) AS avgRating
				, COUNT(r.id) AS totalRatings
				, COUNT(DISTINCT be.id) AS totalBeers
			FROM styles st 
			INNER JOIN beers be ON be.styleID = st.id
			LEFT OUTER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE 
				u.active = "1"
				AND u.banned = "0"
			GROUP BY
				st.id
			HAVING
				totalRatings > ' . HIGHEST_RATED_BY_STYLE_LIMIT_RATINGS . '
			ORDER BY
				avgRating DESC
				, totalRatings DESC
				, st.style ASC
			LIMIT ' . HIGHEST_RATED_BY_STYLE_LIMIT
		;
        // get the record set
		$rs = $this->db->query($query);
		// holder for the results
		$array = array();
		// check if there were any results
		if($rs->num_rows() > 0) {
			// store the results
			$array = $rs->result_array();
		}
		// return the resutls
		return $array;
	}
	
	public function getImageByID($id) {
		// create the query
		$query = '
			SELECT
				be.picture
				, be.beerName
				, e.name
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			WHERE
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		// run the query
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}

	public function updateImageByID($id, $picture) {
		// create the query
		$query = '
			UPDATE beers
			SET
				picture = "' . mysqli_real_escape_string($this->db->conn_id, $picture) . '"
			WHERE
				id = ' . $id
		;
		// run the query 
		$this->db->query($query);
	}
	
	public function removeImageByID($id) {
		// create the query
		$query = '
			UPDATE beers
			SET
				picture = NULL
			WHERE
				id = ' . $id
		;
		// run the query
		$this->db->query($query);
	}
	
	public function getPackageCount($id) {
		// create the query
		$query = '
			SELECT
				p.package
				, COUNT(r.packageID) AS totalPackages
			FROM beers be
			INNER JOIN ratings r ON r.beerID = be.id AND r.shortrating = "0"
			INNER JOIN package p ON p.id = r.packageID
			WHERE
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
			GROUP BY
				p.package
			ORDER BY
				p.package
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
	
	public function getAllBeerStyles() {
		// create the query
		$query = '
			SELECT
				id
				, style
				, origin
				, styleType
			FROM styles
			ORDER BY
				styleType
				, origin
				, style
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
	
	public function getBeerStyleByID($id, $offset) {
		// temporary holder for results
		$array = '';
		
		// create the query
		$query = '
			SELECT
				COUNT(DISTINCT be.id) AS totalBeers
			FROM styles s
			LEFT OUTER JOIN beers be ON be.styleID = s.id
			LEFT OUTER JOIN establishment e ON e.id = be.establishmentID
			LEFT OUTER JOIN ratings r ON r.beerID = be.id
			WHERE 
				s.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.active = 1
				AND r.active = 1
		';		
		// get the record set
		$rs = $this->db->query($query);
		// get the number of rows
		$row = $rs->row_array();
		$total = $row['totalBeers'];
		
		// see if there are any results
		if($total > 0) {
			// , ROUND(AVG(r.rating), 1) AS avgRating
			// create the query
			$query = '
				SELECT
					s.id
					, s.style
					, s.description
					, s.origin
					, s.styleType
					, s.abvrange
					, s.iburange
					, s.srm
					, s.ogravity
					, s.fgravity
					, be.id AS beerID
					, be.beerName
					, be.alcoholContent
					, be.picture
					, e.id AS establishmentID
					, e.name				
					, ROUND(AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))), 1) AS avgRating
					, ROUND(AVG(r.price), 2) AS avgPrice
					, COUNT(be.id) as totalRatings
				FROM styles s
				LEFT OUTER JOIN beers be ON be.styleID = s.id
				LEFT OUTER JOIN establishment e ON e.id = be.establishmentID
				LEFT OUTER JOIN ratings r ON r.beerID = be.id
				WHERE 
					s.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
					AND e.active = 1
					AND r.active = 1
				GROUP BY
					be.id
				ORDER BY
					be.beerName
				LIMIT ' . $offset . ', ' . BEER_STYLE_PAGINATION
			;		
			// get the record set
			$rs = $this->db->query($query);			
		
			if($rs->num_rows() > 0) {
				$array = array('total' => $total, 'rs' => $rs->result_array());
			} else {
				$array = false;
			}
		} else {
			$array = false;
		}
		return $array;
	}
	
	public function getAvgCostPerPackage($id) {
		// create the query
		$query = '
			SELECT
				be.id
				, p.package
				, COUNT(r.id) AS totalServings
				, ROUND(AVG(r.price), 2) AS averagePrice
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id AND r.shortrating = "0"
			INNER JOIN package p ON p.id = r.packageID
			WHERE
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.categoryID = 1
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
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND e.categoryID IN (1, 4, 6)
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
	
	public function tastedTwoBeerDudes($id) {
		$query = '
			SELECT
				AVG((aroma * .' . PERCENT_AROMA . ') + (taste * .' . PERCENT_TASTE . ') + (look * .' . PERCENT_LOOK . ') + (drinkability * .' . PERCENT_DRINKABILITY . ')) AS avergeRating
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
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
	
	public function similarBeerByBeerIDAndStyleID($beerID, $styleID) {
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.alcoholContent
				, be.picture
				, e.id AS establishmentID
				, e.name
				, st.style
				, ROUND(AVG((aroma * .' . PERCENT_AROMA . ') + (taste * .' . PERCENT_TASTE . ') + (look * .' . PERCENT_LOOK . ') + (drinkability * .' . PERCENT_DRINKABILITY . ')), 1) AS avgRating
				, COUNT(r.id) AS totalRatings
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON st.id = be.styleID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				be.id != ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
				AND st.id = ' . mysqli_real_escape_string($this->db->conn_id, $styleID) . '
			GROUP BY
				be.id
			ORDER BY
				avgRating DESC
				, totalRatings DESC
				, be.beerName ASC
			LIMIT 8
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function similarBeerByStyleID($styleID) {
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.alcoholContent
				, be.picture
				, e.id AS establishmentID
				, e.name
				, st.style
				, ROUND(AVG((aroma * (' . PERCENT_AROMA . ' / 100)) + (taste * (' . PERCENT_TASTE . ' / 100)) + (look * (' . PERCENT_LOOK . ' / 100)) + (drinkability * (' . PERCENT_DRINKABILITY . ' / 100))), 1) AS avgRating
				, COUNT(r.rating) AS totalRatings
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON st.id = be.styleID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				st.id = ' . mysqli_real_escape_string($this->db->conn_id, $styleID) . '
			GROUP BY
				be.id
			ORDER BY
				avgRating DESC
				, totalRatings DESC
				, be.beerName ASC
			LIMIT 8
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getStyleIDByBeerID($beerID) {
		$query = '
			SELECT
				be.styleID
			FROM beers be
			WHERE
				be.id = ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
		';
		
		$rs = $this->db->query($query);
		$styleID = 0;
		if($rs->num_rows() > 0) {
			$row = $rs->row_array();
			$styleID = $row['styleID'];
		}
		return $styleID;
	}
	
	public function getBeerRatingByStyleAndUserID($styleID, $userID, $beerID) {
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.alcoholContent
				, be.picture
				, e.id AS establishmentID
				, e.name
				, (aroma * .' . PERCENT_AROMA . ') + (taste * .' . PERCENT_TASTE . ') + (look * .' . PERCENT_LOOK . ') + (drinkability * .' . PERCENT_DRINKABILITY . ') AS rating
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles st ON st.id = be.styleID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
			WHERE
				st.id = ' . mysqli_real_escape_string($this->db->conn_id, $styleID) . '
				AND u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID) . '
				AND be.id != ' . mysqli_real_escape_string($this->db->conn_id, $beerID) . '
			ORDER BY
				be.beerName ASC
			LIMIT ' . SIMILAR_BEER_RATINGS
		;
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getNumBeersAndAverageByUserID($userID) {
		$query = '
			SELECT
				COUNT(DISTINCT be.id) AS beersTasted
				, AVG((aroma * .' . PERCENT_AROMA . ') + (taste * .' . PERCENT_TASTE . ') + (look * .' . PERCENT_LOOK . ') + (drinkability * .' . PERCENT_DRINKABILITY . ')) AS avergeRating
			FROM beers be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN ratings r ON r.beerID = be.id
			INNER JOIN users u ON u.id = r.userID
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
	
	public function getBeerReviewCount($userID) {
		// create the query
		$query = '
			SELECT
				COUNT(DISTINCT r.id) AS beersTasted
			FROM ratings r
			INNER JOIN beers be ON be.id = r.beerID
			INNER JOIN users u ON u.id = r.userID
			WHERE
				u.id = ' . mysqli_real_escape_string($this->db->conn_id, $userID)
		;
		// run the query
		$rs = $this->db->query($query);
		$num = 0;
		if($rs->num_rows() == 1) {
			$row = $rs->row_array();
			$num = $row['beersTasted'];
		}
		return $num;
	}
	
	public function getBeersByEstablishmentID($establishmentID) {
		// create the query
		$query = '
			SELECT
				be.id
				, be.beerName
				, be.styleID
				, s.style
			FROM beers AS be
			INNER JOIN establishment e ON e.id = be.establishmentID
			INNER JOIN styles s ON s.id = be.styleID
			WHERE
				e.id = ' . mysqli_real_escape_string($this->db->conn_id, $establishmentID) . '
			ORDER BY
				beerName ASC
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
	
	public function getTotalBeersInDB() {
		// the query
		$query = '
			SELECT
				COUNT(id) AS beerCount
			FROM beers	
		';
		// get the record set
		$rs = $this->db->query($query);
		// get the number of rows
		$row = $rs->row_array();
		// get the result and send it back
		return $row['beerCount'];
	}
}