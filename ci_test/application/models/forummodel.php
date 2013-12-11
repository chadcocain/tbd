<?php
if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ForumModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        $query = '
            SELECT
                t.id
                , t.name
                , t.description
                , st.id AS sub_topic_id
                , st.name AS sub_topic_name
                , st.description AS sub_topic_desc
                , st.thread_count
                , st.reply_count
                , th.subject
                , TIME_TO_SEC(TIMEDIFF(NOW(), th.dtStamp)) AS secondsLastPost
                , u.username
            FROM forum_topic t
            INNER JOIN forum_sub_topic st ON st.topicID = t.id
                AND st.paused = 0
                AND st.deleted = 0
            LEFT JOIN forum_thread th ON th.id = st.threadID
                AND th.paused = 0
                AND th.deleted = 0
                AND th.malicious = 0
                AND th.sticky = 0
            LEFT JOIN users u ON u.id = th.userID
            WHERE
                t.paused = 0
                AND t.deleted = 0
            ORDER BY
                t.name ASC
                , st.name ASC
        ';
        
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->result_array();
        }
        return $array;
    }
    
    public function getSubTopicAndThreads($id) {
        /*$query = '
            SELECT
                th1.id AS thread_id
                , th1.subTopicID AS sub_topic_id
                , th1.subject
                , DATE_FORMAT(th1.dtStamp, "%Y-%m-%d") AS dt_1
                , TIME_TO_SEC(TIMEDIFF(NOW(), th1.dtStamp)) AS seconds_last_post_1
                , th1.sticky
                , th1.views
                , th1.reply_count
                , th2.userID AS user_id_reply
                , DATE_FORMAT(th2.dtStamp, "%Y-%m-%d") AS dt_2
                , TIME_TO_SEC(TIMEDIFF(NOW(), th2.dtStamp)) AS seconds_last_post_2
                , u1.username AS username_1
                , u1.id AS user_id_1
                , u2.username AS username_2
                , u2.id AS user_id_2
                , st.name AS sub_topic_name
                , t.name AS topic_name
                , t.id AS topic_id
            FROM forum_thread AS th1
            LEFT OUTER JOIN forum_thread AS th2 
                ON th2.id = th1.lastReplyID
                AND th2.malicious = 0
                AND th2.paused = 0
                AND th2.deleted = 0
            INNER JOIN users AS u1 
                ON u1.id = th1.userID
            LEFT OUTER JOIN users AS u2 
                ON u2.id = th2.userID
            INNER JOIN forum_sub_topic AS st ON st.id = th1.subTopicID
            INNER JOIN forum_topic AS t ON t.id = st.topicID
            WHERE
                th1.replyID = 0
                AND th1.subTopicID = ' . $id . '
                AND th1.malicious = 0
                AND th1.paused = 0
                AND th1.deleted = 0
            ORDER BY
                th1.sticky DESC
                , th2.dtStamp DESC
        ';*/
        
        $query = '
            SELECT
                th1.id AS thread_id
                , st.id AS sub_topic_id
                , th1.subject
                , DATE_FORMAT(th1.dtStamp, "%Y-%m-%d") AS dt_1
                , TIME_TO_SEC(TIMEDIFF(NOW(), th1.dtStamp)) AS seconds_last_post_1
                , th1.sticky
                , th1.views
                , th1.reply_count
                , th2.userID AS user_id_reply
                , DATE_FORMAT(th2.dtStamp, "%Y-%m-%d") AS dt_2
                , TIME_TO_SEC(TIMEDIFF(NOW(), th2.dtStamp)) AS seconds_last_post_2
                , u1.username AS username_1
                , u1.id AS user_id_1
                , u2.username AS username_2
                , u2.id AS user_id_2
                , st.name AS sub_topic_name
                , st.description AS sub_topic_desc
                , t.name AS topic_name
                , t.id AS topic_id
            FROM forum_topic AS t
            INNER JOIN forum_sub_topic AS st ON st.topicID = t.id
            LEFT OUTER JOIN forum_thread AS th1
                ON th1.subTopicID = st.id
                AND th1.replyID = 0
                AND th1.malicious = 0
                AND th1.paused = 0
                AND th1.deleted = 0 
            LEFT OUTER JOIN forum_thread AS th2 
                ON th2.id = th1.lastReplyID
                AND th2.malicious = 0
                AND th2.paused = 0
                AND th2.deleted = 0
            LEFT OUTER JOIN users AS u1 
                ON u1.id = th1.userID
            LEFT OUTER JOIN users AS u2 
                ON u2.id = th2.userID
            WHERE
                st.id = ' . $id . '
            ORDER BY
                th1.sticky DESC
                , th2.dtStamp DESC
        ';
        
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->result_array();
        }    //echo '<pre>'; print_r($array); echo '</pre>'; exit;
        return $array;    
    }
    
    public function getSubTopicInfoByID($id) {
        $query = '
            SELECT
                st.name AS sub_topic_name
                , st.description AS sub_topic_desc
                , t.name
                , t.id
            FROM forum_sub_topic AS st
            INNER JOIN forum_topic AS t ON t.id = st.topicID
            WHERE
                st.id = ' . $id
        ;
        
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->row();
        }    //echo '<pre>'; print_r($array); echo '</pre>'; exit;
        return $array;
    }
    
    public function checkThreadExists($id, $sub_topic_id) {
        $query = '
            SELECT
                id
                , subject
                , closed
            FROM forum_thread
            WHERE
                id = ' . $id . '
                AND subTopicID = ' . $sub_topic_id
        ;
        
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->row_array();
        }    //echo '<pre>'; print_r($array); echo '</pre>'; exit;
        return $array;
    }
    
    public function createNewThread($data) {
        // insert the information for the thread
        $query = '
            INSERT INTO forum_thread (
                id
                , userID
                , subTopicID
                , ip
        ';
        if($data['type'] == 'new_thread') {
            $query .= '
                , subject
            ';
        }
        $query .= '        
                , message
                , dtStamp
        ';
        if($data['type'] == 'reply_thread') {
            $query .= '
                , replyID
            ';
        }
        $query .= '
                , views
            ) VALUES (
                NULL
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['user_id']) . '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['sub_topic_id']) . '
                , "' .  mysqli_real_escape_string($this->db->conn_id, $data['remote_addr']) . '"
        ';
        if($data['type'] == 'new_thread') {
            $query .= '
                , "' .  mysqli_real_escape_string($this->db->conn_id, $data['subject']) . '"
            ';
        }
        $query .= '                
                , "' .  mysqli_real_escape_string($this->db->conn_id, $data['message']) . '"
                , NOW()
        ';
        if($data['type'] == 'reply_thread') {
            $query .= '
                , ' . mysqli_real_escape_string($this->db->conn_id, $data['thread_id']) . '
            ';
        }
        $query .= '
                , 0
            )
        '; //echo '<pre>'; print_r($query); exit;
        // run the query        
        $rs = $this->db->query($query);
        // get the insert id
        $insert_id = $this->db->conn_id->insert_id;

        // update the information for the sub topic based on type
        if($data['type'] == 'new_thread') {
            $query = '
                UPDATE forum_sub_topic SET
                    thread_count = thread_count + 1
                    , threadID = ' . $insert_id . '
                WHERE
                    id = ' . $data['sub_topic_id'] . '
                LIMIT 1
            ';
            // run the query        
            $rs = $this->db->query($query);
            
            // update the last reply id
            $query = '
                UPDATE forum_thread SET
                    lastReplyID = ' . $insert_id . '
                WHERE
                    id = ' . $insert_id . '
                    AND subTopicID = ' . $data['sub_topic_id'] . '
                LIMIT 1
            ';
            // run the query        
            $rs = $this->db->query($query);
        } else {
            $query = '
                UPDATE forum_sub_topic SET
                    reply_count = reply_count + 1
                    , threadID = ' . $insert_id . '
                WHERE
                    id = ' . $data['sub_topic_id'] . '
                LIMIT 1
            ';
            // run the query        
            $rs = $this->db->query($query);
            
            // update the last reply id
            $query = '
                UPDATE forum_thread SET
                    lastReplyID = ' . $insert_id . '
                    , reply_count = reply_count + 1
                WHERE
                    id = ' . $data['thread_id'] . '
                    AND subTopicID = ' . $data['sub_topic_id'] . '
                LIMIT 1
            ';
            // run the query        
            $rs = $this->db->query($query);
        }
        

        // return the inserted id value
        return $insert_id;
    }
    
    public function getThreadByID($id, $sub_topic_id) {
        $query = '
            SELECT
                th.id AS thread_id
                , th.subject
                , th.message
                , DATE_FORMAT(th.dtStamp, "%Y-%m-%d %H:%i:%s") AS dtStamp
                , TIME_TO_SEC(TIMEDIFF(NOW(), th.dtStamp)) AS seconds_last_post
                , th.views
                , th.locked
                , th.malicious
                , th.closed
                , st.name AS sub_topic_name
                , t.id AS topic_id
                , t.name AS topic_name
                , u.id AS user_id
                , u.username
                , u.avatar
                , u.avatarImage AS avatar_image
                , u.city
                , u.state
            FROM forum_thread AS th
            INNER JOIN forum_sub_topic AS st 
                ON st.id = ' . $sub_topic_id . '
                AND st.paused = 0
                AND st.deleted = 0
            INNER JOIN forum_topic AS t
                ON t.id = st.topicID
                AND t.paused = 0
                AND t.deleted = 0
            INNER JOIN users AS u
                ON u.id = th.userID
            WHERE
                th.subTopicID = ' . $sub_topic_id . '
                AND (
                    th.id = ' . $id . ' OR th.replyID = ' . $id . '
                )
                AND th.paused = 0
                AND th.deleted = 0
            ORDER BY
                th.dtStamp ASC
        ';
        //echo '<pre>'; print_r($query); exit;
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->result_array();
        }    //echo '<pre>'; print_r($array); echo '</pre>'; exit;
        return $array;
    }
    
    public function addViewToThread($id, $sub_topic_id) {
        $query = '
            UPDATE forum_thread SET
                views = views + 1
            WHERE
                id = ' . $id . '
                AND subTopicID = ' . $sub_topic_id . '
            LIMIT 1
        ';
        // run the query        
        $rs = $this->db->query($query);
    }
    
    public function getQuote($thread_id) {
        $query = '
            SELECT
                message
            FROM forum_thread
            WHERE
                id = ' . $thread_id . '
                AND paused = 0
                AND deleted = 0
                AND malicious = 0
        ';
        $rs = $this->db->query($query);
        $array = false;        
        if($rs->num_rows() > 0) {            
            $array = $rs->row_array();
            $array = $array['message'];
        }    //echo '<pre>'; print_r($array); echo '</pre>'; exit;
        return $array;
    }
    
    public function get_email_for_thread($thread_id, $current_user_id) {
        $query = '
            SELECT
                DISTINCT users.username
                , users.email
            FROM forum_thread
            INNER JOIN users
                ON users.id = forum_thread.userID
            WHERE
                (
                    forum_thread.id = ' . $thread_id . '
                    OR forum_thread.replyID = ' . $thread_id . '
                ) 
                AND forum_thread.userID <> ' . $current_user_id
        ;
        $rs = $this->db->query($query);
        $array = array();        
        if($rs->num_rows() > 0) {            
            $array = $rs->result_array();
        }
        return $array;    
    }
}
?>