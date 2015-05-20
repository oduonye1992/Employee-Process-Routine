<?php
   
class APIClient {
	

	private $apiEndpoint;
	private $apiUsername;
	private $apiPassword;
	 
	public function __construct($apiEndpoint, $apiUsername, $apiPassword) {
		$this->apiEndpoint = $apiEndpoint;
		$this->apiUsername = $apiUsername;
		$this->apiPassword = $apiPassword;
	}

	public function sendRequest($method = 'GET', $data = '') {
    	
		$ch = curl_init();

		/* $_GET Parameters to Send */
					    
		$url = $this->apiEndpoint;

        $method = strtoupper($method);
        if($method == 'GET') {
        	$url .= '?' . $this->encodeParams($data);
        } else if($method == 'POST') {
        
        	curl_setopt($ch, CURLOPT_POST, true);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else if ($method == 'PUT') {
        
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else if($method != 'GET') {
        	 
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiUsername . ':' . $this->apiPassword);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	        'User-Agent: pfabackoffice-api-client-php',
	        'Content-Type: application/json; charset=UTF-8',
	        'Accept: application/json'
        ));
        
        $response = curl_exec($ch);	

		// Check for errors
        if ($response !== FALSE){
        	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        	$response = array("status" => $status, "body" => $response);
        }
        curl_close($ch);

		return $response;
	}
    
	public function findEntities($params) {
		//echo "GETTING DATA. . .\n";
		$allResults = array();
	
		print_r($params);

		//	echo 'POSTDATA:'; print_r($params);	echo '\n';
	
		if (!$result = $this->sendRequest('GET', $params)) {
			
			echo "\nERROR FETCHING NEW DATA";
			echo "\nPROCEEDING TO PROCESSING EXISTING DATA";
	
		} else if ($result && intval($result['status']) == 200 && count($data = json_decode($result['body']))  > 0) {
					
			for ($j = 0; $j < count($data); $j++) {
				array_push($allResults, $data[$j]);
			}
		}
		
		echo "Retrieved " . count($allResults) . " records\n";
		return $allResults;
	}
	
	public function storeEntities($entities) {
		
	}
	
	private function encodeParams($params) {
		$encodedParams = '';
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $subKey => $subValue) {
					$encodedParams .= urlencode($key) . '=' . urlencode($subValue) . '&';
				}
			} else {
				$encodedParams .= urlencode($key) . '=' . urlencode($value) . '&';
			}
		}
		return $encodedParams;
	}
}
