
	<div id="wrapper">
		<div id="container_left">
			<div id="contents">
                <h2 class="brown">Search Results</h2>

                <p class="searchString marginTop_8">
                    Search Term: <span class="bold"><?php echo $original_search_string; ?></span> in 
                    <span class="bold"><?php echo $type; ?></span>
                </p>
				<p>Words actually searched: <span class="bold"><?php echo implode(' ', $final_search_string); ?></span></p>

<?php
    $str = '';
    
    if ($searchRS)
    {
    	// begin the list
    	$str .= '<ul class="marginTop_8">';
    	// counter for declaring background color
    	$cnt = 0;
    	// iterate through the results
    	foreach($searchRS as $item) {
    		// get the background color
    		$bgColor = ($cnt % 2 == 0) ? ' class="padAll bg2"' : ' class="padAll"';
    		switch($type) {
    			case 'user':
    				$str .= '
    					<li' . $bgColor . '>
    						<p class="bold"><a href="' . base_url() . 'user/profile/' . $item['id'] . '">' . $item['username'] . '</a></p>
    					</li>
    				';
    				break;
    			case 'establishment':
    				$str .= '
    					<li' . $bgColor . '>
    						<p class="bold"><a href="' . base_url() . 'establishment/info/rating/' . $item['id'] . '">' . $item['name'] . '</a></p>
    						<p><a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
    					</li>
    				';
    				break;
    			case 'beer':
    			default:
    				// check if retired
    				$retired = $item['retired'] == '1' ? ' <span class="retired">(Retired, no longer in production.)</span>' : '';
    				$str .= '
    					<li' . $bgColor . '>
    						<p class="bold"><a href="' . base_url() . 'beer/review/' . $item['id'] . '">' . $item['beerName'] . '</a>' . $retired . '</p>
    						<p><a href="' . base_url() . 'brewery/info/' . $item['establishmentID'] . '">' . $item['name'] . '</a> - <a href="' . base_url() . 'establishment/city/' . $item['stateID'] . '/' . urlencode($item['city']) . '">' . $item['city'] . '</a>, <a href="' . base_url() . 'establishment/state/' . $item['stateID'] . '">' . $item['stateFull'] . '</a></p>
    					</li>
    				';
    				break;
    		}
    		// increment the counter
    		$cnt++;
    	}
    	// finish the list
    	$str .= '</ul>';
    } else {
    	$str .= '<p class="marginTop_8 bold">No results were found for fitting the search criteria.</p>';
    }
    
    echo $str;
?>


			</div>
		</div>
		<div id="container_right">
			<div class="sideInfo">
                <h4><span>Search Pointers</span></h4>
                <ul>
                    <li>Certain words are discarded and not searched as they are considered &#34;common.&#34;</li>
                    <li>Make sure you are spelling words correctly</li>
                </ul>
			</div>
		</div>
		<br class="both" />
	</div>
	