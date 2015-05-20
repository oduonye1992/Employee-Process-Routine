<?php

class EmployeeImageHandler {

	private $dataManager;
	
	public function __construct($dataManager) {
		$this->dataManager = $dataManager;
	}
	
	public function doProcessEmployeeImages() {

		$startTime = microtime(true);
		$now = date('Y-m-d H:i:s');
        $totalCount = 0;
        $errorCount = 0;
		
		$columns = array('PASSPORT' => 'passport', 'SIGNATURE' => 'signature', 
						 'LEFT_THUMB' => 'left thumb', 'LEFT_INDEX' => 'left index', 'LEFT_MIDDLE' => 'left middle', 'LEFT_RING' => 'left ring', 'LEFT_SMALL' => 'left small',
						 'RIGHT_THUMB' => 'right thumb', 'RIGHT_INDEX' => 'right index', 'RIGHT_MIDDLE' => 'right middle', 'RIGHT_RING' => 'right ring', 'RIGHT_SMALL' => 'right small');
		
		$updatableColumns = array();

		$employeeImages = $this->dataManager->getUnprocessedEmployeeImages(1);//Database_BatchSize);


		for ($i = 0; $i < count($employeeImages); $i++) {
			$employeeID = $employeeImages[$i]['REGISTRATION_CODE'];
            $prefix = 'employee:' . $employeeImages[$i]['REGISTRATION_CODE'];
			$dataError = false;
			
			foreach ($columns as $columnName => $columnTitle) {
				$filename = $employeeImages[$i][$columnName];
				if (!strlen($filename)) {
					continue;
				}
				
				$targetWidth = 0; $targetHeight = 0;
				
				$tempFileA = tempnam(Temp_Dir, ''); 
				$tempFileB = tempnam(Temp_Dir, '');
				rename($tempFileA, ($tempFileA = $tempFileA . '.jpg'));
				
				if ($columnName == 'PASSPORT') {
					$targetWidth = Employee_Media_Passport_Width;
					$targetHeight = Employee_Media_Passport_Height;
					rename($tempFileB, ($tempFileB = $tempFileB . '.jpg'));
				} else if ($columnName == 'SIGNATURE') {
					$targetWidth = Employee_Media_Signature_Width;
					$targetHeight = Employee_Media_Signature_Height;
					rename($tempFileB, ($tempFileB = $tempFileB . '.jpg'));
				} else {
					$targetWidth = Employee_Media_Biometrics_Width;
					$targetHeight = Employee_Media_Biometrics_Height;
					rename($tempFileB, ($tempFileB = $tempFileB . '.bmp'));
				}
				
				$url = Verity_Image_Webroot . $filename;

				$srcRawDataID = -1; $processedRawDataID = -1;

                if (($response = $this->downloadFile($url, $tempFileA)) && $response['status'] == 200) {

                    //print_r($response);
                    //return;
                    //echo $tempFileA;
                    $fh = file_get_contents($tempFileA);//fopen($tempFileA, 'r');
                     //die("stop");

					$srcRawDataID = $this->dataManager->storeRawData('ADMIN', "$prefix $columnTitle source", $tempFileA,
                        'image/jpeg', filesize($tempFileA), $fh);//$tempFileA);
                    //fclose($fh);

					if ($this->processImage($tempFileA, $tempFileB, $targetWidth, $targetHeight)) {
						$isBitmap = strrpos($tempFileB, '.bmp') == strlen($tempFileB) - 4;
						$processedRawDataID = $this->dataManager->storeRawData('ADMIN', "$prefix $columnTitle processed", $tempFileB, $isBitmap ? 'image/bmp' : 'image/jpeg', filesize($tempFileB), $tempFileB);
					}

				}

				if ($srcRawDataID > 0 && $processedRawDataID > 0) {

					$updatableColumns[$columnName . '_SOURCE_RAWDATA_ID'] = $srcRawDataID;
					$updatableColumns[$columnName . '_PROCESSED_RAWDATA_ID'] = $processedRawDataID;
					$updatableColumns[$columnName . '_SOURCE_URL'] = Employee_Media_Webroot . $srcRawDataID;
					$updatableColumns[$columnName . '_PROCESSED_URL'] = Employee_Media_Webroot . $processedRawDataID;
                    $totalCount++;
				} else {
					$dataError = true;
                    $errorCount++;
					break;
				}
			}

			$updatableColumns['IMAGES_PROCESSED'] = $dataError ? 'N' : 'Y';
            //print_r($updatableColumns);
			$this->dataManager->updateEmployeeColumns($employeeID, $updatableColumns);
		}
        $meta = array(
            'duration' => (microtime(true) - $startTime),
            'count' => $totalCount,
            'errors' => $errorCount
        );

        return $meta;
	}

	private function processImage($srcImageFile, $dstImageFile, $width, $height) {

		$image = new Imagick($srcImageFile);

		$image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
		$image->writeImage($dstImageFile);
		
		$image->destroy();
		return true;
	}
	
	private function storeRawData($title, $filename, $contentType, $file) {
		$rawDataID = -1;
		if ($this->dataManager->storeRawData('ADMIN', $title, $filename, $contentType, filesize($file), $file)) {
			//$rawDataID = $this->dataManager->getRawDataIDByTitle($title);
			$rawDataID = $this->dataManager->getLastInsertID();
		}
		return $rawDataID;	
	}
	


	// takes URL of image and Path for the image as parameter
	public function downloadFile($url, $filename){
		$fp = fopen ($filename, 'w+');              // open file handle
	
		$ch = curl_init($url);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
		curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
		// curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
		$response = curl_exec($ch);
		 
		// Check for errors
		if ($response !== FALSE){
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$response = array("status" => intval($status), "file" => $filename);
		}
		curl_close($ch);                              // closing curl handle
		fclose($fp);                                  // closing file handle
		 
		return $response;
	}
	
}
