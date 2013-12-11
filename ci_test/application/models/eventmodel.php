<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class EventModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function getAll() {
		
	}
	
	public function getByID() {
		
	}
	
	public function getNextEvent() {
		$query = '
			SELECT
				e.id
				, e.shortname
				, e.name
				, e.eventactivity
				, e.eventdate
				, e.eventenddate
				, e.timestart
				, e.timeend
				, e.city
				, e.stateID
				, e.location
				, e.ticketprice
				, e.priority
				, s.stateAbbr
				, s.stateFull
				, DATEDIFF(e.eventdate, CURDATE()) AS startDiff
				, DATEDIFF(e.eventenddate, CURDATE()) AS endDiff
			FROM state s
			INNER JOIN events e ON e.stateID = s.id
			WHERE
				e.active = "1"
				AND (DATEDIFF(e.eventdate, CURDATE()) >= 0 OR DATEDIFF(e.eventenddate, CURDATE()) >= 0)
			ORDER BY				
				e.eventDate
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
	
	public function getEventsInMonth($month) {
	
	}
	
	public function getEventPicsByID($eventID) {
		$query = '
			SELECT
				e.shortname
				, e.eventdate
				, e.name
				, e.city
				, s.stateFull
				, s.stateAbbr
				, ep.pictitle
				, ep.picurl
				, ep.picdesc
				, ep.original
			FROM events e
			INNER JOIN event_pics ep ON ep.eventID = e.id
			INNER JOIN state s ON s.id = e.stateID
			WHERE
				e.id = ' . $eventID . '
			ORDER BY
				ep.id ASC
		';
		
		$rs = $this->db->query($query);
		$array = array();
		if($rs->num_rows() > 0) {
			$array = $rs->result_array();
		}
		return $array;
	}
}
?>