function swaplistAdd() {
	//var cnt = tableRows('swapsTable');	
	removeOptions($('slt_beer'));
	//var oRows = $('swapsTable').getElementsByTagName('tr');
}

function removeOptions(selectbox) {
	for(var i = selectbox.length - 1 ; i >= 0; i--) {
		if(selectbox.options[i].selected) {
			selectbox.remove(i);
			break;
		}
	}
}

function checkRowCount() {
	//$('swapsTable').deleteRow(num);
	var oRows = $$('#swapsTable tr');
	//var oRows = $$('table tr');
	//var el = document.getElementById('swapsTable');
	//var oRows = el.getElementsByTagName('tr');
	var iRowCount = oRows.length - 1;
	//alert($('btn_add').disabled);
	if(iRowCount >= 10) {
		//alert(iRowCount);
		if($('btn_add').disabled != true) {
			$('btn_add').disable();
		}
	} else {
		if($('btn_add').disabled == true) {
			$('btn_add').enable();
		}
	}
}

function singleUpdater(txt) {
	// add the passbacked info back to the list area
	$('swapsInfo').update(txt);
	// check if the submit button should be disabled
	checkRowCount();
}

function multiUpdater(txt) {
	// split the reponse text
	var arr_split = txt.split('|');
	// add the first part (the table) to the swapsInfo
	$('swapsInfo').update(arr_split[0]);
	// add the second part (the select box) to beerDropDown
	$('beerDropDown').update(arr_split[1]);
	// check if the submit button should be disabled
	checkRowCount();
}

/**
	Determines if insertion was completely correctly
	and then which 
*/
function updateFeedbackAdd(txt) {
	if(txt == 'bad') {
		// there was a problem processing
		$('magicSpinner').hide(); 
		$('feedbackSubmitButton').show();
	} else {
		// no problems
		$('feedbackContainer').update(txt);
		
		$('magicSpinner').hide(); 
		$('feedbackSubmitButton').show();
		$('ttr_swapFeedback').clear();
		$('formContainerFeedback').hide(); 
		$('addFeedbackLink').show();
	}
}

$j(document).ready(function() {
    $j('.feedback_report_link a').fancybox({
        width: 600
        , type: 'iframe'   
    });
});

/**
* allows for the reporting of a malicious swap feedback
*/
function reportLink(feedbackID) {
    $j.post('http://www.twobeerdudes.com/report/feedback/' + feedbackID, function(data) {
        alert(data);    
    });
}