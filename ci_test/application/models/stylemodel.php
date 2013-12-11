<?php
if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class StyleModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        $query = '
            SELECT
                id
                , style
                , description
            FROM styles
            ORDER BY
                style ASC
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
                , style AS name
                , origin
                , styleType
            FROM styles
            ORDER BY
                styleType ASC
                , origin ASC
                , style ASC
        ';
        
        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() > 0) {
            $array = $rs->result_array();
        }
        return $array;
    }
    
    public function getStyleByID($styleID) {
        $query = '
            SELECT
                id
                , style AS name
            FROM styles
            WHERE
                id = ' . $styleID
        ;
        
        $rs = $this->db->query($query);
        $array = array();
        if($rs->num_rows() == 1) {
            $array = $rs->row_array();
        }
        return $array;
    }
    
    public function getStylesByUserIDForUserProfile($id, $offset) {
        // temporary holder for results
        $array = '';

        // create the query
        $query = '
            SELECT
                COUNT(DISTINCT styles.id) AS rated_styles
            FROM users
            LEFT OUTER JOIN ratings
                ON ratings.userID = users.id
            INNER JOIN beers
                ON beers.id = ratings.beerID
            INNER JOIN styles
                ON styles.id = beers.styleID
            WHERE
                users.id = ' . mysqli_real_escape_string($this->db->conn_id, $id) . '
                AND users.active = "1"
                AND users.banned = "0"
                AND ratings.active = "1"
        ';        
        // get the record set
        $rs = $this->db->query($query);
        // get the number of rows
        $row = $rs->row_array();
        $total = $row['rated_styles'];
        
        echo $total; exit;
    }
}
?>