<?php
if(!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Pages {
	private $ci;
	private $title = 'Beers';

	public function __construct() {
		$this->ci =& get_instance();
	}
	
	public function uploadImageForm($item) {
		$str = '
			<form id="uploadImage" class="edit marginTop_8" method="post" action="' . base_url() . 'page/cropImage/' . $item['type'] . '/' . $item['id'] . '" enctype="multipart/form-data">
				<span id="frm_picture_container">
					<label for="fl_picture">Add Image:</label>
					<input type="file" id="fl_picture" name="fl_picture" />	
				</span>
				<span id="spn_spinner"></span>
				<span id="spn_picture_name"></span>
				<input type="hidden" id="hdn_picture" name="hdn_picture" value="" />
				
				<input type="submit" id="btn_submit" name="btn_submit" value="Continue - Crop Image" style="display: none;" />
			</form>
			
			<script type="text/javascript">
			/*<![CDATA[*/
			var button = $(\'fl_picture\');
			document.observe("dom:loaded", function() {
				new Ajax_upload(button,{
					action: \'' . base_url() . 'ajax/uploadFile/' . $item['type'] . '/' . $item['id'] . '\',
					name: \'imageToUpload\',
					onSubmit : function(file, ext){
						showSpinner(\'spn_spinner\');
					},
					onComplete: function(file, response){
						// check if this was successful
						if(response.indexOf(\'gif\') != -1 || response.indexOf(\'jpg\') != -1 || response.indexOf(\'png\') != -1) {
							// an image name was returned
							// hide the image
							$(\'spn_spinner\').hide();
							$(\'frm_picture_container\').hide();
							// set the holder with the name of the image that was uploaded
							$(\'spn_picture_name\').show();
							$(\'spn_picture_name\').update(response);
							// set the name of the image to be uploaded in the hidden form field
							$(\'hdn_picture\').value = file;
							// show the submit button
							$(\'btn_submit\').show();
						} else {
							// error was returned
							$(\'spn_spinner\').hide();						
							// set the holder with an error string
							$(\'spn_picture_name\').update(response);
							// set the hidden form field value to an empty string
							$(\'hdn_picture\').value = \'\';
							// place the form element back and give them a chance to upload again
							//$(\'spn_picture\').update(\'<input type="file" id="fl_picture" name="fl_picture" />\');
						}
					}
				});
			});
			/*]]>*/
			</script>	
		';
		return $str;
	}
	
	public function cropImage($item) {
		$str = '
			<img src="' . base_url() . 'images/' . $item['type'] . '/tmp/' . $item['fileName'] . '" id="cropThisImage" />
						
			<form style="margin: 8px 0;" class="edit" method="post" action="' . base_url() . 'page/cropImage/' . $item['type'] . '/' . $item['id'] . '">
				<input type="hidden" id="x1" name="x1" value="" />
				<input type="hidden" id="x2" name="x2" value="" />
				<input type="hidden" id="y1" name="y1" value="" />
				<input type="hidden" id="y2" name="y2" value="" />
				<input type="hidden" id="width" name="width" value="" />
				<input type="hidden" id="height" name="height" value="" />
				<input type="hidden" id="hdn_fileName" name="hdn_fileName" value="' . $item['fileName'] . '" />
				<input type="submit" id="btn_crop" name="btn_crop" value="Crop Image" disabled="disabled" />
			</form>
			
			<script type="text/javascript">
			/*<![CDATA[*/
			document.observe("dom:loaded", function() {
				new Cropper.Img(
					\'cropThisImage\', {
						ratioDim: {
							x: ' . $item['width'] . ',
							y: ' . $item['height'] . '
						},
						displayOnInit: true,
						onEndCrop: endCrop
					}
				);
			});
			
			function endCrop(coords, dimensions) {
				$(\'x1\').value = coords.x1;
				$(\'x2\').value = coords.x2;
				$(\'y1\').value = coords.y1;
				$(\'y2\').value = coords.y2;
				$(\'width\').value = dimensions.width;
				$(\'height\').value = dimensions.height;
				
				if(dimensions.width == 0 || dimensions.height == 0) {
					$(\'btn_crop\').disabled = true;
				} else {
					$(\'btn_crop\').disabled = false;
				}
			}
			/*]]>*/
			</script>
		';
		
		return $str;
	}
	
	public function search() {
		
	}
}
?>