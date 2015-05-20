<?php

/*
 * Cron job to pull employees from the remote API server
 */

ini_set('log_errors', 1);


date_default_timezone_set('UTC');

header('Content-type: text/plain');

require_once('config.php');
require_once('controller/EmployeeDataManager.php');
require_once('controller/EmployeeImageHandler.php');
ini_set('error_log', Error_Log);

echo ">>>>>>>>>> Running c.php. Started: " . date('F j, Y, H:i:s') . " <<<<<<<<<<\n\n";

// Open a non-persistent connection
$dataManager = new EmployeeDataManager();

$employeeImageHandler = new EmployeeImageHandler($dataManager);
$meta = $employeeImageHandler->doProcessEmployeeImages();
//$meta = $employeeImageHandler->doProcessEmployeeDocuments();

echo ">>>>>>>>>> Completed pull_employees_media.php. Duration: " . @$meta['duration'] . " seconds.  Total: "  . @$meta['count'] . ". Errors: ". @$meta['errors'] . "\n";
