<?php
$config = array(
	'login' => array(
		array(
			'field' => 'email'
			, 'label' => 'Email Address'
			, 'rules' => 'trim|required'
		) , array(
			'field' => 'password'
			, 'label' => 'Password'
			, 'rules' => 'trim|required'
		)
	), 'createAccount' => array(
		array(
			'field' => 'username'
			, 'label' => 'User Name'
			, 'rules' => 'trim|required|min_length[3]|max_length[15]|alpha_numeric|callback_usernameExists'
		) , array(
			'field' => 'email'
			, 'label' => 'Email Address'
			, 'rules' => 'trim|required|valid_email|callback_emailExists'
		) , array(
			'field' => 'password1'
			, 'label' => 'Password'
			, 'rules' => 'trim|required|min_length[6]|max_length[12]|matches[password2]'
		) , array(
			'field' => 'password2'
			, 'label' => 'Password Verification'
			, 'rules' => 'trim|required'
		) , array(
			'field' => 'state'
			, 'label' => 'State'
			, 'rules' => 'trim|required|min_length[1]|max_length[2]|numeric'
		) , array(
			'field' => 'city'
			, 'label' => 'City'
			, 'rules' => 'trim|required|callback_alphaNumericSpace'
		) , array(
			'field' => 'captcha'
			, 'label' => 'Security Code'
			, 'rules' => 'trim|required|callback_validateCaptcha'
		)
	), 'addReviewBeer' => array(
		/*array(
			'field' => 'slt_rating'
			, 'label' => 'Rating'
			, 'rules' => 'trim|required|numeric'
		)*/
		array(
			'field' => 'aroma'
			, 'label' => 'Aroma'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'taste'
			, 'label' => 'Taste'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'look'
			, 'label' => 'Look'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'drinkability'
			, 'label' => 'Drinkability'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'txt_dateTasted'
			, 'label' => 'Date Tasted'
			, 'rules' => 'trim|required|callback_validMysqlDate'
		), array(
			'field' => 'txt_color'
			, 'label' => 'Color'
			, 'rules' => 'trim|required'
		), array(
			'field' => 'ttr_comments'
			, 'label' => 'Comments'
			//, 'rules' => 'trim|required|callback_filterWords'
			, 'rules' => 'trim|required'
		), array(
			'field' => 'slt_haveAnother'
			, 'label' => 'Have Another'
			, 'rules' => 'trim|required|numeric'
		), array(
			'field' => 'slt_package'
			, 'label' => 'Package'
			, 'rules' => 'trim|required|numeric'
		), array(
			'field' => 'txt_price'
			, 'label' => 'Price'
			, 'rules' => 'trim|required|callback_decimalNumber'
		)
	), 'addShortReviewBeer' => array(
		array(
			'field' => 'txt_dateTasted'
			, 'label' => 'Date Tasted'
			, 'rules' => 'trim|required|callback_validMysqlDate'
		), array(
			'field' => 'aroma'
			, 'label' => 'Aroma'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'taste'
			, 'label' => 'Taste'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'look'
			, 'label' => 'Look'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'drinkability'
			, 'label' => 'Drinkability'
			, 'rules' => 'trim|required|callback_integerBetween'
		), array(
			'field' => 'slt_haveAnother'
			, 'label' => 'Have Another'
			, 'rules' => 'trim|required|numeric'
		)
	), 'addReviewEstablishment' => array(
		array(
            'field' => 'drink'
            , 'label' => 'drink'
            , 'rules' => 'trim|required|callback_integerBetween'
        ), array(
            'field' => 'service'
            , 'label' => 'service'
            , 'rules' => 'trim|required|callback_integerBetween'
        ), array(
            'field' => 'atmosphere'
            , 'label' => 'atmosphere'
            , 'rules' => 'trim|required|callback_integerBetween'
        ), array(
            'field' => 'pricing'
            , 'label' => 'pricing'
            , 'rules' => 'trim|required|callback_integerBetween'
        ), array(
            'field' => 'accessibility'
            , 'label' => 'accessibility'
            , 'rules' => 'trim|required|callback_integerBetween'
        ), array(
			'field' => 'txt_dateVisited'
			, 'label' => 'Date Visited'
			, 'rules' => 'trim|required|callback_validMysqlDate'
		), array(
			'field' => 'ttr_comments'
			, 'label' => 'Comments'
			//, 'rules' => 'trim|required|callback_filterWords'
			, 'rules' => 'trim|required'
		), array(
			'field' => 'slt_visitAgain'
			, 'label' => 'Visit Again'
			, 'rules' => 'trim|required|numeric'
		)/*, array(
			'field' => 'slt_price'
			, 'label' => 'Price'
			, 'rules' => 'trim|required|numeric'
		)*/
	), 'sendMaltedMail' => array(
		array(
			'field' => 'txt_to'
			, 'label' => 'To'
			, 'rules' => 'trim|required|callback_userExists|callback_userBlocked'
		), array(
			'field' => 'txt_subject'
			, 'label' => 'Subject'
			, 'rules' => 'trim|required'
		), array(
			'field' => 'ttr_message'
			, 'label' => 'Message'
			, 'rules' => 'trim|required|callback_mailLength'
		)
	), 'addBeer' => array(
		array(
			'field' => 'txt_beer'
			, 'label' => 'Beer'
			, 'rules' => 'trim|required|callback_alphaNumericSpace'
		), array(
			'field' => 'slt_style'
			, 'label' => 'Style'
			, 'rules' => 'trim|required|callback_styleExists'
		), array(
			'field' => 'slt_seasonal'
			, 'label' => 'Seasonal'
			, 'rules' => 'trim|required|callback_seasonalExists'
		), array(
			'field' => 'txt_seasonalPeriod'
			, 'label' => 'Season Period'
			, 'rules' => 'trim'
		), array(
            'field' => 'ttr_beerNotes'
            , 'label' => 'Beer Notes'
            , 'rules' => 'trim'
        ), array(
			'field' => 'txt_abv'
			, 'label' => 'ABV'
			, 'rules' => 'trim|numeric'
		), array(
			'field' => 'txt_ibu'
			, 'label' => 'IBU'
			, 'rules' => 'trim|integer'
		), array(
			'field' => 'txt_malts'
			, 'label' => 'Malts'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_hops'
			, 'label' => 'Hops'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_yeast'
			, 'label' => 'Yeast'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_food'
			, 'label' => 'Food'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_glassware'
			, 'label' => 'Glassware'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_gravity'
			, 'label' => 'Gravity'
			, 'rules' => 'trim|numeric'
		)
	), 'addEstablishment' => array(
		array(
			'field' => 'slt_category'
			, 'label' => 'Type of Establishment'
			, 'rules' => 'trim|required|callback_alphaNumericSpace|callback_categoryExists'
		), array(
			'field' => 'txt_name'
			, 'label' => 'Name'
			, 'rules' => 'trim|required|callback_alphaNumericSpaceAndOthers'
		), array(
			'field' => 'txt_address'
			, 'label' => 'Address'
			, 'rules' => 'trim'
		), array(
			'field' => 'txt_city'
			, 'label' => 'City'
			, 'rules' => 'trim'
		), array(
			'field' => 'slt_state'
			, 'label' => 'State'
			, 'rules' => 'trim|required|callback_stateExists'
		) , array(
			'field' => 'txt_zip'
			, 'label' => 'Zip'
			, 'rules' => 'trim|min_length[5]|max_length[5]|integer'
		) , array(
			'field' => 'txt_phone'
			, 'label' => 'Phone'
			, 'rules' => 'trim|min_length[10]|max_length[10]|integer'
		) , array(
			'field' => 'txt_url'
			, 'label' => 'URL'
			, 'rules' => 'trim|callback_dropEndSlash'
		) , array(
			'field' => 'txt_twitter'
			, 'label' => 'Twitter Account'
			, 'rules' => 'trim|alpha_dash|max_length[50]'
		)
	), 'resetPassword' => array(
		array(
			'field' => 'email'
			, 'label' => 'Email Address'
			, 'rules' => 'trim|required|valid_email|callback_emailCheckMatch'
		) , array(
			'field' => 'captcha'
			, 'label' => 'Security Code'
			, 'rules' => 'trim|required|callback_validateCaptcha'
		)
	), 'updatePassword' => array(
		array(
			'field' => 'password1'
			, 'label' => 'Password'
			, 'rules' => 'trim|required|min_length[6]|max_length[12]|matches[password2]'
		) , array(
			'field' => 'password2'
			, 'label' => 'Password Verification'
			, 'rules' => 'trim|required'
		)
	), 'contactUs' => array(
		array(
			'field' => 'txt_name'
			, 'label' => 'Name'
			, 'rules' => 'trim|required|min_length[2]|max_length[25]|alpha_numeric'
		) , array(
			'field' => 'txt_email'
			, 'label' => 'Email Address'
			, 'rules' => 'trim|required|valid_email'
		) , array(
			'field' => 'ttr_comments'
			, 'label' => 'Comments'
			, 'rules' => 'trim|required'		
		) , array(
			'field' => 'captcha'
			, 'label' => 'Security Code'
			, 'rules' => 'trim|required|callback_validateCaptcha'
		)
	), 'updateInfo' => array(
		array(
			'field' => 'slt_change'
			, 'label' => 'Requested Change'
			, 'rules' => 'trim|required|callback_changeTypeExists'
		) , array(
			'field' => 'ttr_comments'
			, 'label' => 'Reason'
			, 'rules' => 'trim|required'		
		)
	), 'updateProfile' => array(
		array(
			'field' => 'txt_firstname'
			, 'label' => 'First Name'
			, 'rules' => 'trim|required|max_length[20]|alpha_numeric'
		) , array(
			'field' => 'txt_lastname'
			, 'label' => 'Last Name'
			, 'rules' => 'trim|max_length[20]|alpha_numeric'		
		) , array(
			'field' => 'txt_city'
			, 'label' => 'City'
			, 'rules' => 'trim|min_length[2]|max_length[40]|alpha_numeric'		
		) , array(
			'field' => 'ttr_notes'
			, 'label' => 'Notes'
			, 'rules' => 'trim|max_length[500]'		
		) , array(
			'field' => 'slt_state'
			, 'label' => 'State'
			, 'rules' => 'trim|required'		
		)
	), 'reportFeedback' => array(
		array(
			'field' => 'ttr_report'
			, 'label' => 'Justification'
			, 'rules' => 'trim|required|min_length[10]'
		)
    ), 'new_thread' => array(
        array(
            'field' => 'txt_subject'
            , 'label' => 'Subject'
            , 'rules' => 'trim|required|min_length[5]'
        ), array(
            'field' => 'ttr_message'
            , 'label' => 'Message'
            , 'rules' => 'trim|required|min_length[10]|max_length[400]'
        )
    ), 'reply_thread' => array(
        array(
            'field' => 'ttr_message'
            , 'label' => 'Message'
            , 'rules' => 'trim|required|min_length[10]|max_length[400]'
        )
    )
);
?>