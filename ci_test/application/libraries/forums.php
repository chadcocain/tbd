<?php
if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Forums {
    private $ci;
    private $thread_search = array(
        '[q]' 
        , '[/q]'
    );
    private $thread_replace = array(
        '<div class="thread_quote"><p>Quote:</p><p class="quote_contents">'
        , '</p></div>'
    );
    
    public function __construct() {
        $this->ci =& get_instance();
    }
    
    public function getAllForums() {
        // get all the beers
        $forums = $this->ci->ForumModel->getAll();
        //echo '<pre>'; print_r($forums); exit;
        // holder for string value
        $str = '
            <h2 class="brown">Forums</h2>
            <p class="marginTop_8">Use Two Beer Dudes forums to discuss any and all topics of American craft beer.</p>
        ';
        
        // check if there are results
        if(empty($forums)) {
            $str .= '<p class="marginTop_8">There are currently no forums.</p>';
        } else {
            // holder array for topics 
            $topics = array();
            // counter
            $cnt = 0;
            // iterate through the forums
            foreach($forums as $forum) {
                // check if the topic exists
                if(!in_array($forum['id'], $topics)) {
                    // check if this is empty or not
                    if(!empty($topics)) {
                        $str .= '
                                </tbody>
                            </table>
                        </div>
                        ';
                    }
                    // add it to the array
                    $topics[] = $forum['id'];
                    
                    // start the display
                    $str .= '
                        <div id="topic_' . $forum['id'] . '">
                            <table id="sub_topic' . $forum['id'] . '" style="width: 100%;" class="tbl_standard">
                                <caption><a name="' . $forum['id'] . '"><h3 class="green">' . $forum['name'] . '</h3></a></caption>
                                <tbody>
                                    <tr>
                                        <th class="header">Topic</th>
                                        <th class="header">Posts</th>
                                        <th class="header">Replies</th>
                                        <th class="header">Last Draft</th>
                                    </tr>
                    ';
                }
                // get the date formatted if present
                $lastActivity = 'no posts';
                if(!empty($forum['secondsLastPost'])) {
                    // determine the time since last activity
                    $lastActivity = determineTimeSinceLastActive($forum['secondsLastPost']);   
                } 
                // get the style of the row
                $style = ($cnt % 2 == 0) ? '' : ' class="gray"';
                // add the sub topic
                $str .= '
                                    <tr' . $style . '>
                                        <td>
                                            <h4><a href="' . base_url() . 'forum/dst/' . $forum['sub_topic_id'] . '">' . $forum['sub_topic_name'] . '</a></h4>
                                            <p>' . $forum['sub_topic_desc'] . '</p>
                                        </td>
                                        <td class="center">' . number_format($forum['thread_count']) . '</td>
                                        <td class="center">' . number_format($forum['reply_count']) . '</td>
                                        <td>' . $lastActivity . '</td>
                                    </tr>                                
                ';
                // increment the counter
                $cnt++;
            }
            // finish off the division
            $str .= '
                                </tbody>
                            </table>
                        </div>
            ';
        }
        
        $return['leftCol'] = $str;
        $return['rightCol'] = '
            <h4><span>Holder</span></h4>
        ';
        // set the page information
	$return['seo'] = getSEO();        
        
        // return data back to call
        return $return;
    }
    
    public function getSubTopic() {
        // get the id of the sub topic to display
        $subTopicID = $this->ci->uri->segment(3);
        
        // check that the sub topic id exists as well as
        // get the content of the forum
        $items = $this->ci->ForumModel->getSubTopicAndThreads($subTopicID);
        
        // holder for string value
        $str = '
            <h2 class="brown">Viewing ' . $items[0]['sub_topic_name'] . '</h2>
            <p class="marginTop_8"><span class="bold"><a href="' . base_url() . 'forum#' . $items[0]['topic_id'] . '">' . $items[0]['topic_name'] . '</a> -&gt; ' . $items[0]['sub_topic_name'] . '</span></p>                 
            <p class="marginTop_8"><a href="' . base_url() . 'forum/atr/nt/' . $subTopicID . '">Create New Thread</a></p>
        ';
         //<a href="' . base_url() . 'forum/st/' . $subTopi . '/' . $thread_id . '">' . $thread_subject . '</a></span></p>
         //<a href="' . base_url() . 'forum/dst/' . $subTopicID . '">' . $items[0]['sub_topic_name'] . '</a>
        // check if there are results
        if(empty($items[0]['thread_id'])) {
            $str .= '<p class="marginTop_8">There are no posts for this topic.  Be the first to create a <a href="' . base_url() . 'forum/atr/nt/' . $subTopicID . '">thread</a>.</p>';
        } else {
            // start the display
            $str .= '
            <div id="sub_topic">
                <table id="thread_table" class="tbl_standard" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="header">Post</th>
                            <th class="header">Created By</th>
                            <th class="header">Replies</th>
                            <th class="header">Views</th>
                            <th class="header">Last Draft</th>
                        </tr>
                    </thead>
                    </tbody>
            ';
            
            // counter
            $cnt = 0;
            // iterate through the forums
            foreach($items as $item) {              
                // get the date formatted if present
                $lastActivity = '';
                if(!empty($item['seconds_last_post_2'])) {
                    // determine the time since last activity
                    $lastActivity = determineTimeSinceLastActive($item['seconds_last_post_2']) . ' by <a href="' . base_url() . 'user/profile/' . $item['user_id_2'] . '">' . $item['username_2'] . '</a>';   
                } else {
                    $lastActivity = determineTimeSinceLastActive($item['seconds_last_post_1']) . ' by <a href="' . base_url() . 'user/profile/' . $item['user_id_1'] . '">' . $item['username_1'] . '</a>';      
                }
                
                // get the style of the row
                $style = ($cnt % 2 == 0) ? '' : ' class="gray"';
                // add the sub topic
                $str .= '
                        <tr' . $style . '>
                            <td><a href="' . base_url() . 'forum/st/' . $item['sub_topic_id'] . '/' . $item['thread_id'] . '">' . $item['subject'] . '</a></td>
                            <td class="center"><a href="' . base_url() . 'user/profile/' . $item['user_id_1'] . '">' . $item['username_1'] . '</a></td>
                            <td class="center">' . number_format($item['reply_count']) . '</td>
                            <td class="center">' . number_format($item['views']) . '</td>
                            <td class="center">' . $lastActivity . '</td>
                        </tr>                                
                ';
                // increment the counter
                $cnt++;
            }
            // finish off the division
            $str .= '
                    </tbody>
                </table>
            </div>
            ';
        }
        
        $return['leftCol'] = $str;
        $return['rightCol'] = '
            <h4><span>Holder</span></h4>
        ';
        //echo '<pre>'; print_r($items); exit;
        // configuration for dynamic seo
        $config = array(
            'forum_sub_topic' => $items[0]['sub_topic_name']
            , 'description' => $items[0]['sub_topic_desc']
        );
        $return['seo'] = getDynamicSEO($config);
        
        // return data back to call
        return $return;
    }
    
    public function getThread() {
        // get the sub topic id value of the thread
        $sub_topic_id = $this->ci->uri->segment(3);
        // get the id value of the thread
        $thread_id = $this->ci->uri->segment(4);
        // now check for an instance of a # in the id value
        if(stristr($thread_id, '#')) {
            $thread_id = substr($thread_id, 0, (strpos($thread, '#') - 1));
        }
        
        // this is a view, so add it to the count
        $this->ci->ForumModel->addViewToThread($thread_id, $sub_topic_id);
        
        // get the thread data
        $data = $this->ci->ForumModel->getThreadByID($thread_id, $sub_topic_id);
        //echo '<pre>'; print_r($data); echo '</pre>'; exit;
        
        // holder for the output string
        $str = '';
        if(empty($data) || $data[0]['malicious'] == 1) {
            // this shouldn't happen
            header('Location: ' . base_url() . 'forum');
            exit;
        } else {
            // number of posts in this thread
            $num_posts = count($data) > 1 ? number_format(count($data)) . ' posts' : number_format(count($data)) . ' post';
            // number of views
            $thread_views = $data[0]['views'] > 1 ? number_format($data[0]['views']) . ' views' : number_format($data[0]['views']) . ' view';
            
            // check if the thread is closed
            $closed = false;
            $closed_text = '';
            if($data[0]['closed'] == 1) {
                $closed = true;
                $closed_text = '<p class="green marginTop_8 bold">No more replies accepted as this draft is closed.</p>';
            }
           
            $str .= '
                <h3 class="brown">' . $data[0]['subject'] . '</h3>
                <p>' . $num_posts . ' and ' . $thread_views . '</p>
                <p class="bold"><a href="' . base_url() . 'forum#' . $data[0]['topic_id'] . '">' . $data[0]['topic_name'] . '</a> -&gt; <a href="' . base_url() . 'forum/dst/' . $sub_topic_id . '">' . $data[0]['sub_topic_name'] . '</a> -&gt; ' . substr($data[0]['subject'], 0, 40) . '...</span></p>
                ' . $closed_text . '
                <div id="thread_container" class="marginTop_8">
            ';
            // there is some meat here, so start processing
            foreach($data as $thread) {
                // check that the current thread/reply is not malicious
                if($thread['malicious'] != 1) {
                    // check for user image
		            $user_image = ($thread['avatar'] == 1 && !empty($thread['avatar_image'])) ? 'images/avatars/' . $thread['avatar_image'] : 'images/fakepic.png';
                    // format the message for screen display
                    $message = $thread['message'];
                    $message = str_replace($this->thread_search, $this->thread_replace, $message);
                    //echo '<pre>'; print_r($message); echo '</pre>';
                    // screen output
                    $str .= '
                        <a name="' . $thread['thread_id'] . '"></a>
                        <div class="singleReviewContainer">
                            <div class="topCurve">&nbsp;</div>
                            <div class="reviewBorder">
                                <div class="singleBeerReview">
                                    <div class="reviewer">
                                        <div class="user_image"><img src="' . base_url() . $user_image . '" style="width: 50px; height: 50px;" /></div>
                                        <div class="user_info">
                                            <ul>
                                                <li><span class="weight700"><a href="' . base_url() . 'user/profile/' . $thread['user_id'] . '">' . $thread['username'] . '</a></span> from ' . $thread['city'] . ', ' . $thread['state'] . '</li>									
                                                <li>' . $thread['dtStamp'] . ' about ' . determineTimeSinceLastActive($thread['seconds_last_post']) . '</li>
                                                <li>
                                                    <ul class="sub_nav">
                    ';
                    if($closed === false) {
                        $str .= '
                                                        <li><a href="' . base_url() . 'forum/atr/rp/' . $sub_topic_id . '/' . $thread_id . '">reply</a></li>
                                                        <li><a href="' . base_url() . 'forum/atr/rp/' . $sub_topic_id . '/' . $thread_id . '/q/' . $thread['thread_id'] . '">reply with quote</a></li>
                        ';
                    }
                    $str .= '
                                                        <li><a href="' . base_url() . 'forum/st/' . $sub_topic_id . '/' . $thread_id . '#' . $thread['thread_id'] . '">Permalink</a>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        <br class="left" />
                                    </div>
                                </div>

                                <div class="content_beerReview">
                                    <div class="beerReview_comments">
                                        <div class="thread_message">' . nl2br(make_clickable($message)) . '</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bottomCurve">&nbsp;</div>
                        </div>
                    ';
                }
            }
            $str .= 
                    $closed_text . '
                </div>
            ';
        }
        
        $return['leftCol'] = $str;
        // configuration for dynamic seo
        $config = array(
            'forum_thread' => $data[0]['subject']
            , 'sub_topic_name' => $data[0]['sub_topic_name']
        );
        $return['seo'] = getDynamicSEO($config);
        
        return $return;
    }
}
?>