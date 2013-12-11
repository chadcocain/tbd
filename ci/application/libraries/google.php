<?php 
class Google {
	private $id;
	private $address;
	private $latitude;
	private $longitude;
	private $ci;
	
	public function __construct() {
		// set the code igniter instance
		$this->ci =& get_instance();
	}
	
	public function init($config = array()) {
		if(is_array($config) && count($config) > 0) {
			if(array_key_exists('id', $config) && !empty($config['id'])) {
				$this->setID($config['id']);
			}
			if(array_key_exists('address', $config) && !empty($config['address'])) {
				$this->setAddress($config['address']);
			}
		}
	}
	
	public function determineLatitudeAndLongitude() {
		$url = GEOCODER . urlencode($this->getAddress());
		$str = file_get_contents($url);
		// check that there are commas in this
		if(strstr($str, ',')) {
			// get the pieces by comma
			$pieces = explode(',', $str);
			// check to make sure we are least two in size
			if(count($pieces) > 2) {
				// get the latitude and longitude
				$this->setLatitude($pieces[0]);
				$this->setLongitude($pieces[1]);
				// check that we have numeric values
				if($this->getLatitude() != false && $this->getLongitude() != false) {
					// create an array of information to send
					$array = array(
						'id' => $this->getId()
						, 'lat' => $this->getLatitude()
						, 'long' => $this->getLongitude()
					);
					// set the latitude and longitude in the db
					$this->ci->EstablishmentModel->setLatitudeAndLongitude($array);
				}
			}			 
		}
	}
	
	public function determineLatitudeAndLongitudeViaGoogle() {
        $ch = curl_init();
        $post_array = array(
            'e_id' => $this->getId(),
            'address' => $this->getAddress()
        );
        curl_setopt($ch, CURLOPT_URL, base_url() . 'establishment/google_map_processor');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_array));
        curl_exec($ch);
                
        if (curl_errno($ch))
        {
            echo 'error: ' . curl_error($ch);
        }
        
        curl_close($ch);
    }
    
    public function determineLatitudeAndLongitudeViaGoogle_old() {
        // base url to google lat and long
		$base_url = MAPS_HOST . GOOGLE_XML_PATH . GOOGLEAPI;
		// full url including the current address
		$request_url = $base_url . '&q=' . urlencode($this->getAddress());
        echo $request_url; exit;
		// get the xml object
		$xml = simplexml_load_file($request_url);
		// get the status code
		$status = $xml->Response->Status->code;
		// check for success
    	if(strcmp($status, '200') == 0) {
    		// get the coordiante object from the xml
    		$coords = $xml->Response->Placemark->Point->coordinates;
    		// split the coords object up into pieces
    		$coordsExplode = explode(',', $coords);    		
    		// get the latitude and longitude
			$this->setLatitude($coordsExplode[1]);
			$this->setLongitude($coordsExplode[0]);
			// create an array of information to send
			$array = array(
				'id' => $this->getId()
				, 'lat' => $this->getLatitude()
				, 'long' => $this->getLongitude()
			);
			// set the latitude and longitude in the db
			$this->ci->EstablishmentModel->setLatitudeAndLongitude($array);
    	}
	}
	
	public function setid($int) {
		$this->id = $int;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setAddress($str) {
		$this->address = $str;
	}
	
	public function getAddress() {
		return $this->address;
	}
	
	public function setLatitude($decimal) {
		$bl = $this->checkDecimal($decimal);
		if($bl == true) {
			$this->latitude = $decimal;
		} else {
			$this->latitude = false;
		}
	}
	
	public function getLatitude() {
		return $this->latitude;
	}
	
	public function setLongitude($decimal) {
		$bl = $this->checkDecimal($decimal);
		if($bl == true) {
			$this->longitude = $decimal;
		} else {
			$this->longitude = false;
		}
	}
	
	public function getLongitude() {
		return $this->longitude;
	}
	
	private function checkDecimal($dec) {
		return is_numeric($dec);
	}
 }
?>