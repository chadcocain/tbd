<?php
function addSocialMedia($config) {
	// holder for the output
	$str = '';
	// check the type of twitter
	switch($config['type']) {
		case 'beerReview':
			// this is for the ability to add to twitter
		    if(SHOW_TWITTER_BEER_REVIEWS === true) {
		    	// check if there is a value for the twitter account
		    	$twitter = !empty($config['beer']['twitter']) ? '@' . $config['beer']['twitter'] : $config['beer']['name'];
		        $str = '
		            <h4><span>Spread the Word</span></h4>
		            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		            <p>
		                <a 
		                    href="http://twitter.com/share" 
		                    class="twitter-share-button" 
		                    data-url="' . base_url() . 'beer/review/' . $config['id'] . '"
		                    data-via="twobeerdudes"
		                    data-text="' . $config['beer']['beerName'] . ' by ' . $twitter . '"
		                    data-count="horizontal"
		                >Tweet</a>
		            </p>		            
		        ';
		    } 
		    // this is for the ability to add to facebook
		    if(SHOW_FACEBOOK_BEER_REVIEWS === true) {
		    	$str .= '
                    <div id="fb-root"></div>
		    		<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		            <fb:like href="' . base_url() . 'beer/review/' . $config['id'] . '" layout="button-count" show_faces="false" width="270" font="arial" style="margin-bottom: 0.4em; height: 24px;"></fb:like>
		        ';
		    }
			break;
        case 'establishmentHome':
        case 'establishmentReview':
            // this is for the ability to add to twitter
            if(SHOW_TWITTER_ESTABLISHMENT === true) {
                // check if there is a value for the twitter account
                $twitter = !empty($config['establishment']['twitter']) ? '@' . $config['establishment']['twitter'] : $config['establishment']['name'];
                $str = '
                    <h4><span>Spread the Word</span></h4>
                    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                    <p>
                        <a 
                            href="http://twitter.com/share" 
                            class="twitter-share-button" 
                            data-url="' . base_url() . 'brewery/info/' . $config['establishment']['id'] . '"
                            data-via="twobeerdudes"
                            data-text="Check out: ' . $twitter . '"
                            data-count="horizontal"
                        >Tweet</a>
                    </p>                    
                ';
            } 
            // this is for the ability to add to facebook
            if(SHOW_FACEBOOK_ESTABLISHMENT === true) {
                $str .= '
                    <div id="fb-root"></div>
                    <script src="http://connect.facebook.net/en_US/all.js#appId=202679286436515&amp;xfbml=1"></script>
                    <fb:like href="' . base_url() . 'brewery/info/' . $config['establishment']['id'] . '" layout="button_count" show_faces="false" width="270" font="arial" style="margin-bottom: 0.4em; height: 24px;"></fb:like>
                ';
            }
            break;
	}
	return $str;
}

function showTwitterForEstablishment($twitter) {
    // holder for output
    $str = '';
    // check if twitter value is empty or not
    if(!empty($twitter)) {
        $str = '
            <a href="http://twitter.com/' . $twitter . '" target="_blank"><img src="' . base_url() . 'images/tweetie_bird.gif" alt="' . $twitter . '" title="' . $twitter . '" width="24" height="24" /></a>
        ';
    }
    return $str;
}
?>