<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Userslib {
    private $ci;
    private $title = 'Users';

    public function __construct() {
            $this->ci =& get_instance();
    }
	
    public function showProfile($id, $ajax = false) {	
        // get the user info - this will be empty if the user
        // isn't logged in.  Also need to make sure, for certain
        // functionality that the user and visitor are the same
        $userInfo = $this->ci->session->userdata('userInfo');

        // get the generic information for this user
        $userProfile = $this->ci->UserModel->getUserProfile($id);

        // holder for output
        $str = '';
        // check if this is an ajax call
        if($ajax === false) {			
            //echo '<pre>'; print_r($userProfile); echo '</pre>'; 
            $lastActivity = determineTimeSinceLastActive($userProfile['secondsLastLogin']);

            // get the avatar
            $avatar = 'nobody.gif';
            if($userProfile['avatar'] && file_exists('./images/avatars/' . $userProfile['avatarImage'])) {
                $avatar = $userProfile['avatarImage'];
            }
            // check if the user is the same as the page person
            if($id == $userInfo['id']) {
                $avatar = '
                <div id="nubbin_' . $id . '" class="nubbin">
                    <ul>
                        <li class="edit"><a href="' . base_url() . 'page/uploadImage/avatars/' . $id . '"><img src="' . base_url() . 'images/nubbin_editPhoto.jpg" title="edit image" alt="edit image" /></a></li>
                    </ul>
                </div>
                <div class="userAvatar">
                    <img src="' . base_url() . 'images/avatars/' . $avatar . '" title="' . $userProfile['username'] . ' avatar picture" alt="' . $userProfile['username'] . ' avatar picture" />
                </div>
                ';
            } else {
                $avatar = '
                <div class="userAvatar">
                    <img src="' . base_url() . 'images/avatars/' . $avatar . '" title="' . $userProfile['username'] . ' avatar picture" alt="' . $userProfile['username'] . ' avatar picture" />
                </div>
                ';
            }

            $str = '
                <h2 class="brown">User Profile: ' . $userProfile['username'] . '</h2>
                <div class="marginTop_8" style="position: relative;">
                ' . $avatar . '
                <div class="userInfo">
                    <ul>
                        <li>Name: <span class="bold">' . $userProfile['firstname'] . ' ' . $userProfile['lastname'] . '</span></li>
                        <li>Location: <span class="bold">' . $userProfile['city'] . ', ' . $userProfile['state'] . '</span></li>
                        <li>Joined: <span class="bold">' . $userProfile['joinDate'] . '</span></li>
                        <li>Last Login: <span class="bold">'  . $lastActivity . '</span></li>
                    </ul>
                </div>
                <br class="left" />
            ';

            // check if the notes are set
            if(!empty($userProfile['notes'])) {
                $str .= '
                <div id="userNotes">
                    <div class="topCurve">&nbsp;</div>
                    <div id="userNotesContainer">
                        <h3 class="brown">Notes</h3>
                        <p>' . nl2br($userProfile['notes']) . '</p>
                    </div>
                    <div class="bottomCurve_gray">&nbsp;</div>
                </div>
                ';
            }

            $beer_ratings = $this->ci->RatingModel->getBeerRatingByUserIDStatistics($id);
            $establishment_ratings = $this->ci->RatingModel->getEstablishmentRatingByUserIDStatistics($id);
            if(!empty($beer_ratings) || !empty($establishment_ratings)) {
                $str .= '
                <div class="marginTop_8" id="statistics">
                <h3 class="brown">Malted Measurements</h3>
                ';
                if(!empty($beer_ratings)) {
                    $str .= '                        
                <table id="leftTable">
                    <tr>
                        <th colspan="2">American Craft Beers</th>
                    </tr>
                    <tr>
                        <td>Beers:</td>
                        <td><a href="' . base_url() . 'user/beer/' . $userProfile['username'] . '">' . $beer_ratings[0]['rated_beers'] . '</a></td>
                    </tr>
                    <tr class="gray">
                        <td>Styles:</td>
                        <td>' . $beer_ratings[0]['rated_styles'] . '</td>
                    </tr>
                    <tr>
                        <td>Average Rating:</td>
                        <td>' . number_format(round($beer_ratings[0]['rated_beer_average'], 1), 1) . '</td>
                    </tr>
                    <tr class="gray">
                        <td>Maximum Rating:</td>
                        <td>' . number_format(round($beer_ratings[0]['rated_beer_max'], 1), 1) . '</td>
                    </tr>
                    <tr>
                        <td>Minimum Rating:</td>
                        <td>' . number_format(round($beer_ratings[0]['rated_beer_min'], 1), 1) . '</td>
                    </tr>                    
                </table> 
                    ';
                }
                if(!empty($establishment_ratings)) {
                    $rated_establishments = 0;
                    $rated_establishment_max = 0;
                    $rated_establishment_min = 0;
                    $total = 0;
                    $str_category = '';
                    $cnt = 0;
                    foreach($establishment_ratings AS $array_cut) {//echo '<pre>'; print_r($array_cut); exit;
                        $rated_establishments += $array_cut['rated_establishments'];
                        $total += ($array_cut['rated_establishments'] * $array_cut['rated_establishment_average']);
                        if($array_cut['rated_establishment_max'] > $rated_establishment_max) {
                            $rated_establishment_max = $array_cut['rated_establishment_max'];
                        }
                        if($array_cut['rated_establishment_min'] > $rated_establishment_min) {
                            $rated_establishment_min = $array_cut['rated_establishment_min'];
                        }
                        $css_row = ($cnt % 2 == 1) ? ' class="gray"' : '';
                        $str_category .= '
                    <tr' . $css_row . '>
                        <td class="add_margin_left">' . ucwords($array_cut['name']) . ' (' . $array_cut['rated_establishments'] . ')</td>
                        <td>' . number_format(round($array_cut['rated_establishment_average'], 1), 1) . '</td>
                    </tr>
                        ';
                        $cnt++;
                    }
                    
                    $css_id = empty($beer_ratings) ? ' id="leftTable"' : '';
                    $str .= '
                <table' . $css_id . '>
                    <tr>
                        <th colspan="2">American Establishments</th>
                    </tr>
                    <tr>
                        <td>Total Establishments</td>
                        <td>' . $rated_establishments . '</td>
                    </tr>
                    <tr class="gray">
                        <td>Average Rating</td>
                        <td>' . number_format(round(($total / $rated_establishments), 1), 1) . '</td>
                    </tr>
                    ' . $str_category . '
                </table>
                    ';
                }
                
                $str .= '
                </div>
                <br class="left" />
                ';
            }

            // start the swap text
            $str .= '
                <div id="swaps" class="marginTop_8">
                    <h3 class="brown">Beer Swapping</h3>
            ';
            // the swap in/out information can only be seen by the user
            // whose account this is
            // this is good for swap ins and outs, but swap feedback should
            // be able to be seen by all
            if($id == $userInfo['id']) {	
                // the user must also have set their city and sate or they are not allowed
                // to swap
                if(!empty($userInfo['city']) && !empty($userInfo['state'])) {	
                    // get the swap information
                    // swapins
                    $swapins = $this->ci->SwapModel->getSwapInsByUserID($id);		
                    // swapouts
                    $swapouts = $this->ci->SwapModel->getSwapOutsByUserID($id);			
                    // get the number of swapins
                    $siNum = empty($swapins[0]['beerID']) ? 0 : count($swapins);
                    // get the number of swapouts
                    $soNum = empty($swapouts[0]['beerID']) ? 0 : count($swapouts);

                    // start the swap text
                    $str .= '						
                    <p><a href="' . base_url() . 'user/swaplist/ins">' . $siNum . ' swap ins</a> | <a href="' . base_url() . 'user/swaplist/outs">' . $soNum  . ' swap outs</a></p>
                    ';
                } else {
                    $str .= '
                    <p>You are not allowed to swap since your city and state are not set.  Set them in the <a href="' . base_url() . 'user/editProfile">edit user profile</a> page.</p>
                    ';
                }
            } else {
                // show the beers that they have up for trade
                //$str .= '<p>A user may only see his/her own swap ins and swap outs.</p>';				

                // get a record set of swap ins
                $swapins = $this->ci->SwapModel->getSwapInsByUserID($id);		
                // get the number of swapins
                $siNum = empty($swapins[0]['beerID']) ? 0 : count($swapins);

                // swapouts
                $swapouts = $this->ci->SwapModel->getSwapOutsByUserID($id);		
                // get the number of swapouts
                $soNum = empty($swapouts[0]['beerID']) ? 0 : count($swapouts);

                $str .= '
                    <div id="swapsInfo">
                        <p>
                ';
                if($siNum > 0) {
                    $str .= '<a href="' . base_url() . 'user/swaplist/ins/' . $id . '">' . $siNum . ' swap ins </a>';
                } else {
                    $str .= $siNum . ' swap ins';
                }
                $str .= ' | ';
                if($soNum > 0) {
                    $str .= '<a href="' . base_url() . 'user/swaplist/outs/' . $id . '">' . $soNum . ' swap outs </a>';
                } else {
                    $str .= $soNum . ' swap outs';
                }
                $str .= '
                        </p>
                    </div>
                ';

                /*if($siNum > 0 || $soNum > 0) {
                    // there are swap ins to show
                    $str .= '
                        <table id="swapsTable">
                            <tr>
                                <th>&nbsp;</th>
                                <th>Beer</th>
                                <th>Type</th>
                            </tr>
                    ';
                    // keep the count of the swaps
                    $cnt = 1;
                    // check for any swapins
                    if($siNum > 0) {
                        // iterate through the results
                        foreach($swapins as $item) {
                            // create the string info for each beer
                            $str .= '
                            <tr>
                                <td>' . $cnt . '.</td>
                                <td><a href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a><br /><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></td>
                                <td>IN</td>
                            </tr>
                            ';
                            // iterate the counter
                            $cnt++;
                        }
                    }

                    // check for any swapouts
                    if($soNum > 0) {
                        // iterate through the results
                        foreach($swapouts as $item) {
                            // create the string info for each beer
                            $str .= '
                            <tr>
                                <td>' . $cnt . '.</td>
                                <td><a href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a><br /><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></td>
                                <td>OUT</td>
                            </tr>
                            ';
                            // iterate the counter
                            $cnt++;
                        }
                    }
                    // finish off the list
                    $str .= '
                        </table>
                    ';
                }
                $str .= '
                    </div>
                ';*/				
            }
            // header for this area
            $str .= '
                    <h3 class="marginTop_8 brown">Beer Swapping Feedback</h3>
                    <a name="swaps"></a>
            ';
            // check that the user isn't looking at their own profile
            if($id != $userInfo['id']) {
                $str .= '
                    <p id="addFeedbackLink">Traded with <span class="bold">' . $userProfile['username'] . '</span>, add <a href="#" onclick="$(\'formContainerFeedback\').show(); $(\'addFeedbackLink\').hide(); return false;">beer swapping feedback</a>.</p>
                ';
            }		
        }			

        // form for providing feedback
        if($ajax === false) {
            $str .= '
                    <div id="formContainerFeedback" class="formBlock" style="display: none;">
                        <form class="edit" method="post" action="' . base_url() . 'ajax/swapFeedbackAdd" onsubmit="new Ajax.Request(\'' . base_url() . 'ajax/swapFeedbackAdd\', {asynchronous: true, evalScripts: true, method: \'post\', parameters: Form.serialize(this), onLoading: function() {$(\'magicSpinner\').show(); $(\'feedbackSubmitButton\').hide();}, onComplete: function(response) {updateFeedbackAdd(response.responseText);}}); return false;">
                            <label for="ttr_swapFeedback"><span class="required">*</span> Swap Feedback:</label>
                            <textarea id="ttr_swapFeedback" name="ttr_swapFeedback"></textarea>	
                            <div class="explanation">Brief write up of experience you had swapping with <span class="bold">' . $userProfile['username'] . '</span>.</div>

                            <input type="hidden" id="hdn_writerUserID" name="hdn_writerUserID" value="' . $userInfo['id'] . '" />
                            <input type="hidden" id="hdn_feedbackUserID" name="hdn_feedbackUserID" value="' . $id . '" />

                            <div id="feedbackSubmitButton" class="marginTop_8">
                                <input type="submit" id="btn_submit" name="btn_submit" value="Add Feedback"> or 
                                <a href="#" onclick="$(\'addFeedbackLink\').show(); $(\'ttr_swapFeedback\').clear(); $(\'formContainerFeedback\').hide(); return false;">I&#39;m done</a>
                            </div>
                            <div id="magicSpinner" style="display: none;">
                                <img src="' . base_url() . 'images/spinner.gif" style="margin: 1.0em auto; display: block; width: 16px; height: 16px;" />
                            </div>
                        </form>
                    </div>

                    <div id="feedbackContainer">
            ';
        }	

        // swap feedback count
        $swapFeedbackCount = $this->ci->SwapModel->getSwapFeedbackCountByFeedbackUserID($id);
        // header for this area
        $str .= '
                        <p>Current Swap Feedback: <span class="green bold">' . $swapFeedbackCount['feedbackCount'] . '</span></p>
                        <p style="margin-top: 8px;">&nbsp;</p>
        ';
        // check if there was any swap feedback		
        if($swapFeedbackCount['feedbackCount']) {
            $offset = $this->ci->uri->segment(5);
            if(empty($offset) || !ctype_digit($offset)) {
                $offset = 0;
            }
            // get all the ratings
            //$records = $this->ci->RatingModel->getAllPagination($offset);

            // get rolling with pagnation
            $this->ci->load->library('pagination');
            // configuration array for pagination
            $config['base_url'] = base_url() . 'user/profile/' . $id . '/pgn';
            $config['total_rows'] = $swapFeedbackCount['feedbackCount'];
            $config['per_page'] = PER_PAGE_SWAP_FEEDBACK;
            $config['name_anchor'] = 'feedbackContainer';
            $config['uri_segment'] = 5;
            $config['num_links'] = 2;
            $config['full_tag_open'] = '<p>';
            $config['full_tag_close'] = '</p>';
            $this->ci->pagination->initialize($config);
            $num_pages = $swapFeedbackCount['feedbackCount'] / PER_PAGE_SWAP_FEEDBACK;
            // initiliaze the pagnation display variables
            $pagination = '';
            $pagination_bottom = '';
            // check that there is more than one page of results
            if($num_pages > 1) {
                $pagination = '
                    <div class="pagnation" style="margin-bottom: 1.0em;">
                        ' . $this->ci->pagination->create_links() . '
                        <br class="both" />
                    </div>
                ';
                $pagination_bottom = '
                    <div class="pagnation">
                        ' . $this->ci->pagination->create_links() . '
                        <br class="both" />
                    </div>
                ';
            }

            // add the beginning pagination to the output
            $str .= $pagination;

            // get the actual swap feedback
            $swapFeedback = $this->ci->SwapModel->getSwapFeedbackByFeedbackUserID($id, $offset);
            //echo '<pre>'; print_r($swapFeedback); exit;
            // iterate through the feedback
            foreach($swapFeedback as $item) {
                // get avatar information
                $avatar = 'nobody.gif';
                if($item['avatar'] && file_exists('./images/avatars/' . $item['avatarImage'])) {
                    $avatar = $item['avatarImage'];
                } 
                $avatar = '<img src="' . base_url() . 'images/avatars/' . $avatar . '" title="' . $item['username'] . ' avatar picture" alt="' . $item['username'] . ' avatar picture" />';
                // determine if they have set a location
                $location =	(!empty($item['city']) && !empty($item['state'])) ? $item['city'] . ', ' . $item['state'] : 'N/A' ;
                // check that the user who wrote the feedback is active
                $user = $item['active'] != '1' ? $item['username'] : '<a href="' . base_url() . 'user/profile/' . $item['writerUserID'] . '">' . $item['username'] . '</a>';

                $str .= '							
                    <!--<div>	
                        <div>' . $avatar . '</div>
                        <p>' . $user . '</p>
                        <p>Joined: ' . $item['joindate'] . '</p>
                        <p>Location: ' . $location . '</p>
                    </div>
                    <div>
                        <div>' . $item['feedbackDate'] . '</div>
                        <div>' . nl2br($item['feedback']) . '</div>
                    </div>	-->

                    <div class="singleReviewContainer">
                        <div class="topCurve">&nbsp;</div>
                        <div class="reviewBorder">
                            <div class="singleBeerReview">
                                <div class="reviewer">
                                    <p class="feedback_report_link"><a href="' . base_url() . 'report/feedback/' . $item['id'] . '">Report as Malicious</a></p>
                                    <div class="user_image" style="margin-left: 0.8em;">' . $avatar . '</div>
                                    <div class="user_info feedback_pullback">
                                        <ul>
                                            <li><span class="weight700"><a href="' . base_url() . 'user/profile/' . $item['writerUserID'] . '">' . $user . '</a></span> from ' . $item['city'] . ', ' . $item['state'] . '</li>									
                                            <li>Joined: ' . $item['joindate'] . '</li>
                                        </ul>
                                    </div>

                                    <br class="both" />
                                </div>
                            </div>

                            <div class="content_beerReview">
                                <p style="font-size: 70%;">Date Reviewed: ' . $item['feedbackDate'] . '</p>
                                <p>' . nl2br($item['feedback']) . '</p>
                            </div>
                        </div>
                        <div class="bottomCurve">&nbsp;</div>
                    </div>				
                ';
            }
            // finish off the pagination
            $str .= $pagination_bottom;
        }

        // check for ajax call
        if($ajax === false) {
            // continue with the text
            $str .= '
                    </div>
                </div>
            </div>
            ';

            // get configuration values for creating the seo
            $config = array(
                'username' => $userProfile['username']
                , 'seoType' => 'userProfile'
            );
            // set the page information
            $seo = getDynamicSEO($config);
            $array = $seo + array('str' => $str);
            return $array;
        } else {
            echo $str;
        }
    }
	
	public function showSwaplistIns($id, $ajax = false, $remove = false, $logged_in_user = false) {
            // get a record set of swap ins
            $swapins = $this->ci->SwapModel->getSwapInsByUserID($id);		

            // get the number of swapins
            $siNum = count($swapins);		

            // holder for no ajax
            $str_noAjax = '';
            // check if this is an ajax call
            if($ajax === false) {
                    $str_noAjax = '
            <div id="contents_left">
                <h2 class="brown">Swap Ins List</h2>
                <div id="swapsInfo">
                    ';
            }
            //echo '<pre>'; print_r($swapins); echo '</pre>';exit;
            // begin the output to the screen
            $str = '';
            // check if there is at least one swap in
            if($siNum < 1) {
                // there are no swap ins to show
                $str .= '<p>There are no beers in your Swap Ins list.</p>';
                // there are no swap outs to show
                if($logged_in_user === true) {
                    $str .= '<p class="marginTop_8">There are no beers on your Swap Outs list.</p>';
                } else {
                    if(empty($swapouts)) {
                        header('Location: ' . base_url() . 'user/login/');
                        exit;
                    } else {
                        $str .= '<p class="marginTop_8"><a href="' . base_url() . 'user/profile/' . $id . '">' . $swapins[0]['username'] . '</a> doesn\'t have beers on his/her Swap Outs list.</p>';
                    }                
                }
            } else {
                if(empty($swapins[0]['beerID'])) {
                    $str .= '<p class="marginTop_8"><a href="' . base_url() . 'user/profile/' . $id . '">' . $swapins[0]['username'] . '</a> doesn\'t have beers on his/her Swap Outs list.</p>';
                } else {
                // there are swap ins to show
                $str .= '
                    <table id="swapsTable">
                        <tr class="bg2">
                            <th>&nbsp;</th>
                            <th>Beer</th>
                            <th># Ins</th>
                            <th># Outs</th>
                            <th>Date</th>
                ';
                if($logged_in_user === true) {
                    $str .= '
                            <th>&nbsp;</td>
                    ';
                }
                $str .= '
                        </tr>
                ';
                // keep the count of the swaps
                $cnt = 1;
                // iterate through the results
                foreach($swapins as $item) {
                    // get the count of the number of people who have this beer for swap out
                    $swapOutCount = $this->ci->SwapModel->numberSwapOutsByBeerID($item['beerID']);
                    // get the count of the number of people who have this beer for swap out
                    $swapInCount = $this->ci->SwapModel->numberSwapInsByBeerID($item['beerID']);
                    // determine the background color
                    $color = ($cnt % 2) == 1 ? '' : ' class="bg2"';
                    // create the string info for each beer
                    $str .= '
                        <tr' . $color . '>
                            <td>' . $cnt . '.</td>
                            <td><a href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a><br /><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></td>
                            <td><a href="' . base_url() . 'beer/swaps/ins/' . $item['beerID'] . '">' . $swapInCount . '</a></td>
                            <td><a href="' . base_url() . 'beer/swaps/outs/' . $item['beerID'] . '">' . $swapOutCount . '</a></td>
                            <td>' . $item['insDate'] . '</td>
                    ';
                    if($logged_in_user === true) {
                        $str .= '
                            <td><a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/swapremove/ins/' . $item['beerID'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'swapsInfo\');}, onComplete: function(response) {$(\'swapsInfo\').update(response.responseText);}}); return false;">remove</a></td>
                        ';
                    }
                    $str .= '
                        </tr>
                    ';
                    // iterate the counter
                    $cnt++;
                }
                    
                // finish off the list
                $str .= '
                        </table>
                ';
                }
            }
            $str .= $ajax === false ? '</div>' : '';
            // finish off the output
            $str = empty($str_noAjax) ? $str : $str_noAjax . $str . '</div>';
            // send back the output
            if($ajax === false) {
                    return $str;
            } else {
                    //$str = $remove === true ? $str . '|' . $beerDropDown : $str;
                    echo $str;
            }
	}
	
    public function showSwaplistOuts($id, $ajax = false, $remove = false, $logged_in_user = false) {
        // swapouts
        $swapouts = $this->ci->SwapModel->getSwapOutsByUserID($id);			

        // get the number of swapouts
        $soNum = count($swapouts);

        // holder for no ajax
        $str_noAjax = '';
        // check if this is an ajax call
        if($ajax === false) {
                $str_noAjax = '
        <div id="contents_left">
            <h2 class="brown">Swap Outs List</h2>
            <div id="swapsInfo">
                ';
        }

        // begin the output to the screen
        $str = '';
        // check if there is at least one swap out
        if($soNum < 1) {
            // there are no swap outs to show
            if($logged_in_user === true) {
                $str .= '<p class="marginTop_8">There are no beers on your Swap Outs list.</p>';
            } else {
                if(empty($swapouts)) {
                    header('Location: ' . base_url() . 'user/login/');
                    exit;
                } else {
                    $str .= '<p class="marginTop_8"><a href="' . base_url() . 'user/profile/' . $id . '">' . $swapouts[0]['username'] . '</a> doesn\'t have beers on his/her Swap Outs list.</p>';
                }                
            }
        } else {
            if(empty($swapouts[0]['beerID'])) {
                $str .= '<p class="marginTop_8"><a href="' . base_url() . 'user/profile/' . $id . '">' . $swapouts[0]['username'] . '</a> doesn\'t have beers on his/her Swap Outs list.</p>';
            } else {
                // there are swap ins to show
                $str .= '
                    <table id="swapsTable">
                        <tr class="bg2">
                            <th>Beer</th>
                            <th># Outs</th>
                            <th># Ins</th>
                            <th>Date</th>
                ';
                if($logged_in_user === true) {
                    $str .= '
                            <th>&nbsp;</td>
                    ';
                }
                $str .= '            
                        </tr>
                ';

                // keep the count of the swaps
                $cnt = 1;
                // iterate through the results
                foreach($swapouts as $item) {
                    // get the count of the number of people who have this beer for swap out
                    $swapInCount = $this->ci->SwapModel->numberSwapInsByBeerID($item['beerID']);
                    // get the count of the number of people who have this beer for swap out
                    $swapOutCount = $this->ci->SwapModel->numberSwapOutsByBeerID($item['beerID']);
                    // determine the background color
                    $color = ($cnt % 2) == 1 ? '' : ' class="bg2"';
                    // create the string info for each beer
                    $str .= '
                        <tr' . $color . '>
                            <td><a href="' . base_url() . 'beer/review/' . $item['beerID'] . '">' . $item['beerName'] . '</a><br /><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a></td>
                            <td><a href="' . base_url() . 'beer/swaps/outs/' . $item['beerID'] . '">' . $swapOutCount . '</a></td>
                            <td><a href="' . base_url() . 'beer/swaps/ins/' . $item['beerID'] . '">' . $swapInCount . '</a></td>
                            <td>' . $item['outsDate'] . '</td>
                    ';
                    if($logged_in_user === true) {
                        $str .= '
                            <td><a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/swapremove/outs/' . $item['beerID'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'swapsInfo\'); showSpinner(\'beerDropDown\');}, onComplete: function(response) {multiUpdater(response.responseText);}}); return false;">remove</a></td>
                        ';
                    }
                    $str .= '
                        </tr>
                    ';
                    // iterate the counter
                    $cnt++;
                }
                // finish off the list
                $str .= '
                        </table>
                ';
            }
        }
        $str .= $ajax === false ? '</div>' : '';
        // finish off the output
        $str = empty($str_noAjax) ? $str : $str_noAjax . $str . '</div>';
        // send back the output
        if($ajax === false) {
                return $str;
        } else {
                //$str = $remove === true ? $str . '|' . $beerDropDown : $str;
                echo $str;
        }
    }
	
	public function showMessages($userInfo, $ajax = false, $actionButtons = true) {
		// get the pms for current user
		$pms = $this->ci->UserModel->getPMSByUserID($userInfo['id']);
		
		// start output
		$str = '';
		// holder for action button textdomain
		$ab = '';
		if($ajax === false) {
			$str = '<div id="maltedMail">';
			if($actionButtons === true) {
				// get the action buttons
				$button = $this->createMailActionButtons();
				if(!empty($button)) {
					// start the unordered list
					$ab = '<ul>';
					// get the buttons in the correct order
					foreach($button as $key => $value) {
						$ab .= '<li>' . $value . '</li>';
					}				
					// finish the unordered list
					$ab .= '</ul>';
	 			}
			}
		}		
		if($pms === false) {
			$str .= '
			<div id="maltedMailInfo" class="maltedMailInfo">0 messages</div>
			<p>No private messages in your inbox.</p>
			<!--<ul>' . $ab . '</ul>-->
			';
		} else {
			// get the number of pms
			$cnt = count($pms);
			// determine the right text
			$str_cnt = $cnt > 1 ? 'messages' : 'message';
			// create the output
			$str .= '
			<div id="maltedMailInfo" class="maltedMailInfo">' . $cnt . ' ' . $str_cnt . '</div>
			';
			// counter to help determine background color
			$i = 0;
			// iterate through the record set
			foreach($pms as $item) {
				$bold = $item['timeRead'] == null ? 'class="bold"' : '';
				$class = ($i % 2) == 0 ? ' bg2' : '';
				$str .= '
			<div id="malted_' . $item['id'] . '" class="maltedMessage' . $class . '">
				<div class="maltedImage"></div>
				<div class="maltedInfo">
					<div class="maltedLeft">
						<span ' . $bold . '><a href="' . base_url() . 'user/pms/showMessage/' . $item['id'] . '">' . $item['subject'] . '</a></span>
						<br />
						<a style="text-decoration: none;" href="' . base_url() . 'user/profile/' . $item['from_userID'] . '" class="smallerText">' . $item['username'] . '</a>
					</div>
					<div class="maltedRight">' . $item['timesent'] . '</div>
					<br class="both" />
				</div>
				<div class="maltedRemove"><a href="#" onclick="new Ajax.Request(\'' . base_url() . 'ajax/mailremove/' . $item['id'] . '\', {asynchronous: true, evalScripts: true, method: \'get\', onLoading: function() {showSpinner(\'maltedMail\');}, onComplete: function(response) {$(\'maltedMail\').update(response.responseText);}}); return false;">remove</a></div>
				' . $ab . '
			</div>
			
				';
				// increment the counter
				$i++;
			}
		}
		
		// finish the output
		if($ajax === false) {
			$str .= '</div>';
			// return the output
			return $str;
		} else {
			echo $str;
		}		
	}
	
	public function showMessageByID($messageID, $userInfo) {
		// get the information for this message
		$msg = $this->ci->UserModel->getPMByMessageID($messageID, $userInfo['id']);			
		
		// start output
		$str = '<div id="maltedMail">';
		if($msg === false) {
			// no message matching passed in information
			$str = '<p>No message found matching requested information.</p>';
			
			// get the action buttons
			$button = $this->createMailActionButtons();
			if(!empty($button)) {
				$str .= '<ul>';
				// get the buttons in the correct order
				foreach($button as $key => $value) {
					$str .= '<li>' . $value . '</li>';				
				}	
				$str .= '</ul>';			
			}
		} else {
			// holder for action button textdomain
			$ab = '';
			// get the action buttons
			$button = $this->createMailActionButtons($messageID);
			if(!empty($button)) {
				$ab .= '<ul>';
				// get the buttons in the correct order
				foreach($button as $key => $value) {
					$ab .= '<li>' . $value . '</li>';				
				}	
				$ab .= '</ul>';			
			}			
			
			// iterate through the results
			foreach($msg as $item) {
				// get the avatar
				$avatar = 'nobody.gif';
				if($item['avatar'] && file_exists('./images/avatars/' . $item['avatarImage'])) {
					$avatar = $item['avatarImage'];
				} 
				$avatar = '<img src="' . base_url() . 'images/avatars/' . $avatar . '" title="' . $item['username'] . ' avatar picture" alt="' . $item['username'] . ' avatar picture" />';
				
				$location =	(!empty($item['city']) && !empty($item['state'])) ? $item['city'] . ', ' . $item['state'] : 'N/A' ;
				
				$str .= '			
			<div class="maltedLeft">	
				<div>Author</div>			
				<div>' . $avatar . '</div>
				<p><a href="' . base_url() . 'user/profile/' . $item['from_userID'] . '">' . $item['username'] . '</a></p>
				<p>Joined: ' . $item['joindate'] . '</p>
				<p>Location: ' . $location . '</p>
			</div>
			<div class="maltedRight">
				<div><span class="bold">' . $item['subject'] . '</span> ' . $item['timesent'] . '</div>
				<div>' . nl2br($this->formatUserText($item['message'], 4)) . '</div>
				' . $ab . '
			</div>
				';
				
				// check if the message needs to be marked as read
				if($item['timeRead'] == null) {
					// mark the message as read
					$this->ci->UserModel->updateTimeRead($messageID, $userInfo['id']);
				}
			}
		}		
		// finish the output
		$str .= '</div>';
		// return the output
		return $str;
	}
	
	public function showBuddyList($userInfo) {
		//echo '<pre>'; print_r($userInfo); echo '</pre>'; 
		
		$form = form_createMessage();
		//echo '<pre>'; print_r($form); echo '</pre>';
		
		$str = $form;
		// return the output
		return $str;
	}
	
	private function createMailActionButtons($messageID = 0) {
		// holder for the different action buttons
		$buttons = array();		
		// create action for creating new message 
		$buttons[2] = '<a href="' . base_url() . 'user/pms/create">New Malt Mail</a>';
		// check if there was a message id passed
		if($messageID > 0) {
			// create action for replying to message
			$buttons[0] = '<a href="' . base_url() . 'user/pms/reply/' . $messageID . '">Reply to Malt Mail</a>';
			// create action for forwarding message 
			$buttons[1] = '<a href="' . base_url() . '/user/pms/forward/' . $messageID . '">Forward Malt Mail</a>';
		}
		// sort the array
		ksort($buttons);
		// return the action buttons
		return $buttons;
	}
	
	private function formatUserText($text, $limit = -1) {
		// trim up the text
		$text = trim($text);
		// an array of all the things that can be parsed out of the text
		$array = array(
			'\[quote\]' => '<div class="pms_quote"><p>quote:</p>'
			, '\[\/quote]' => '</div>'
		);
		// iterate through the array and format the text for the screen
		foreach($array as $search => $replace) {
			// make the changes to the text
			$text = preg_replace('/' . $search . '/', $replace, $text, $limit);
		}
		// return the formatted text
		return $text;
	}
    
    public function display_user_beers($user_name, $user_id, $logged_user_id) {
        // holder for output
        $str_left = '
            <h2 class="brown">American Craft Beers Rated by ' . $user_name . '</h3>
        ';
        // holder for right output
        $str_right = '';
        
        $offset = $this->ci->uri->segment(4);
        if(empty($offset) || !ctype_digit($offset)) {
            $offset = 0;
        }
        
        // check that the user exists
        if(!empty($user_id)) {
            // get the rating information for the user
            $records = $this->ci->RatingModel->getRatingsByUserIDForUserProfile($user_id, $offset); 
            //echo '<pre>'; print_r($info); exit;  
            // check if they reated any beers
            if(is_array($records)) {
                // total number of results
                $total_results = $records['total'];
                // get the array of results
                $info = $records['rs'];
                // get rolling with pagnation
                $this->ci->load->library('pagination');
                // configuration array for pagination
                $config['base_url'] = base_url() . 'user/beer/' . $user_name;
                $config['total_rows'] = $total_results;
                $config['per_page'] = USER_BEER_REVIEWS_PAGINATION;
                $config['uri_segment'] = 4;
                $config['num_links'] = 3;
                $config['full_tag_open'] = '<p>';
                $config['full_tag_close'] = '</p>';
                $this->ci->pagination->initialize($config);
                $num_pages = $total_results / USER_BEER_REVIEWS_PAGINATION;
                $pagination = '
                    <div class="pagnation" style="margin-bottom: 1.0em;">
                        <div class="green"><span class="bold">' . number_format($total_results) . '</span> American Craft Beers Reviewed</div>
                        <br class="both" />
                    </div>
                ';
                $pagination_bottom = '';
        
                if($num_pages > 1) {
                    $pagination = '
                        <div class="pagnation">
                            <div class="green"><span class="bold">' . number_format($total_results) . '</span> American Craft Beers Reviewed</div>
                            ' . $this->ci->pagination->create_links() . '
                            <br class="both" />
                        </div>
                    ';
                    $pagination_bottom = '
                        <div class="pagnation">
                            ' . $this->ci->pagination->create_links() . '
                            <br class="both" />
                        </div>
                    ';
                }
                
                $str_left .= $pagination . '
                <table id="rated_by" class="tablesorter">
                    <thead>
                        <tr class="gray2">
                            <th>H.A.</th>
                            <th>Beer/Brewery</th>
                            <th>Style</th>
                            <th class="center">Rating</th>
                            <th class="center">ABV</th>
                            <th class="center">IBU</th>
                            <th class="center">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($info as $array) {
                    // determine the have another image
                    $have_another = $array['haveAnother'] == "0" ? 'haveanother_no25.jpg' : 'haveanother_yes25.jpg' ;
                    $str_left .= '
                        <tr>
                            <td class="center"><img src="' . base_url() . 'images/' . $have_another . '" /></td>
                            <td>
                                <div class="user_beer"><a href="' . base_url() . 'beer/review/' . $array['beer_id'] . '" class="bold">' . $array['beerName'] . '</a></div>
                                <div class="user_beer"><a href="' . base_url() . 'brewery/info/' . $array['establishment_id'] . '">' . $array['name'] . '</a></div>
                            </td>
                            <td><a href="' . base_url() . 'beer/style/' . $array['style_id'] . '">' . $array['style'] . '</a></td>
                            <td class="center">' . number_format($array['rating'], 2) . '</td>
                            <td class="center">' . (!empty($array['alcoholContent']) ? $array['alcoholContent'] : '-') . '</td>
                            <td class="center">' . (!empty($array['ibu']) ? $array['ibu'] : '-') . '</td>
                            <td class="center" style="white-space: nowrap;">' . $array['dateTasted'] . '</td>
                        </tr>
                    ';
                }
                $str_left .= '
                    </tbody>
                </table>
                <script type="text/javascript">
                $j(document).ready(function() { 
                    // call the tablesorter plugin 
                    $j(\'#rated_by\').tablesorter({
                        sortList: [[1,0]]
                        , headers: { 
                            // assign the secound column (we start counting zero) 
                            0: { 
                                // disable it by setting the property sorter to false 
                                sorter: false 
                            }                            
                        } 
                    }); 
                });
                </script>
                ' . $pagination_bottom;
                if($logged_user_id != $user_id) {
                    $str_right = '
                    <h4><span>More of ' . $user_name . '...</span></h4>
                    <ul>
                        <li><a href="' . base_url() . 'user/profile/' . $user_id . '">Profile</a></li>
                        <li><a href="' . base_url() . 'user/swaplist/ins/' . $user_id . '">Swap Ins</a></li>
                        <li><a href="' . base_url() . 'user/swaplist/outs/' . $user_id . '">Swap Outs</a></li>
                        <li><a href="' . base_url() . 'user/pms/create/' . $user_id . '"><img alt="send two beer dudes malted mail to '. $user_name . '" src="' . base_url() . 'images/email_icon.jpg"> Send Malted Mail</a></li>
                    </ul>
                    ';
                }
            } else {
                $str_left .= '
                    <p>' . $user_name . ' has not completed any American craft beer reviews.</p>
                ';
            }
        } else {
            $str_left .= '
                <p>The user information could not be found for requested user.</p>
            ';
        }
        //echo '<pre>'; print_r($user_info); echo '</pre>';
        
        // get configuration values for creating the seo
        $config = array(
            'user_name' => $user_name
            , 'seoType' => 'user_beer'
        );
        // set the page information
        $seo = getDynamicSEO($config);
        //$array = $seo + array('str' => $str);
        $array_return = array(
            'seo' => $seo
            , 'left_column' => $str_left
            , 'right_column' => $str_right
        );
        // send back the text
        return $array_return;
    }
    
    public function display_user_styles($user_name, $user_id, $logged_user_id) {
        // holder for output
        $str_left = '
            <h2 class="brown">American Craft Beer Styles by ' . $user_name . '</h3>
        ';
        // holder for right output
        $str_right = '';
        
        $offset = $this->ci->uri->segment(4);
        if(empty($offset) || !ctype_digit($offset)) {
            $offset = 0;
        }
        
        // check that the user exists
        if(!empty($user_id)) {
            // get the rating information for the user
            $records = $this->ci->StyleModel->getStylesByUserIDForUserProfile($user_id, $offset); 
            //echo '<pre>'; print_r($info); exit;  
            // check if they reated any beers
            if(is_array($records)) {
                // total number of results
                $total_results = $records['total'];
                // get the array of results
                $info = $records['rs'];
                // get rolling with pagnation
                $this->ci->load->library('pagination');
                // configuration array for pagination
                $config['base_url'] = base_url() . 'user/beer/' . $user_name;
                $config['total_rows'] = $total_results;
                $config['per_page'] = USER_STYLE_REVIEWS_PAGINATION;
                $config['uri_segment'] = 4;
                $config['num_links'] = 3;
                $config['full_tag_open'] = '<p>';
                $config['full_tag_close'] = '</p>';
                $this->ci->pagination->initialize($config);
                $num_pages = $total_results / USER_STYLE_REVIEWS_PAGINATION;
                $pagination = '
                    <div class="pagnation" style="margin-bottom: 1.0em;">
                        <div class="green"><span class="bold">' . number_format($total_results) . '</span> American Craft Beers Reviewed</div>
                        <br class="both" />
                    </div>
                ';
                $pagination_bottom = '';
        
                if($num_pages > 1) {
                    $pagination = '
                        <div class="pagnation">
                            <div class="green"><span class="bold">' . number_format($total_results) . '</span> American Craft Beers Reviewed</div>
                            ' . $this->ci->pagination->create_links() . '
                            <br class="both" />
                        </div>
                    ';
                    $pagination_bottom = '
                        <div class="pagnation">
                            ' . $this->ci->pagination->create_links() . '
                            <br class="both" />
                        </div>
                    ';
                }
                
                $str_left .= $pagination . '
                <table id="rated_by" class="tablesorter">
                    <thead>
                        <tr class="gray2">
                            <th>H.A.</th>
                            <th>Beer/Brewery</th>
                            <th>Style</th>
                            <th class="center">Rating</th>
                            <th class="center">ABV</th>
                            <th class="center">IBU</th>
                            <th class="center">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
                foreach($info as $array) {
                    // determine the have another image
                    $have_another = $array['haveAnother'] == "0" ? 'haveanother_no25.jpg' : 'haveanother_yes25.jpg' ;
                    $str_left .= '
                        <tr>
                            <td class="center"><img src="' . base_url() . 'images/' . $have_another . '" /></td>
                            <td>
                                <div class="user_beer"><a href="' . base_url() . 'beer/review/' . $array['beer_id'] . '" class="bold">' . $array['beerName'] . '</a></div>
                                <div class="user_beer"><a href="' . base_url() . 'brewery/info/' . $array['establishment_id'] . '">' . $array['name'] . '</a></div>
                            </td>
                            <td><a href="' . base_url() . 'beer/style/' . $array['style_id'] . '">' . $array['style'] . '</a></td>
                            <td class="center">' . number_format($array['rating'], 2) . '</td>
                            <td class="center">' . (!empty($array['alcoholContent']) ? $array['alcoholContent'] : '-') . '</td>
                            <td class="center">' . (!empty($array['ibu']) ? $array['ibu'] : '-') . '</td>
                            <td class="center" style="white-space: nowrap;">' . $array['dateTasted'] . '</td>
                        </tr>
                    ';
                }
                $str_left .= '
                    </tbody>
                </table>
                <script type="text/javascript">
                $j(document).ready(function() { 
                    // call the tablesorter plugin 
                    $j(\'#rated_by\').tablesorter({
                        sortList: [[1,0]]
                        , headers: { 
                            // assign the secound column (we start counting zero) 
                            0: { 
                                // disable it by setting the property sorter to false 
                                sorter: false 
                            }                            
                        } 
                    }); 
                });
                </script>
                ' . $pagination_bottom;
                if($logged_user_id != $user_id) {
                    $str_right = '
                    <h4><span>More of ' . $user_name . '...</span></h4>
                    <ul>
                        <li><a href="' . base_url() . 'user/profile/' . $user_id . '">Profile</a></li>
                        <li><a href="' . base_url() . 'user/swaplist/ins/' . $user_id . '">Swap Ins</a></li>
                        <li><a href="' . base_url() . 'user/swaplist/outs/' . $user_id . '">Swap Outs</a></li>
                        <li><a href="' . base_url() . 'user/pms/create/' . $user_id . '"><img alt="send two beer dudes malted mail to '. $user_name . '" src="' . base_url() . 'images/email_icon.jpg"> Send Malted Mail</a></li>
                    </ul>
                    ';
                }
            } else {
                $str_left .= '
                    <p>' . $user_name . ' has not completed any American craft beer reviews.</p>
                ';
            }
        } else {
            $str_left .= '
                <p>The user information could not be found for requested user.</p>
            ';
        }
        //echo '<pre>'; print_r($user_info); echo '</pre>';
        
        // get configuration values for creating the seo
        $config = array(
            'user_name' => $user_name
            , 'seoType' => 'user_beer'
        );
        // set the page information
        $seo = getDynamicSEO($config);
        //$array = $seo + array('str' => $str);
        $array_return = array(
            'seo' => $seo
            , 'left_column' => $str_left
            , 'right_column' => $str_right
        );
        // send back the text
        return $array_return;
    }
}
?>
