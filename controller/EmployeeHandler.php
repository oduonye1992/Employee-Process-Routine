<?php

class EmployeeHandler {

	private $dataManager;
	private $apiClient;
	
	public function __construct($dataManager, $apiClient) {
		$this->dataManager = $dataManager;
		$this->apiClient = $apiClient;
	}
	
	public function doPull() {

		$startTime = microtime(true);
		$now = date('Y-m-d H:i:s');
		
		$stats = $this->dataManager->getLastChangedStats();

		$page = intval($stats['LAST_CHECKED_PAGE']); //echo "\nLAST CHECKED TIME: ".$from;
		$from = $stats['LAST_CHECKED_TIME']; //echo "\nLAST CHECKED TIME: ".$from;
		$from = date('Y-m-d\TH:i:s', strtotime($from));// echo "\nLAST CHECKED TIME: ".$from;
		$to = date("Y-m-d\TH:i:s"); //echo "CURRENT DATE: ".$to; 

		$metadata = getMetadata();	//print_r($metadata);

		$page = max($page, 0) + 1;
		$pagesize = API_PageSize;

		//START HERE
		$params = array('last_modified_date[0]' => $from, 'last_modified_date[1]' => $to, 'page' => $page, 'pagesize' => $pagesize);
		$employees = $this->apiClient->findEntities($params);
		$totalCount = count($employees);

		echo "\nLast page was $page. Retrieved " . $totalCount . " employees\n";


		$errorCount = 0;
		for ($i=0; $i < $totalCount; $i++) { 
			//check if user exists 
			try {
				$this->dataManager->storeEmployee($employees[$i], $metadata);
			} catch (Exception $e) { 
				//print_r($employees[$i]);
				print_r($e->getMessage());
				//print_r($e->getTrace()[0]);
		
				$this->dataManager->logSyncError($employees[$i], $e);
		
				//if (true) exit;
				$errorCount++;
				// /print_r($e); 
			}
		}
		
		//update the last checked time
		if ($totalCount >= $pagesize) {	
			//echo "\n RECORDS STILL EXISTS ON THE SERVER THAT MATCHES YOUR QUERY. . .STORING THE LAST PAGE CHECKED (PAGE: $page) . . .RUN AGAIN TO CONTINUE";
			$this->dataManager->updateSyncTime($from, $page);
		} else {
			//echo "\n ALL RECORDS FETCHED FROM THE SERVER AND PROCESSESED. UPDATING LAST_CHECKED_TIME TO $to";
			$this->dataManager->updateSyncTime($to, 0);
			
		}
		
		$meta = array(
			'duration' => (microtime(true) - $startTime),
			'count' => $totalCount,
			'errors' => $errorCount,
			'page' =>  $page,
			'time' => $to
		);
		
		return $meta;
	}	
}
