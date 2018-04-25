<?php
/**
 * $ID:  $
 *
 * Changes
 * -------
 *
 * Copyright (c) 2007 Trafficspaces Inc. All rights reserved.
 */
date_default_timezone_set('UTC');

define('Temp_Dir', '/tmp');

define('Content_Type_JAVASCRIPT',	'application/javascript');
define('Content_Type_JSON',	'application/json');
define('Content_Type_PLAIN',	'text/plain');

// Database-related configuration
define('Database_DSN', '');
define('Database_Username', 'sa');
define('Database_Password', '');

define('Employees_Table_Catalog', '');
define('Employees_Table_Schema', '');
define('Employees_Table_Name', '');

define('Verity_Image_Webroot', '');
define('Verity_Documents_Webroot', '');

define('Employee_Media_Webroot', '');

define('Employee_Media_Passport_Width', 120);
define('Employee_Media_Passport_Height', 140);
define('Employee_Media_Signature_Width', 140);
define('Employee_Media_Signature_Height', 120);
define('Employee_Media_Biometrics_Width', 120);
define('Employee_Media_Biometrics_Height', 140);

define('API_Username', '');
define('API_Password', '');
define('API_Endpoint_Employees', '');
define('API_PageSize', 25);
define('Database_BatchSize', 100);

define('Error_Log', '/tmp/php-error.log');
