<?php
function getSortFileList($config) {
	// open up the directory resourece
	$handle = opendir($config['path']);
	// holder array for the files that will be found
	$arr_file = array();
	// check if the handle was opened correctly
	if($handle) {
		// iterate through the directory
		while(false !== ($file = readdir($handle))) {
			// make sure we have a file and not . or ..
			if(!is_dir($file) && $file != '.' && $file != '..') {
				// get the stat info on the file, this is an array
				$stat = stat($config['path'] . $file);
				// add the current file to the array
				// key is the last modified time
				// value is the file name
				$arr_file[$stat[$config['time']]] = $file;
			}
		}
		// close the resource
		closedir($handle);
	}
	// check that the arry isn't empty
	if(!empty($arr_file)) {
		// determine the way to sort
		switch($config['sort']) {
			case 'ksort':
				// sort the array by key - this will leave
				// an array sorted from oldest file to newest
				ksort($arr_file);
				// done
				break;
			case 'krsort':
				// sort the array by key - this will leave
				// an array sorted from newest file to oldest
				krsort($arr_file);
				// done
				break;
		}	
	}
	// return the sorted file
	return $arr_file;
}

// path to where the files are on the server
$config['path'] = './images/';
// the file time (associative value) for the key
$config['time'] = 'mtime';
// the type of sort you wish to do
$config['sort'] = 'ksort';
// get the array of files
$array = getSortFileList($config);
?>