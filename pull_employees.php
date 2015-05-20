<?php

/*
 * Cron job to pull employees from the remote API server
 */

ini_set('log_errors', 1);
ini_set('error_log', Error_Log);

date_default_timezone_set('UTC');

header('Content-type: text/plain');

require_once('config.php');
require_once('controller/APIClient.php');
require_once('controller/DataManager.php');
require_once('controller/EmployeeHandler.php');

echo ">>>>>>>>>> Running pull_employees.php. Started: " . date("F j, Y, H:i:s") . " <<<<<<<<<<\n\n";

// Open a non-persistent connection
$employeesAPIClient = new APIClient(API_Endpoint_Employees, API_Username, API_Password);
$dataManager		= new EmployeeDataManager();

$employeeHandler	= new EmployeeHandler($dataManager, $employeesAPIClient);
$meta = $employeeHandler->doPull();

echo ">>>>>>>>>> Completed pull_employees.php. Duration: " . @$meta['duration'] . " seconds.  Total: "  . @$meta['count'] . ". Errors: ". @$meta['errors'] . ". Last Page: " . @$meta['page'] . ". Last Time " . @$meta['time'] . "\n";
