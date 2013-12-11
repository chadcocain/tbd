function multiMalt(txt) {
	// split the reponse text
	//var arr_split = txt.split('|');
	// add the first part (the table) to the swapsInfo
	//$('maltedMail').update(arr_split[0]);
	$('maltedMail').update(txt);
	// add the second part (the select box) to beerDropDown
	//$('beerDropDown').update(arr_split[1]);
}