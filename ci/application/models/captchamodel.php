<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class CaptchaModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function insertCaptcha($data) {
		// create the qeury
		$query = '
			INSERT INTO captcha (
				id
				, captcha_time
				, ip_address
				, word
			) VALUES (
				NULL
				, ' . $data['captcha_time'] . '
				, "' . $data['ip_address'] . '"
				, "' . $data['word'] . '"
			)
		';
		// inser the captcha values
		$this->db->query($query);
	}
	
	public function deleteOldCaptcha($expiration) {
		// create the query
		$query = '
			DELETE FROM
				captcha
			WEHRE	
				captcha_time < ' . $expiration
		;
		// run the query
		$this->db->query($query);
		// create the query to optimize and clean up
		$query = '
			OPTIMIZE TABLE captcha
		';
		// run the query
		$this->db->query($query);
	}
	
	public function findCaptcha($data) {
		// create the query
		$query = '
			SELECT
				COUNT(*) AS count
			FROM captcha				
			WHERE
				word = "' . $data['word'] . '"
				AND ip_address = ' . $data['ip_address'] . '
				AND date > ' . $data['expiration']
		;
		// run the query
		$rs = $this->db->query($query);
		// get the returned row
		$row = $query->row();
		// return to the calling
		return $row->count;
	}
}
?>