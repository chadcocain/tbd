<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class UserModel extends CI_Model {
	private $id;
	private $username;
	private $firstName;
	private $lastName;
	private $email;
	private $password;
	private $userTypeID;
	private $userType;
	private $birthdate;
	private $city;
	private $state;
	private $notes;
	private $avatar;
	private $avatarImage;
	private $lastLogin;
	private $joinDate;
	private $formatLastLogin;
	private $uploadImage;
	private $stateID;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function login($config) {
		$email = $config['email'];
		$password = $config['password'];
		
		$query = '
			SELECT
				u.id
				, u.usertype_id
				, ut.usertype
				, u.username
				, u.firstname
				, u.lastname
				, u.email
				, u.birthdate
				, u.city
				, u.state	
				, u.notes		
				, u.avatar
				, u.avatarImage
				, u.lastlogin
				, u.joindate
				, DATE_FORMAT(u.lastlogin, "%a, %b %d, %Y at %T") AS formatLastLogin
				, u.uploadImage
				, s.id AS stateID
			FROM users u
			INNER JOIN usertype ut ON ut.id = u.usertype_id
			LEFT JOIN state s ON s.stateAbbr = u.state
			WHERE
				active = "1"
				AND banned = "0"
				AND email = "' . mysqli_real_escape_string($this->db->conn_id, $email) . '"
				AND password = SHA1("' . mysqli_real_escape_string($this->db->conn_id, $password) . '")
		';
		
		if(key_exists('type', $config) && $config['type'] == 'admin') {
			$query .= '
				AND ut.usertype = "admin"
			';
		}
		
		$rs = $this->db->query($query);
		$boolean = false;
		if($rs->num_rows() == 1) {
			$row = $rs->row_array();
			
			$this->setID($row['id']);
			$this->setUserName($row['username']);
			$this->setFirstName($row['firstname']);
			$this->setLastName($row['lastname']);
			$this->setEmail($row['email']);
			$this->setBirthDate($row['birthdate']);
			$this->setCity($row['city']);
			$this->setState($row['state']);
			$this->setAvatar($row['avatar']);
			$this->setAvatarImage($row['avatarImage']);
			$this->setUserTypeID($row['usertype_id']);
			$this->setUserType($row['usertype']);
			$this->setLastLogin($row['lastlogin']);
			$this->setJoinDate($row['joindate']);
			$this->setFormatLastLogin($row['formatLastLogin']);
			$this->setUploadImage($row['uploadImage']);
			$this->setStateID($row['stateID']);
			$this->setNotes($row['notes']);

			$this->updateLastLogin();
			
			$boolean = true;
		}
		return $boolean;
	}
	
	public function getUserProfile($id) {
		// create the query
		$query = '
			SELECT
				id
				, username
				, firstname
				, lastname
				, email
				, birthdate
				, city
				, state
				, notes
				, avatar
				, avatarImage
				, lastlogin
				, DATE_FORMAT(joindate, "%a, %b %d, %Y at %T") AS joinDate
				, DATE_FORMAT(lastlogin, "%a, %b %d, %Y at %T") AS formatLastLogin
				, TIME_TO_SEC(TIMEDIFF(NOW(), lastlogin)) AS secondsLastLogin
				, uploadImage
			FROM users u
			WHERE
				active = "1"
				AND banned = "0"
				AND id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
		';
		// run the query
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() == 1) {
			$array = $rs->row_array();
		}
		return $array;
	}
	
	public function updateProfileByID($id, $data) {
		// create the query
		$query = '
			UPDATE users
			SET
				username = "' . mysqli_real_escape_string($this->db->conn_id, $data['username']) . '"
				, firstname = "' . mysqli_real_escape_string($this->db->conn_id, $data['firstname']) . '"
				, lastname = "' . mysqli_real_escape_string($this->db->conn_id, $data['lastname']) . '"
				, email = "' . mysqli_real_escape_string($this->db->conn_id, $data['email']) . '"
				, birthdate = "' . mysqli_real_escape_string($this->db->conn_id, $data['birthdate']) . '"
				, city = "' . mysqli_real_escape_string($this->db->conn_id, $data['city']) . '"
				, state = "' . mysqli_real_escape_string($this->db->conn_id, $data['state']) . '"
				, notes = "' . mysqli_real_escape_string($this->db->conn_id, $data['notes']) . '"
			WHERE
				active = "1"
				AND banned = "0"
				AND id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
		';
		// run the query
		$this->db->query($query);
	}
	
	public function updateLastLogin($id = '') {
		$id = empty($id) ? $this->getID() : $id;
		$query = '
			UPDATE users
			SET
				lastlogin = NOW()
			WHERE
				id = ' . $id;
		;
		$rs = $this->db->query($query);
	}
	
	public function createAccount($config) {
		// create the query
		$query = '
			INSERT INTO users (
				id
				, usertype_id
				, username
				, password
				, email
				, city
				, state
				, ip
				, activationdate
				, activationcode
				, active
			) VALUES (
				NULL
				, ' . $config['usertype'] . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['username']) . '"
				, SHA1("' . mysqli_real_escape_string($this->db->conn_id, $config['password1']) . '")
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['email']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['city']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['state']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['ip']) . '"
				, NOW()
				, SHA1(NOW())
				, "0"
			)
		';
		// run the query
		$this->db->query($query);
		// return the inserted id
		return $this->db->insert_id();
	}
	
	public function getActivationCode($userID) {
		// create the query
		$query = '
			SELECT
				CONCAT(id, "_", activationcode) AS aCode
			FROM users
			WHERE
				id = ' . $userID . '
			LIMIT 1
		';
		// create the record set
		$rs = $this->db->query($query);
		$result = false;
		if($rs->num_rows() == 1) {
			$row = $rs->row_array();
			$result = $row['aCode'];
		}
		return $result;
	}
	
	public function activateAccount($config) {
		// get the id and the activation code separate
		$parts = explode('_', $config['activationCode']);
		// create the query to see if this account exists
		$query = '
			UPDATE users SET
				joindate = NOW()
				, active = "1"
			WHERE
				activationdate > DATE_SUB(NOW(), INTERVAL 48 HOUR)
				AND id = ' . mysqli_real_escape_string($this->db->conn_id, $parts[0]) . '
				AND activationcode = "' . mysqli_real_escape_string($this->db->conn_id, $parts[1]) . '"
				AND active = "0"
		';
		// run the query
		$this->db->query($query);
		// return the number of affected rows (should be one)
		return $this->db->affected_rows();
	}
	
	public function idCheck($id) {
		// create the query
		$query = '
			SELECT
				id
			FROM users
			WHERE
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND active = "1"
				AND banned = "0"
		';
		// create the record set
		$rs = $this->db->query($query);
		$result = false;
		if($rs->num_rows() == 1) {
			$result = true;
		}
		return $result;
	}
	
	public function emailCheck($email) {
		// create the query
		$query = '
			SELECT
				email
			FROM users
			WHERE
				email = "' . mysqli_real_escape_string($this->db->conn_id, $email) . '"
		';
		// create the record set
		$rs = $this->db->query($query);
		$result = true;
		if($rs->num_rows() > 0) {
			$result = false;
		}
		return $result;
	}
	
	public function emailCheckMatch($email) {
		// create the query
		$query = '
			SELECT
				email
			FROM users
			WHERE
				email = "' . mysqli_real_escape_string($this->db->conn_id, $email) . '"
		';
		// create the record set
		$rs = $this->db->query($query);
		$result = false;
		if($rs->num_rows()== 1) {
			$result = true;
		}
		return $result;
	}
	
	public function getInfoIfEmailExists($email) {
		// create the query
		$query = '
			SELECT
				id
				, username
				, email
			FROM users
			WHERE
				email = "' . mysqli_real_escape_string($this->db->conn_id, $email) . '"
				AND active = "1"
				AND banned = "0"
		';
		// create the record set
		$rs = $this->db->query($query);
		// holder for the result
		$result = array();
		// check the number of results
		if($rs->num_rows() == 1) {
			// there was a match
			// holder of values to pass
			$array = array();
			// get the datetime stamp
			$array['datetime'] = date('Y-m-d H:i:s');
			// create the validation key
			$array['validationKey'] = sha1($array['datetime']);
			// need the email address too
			$array['email'] = $email;
			// set the password reset info
			$this->setPasswordResetValidationValues($array);
			// set the result array
			$result = array_merge($rs->row_array(), $array);
		}
		// return the results
		return $result;
	}
	
	private function setPasswordResetValidationValues($config) {
		// create the query
		$query = '
			UPDATE users
			SET
				passwordresetcode = "' . $config['validationKey'] . '"
				, passwordresetdate = "' . $config['datetime'] . '"
			WHERE
				email = "' . mysqli_real_escape_string($this->db->conn_id, $config['email']) . '"
			LIMIT 1
		';
		// run the query
		$this->db->query($query);
	}
	
	public function validatePasswordCode($config) {
		// create the query
		$query = '
			SELECT
				id
				, username
				, email
			FROM users
			WHERE
				id = "' . mysqli_real_escape_string($this->db->conn_id, $config['userID']) . '"
				AND passwordresetcode = "' . mysqli_real_escape_string($this->db->conn_id, $config['activationCode']) . '"
				AND passwordresetdate > DATE_SUB(NOW(), INTERVAL 4 HOUR)
				AND active = "1"
				AND banned = "0"
		';
		// run the query
		$rs = $this->db->query($query);
		// holder for the result
		$result = false;
		// check the number of results
		if($rs->num_rows() == 1) {
			// there was a match
			// holder of values to pass
			$array = array();
			// get the password
			$array['newPassword'] = substr(sha1(date('Y-m-d H:i:s')), 0, 8);
			// the user id to change password for
			$array['userID'] = $config['userID'];
			// set the password reset info
			$this->setPassword($array);
			// get the row of information from the query
			$row = $rs->row_array();
			// set the result array
			$result = array('newPassword' => $array['newPassword'], 'username' => $row['username'], 'email' => $row['email']);
		}
		// return the results
		return $result;
	}
	
	public function setPassword($config) {
		// create the query
		$query = '
			UPDATE users			
			SET
				password = SHA1("' . mysqli_real_escape_string($this->db->conn_id, $config['newPassword']) . '")
			WHERE
				id = "' . mysqli_real_escape_string($this->db->conn_id, $config['userID']) . '"
			LIMIT 1
		';
		// run the query
		$this->db->query($query);
	}
	
	public function usernameCheck($username) {
		// create the query
		$query = '
			SELECT
				id
				, username
			FROM users
			WHERE
				username = "' . mysqli_real_escape_string($this->db->conn_id, $username) . '"
		';
		// create the record set
		$rs = $this->db->query($query);
		// holder for the boolean
		$result = false;
		// check if there were any results
		if($rs->num_rows() > 0) {
			// there was a result
			$result = true;
		}
		// return the boolean
		return $result;
	}
	
	public function usernameCheckCreateAccount($username) {
		// create the query
		$query = '
			SELECT
				id
				, username
			FROM users
			WHERE
				username = "' . mysqli_real_escape_string($this->db->conn_id, $username) . '"
		';
		// create the record set
		$rs = $this->db->query($query);
		// holder for the boolean
		$result = true;
		// check if there were any results
		if($rs->num_rows() > 0) {
			// there was a result
			$result = false;
		}
		// return the boolean
		return $result;
	}
	
	public function getUsernameByID($id) {
		// create the query
		$query = '
			SELECT
				id
				, username
			FROM users
			WHERE
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
				AND active = "1"
				AND banned = "0"
		';
		// create the record set
		$rs = $this->db->query($query);
		// holder for the return value
		$result = array();
		// check if there were any results
		if($rs->num_rows() > 0) {
			// there was a result
			$row = $rs->row_array();
			$result = $row['username'];
		}
		// return the result
		return $result;
	}
	
	public function getIDByUsername($username) {
		// create the query
		$query = '
			SELECT
				id
				, username
			FROM users
			WHERE
				username = "' . mysqli_real_escape_string($this->db->conn_id, $username) . '"
				AND active = "1"
				AND banned = "0"
		';
		// create the record set
		$rs = $this->db->query($query);
		// holder for the return value
		$result = array();
		// check if there were any results
		if($rs->num_rows() > 0) {
			// there was a result
			$row = $rs->row_array();
			$result = $row['id'];
		}
		// return the result
		return $result;
	}
	
	public function checkBlockUsername($username, $blockID) {
		// create the query
		$query = '
			SELECT
				u.id
				, b.block_userID
				, b.userID
			FROM users u
			INNER JOIN pms_blocked b ON b.userID = u.id
			WHERE
				u.username = "' . mysqli_real_escape_string($this->db->conn_id, $username) . '"
				AND b.block_userID = ' . $blockID
		;
		// create the record set
		$rs = $this->db->query($query);
		// holder for the boolean
		$boolean = true;
		// check if there were any results
		if($rs->num_rows() > 0) {
			// there was a result
			$boolean = false;
		}
		// return the boolean
		return $boolean;
	}
	
	public function getPMSByUserID($id) {
		// create the query
		$query = '
			SELECT
				p.id
				, p.from_userID
				, p.subject
				, p.message
				, DATE_FORMAT(p.timeSent, "%W, %M %d, %Y<br />%T") AS timesent
				, p.timeRead
				, p.forwardID
				, p.replyID
				, u.username
			FROM pms p
			INNER JOIN users u ON u.id = p.from_userID
			WHERE
				p.to_userID = ' . $id . '
			ORDER BY
				p.timeSent DESC
		';
		// create the record set
		$rs = $this->db->query($query);
		$array = false;
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getPMByMessageID($messageID, $userID) {
		// create the query
		$query = '
			SELECT
				p.id
				, p.from_userID
				, p.subject
				, p.message
				, DATE_FORMAT(p.timeSent, "%W, %M %d, %Y, %T") AS timesent
				, p.timeRead
				, p.forwardID
				, p.replyID
				, u.username
				, u.city
				, u.state
				, u.avatar
				, u.avatarImage
				, DATE_FORMAT(u.joindate, "%M, %Y") AS joindate
			FROM pms p
			INNER JOIN users u ON u.id = p.from_userID
			WHERE
				p.to_userID = ' . $userID . '
				AND p.id = ' . $messageID . '
			ORDER BY
				p.timeSent DESC
		';
		// create the record set
		$rs = $this->db->query($query);
		$array = false;
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function insertPM($config) {
		// array of tables to run query on
		//$arr_tables = array('pms', 'pms_sent');
		$arr_tables = array('pms');
		// create the query text
		$query = '
				id
				, from_userID
				, to_userID
				, subject
				, message
				, timeSent
		';
		$query .= key_exists('forwardID', $config) ? ', forwardID' : '';
		$query .= key_exists('replyID', $config) ? ', replyID' : '';
		$query .= '
			) VALUES (
				NULL
				, ' . mysqli_real_escape_string($this->db->conn_id, $config['fromID']) . '
				, ' . mysqli_real_escape_string($this->db->conn_id, $config['toID']) . '
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['subject']) . '"
				, "' . mysqli_real_escape_string($this->db->conn_id, $config['message']) . '"
				, NOW()
		';
		$query .= key_exists('forwardID', $config) ? mysqli_real_escape_string($this->db->conn_id, $config['forwardID']) : '';
		$query .= key_exists('replyID', $config) ? mysqli_real_escape_string($this->db->conn_id, $config['replyIDID']) : '';
		$query .= '
			)
		';
		// iterate through the table array
		foreach($arr_tables as $table) {
			$query = 'INSERT INTO ' . $table . ' (' . $query;
			// create the record set
			$this->db->query($query);
		}
	}
	
	public function removePM($messageID, $userID) {
		// create the query
		$query = '
			DELETE FROM pms
			WHERE
				id = ' . $messageID . '
				AND to_userID = ' . $userID . '
			LIMIT 1
		';
		// create the record set
		$rs = $this->db->query($query);
		// create the query
		$query = 'OPTIMIZE TABLE pms';
		// create the record set
		$rs = $this->db->query($query);
	}
	
	public function numNewMessages($userID) {
		// create the query
		$query = '
			SELECT
				COUNT(p.id) AS pmCount
			FROM pms p
			INNER JOIN users u ON u.id = p.from_userID
			WHERE
				p.to_userID = ' . $userID . '
				AND timeRead IS NULL
		';
		// create the record set
		$rs = $this->db->query($query);
		$row = $rs->row_array();
		return $row['pmCount'];
	}
	
	public function updateTimeRead($messageID, $userID) {
		// create the query
		$query = '
			UPDATE pms SET
				timeRead = NOW()
			WHERE
				to_userID = ' . $userID . '
				AND id = ' . $messageID . '
			LIMIT 1
		';
		// create the record set
		$rs = $this->db->query($query);
	}
	
	public function getMessageInfoByMessageID($messageID) {
		// create the query
		$query = '
			SELECT
				p.id
				, p.from_userID
				, p.subject
				, p.message
				, DATE_FORMAT(p.timeSent, "%W, %M %d, %Y, %T") AS timesent
				, p.timeRead
				, p.forwardID
				, p.replyID
				, u.username
			FROM pms p
			INNER JOIN users u ON u.id = p.from_userID
			WHERE
				p.id = ' . mysqli_real_escape_string($this->db->conn_id, $messageID) . '
		';
		// create the record set
		$rs = $this->db->query($query);
		$array = false;
		if($rs->num_rows() > 0) {
			$array = $rs->row_array();
		}
		return $array;
		/*	SELECT
				u.username
			FROM pms p
			INNER JOIN users u ON u.id = p.from_userID
			WHERE
				p.id = ' . mysqli_real_escape_string($this->db->conn_id, $messageID)
		;
		// create the record set
		$rs = $this->db->query($query);
		$result = false;
		if($rs->num_rows() > 0) {
			$row = $rs->row_array();
			$result = $row['username'];
		}
		return $result;*/
	}
	
	public function updateAvatar($id, $image) {
		// create the query
		$query = '
			UPDATE users SET
				avatar = "1"
				, avatarImage = "' . $image . '"
			WHERE
				id = ' . mysqli_real_escape_string($this->db->conn_id, $id)
		;
		// run the query
		$this->db->query($query);
		// return the number of affected rows (should be one)
		return $this->db->affected_rows();
	}
    
    public function addToDudeList($id, $dudeID) {
        // create the query to check if they are already on the list
        $query = '
            SELECT
                userID
            FROM dudes
            WHERE
                userID = ' . $id . '
                AND dudeID = ' . $dudeID . '
            LIMIT 1
        ';
        // run the query
        $rs = $this->db->query($query);
        // holder array 
        $array = false;
        // see if there was a row returned
        if($rs->num_rows() > 0) {
            $array = $rs->row_array();
        }
        // check that we dont' have results
        if($array === false) {
            // create the insert query
            $query = '
                INSERT INTO dudes (
                    userID
                    , dudeID
                ) VALUES (
                    ' . $id . '
                    , ' . $dudeID . '
                )
            ';
            // run the query
            $this->db->query($query);    
        }
        return $array;
    }
    
    public function removeFromDudeList($id, $dudeID) {
        // create the query
        $query = '
            DELETE FROM dudes
            WHERE
                userID = ' . $id . '
                AND dudeID = ' . $dudeID . '
            LIMIT 1
        ';
        // run the query
        $rs = $this->db->query($query);
		// clean up
		$query = 'OPTIMIZE TABLE dudes';
		// run the query
		$this->db->query($query);
    }
    
    public function selectDudeList($id) {
        // create the query
        $query = '
            SELECT
                u.id
                , u.username    
            FROM dudes AS d
            INNER JOIN users AS u
                ON u.id = d.dudeID
            WHERE
                d.userID = ' . $id . '
            ORDER BY 
                u.username ASC
        ';
        // create the record set
        $rs = $this->db->query($query);
        // holder for the passback
        $array = false;
        // check if there were results
        if($rs->num_rows() > 0) {
            // store the results
            $array = $rs->result_array();
        }
        // return the results
        return $array;
        
    }
	
	public function setID($int) {
		$this->id = $int;
	}
	
	public function getID() {
		return $this->id;
	}
	
	public function setUserName($str) {
		$this->username = $str;
	}
	
	public function getUserName() {
		return $this->username;
	}
	
	public function setFirstName($str) {
		$this->firstName = $str;
	}
	
	public function getFirstName() {
		return $this->firstName;
	}
	
	public function setLastName($str) {
		$this->lastName = $str;
	}
	
	public function getLastName() {
		return $this->lastName;
	}
	
	public function setEmail($str) {
		$this->email = $str;	
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setUserTypeID($int) {
		$this->userTypeID = $int;
	}
	
	public function getUserTypeID() {
		return $this->userTypeID;
	}
	
	public function setUserType($str) {
		$this->userType = $str;
	}
	
	public function getUserType() {
		return $this->userType;
	}
	
	public function setBirthDate($str) {
		$this->birthdate = $str;
	}
	
	public function getBirthDate() {
		return $this->birthdate;
	}
	
	public function setCity($str) {
		$this->city = $str;
	}
	
	public function getCity() {
		return $this->city;
	}
	
	public function setState($str) {
		$this->state = $str;
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function setNotes($str) {
		$this->notes = $str;
	}
	
	public function getNotes() {
		return $this->notes;
	}
	
	public function setAvatar($str) {
		$this->avatar = $str;
	}
	
	public function getAvatar() {
		return $this->avatar;
	}
	
	public function setAvatarImage($str) {
		$this->avatarImage = $str;
	}
	
	public function getAvatarImage() {
		return $this->avatarImage;
	}
	
	public function setLastLogin($str) {
		$this->lastLogin = $str;
	}
	
	public function getLastLogin() {
		return $this->lastLogin;
	}
	
	public function setJoinDate($str) {
		$this->joinDate = $str;
	}
	
	public function getJoinDate() {
		return $this->joinDate;
	}
	
	public function setFormatLastLogin($str) {
		$this->formatLastLogin = $str;
	}
	
	public function getFormatLastLogin() {
		return $this->formatLastLogin;
	}
	
	public function setUploadImage($str) {
		$this->uploadImage = $str;
	}
	
	public function getUploadImage() {
		return $this->uploadImage;
	}
	
	public function setStateID($int) {
		$this->stateID = $int;
	}
	
	public function getStateID() {
		return $this->stateID;
	}

	/**
	 * getAll()
	 * retrieves all records from the database
	 *
	 * @return associative array 
	 * 'total' => number of records
	 * 'records' => associative array of all users found
	 */
	public function getAll() {
		$query = $this->db->query('SELECT count(*) as total FROM users ');
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$total = $row['total'];
			$query = $this->db->query('SELECT * FROM users ');
			return array('total' => $total, 'records' => $query->result_array());
		} else {
			return array('total' => 0, 'records' => array());
		}
	}	
	/**
	 * getActiveUsers()
	 * retrieves all active records from the database
	 *
	 * @return associative array 
	 * 'total' => number of records
	 * 'records' => associative array of all records found
	 */	
	public function getActiveUsers() {
		$query = $this->db->query('SELECT count(*) as total FROM users WHERE active=1 ');
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$total = $row['total'];
			$query = $this->db->query('SELECT * FROM users WHERE active=1 ');
			return array('total' => $total, 'records' => $query->result_array());
		} else {
			return array('total' => 0, 'records' => array());
		}
	}
	
	public function getAuthorizedUsers() {
		$site = resolve_server_url();
		$sql = "
		SELECT count(*) AS total 
		FROM users u 
		JOIN user_sites us ON u.id=us.user_id 
		JOIN sites s ON s.id=us.site_id  
		WHERE (u.role_id>1) AND (s.url='$site')
		";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$total = $row['total'];
			$sql = "
			SELECT DISTINCT u.name, u.email, u.phone, u.login, r.display, us.user_id 
			FROM users u 
			JOIN user_sites us ON u.id=us.user_id 
			JOIN sites s ON s.id=us.site_id 
			JOIN roles r ON r.id=u.role_id 
			WHERE (u.role_id>1) AND (s.url='$site') AND (u.active=1)
			";
			$query = $this->db->query($sql);
			return array('total' => $total, 'records' => $query->result_array());
		} else {
			return array('total' => 0, 'records' => array());
		}

	}
	public function checkSiteAccess() {
		$user_id = $this->_id;
		$url = resolve_server_url();
		$sql = "SELECT * FROM user_sites us JOIN sites s ON us.site_id=s.id WHERE s.url='".$url."' and us.user_id=".$user_id." ";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {	
			return true;
		} else {
			return false;
		}
	}
    
    public function auto_complete_search($term) {
        $query = '
            SELECT
                users.id
                , users.username AS name
            FROM users
            WHERE
                users.username LIKE "' . mysqli_real_escape_string($this->db->conn_id, $term) . '%"
            ORDER BY
               users.username DESC
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