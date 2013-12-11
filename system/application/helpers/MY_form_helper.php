<?php
function form_beerReview($config) {
	//$rating = key_exists('rating', $config) ? $config['rating']['rating'] : set_value('slt_rating');
	$aroma = key_exists('rating', $config) ? $config['rating']['aroma'] : set_value('aroma');
	$taste = key_exists('rating', $config) ? $config['rating']['taste'] : set_value('taste');
	$look = key_exists('rating', $config) ? $config['rating']['look'] : set_value('look');
	$drinkability = key_exists('rating', $config) ? $config['rating']['drinkability'] : set_value('drinkability');
	$dateTasted = key_exists('rating', $config) ? $config['rating']['dateTasted'] : set_value('txt_dateTasted');
	$color = key_exists('rating', $config) ? $config['rating']['color'] : set_value('txt_color');
	$comments = key_exists('rating', $config) ? $config['rating']['comments'] : set_value('ttr_comments');
	$haveAnother = key_exists('rating', $config) ? $config['rating']['haveAnother'] : set_value('slt_haveAnother');
	$packageID = key_exists('rating', $config) ? $config['rating']['packageID'] : set_value('slt_package');
	$price = key_exists('rating', $config) ? $config['rating']['price'] : set_value('price');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	// load the package model
	$ci->load->model('PackageModel', '', true);
	// get the different packages
	$packages = $ci->PackageModel->getAllForDropDown();
	$array = array(
		'data' => $packages
		, 'id' => 'slt_package'
		, 'name' => 'slt_package'
		, 'selected' => $packageID
	);
	$packageDropDown = '<label for="slt_package"><span class="required">*</span> Package:</label>' . createDropDown($array);
	
	unset($array);
	$array = array(
		'data' => array(
			array('id' => '0', 'name' => 'No')
			, array('id' => '1', 'name' => 'Yes')
		)
		, 'id' => 'slt_haveAnother'
		, 'name' => 'slt_haveAnother'
		, 'selected' => $haveAnother
	);
	$haveAnotherDropDown = '<label for="slt_haveAnother"><span class="required">*</span> Have Another:</label>' . createDropDown($array);	
	
	/*unset($array);
	$array = array(
		'data' => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10')
		, 'id' => 'slt_rating'
		, 'name' => 'slt_rating'
		, 'selected' => $rating
	);
	$ratingDropDown = createDropDownNoKeys($array);*/
	
	$form = '
		<form id="beer_review_form" class="edit" method="post" action="' . base_url() . 'beer/createReview/' . $config['id'] . '">
			
			<div class="formBlock">
	';
	/*if(form_error('slt_rating')) {
		$form .= '<div class="formError">' . form_error('slt_rating') . '</div>';
	}
	$form .= '
				<label for="slt_rating"><span class="required">*</span> Rating:</label>
				' . $ratingDropDown . '
				<div class="explanation">Select a <a href="' . base_url() . 'beer/ratingSystem">rating system</a> value between 1 and 10.</div>
			</div>
			
			<div class="formBlock">
	';*/

	if(form_error('txt_aroma')) {
		$form .= '<div class="formError">' . form_error('txt_aroma') . '</div>';
	}
	$form .= '
				<label for="txt_aroma"><span class="required">*</span> Aroma: <span id="span_aroma"></span></label>
				<div id="slider_aroma" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_aroma" name="aroma" value="' . $aroma . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_AROMA . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">
	';
	if(form_error('txt_taste')) {
		$form .= '<div class="formError">' . form_error('txt_taste') . '</div>';
	}
	$form .= '
				<label for="txt_taste"><span class="required">*</span> Taste: <span id="span_taste"></span></label>
				<div id="slider_taste" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_taste" name="taste" value="' . $taste. '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_TASTE . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">
	';
	if(form_error('txt_look')) {
		$form .= '<div class="formError">' . form_error('txt_look') . '</div>';
	}
	$form .= '
				<label for="txt_look"><span class="required">*</span> Look: <span id="span_look"></span></label>
				<div id="slider_look" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_look" name="look" value="' . $look . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_LOOK . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">			
	';
	if(form_error('txt_drinkability')) {
		$form .= '<div class="formError">' . form_error('txt_drinkability') . '</div>';
	}
	$form .= '
				<label for="txt_drinkability"><span class="required">*</span> Drinkability: <span id="span_drinkability"></span></label>
				<div id="slider_drinkability" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_drinkability" name="drinkability" value="' . $drinkability . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_DRINKABILITY . '% of the overall score.</div>
			</div>
			
			<div class="formBlock">
				<div>
					<p class="bold" style="width: 100%;">Overall Rating: <span id="overallRating" class="required bold" style="text-align: right;"></span></p>
				</div>
			</div>
			
			<div class="formBlock">			
	';
	
	
	if(form_error('txt_dateTasted')) {
		$form .= '<div class="formError">' . form_error('txt_dateTasted') . '</div>';
	}
	$form .= '
			
				<label for="txt_dateTasted"><span class="required">*</span> Date Tasted:</label>
				<input type="text" id="txt_dateTasted" name="txt_dateTasted" value="' . $dateTasted . '" />
				<div class="explanation">Date is in yyyy-mm-dd format.  Please use calendar to auto select, it will format appropriately.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_color')) {
		$form .= '<div class="formError">' . form_error('txt_color') . '</div>';
	}
	$form .= '			
				<label for="txt_color"><span class="required">*</span> Color:</label>
				<input type="text" id="txt_color" name="txt_color" value="' . $color . '" />
				<div class="explanation">Try to use colors (or numbers) from the American <a href="' . base_url() . 'beer/srm">degrees SRM</a> scale</div>
			</div>
			
			<div class="formBlock">	
	';
	if(form_error('ttr_comments')) {
		$form .= '<div class="formError">' . form_error('ttr_comments') . '</div>';
	}
	$form .= '			
				<label for="ttr_comments"><span class="required">*</span> Comments:</label>
				<textarea id="ttr_comments" name="ttr_comments">' . $comments . '</textarea>	
				<div class="explanation">Your thoughts about the beer.</div>
			</div>
			
			<div class="formBlock">				
	';
	if(form_error('slt_haveAnother')) {
		$form .= '<div class="formError">' . form_error('slt_haveAnother') . '</div>';
	}
	$form .= '			
				' . $haveAnotherDropDown . '
				<div class="explanation">Quite simply: would you have another one if presented with the chance.</div>
			</div>
			
			<div class="formBlock">				
	';
	if(form_error('slt_package')) {
		$form .= '<div class="formError">' . form_error('slt_package') . '</div>';
	}
	$form .= '			
				' . $packageDropDown . '
				<div class="explanation">The packaging format of the brew you are reviewing.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_price')) {
		$form .= '<div class="formError">' . form_error('txt_price') . '</div>';
	}
	$form .= '			
				<label for="txt_price">Price:</label>
				<input type="text" id="txt_price" name="txt_price" value="' . $price . '" />
				<div class="explanation">The price of the beer in dd.cc format.  Don\'t include the dollar sign ($) and for values under a dollar, use a zero (ex: 0.85).</div>
			</div>
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Submit Beer Review" />
		</form>

                <script type="text/javascript">
		/*<![CDATA[*/
		Calendar.setup({
			dateField : \'txt_dateTasted\',
			triggerElement : \'txt_dateTasted\'
		})
		
		var slider_aroma = $(\'slider_aroma\');
		var aroma = $(\'txt_aroma\');
		var span_aroma = $(\'span_aroma\');
		var start_aroma = aroma.getValue() == \'\' ? 1 : aroma.getValue();
		var slider_taste = $(\'slider_taste\');
		var taste = $(\'txt_taste\');
		var span_taste = $(\'span_taste\');
		var start_taste = taste.getValue() == \'\' ? 1 : taste.getValue();
		var slider_look = $(\'slider_look\');
		var look = $(\'txt_look\');
		var span_look = $(\'span_look\');
		var start_look = look.getValue() == \'\' ? 1 : look.getValue();
		var slider_drinkability = $(\'slider_drinkability\');
		var drinkability = $(\'txt_drinkability\');
		var span_drinkability = $(\'span_drinkability\');
		var start_drinkability = drinkability.getValue() == \'\' ? 1 : drinkability.getValue();
		(function() {
			new Control.Slider(slider_aroma.down(\'.handle\'), slider_aroma, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_aroma
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					aroma.setValue(value);
					span_aroma.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					aroma.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_taste.down(\'.handle\'), slider_taste, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_taste
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					taste.setValue(value);
					span_taste.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					taste.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_look.down(\'.handle\'), slider_look, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_look
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					look.setValue(value);
					span_look.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					look.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_drinkability.down(\'.handle\'), slider_drinkability, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_drinkability
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					drinkability.setValue(value);
					span_drinkability.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					drinkability.setValue(value);
					overallAverage();
				}
			});
		})();
		
		Event.observe(window, \'load\', updateFields);
		function updateFields() {
			span_aroma.update(\'(\' + setValue(aroma) + \')\');
			//var tmp = taste.getValue() == \'\' ? 1 : taste.getValue();
			span_taste.update(\'(\' + setValue(taste) + \')\');
			span_look.update(\'(\' + setValue(look) + \')\');
			span_drinkability.update(\'(\' + setValue(drinkability) + \')\');
			
			overallAverage();
		}
		
		function overallAverage() {
			var mth = (aroma.getValue() * (' . PERCENT_AROMA . '/100)) + (taste.getValue() * (' . PERCENT_TASTE . '/100)) + (look.getValue() * (' . PERCENT_LOOK . '/100)) + (drinkability.getValue() * (' . PERCENT_DRINKABILITY . '/100));
			var avg = roundNumber(mth, 1).toFixed(1);
			$(\'overallRating\').update(avg);
		}
		
		function roundNumber(num, dec) {
			var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
			return result;
		}
		
		function setValue(el) {
			var tmp = 0;
			if(el.getValue() == \'\') {
				tmp = 1;
				el.value = tmp;
			} else {
				tmp = el.getValue()
			}
			return tmp;
		}
		/*]]>*/
		</script>
                
        <script type="text/javascript">
        $j(document).ready(function() {
            $j(\'#btn_submit\').click(function() {
                $j(this).attr(\'disabled\', \'disabled\').val(\'Processing...\');
                $j(\'#beer_review_form\').submit();
            });
        });
		</script>
	';
	return $form;
}

function form_beerShortReview($config) {
	$dateTasted = key_exists('rating', $config) ? $config['rating']['dateTasted'] : set_value('txt_dateTasted');
	$aroma = key_exists('rating', $config) ? $config['rating']['aroma'] : set_value('aroma');
	$taste = key_exists('rating', $config) ? $config['rating']['taste'] : set_value('taste');
	$look = key_exists('rating', $config) ? $config['rating']['look'] : set_value('look');
	$drinkability = key_exists('rating', $config) ? $config['rating']['drinkability'] : set_value('drinkability');
	$haveAnother = key_exists('rating', $config) ? $config['rating']['haveAnother'] : set_value('slt_haveAnother');
	
	// get the code igniter instance
	$ci =& get_instance();

	// create the have another dropdown
	$array = array(
		'data' => array(
			array('id' => '0', 'name' => 'No')
			, array('id' => '1', 'name' => 'Yes')
		)
		, 'id' => 'slt_haveAnother'
		, 'name' => 'slt_haveAnother'
		, 'selected' => $haveAnother
	);
	$haveAnotherDropDown = '<label for="slt_haveAnother"><span class="required">*</span> Have Another:</label>' . createDropDown($array);	
	
	$form = '
		<form id="beer_review_form" class="edit" method="post" action="' . base_url() . 'beer/createReview/' . $config['id'] . '/short">
			<div class="formBlock">
	';
	if(form_error('txt_dateTasted')) {
		$form .= '<div class="formError">' . form_error('txt_dateTasted') . '</div>';
	}
	$form .= '
			
				<label for="txt_dateTasted"><span class="required">*</span> Date Tasted:</label>
				<input type="text" id="txt_dateTasted" name="txt_dateTasted" value="' . $dateTasted . '" />
				<div class="explanation">Date is in yyyy-mm-dd format.  Please use calendar to auto select, it will format appropriately.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_aroma')) {
		$form .= '<div class="formError">' . form_error('txt_aroma') . '</div>';
	}
	$form .= '
				<label for="txt_aroma"><span class="required">*</span> Aroma: <span id="span_aroma"></span></label>
				<div id="slider_aroma" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_aroma" name="aroma" value="' . $aroma . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_AROMA . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">
	';
	if(form_error('txt_taste')) {
		$form .= '<div class="formError">' . form_error('txt_taste') . '</div>';
	}
	$form .= '
				<label for="txt_taste"><span class="required">*</span> Taste: <span id="span_taste"></span></label>
				<div id="slider_taste" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_taste" name="taste" value="' . $taste. '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_TASTE . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">
	';
	if(form_error('txt_look')) {
		$form .= '<div class="formError">' . form_error('txt_look') . '</div>';
	}
	$form .= '
				<label for="txt_look"><span class="required">*</span> Look: <span id="span_look"></span></label>
				<div id="slider_look" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_look" name="look" value="' . $look . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_LOOK . '% of the overall score.</div>
			</div>	
			
			<div class="formBlock">			
	';
	if(form_error('txt_drinkability')) {
		$form .= '<div class="formError">' . form_error('txt_drinkability') . '</div>';
	}
	$form .= '
				<label for="txt_drinkability"><span class="required">*</span> Drinkability: <span id="span_drinkability"></span></label>
				<div id="slider_drinkability" class="slider"><div class="handle"></div></div>
				<input type="hidden" id="txt_drinkability" name="drinkability" value="' . $drinkability . '" />
				<div class="explanation">Select a value between 1 and 10.  This will make up ' . PERCENT_DRINKABILITY . '% of the overall score.</div>
			</div>
			
			<div class="formBlock">
				<div>
					<p class="bold" style="width: 100%;">Overall Rating: <span id="overallRating" class="required bold" style="text-align: right;"></span></p>
				</div>
			</div>
			
			<div class="formBlock">			
	';			
	if(form_error('slt_haveAnother')) {
		$form .= '<div class="formError">' . form_error('slt_haveAnother') . '</div>';
	}
	$form .= '			
				' . $haveAnotherDropDown . '
				<div class="explanation">Quite simply: would you have another one if presented with the chance.</div>
			</div>
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Submit Short Beer Review" />
		</form>
		<script type="text/javascript">
		/*<![CDATA[*/
		Calendar.setup({
			dateField : \'txt_dateTasted\',
			triggerElement : \'txt_dateTasted\'
		})
		
		var slider_aroma = $(\'slider_aroma\');
		var aroma = $(\'txt_aroma\');
		var span_aroma = $(\'span_aroma\');
		var start_aroma = aroma.getValue() == \'\' ? 1 : aroma.getValue();
		var slider_taste = $(\'slider_taste\');
		var taste = $(\'txt_taste\');
		var span_taste = $(\'span_taste\');
		var start_taste = taste.getValue() == \'\' ? 1 : taste.getValue();
		var slider_look = $(\'slider_look\');
		var look = $(\'txt_look\');
		var span_look = $(\'span_look\');
		var start_look = look.getValue() == \'\' ? 1 : look.getValue();
		var slider_drinkability = $(\'slider_drinkability\');
		var drinkability = $(\'txt_drinkability\');
		var span_drinkability = $(\'span_drinkability\');
		var start_drinkability = drinkability.getValue() == \'\' ? 1 : drinkability.getValue();
		(function() {
			new Control.Slider(slider_aroma.down(\'.handle\'), slider_aroma, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_aroma
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					aroma.setValue(value);
					span_aroma.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					aroma.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_taste.down(\'.handle\'), slider_taste, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_taste
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					taste.setValue(value);
					span_taste.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					taste.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_look.down(\'.handle\'), slider_look, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_look
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					look.setValue(value);
					span_look.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					look.setValue(value);
					overallAverage();
				}
			});
			
			new Control.Slider(slider_drinkability.down(\'.handle\'), slider_drinkability, {
				axis: \'horizontal\'
				, range: $R(1, 10)
				, minimum: 0
				, alignX: 1
				, increment: 13
				, sliderValue: start_drinkability
				, values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
				, onSlide: function(value) {
					drinkability.setValue(value);
					span_drinkability.update(\'(\' + value + \')\');
					overallAverage();
				}
				, onChange: function(value) {
					drinkability.setValue(value);
					overallAverage();
				}
			});
		})();
		
		Event.observe(window, \'load\', updateFields);
		function updateFields() {
			span_aroma.update(\'(\' + setValue(aroma) + \')\');
			//var tmp = taste.getValue() == \'\' ? 1 : taste.getValue();
			span_taste.update(\'(\' + setValue(taste) + \')\');
			span_look.update(\'(\' + setValue(look) + \')\');
			span_drinkability.update(\'(\' + setValue(drinkability) + \')\');
			
			overallAverage();
		}
		
		function overallAverage() {
			var mth = (aroma.getValue() * (' . PERCENT_AROMA . '/100)) + (taste.getValue() * (' . PERCENT_TASTE . '/100)) + (look.getValue() * (' . PERCENT_LOOK . '/100)) + (drinkability.getValue() * (' . PERCENT_DRINKABILITY . '/100));
			var avg = roundNumber(mth, 1).toFixed(1);
			$(\'overallRating\').update(avg);
		}
		
		function roundNumber(num, dec) {
			var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
			return result;
		}
		
		function setValue(el) {
			var tmp = 0;
			if(el.getValue() == \'\') {
				tmp = 1;
				el.value = tmp;
			} else {
				tmp = el.getValue()
			}
			return tmp;
		}
		/*]]>*/
		</script>
                
        <script type="text/javascript">
		$j(document).ready(function() {
            $j(\'#btn_submit\').click(function() {
                $j(this).attr(\'disabled\', \'disabled\').val(\'Processing...\');
                $j(\'#beer_review_form\').submit();
            });
        });
        </script>
	';
	return $form;
}

function form_createMessage($config) {
	$to = (key_exists('nameToSendTo', $config) && empty($_POST)) ? $config['nameToSendTo'] : set_value('txt_to');
	$subject = (key_exists('subjectText', $config) && empty($_POST)) ? $config['subjectText'] : set_value('txt_subject');
	$message = (key_exists('messageText', $config) && empty($_POST)) ? $config['messageText'] : set_value('ttr_message');
	
	// get the code igniter instance
	$ci =& get_instance();
	// get where to send the form
	$formType = empty($config['formType']) ? 'create' : $config['formType'];
	
	$form = '
		<form class="edit" method="post" action="' . base_url() . 'user/pms/' . $formType . '">
			
			<div class="formBlock">
	';
	if(form_error('txt_to')) {
		$form .= '<div class="formError">' . form_error('txt_to') . '</div>';
	}
	$form .= '
				<label for="txt_to"><span class="required">*</span> To:</label>
				<input type="text" id="txt_to" name="txt_to" value="' . $to . '" />
				<div class="explanation">Member name of the person you want to send Malted Mail to.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_subject')) {
		$form .= '<div class="formError">' . form_error('txt_subject') . '</div>';
	}
	$form .= '
			
				<label for="txt_subject"><span class="required">*</span> Subject:</label>
				<input type="text" id="txt_subject" name="txt_subject" value="' . $subject . '" />
				<div class="explanation">What the email is about.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('ttr_message')) {
		$form .= '<div class="formError">' . form_error('ttr_message') . '</div>';
	}
	$form .= '			
				<label for="ttr_message" class="ttr_message"><span class="required">*</span> Message: <span class="charsRemaining"></span></label> 
				<textarea id="ttr_message" name="ttr_message" maxlength="' . PRIVATE_MESSAGE_MAX_LENGTH . '">' . $message . '</textarea>	
				<div class="explanation">The body of your message.  Maximum length: ' . PRIVATE_MESSAGE_MAX_LENGTH . ' total characters, spaces, etc.</div>
			</div>
            
            <script type="text/javascript">
            var $j = jQuery.noConflict();
            $j(document).ready(function() {  
                var onEditCallback = function(remaining) {
                    $j(this).siblings(\'.ttr_message\').children(\'.charsRemaining\').text(\'Characters remaining: \' + remaining);
                }
             
                $j(\'textarea[maxlength]\').limitMaxlength( {
                    onEdit: onEditCallback,
                });
            });
            </script>
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Send Malted Mail" />
		</form>
	';
	return $form;
}

function form_addBeer($config) {
	$beer = set_value('txt_beer');
	//$brewery = set_value('slt_brewery');
	$style = set_value('slt_style');
	$seasonal = set_value('slt_seasonal');
	$seasonalPeriod = set_value('txt_seasonalPeriod');
	$alcoholContent = set_value('txt_abv');
	$ibu = set_value('txt_ibu');
    $beerNotes = set_value('ttr_beerNotes');
	$malts = set_value('txt_malts');
	$hops = set_value('txt_hops');
	$yeast = set_value('txt_yeast');
	$food = set_value('txt_food');
	$glassware = set_value('txt_glassware');
	$gravity = set_value('txt_gravity');
	
	// get the code igniter instance
	//$ci =& get_instance();

	$form = '
		<form id="addBeerForm" class="edit" method="post" action="' . base_url() . 'beer/addBeer/' . $config['establishmentID'] . '">
			<div class="formBlock">
	';
	if(form_error('txt_beer')) {
		$form .= '<div class="formError">' . form_error('txt_beer') . '</div>';
	}
	$form .= '		
				<label for="txt_beer"><span class="required">*</span> Beer:</label>
				<input type="text" id="txt_beer" name="txt_beer" value="' . $beer . '" />
				<div class="explanation">The name of the beer.  Do not add a new beer for vintages (ex. 2010) unless the vintage has changed drastically from the previous year(s).  This is a beer site no mead, cider, etc.</div>
			</div>

			<div class="formBlock">
	';
	/*if(form_error('slt_brewery')) {
		$form .= '<div class="formError">' . form_error('slt_brewery') . '</div>';
	}

	$array = array(
		'data' => $config['breweries']
		, 'id' => 'slt_brewery'
		, 'name' => 'slt_brewery'
		, 'selected' => $brewery
	);
	$form .= '
				<label for="slt_brewery"><span class="required">*</span> Brewery:</label>
				' . createDropDown($array) . '
				<div class="explanation">If the brewery isn\'t listed, you will have to create it first.  Contract beers should be added under the brewery that owns the rights, not the brewing brewery.</div>
			</div>			
	
			<div class="formBlock">
	';*/
	if(form_error('slt_style')) {
		$form .= '<div class="formError">' . form_error('slt_style') . '</div>';
	}

	$array = array(
		'data' => $config['styles']
		, 'id' => 'slt_style'
		, 'name' => 'slt_style'
		, 'selected' => $style
	);
	$form .= '
				<label for="slt_style"><span class="required">*</span> Style:</label>
				' . createDropDownStyles($array) . '
				<div class="explanation"><a href="http://www.twobeerdudes.com/beer/style">Style</a> of the beer.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('slt_seasonal')) {
		$form .= '<div class="formError">' . form_error('slt_seasonal') . '</div>';
	}
	
	$array = array(
		'data' => array(array('id' => '0', 'name' => 'No'), array('id' => '1', 'name' => 'Yes'))
		, 'id' => 'slt_seasonal'
		, 'name' => 'slt_seasonal'
		, 'selected' => $seasonal
		, 'onchange' => 'onchange="hideShowBasedOnAnother(this, $(\'sp\'));"'
	);
	$form .= '
				<label for="slt_seasonal"><span class="required">*</span> Seasonal:</label>
				' . createDropDown($array) . '
				<div class="explanation">Whether or not the beer is seasonal.</div>
			</div>
	';

	// determine if to show the seaonalPeriod input
	$showSeasonPeriod = $seasonal == 0 ? ' style="display: none;"' : '';
			
	$form .= '	
			<span id="sp"' . $showSeasonPeriod . '> 
				<div class="formBlock">
					<label for="txt_seasonalPeriod"><span class="required">*</span> Seasonal Period:</label>
					<input type="text" id="txt_seasonalPeriod" name="txt_seasonalPeriod" value="' . $seasonalPeriod . '" />
					<div class="explanation">The actual season of the beer.  Examples: Winter, Special, One-time, Jan - Feb, etc.</div>
				</div>
			</span>		
			
			<div class="formBlock">
	';
	if(form_error('txt_abv')) {
		$form .= '<div class="formError">' . form_error('txt_abv') . '</div>';
	}
	$form .= '
				<label for="txt_abv">ABV:</label>
				<input type="text" id="txt_abv" name="txt_abv" value="' . $alcoholContent . '" />
				<div class="explanation">Alcohol By Volume of the beer.  A decimal with one or two places after the decimal.  Not percent sign needed</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_ibu')) {
		$form .= '<div class="formError">' . form_error('txt_ibu') . '</div>';
	}
	$form .= '	
				<label for="txt_ibu">IBU:</label>
				<input type="text" id="txt_ibu" name="txt_ibu" value="' . $ibu . '" />
				<div class="explanation">International Bittering Unit (IBU). Should be an integer value.</div>
			</div>			
			
			<div class="formBlock">
                <label for="ttr_beerNotes">Beer Notes:</label>
                <textarea id="ttr_beerNotes" name="ttr_beerNotes">' . $beerNotes . '</textarea>
                <div class="explanation">Information about the beer that could help identify, provide more insite, etc.</div>
            </div>
            
            <div class="formBlock"> 
	';
	if(form_error('txt_malts')) {
		$form .= '<div class="formError">' . form_error('txt_malts') . '</div>';
	}
	$form .= '
				<label for="txt_malts">Malts:</label>
				<input type="text" id="txt_malts" name="txt_malts" value="' . $malts . '" />
				<div class="explanation">List of the malts used for the beer.  Separate them by a comma.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_hops')) {
		$form .= '<div class="formError">' . form_error('txt_hops') . '</div>';
	}
	$form .= '
				<label for="txt_hops">Hops:</label>
				<input type="text" id="txt_hops" name="txt_hops" value="' . $hops . '" />
				<div class="explanation">List of the hops used for the beer.  Separate them by a comma.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_yeast')) {
		$form .= '<div class="formError">' . form_error('txt_yeast') . '</div>';
	}
	$form .= '
				<label for="txt_yeast">Yeast:</label>
				<input type="text" id="txt_yeast" name="txt_yeast" value="' . $yeast . '" />
				<div class="explanation">The yeast used for the beer.</div>
			</div>			
			
			<div class="formBlock">
	';
	if(form_error('txt_food')) {
		$form .= '<div class="formError">' . form_error('txt_food') . '</div>';
	}
	$form .= '
				<label for="txt_food">Food:</label>
				<input type="text" id="txt_food" name="txt_food" value="' . $food . '" />
				<div class="explanation">Tasty grub that would go well with the beer.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_glassware')) {
		$form .= '<div class="formError">' . form_error('txt_glassware') . '</div>';
	}
	$form .= '
				<label for="txt_glassware">Glassware:</label>
				<input type="text" id="txt_glassware" name="txt_glassware" value="' . $glassware . '" />
				<div class="explanation">Names of glasses that would go best.  Separate them by a comma.</div>
			</div>	
			
			<div class="formBlock">		
	';
	if(form_error('txt_gravity')) {
		$form .= '<div class="formError">' . form_error('txt_gravity') . '</div>';
	}
	$form .= '
				<label for="txt_gravity">Gravity:</label>
				<input type="text" id="txt_gravity" name="txt_gravity" value="' . $gravity . '" />
				<div class="explanation">Should be a decimal value that is the gravity of the beer.</div>
			</div>	
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Add Beer" />
		</form>
	';
	
	return $form;
}

function form_addEstablishment($config) {
	$category = set_value('slt_category');
	$name = set_value('txt_name');
	$address = set_value('txt_address');
	$city = set_value('txt_city');
	$state = set_value('slt_state');
	$zip = set_value('txt_zip');
	$phone = set_value('txt_phone');
	$url = set_value('txt_url');
	$twitter = set_value('txt_twitter');

	$form = '
		<form id="addEstablishment" class="edit" method="post" action="' . base_url() . 'brewery/addEstablishment">
			<div class="formBlock">
	';
	if(form_error('slt_category')) {
		$form .= '<div class="formError">' . form_error('slt_category') . '</div>';
	}

	$array = array(
		'data' => $config['categories']
		, 'id' => 'slt_category'
		, 'name' => 'slt_category'
		, 'selected' => $category
		, 'upperCase' => true
	);
	$form .= '
				<label for="slt_category"><span class="required">*</span> Type of Establishment:</label>
				' . createDropDown($array) . '
				<div class="explanation"></div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_name')) {
		$form .= '<div class="formError">' . form_error('txt_name') . '</div>';
	}
	$form .= '		
				<label for="txt_beer"><span class="required">*</span> Name:</label>
				<input type="text" id="txt_name" name="txt_name" value="' . $name . '" />
				<div class="explanation">The name of the establishment.  Please search the site before adding new.</div>
			</div>

			<div class="formBlock">
	';
	if(form_error('txt_address')) {
		$form .= '<div class="formError">' . form_error('txt_address') . '</div>';
	}
	$form .= '
				<label for="txt_address">Address:</label>
				<input type="text" id="txt_address" name="txt_address" value="' . $address . '" />
				<div class="explanation">The street address or PO Box of the establishment.</div>
			</div>	
			
	<div class="formBlock">
	';
	if(form_error('txt_city')) {
		$form .= '<div class="formError">' . form_error('txt_city') . '</div>';
	}
	$form .= '
				<label for="txt_city">City:</label>
				<input type="text" id="txt_city" name="txt_city" value="' . $city . '" />
				<div class="explanation">The city that the establishment is located in.</div>
			</div>		
	
			<div class="formBlock">
	';
	if(form_error('slt_state')) {
		$form .= '<div class="formError">' . form_error('slt_state') . '</div>';
	}

	$array = array(
		'data' => $config['states']
		, 'id' => 'slt_state'
		, 'name' => 'slt_state'
		, 'selected' => $state
	);
	$form .= '
				<label for="slt_state"><span class="required">*</span> State:</label>
				' . createDropDown($array) . '
				<div class="explanation">American craft brew site, so only the 50 states.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_zip')) {
		$form .= '<div class="formError">' . form_error('txt_zip') . '</div>';
	}
	$form .= '
				<label for="txt_zip">Zip:</label>
				<input type="text" id="txt_zip" name="txt_zip" value="' . $zip . '" />
				<div class="explanation">Appropriate zip code of the establishment.</div>
			</div>

			<div class="formBlock">
	';
	if(form_error('txt_phone')) {
		$form .= '<div class="formError">' . form_error('txt_phone') . '</div>';
	}
	$form .= '
				<label for="txt_phone" class="ttr_message">Phone: <span class="charsRemaining"></span></label>
				<input type="text" id="txt_phone" name="txt_phone" value="' . $phone . '" maxlength="10" />
				<div class="explanation">Phone number including area code.  Should only be 10 digits: ex. 1234567890.  No spaces, hyphens, etc needed.</div>
			</div>
            
            <script type="text/javascript">
            $(document).ready(function() { 
                fireItUp($( \'#txt_phone\'));
                $(\'#txt_phone\').bind(\'keyup keydown focus\', function() {
                    var str = $(this).val();
                    $(this).val(str.replace(/[^0-9]+/g, \'\'));
                    
                    fireItUp(this);
                });
            });
            
            function fireItUp(obj) {
                var len = $(obj).val().length;  
                var remaining = $(obj).attr(\'maxlength\') - len;   
                $(obj).siblings(\'.ttr_message\').children(\'.charsRemaining\').text(\'Digits remaining: \' + remaining);
                //alert($(obj).siblings(\'.ttr_message\').children(\'.charsRemaining\').text()); 
                if(remaining > 0) {
                    $(obj).siblings(\'.ttr_message\').children(\'.charsRemaining\').css(\'background-color\', \'#fff\');
                } else {
                    $(obj).siblings(\'.ttr_message\').children(\'.charsRemaining\').css(\'background-color\', \'#c00\');
                }
            } 
            </script>      
            
            <div class="formBlock">
	';
	if(form_error('txt_url')) {
		$form .= '<div class="formError">' . form_error('txt_url') . '</div>';
	}
	$form .= '	
				<label for="txt_url">URL:</label>
				<input type="text" id="txt_url" name="txt_url" value="' . $url . '" />
				<div class="explanation">Web site address for the establishment.  Don\'t use a slash at the end: ex. http://www.site.com</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_twitter')) {
		$form .= '<div class="formError">' . form_error('txt_twitter') . '</div>';
	}
	$form .= '	
				<label for="txt_twitter">Twitter Account:</label>
				<input type="text" id="txt_twitter" name="txt_twitter" value="' . $twitter . '" />
				<div class="explanation">Twitter account/username for the establishment.  Don\'t use @.</div>
			</div>		
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Add Establishment" />
		</form>
	';
	
	return $form;
}

function form_search() {
	$form = '
		<form id="editBeerForm" class="edit" method="post" action="' . base_url() . 'page/search">
			<select id="slt_searchType" name="slt_searchType">
				<option value="beer">Beer</option>
				<option value="establishment">Establishment</option>
				<option value="user">User</option>
			</select>
		
			<input type="text" id="txt_search" name="txt_search" />
			
			<!--<input type="button" id="btn_submit" name="btn_submit" value="" />-->
			<!--<img src="' . base_url() . 'images/search.png" width="60" height="21" alt="search button" />-->
			<input id="img_search" type="image" src="' . base_url() . 'images/search.png" />
		</form>
	';
	// return the form
	return $form;
}

function form_estblishmentReview($config) {
    $drink = key_exists('rating', $config) ? $config['rating']['drink'] : set_value('drink');
    $service = key_exists('rating', $config) ? $config['rating']['service'] : set_value('service');
    $atmosphere = key_exists('rating', $config) ? $config['rating']['atmosphere'] : set_value('atmosphere');
    $pricing = key_exists('rating', $config) ? $config['rating']['pricing'] : set_value('pricing');
    $accessibility = key_exists('rating', $config) ? $config['rating']['accessibility'] : set_value('accessibility');
	$dateVisited = key_exists('rating', $config) ? $config['rating']['dateVisited'] : set_value('txt_dateVisited');
	$comments = key_exists('rating', $config) ? $config['rating']['comments'] : set_value('ttr_comments');
	$visitAgain = key_exists('rating', $config) ? $config['rating']['visitAgain'] : set_value('slt_visitAgain');
	$price = key_exists('rating', $config) ? $config['rating']['price'] : set_value('slt_price');
	
	// get the code igniter instance
	$ci =& get_instance();
	
	unset($array);
	$array = array(
		'data' => array(
			array('id' => '0', 'name' => 'No')
			, array('id' => '1', 'name' => 'Yes')
		)
		, 'id' => 'slt_visitAgain'
		, 'name' => 'slt_visitAgain'
		, 'selected' => $visitAgain
	);
	$visitAgainDropDown = '<label for="slt_visitAgain"><span class="required">*</span> Visit Again:</label>' . createDropDown($array);	
	
	/*unset($array);
	$array = array(
		'data' => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10')
		, 'id' => 'slt_rating'
		, 'name' => 'slt_rating'
		, 'selected' => $rating
	);
	$ratingDropDown = createDropDownNoKeys($array);*/
	
	/*unset($array);
	$array = array(
		'data' => array('1', '2', '3', '4', '5')
		, 'id' => 'slt_price'
		, 'name' => 'slt_price'
		, 'selected' => $price
	);
	$priceDropDown = createDropDownNoKeys($array);*/
	
	$form = '
		<form id="establishment_review_form" class="edit" method="post" action="' . base_url() . 'establishment/createReview/' . $config['id'] . '">
			
			<div class="formBlock">
	';
	/*if(form_error('slt_rating')) {
		$form .= '<div class="formError">' . form_error('slt_rating') . '</div>';
	}
	$form .= '
				<label for="slt_rating"><span class="required">*</span> Rating:</label>
				' . $ratingDropDown . '
				<div class="explanation">Select a <a href="' . base_url() . 'establishment/ratingSystem">rating system</a> value between 1 and 10.</div>
			</div>
			
			<div class="formBlock">
	';*/
    if(form_error('txt_drink')) {
        $form .= '<div class="formError">' . form_error('txt_drink') . '</div>';
    }
    $form .= '
                <label for="txt_drink"><span class="required">*</span> Quality: <span id="span_drink"></span></label>
                <div id="slider_drink" class="slider"><div class="handle"></div></div>
                <input type="hidden" id="txt_drink" name="drink" value="' . $drink . '" />
                <div class="explanation">What was the quality of the selection, taps, food, drink, bottles, etc.  This will make up ' . PERCENT_DRINK . '% of the overall score.</div>
            </div>    
            
            <div class="formBlock">
    ';
    if(form_error('txt_service')) {
        $form .= '<div class="formError">' . form_error('txt_service') . '</div>';
    }
    $form .= '
                <label for="txt_service"><span class="required">*</span> Service: <span id="span_service"></span></label>
                <div id="slider_service" class="slider"><div class="handle"></div></div>
                <input type="hidden" id="txt_service" name="service" value="' . $service . '" />
                <div class="explanation">Did you feel the staff took care of you well?  Were they friendly? Helpful?  This will make up ' . PERCENT_SERVICE . '% of the overall score.</div>
            </div>    
            
            <div class="formBlock">
    ';
    if(form_error('txt_atmoshpere')) {
        $form .= '<div class="formError">' . form_error('txt_atmoshpere') . '</div>';
    }
    $form .= '
                <label for="txt_atmosphere"><span class="required">*</span> Atmosphere: <span id="span_atmosphere"></span></label>
                <div id="slider_atmosphere" class="slider"><div class="handle"></div></div>
                <input type="hidden" id="txt_atmosphere" name="atmosphere" value="' . $atmosphere . '" />
                <div class="explanation">Was the place clean?  Could you hear each other talk?  If you have kids, was it kid friendly?  This will make up ' . PERCENT_ATMOSPHERE . '% of the overall score.</div>
            </div>    
            
            <div class="formBlock">
    ';
    if(form_error('txt_pricing')) {
        $form .= '<div class="formError">' . form_error('txt_pricing') . '</div>';
    }
    $form .= '
                <label for="txt_pricing"><span class="required">*</span> Pricing: <span id="span_pricing"></span></label>
                <div id="slider_pricing" class="slider"><div class="handle"></div></div>
                <input type="hidden" id="txt_pricing" name="pricing" value="' . $pricing . '" />
                <div class="explanation">Based on what you paid for drink, food, souvenirs, etc how well does it compare in terms of value?  10 is a bargain, while 1 is high pricing.  This will make up ' . PERCENT_PRICING . '% of the overall score.</div>
            </div>    
            
            <div class="formBlock">
    ';
    if(form_error('txt_accessibility')) {
        $form .= '<div class="formError">' . form_error('txt_accessibility') . '</div>';
    }
    $form .= '
                <label for="txt_accessibility"><span class="required">*</span> Accessibility: <span id="span_accessibility"></span></label>
                <div id="slider_accessibility" class="slider"><div class="handle"></div></div>
                <input type="hidden" id="txt_accessibility" name="accessibility" value="' . $accessibility . '" />
                <div class="explanation">How easy is it to get to?  Is there a wait to get in?  This will make up ' . PERCENT_ACCESSIBILITY . '% of the overall score.</div>
            </div>
            
            <div class="formBlock">
                <div>
                    <p class="bold" style="width: 100%;">Overall Rating: <span id="overallRating" class="required bold" style="text-align: right;"></span></p>
                </div>
            </div>    
            
            <div class="formBlock">
    ';
	if(form_error('txt_dateTasted')) {
		$form .= '<div class="formError">' . form_error('txt_dateTasted') . '</div>';
	}
	$form .= '
			
				<label for="txt_dateVisited"><span class="required">*</span> Date Visited:</label>
				<input type="text" id="txt_dateVisited" name="txt_dateVisited" value="' . $dateVisited . '" />
				<div class="explanation">Date is in yyyy-mm-dd format.  Please use calendar to auto select, it will format appropriately.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('ttr_comments')) {
		$form .= '<div class="formError">' . form_error('ttr_comments') . '</div>';
	}
	$form .= '			
				<label for="ttr_comments"><span class="required">*</span> Comments:</label>
				<textarea id="ttr_comments" name="ttr_comments">' . $comments . '</textarea>	
				<div class="explanation">Your thoughts about the establishment.</div>
			</div>
			
			<div class="formBlock">				
	';
	if(form_error('slt_haveAnother')) {
		$form .= '<div class="formError">' . form_error('slt_haveAnother') . '</div>';
	}
	$form .= '			
				' . $visitAgainDropDown . '
				<div class="explanation">Quite simply: would you visit again if presented with the chance.</div>
			</div>
	';
    /*$form .= '		
			<div class="formBlock">				
	';
	if(form_error('slt_price')) {
		$form .= '<div class="formError">' . form_error('slt_price') . '</div>';
	}
	$form .= '
				<label for="slt_price"><span class="required">*</span> Price:</label>' .			
				$priceDropDown . '
				<div class="explanation">How fair the prices of the beer, food, etc. at the establishment.  1 low and 5 is over priced.</div>
			</div>
	'; */
    $form .= '		
			<input type="submit" id="btn_submit" name="btn_submit" value="Submit Establishment Review" />
		</form>
		<script type="text/javascript">
        /*<![CDATA[*/
        Calendar.setup({
            dateField : \'txt_dateVisited\',
            triggerElement : \'txt_dateVisited\'
        })
        
        var slider_drink = $(\'slider_drink\');
        var drink = $(\'txt_drink\');
        var span_drink = $(\'span_drink\');
        var start_drink = drink.getValue() == \'\' ? 1 : drink.getValue();
        var slider_service = $(\'slider_service\');
        var service = $(\'txt_service\');
        var span_service = $(\'span_service\');
        var start_service = service.getValue() == \'\' ? 1 : service.getValue();
        var slider_atmosphere = $(\'slider_atmosphere\');
        var atmosphere = $(\'txt_atmosphere\');
        var span_atmosphere = $(\'span_atmosphere\');
        var start_atmosphere = atmosphere.getValue() == \'\' ? 1 : atmosphere.getValue();
        var slider_pricing = $(\'slider_pricing\');
        var pricing = $(\'txt_pricing\');
        var span_pricing = $(\'span_pricing\');
        var start_pricing = pricing.getValue() == \'\' ? 1 : pricing.getValue();
        var slider_accessibility = $(\'slider_accessibility\');
        var accessibility = $(\'txt_accessibility\');
        var span_accessibility = $(\'span_accessibility\');
        var start_accessibility = accessibility.getValue() == \'\' ? 1 : accessibility.getValue();
        (function() {
            new Control.Slider(slider_drink.down(\'.handle\'), slider_drink, {
                axis: \'horizontal\'
                , range: $R(1, 10)
                , minimum: 0
                , alignX: 1
                , increment: 13
                , sliderValue: start_drink
                , values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                , onSlide: function(value) {
                    drink.setValue(value);
                    span_drink.update(\'(\' + value + \')\');
                    overallAverage();
                }
                , onChange: function(value) {
                    drink.setValue(value);
                    overallAverage();
                }
            });
            
            new Control.Slider(slider_service.down(\'.handle\'), slider_service, {
                axis: \'horizontal\'
                , range: $R(1, 10)
                , minimum: 0
                , alignX: 1
                , increment: 13
                , sliderValue: start_service
                , values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                , onSlide: function(value) {
                    service.setValue(value);
                    span_service.update(\'(\' + value + \')\');
                    overallAverage();
                }
                , onChange: function(value) {
                    service.setValue(value);
                    overallAverage();
                }
            });
            
            new Control.Slider(slider_atmosphere.down(\'.handle\'), slider_atmosphere, {
                axis: \'horizontal\'
                , range: $R(1, 10)
                , minimum: 0
                , alignX: 1
                , increment: 13
                , sliderValue: start_atmosphere
                , values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                , onSlide: function(value) {
                    atmosphere.setValue(value);
                    span_atmosphere.update(\'(\' + value + \')\');
                    overallAverage();
                }
                , onChange: function(value) {
                    atmosphere.setValue(value);
                    overallAverage();
                }
            });
            
            new Control.Slider(slider_pricing.down(\'.handle\'), slider_pricing, {
                axis: \'horizontal\'
                , range: $R(1, 10)
                , minimum: 0
                , alignX: 1
                , increment: 13
                , sliderValue: start_pricing
                , values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                , onSlide: function(value) {
                    pricing.setValue(value);
                    span_pricing.update(\'(\' + value + \')\');
                    overallAverage();
                }
                , onChange: function(value) {
                    pricing.setValue(value);
                    overallAverage();
                }
            });
            
            new Control.Slider(slider_accessibility.down(\'.handle\'), slider_accessibility, {
                axis: \'horizontal\'
                , range: $R(1, 10)
                , minimum: 0
                , alignX: 1
                , increment: 13
                , sliderValue: start_accessibility
                , values: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                , onSlide: function(value) {
                    accessibility.setValue(value);
                    span_accessibility.update(\'(\' + value + \')\');
                    overallAverage();
                }
                , onChange: function(value) {
                    accessibility.setValue(value);
                    overallAverage();
                }
            });
        })();
        
        Event.observe(window, \'load\', updateFields);
        function updateFields() {
            span_drink.update(\'(\' + setValue(drink) + \')\');
            span_service.update(\'(\' + setValue(service) + \')\');
            span_atmosphere.update(\'(\' + setValue(atmosphere) + \')\');
            span_pricing.update(\'(\' + setValue(pricing) + \')\');
            span_accessibility.update(\'(\' + setValue(accessibility) + \')\');
            
            overallAverage();
        }
        
        function overallAverage() {
            var mth = (drink.getValue() * (' . PERCENT_DRINK . '/100)) + (service.getValue() * (' . PERCENT_SERVICE . '/100)) + (atmosphere.getValue() * (' . PERCENT_ATMOSPHERE . '/100)) + (pricing.getValue() * (' . PERCENT_PRICING . '/100)) + (accessibility.getValue() * (' . PERCENT_ACCESSIBILITY . '/100));
            var avg = roundNumber(mth, 1).toFixed(1);
            $(\'overallRating\').update(avg);
        }
        
        function roundNumber(num, dec) {
            var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
            return result;
        }
        
        function setValue(el) {
            var tmp = 0;
            if(el.getValue() == \'\') {
                tmp = 1;
                el.value = tmp;
            } else {
                tmp = el.getValue()
            }
            return tmp;
        }
        /*]]>*/
        </script>
        
        <script type="text/javascript">
        $j(document).ready(function() {
            $j(\'#btn_submit\').click(function() {
                $j(this).attr(\'disabled\', \'disabled\').val(\'Processing...\');
                $j(\'#establishment_review_form\').submit();
            });
        });
        </script>
	';
	return $form;
}

function form_contactUs($config) {
	$name = key_exists('name', $config) ? $config['name'] : set_value('txt_name');
	$email = key_exists('email', $config) ? $config['email'] : set_value('txt_email');
	$comments = key_exists('comments', $config) ? $config['comments'] : set_value('ttr_comments');

	// get the code igniter instance
	$ci =& get_instance();
	// create the form	
	$form = '
		<form class="edit" method="post" action="' . base_url() . 'page/contactUs" class="marginTop_8">
			
			<div class="formBlock">
	';
	if(form_error('txt_name')) {
		$form .= '<div class="formError">' . form_error('txt_name') . '</div>';
	}
	$form .= '
				<label for="txt_name"><span class="required">*</span> Name:</label>
				<input type="text" id="txt_name" name="txt_name" value="' . $name . '" />
				<div class="explanation">Your name.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('txt_email')) {
		$form .= '<div class="formError">' . form_error('txt_email') . '</div>';
	}
	$form .= '
			
				<label for="txt_email"><span class="required">*</span> Email:</label>
				<input type="text" id="txt_email" name="txt_email" value="' . $email . '" />
				<div class="explanation">Your email address so we can send some beer goodness back.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('ttr_comments')) {
		$form .= '<div class="formError">' . form_error('ttr_comments') . '</div>';
	}
	$form .= '			
				<label for="ttr_comments"><span class="required">*</span> Comments:</label>
				<textarea id="ttr_comments" name="ttr_comments">' . $comments . '</textarea>	
				<div class="explanation">What&#39;s up?</div>
			</div>
			
			<div class="formBlock">
	';
	
	// load the captcha plugin
	$ci->load->plugin('captcha');
	// captcha
	$vals = array(
		'img_path' => './captcha/'
		, 'img_url' => base_url() . 'captcha/'
		, 'font_path'	 => './font/verdana.ttf',
	);	
	$cap = create_captcha($vals);

	$data = array(
		'captcha_id' => '',
		'captcha_time' => $cap['time'],
		'ip_address' => $ci->input->ip_address(),
		'word' => $cap['word']
	);
	// load the captcha model
	$ci->load->model('CaptchaModel', '', true);
	// run the captcha query
	$ci->CaptchaModel->insertCaptcha($data);
	
	if(form_error('captcha')) {
		$form .= '<div class="formError">' . form_error('captcha') . '</div>';
	}
	$form .= '
				<label for="txt_captcha"><span class="required">*</span> Security Code:</label>
				' . $cap['image'] . '
				<input type="text" id="txt_captcha" name="captcha" value="" />
				<div class="explanation">Helps us to keep out bots and other unwanted bits and bytes.</div>
			</div>
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Contact Us" class="marginTop_8" />
		</form>
	';
	return $form;
}

function form_updateInfo($config) {
	$change = key_exists('change', $config) ? $config['change'] : set_value('slt_change');
	$comments = key_exists('comments', $config) ? $config['comments'] : set_value('ttr_comments');

	// get the code igniter instance
	$ci =& get_instance();
	// load the change type model
	
	$array = array(
		'data' => $config['ct']
		, 'id' => 'slt_change'
		, 'name' => 'slt_change'
		, 'selected' => $change
	);
	$changeDropDown = createDropDown($array);	
	
	// create the form	
	$form = '
		<form class="edit" method="post" action="' . base_url() . 'page/updateInfo/' . $config['type'] . '/' . $config['id'] . '" class="marginTop_8">
			
			<div class="formBlock">
	';
	if(form_error('slt_change')) {
		$form .= '<div class="formError">' . form_error('slt_change') . '</div>';
	}
	$form .= '
				<label for="slt_change"><span class="required">*</span> Requested Change:</label>
				' . $changeDropDown . '
				<div class="explanation">Select the change that you are wanting to have made.</div>
			</div>
			
			<div class="formBlock">
	';
	if(form_error('ttr_comments')) {
		$form .= '<div class="formError">' . form_error('ttr_comments') . '</div>';
	}
	$form .= '			
				<label for="ttr_comments"><span class="required">*</span> Reason:</label>
				<textarea id="ttr_comments" name="ttr_comments">' . $comments . '</textarea>	
				<div class="explanation">A detailed reason for the requested change.  Be specific.</div>
			</div>
			
			<input type="submit" id="btn_submit" name="btn_submit" value="Send Update Info" class="marginTop_8" />
		</form>
	';
	return $form;
}

function markupPreviousMessage($config) {
	$str = 
		"\r\n\r\n" . '[quote]' . 
		"\r\n" . $config['username'] . ' said on ' . $config['timesent'] . 
		"\r\n" . $config['message'] . 
		"\r\n" . '[/quote]'
	;
	return $str;
}

function indentPreviousMessage($message, $class = '') {
	// check if class is empty
	if(empty($class)) {
		// set it to a generic value
		$class = 'pms_indent';
	}
	return '<div class="' . $class . '">' . $message . '</div>';
}

function createThreadForm($config) {
    $id = $config['id'];
    $type = $config['type'];
    $topic_id = $config['topic_id'];
    $topic_name = $config['topic_name'];
    $sub_topic_name = $config['sub_topic_name'];
    $desc = $config['description'];
    $thread_id = $config['thread_id'];
    $thread_subject = $config['thread_subject'];
    $subject = key_exists('subject', $config) ? $config['subject'] : set_value('subject');
    $message = key_exists('message', $config) ? $config['message'] : set_value('message');
    
    // get the code igniter instance
    $ci =& get_instance();
    
    // holder for h2 header content
    $h2 = '';
    // holder for button text
    $button = '';
    // holder for the uri portion of the url
    $uri = '';
    // determine the h2 header content and button text    
    if($type == 'new_thread') {
        $h2 = '            
            <h2 class="brown">Create New Thread</h2>
            <p class="marginTop_8"><span class="bold"><a href="' . base_url() . 'forum#' . $topic_id . '">' . $topic_name . '</a> -&gt; <a href="' . base_url() . 'forum/dst/' . $id . '">' . $sub_topic_name . '</a> -&gt; ' . $desc . '</span></p>
            <p>A new thread will be created in the forum above.  Are you sure that\'s where you want it?</p>
        ';
        $button = 'Create New Thread';
    } else {
        $h2 = '
            <h2 class="brown">Reply To Thread</h2>
            <p class="marginTop_8"><span class="bold"><a href="' . base_url() . 'forum#' . $topic_id . '">' . $topic_name . '</a> -&gt; <a href="' . base_url() . 'forum/dst/' . $id . '">' . $sub_topic_name . '</a> -&gt; <a href="' . base_url() . 'forum/st/' . $id . '/' . $thread_id . '">' . $thread_subject . '</a></span></p>
            <p>A new reply will be created to the thread above.  Are you sure that\'s where you want it?</p>
        ';
        $button = 'Reply To Thread';
    }
    
    $form = $h2 . '
    <form class="edit" method="post" action="' . base_url() . substr($ci->uri->uri_string(), 1) . '" class="marginTop_8">
        <div class="formBlock">
    ';
    // make sure that subject needs to be shown
    if($type == 'new_thread') {
        if(form_error('txt_subject')) {
            $form .= '<div class="formError">' . form_error('txt_subject') . '</div>';
        }
        $form .= '
                <label for="txt_subject"><span class="required">*</span> Subject:</label>
                <input type="text" id="txt_subject" name="txt_subject" value="' . $subject . '" />
                <div class="explanation">The subject of the thread.</div>
            </div>	

            <div class="formBlock">
            ';
    }
    if(form_error('ttr_message')) {
        $form .= '<div class="formError">' . form_error('ttr_message') . '</div>';
    }
    $form .= '
            <label for="ttr_message"><span class="required">*</span> Message:</label>
            <textarea id="ttr_message" name="ttr_message">' . $message . '</textarea>
            <div class="explanation">What you want to say.</div>
        </div>	

        <input type="submit" id="btn_submit" name="btn_submit" value="' . $button . '" class="marginTop_8" />
    </form>
    ';   
    
    // send back the form
    return $form;
}
?>