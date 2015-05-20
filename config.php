<?php
/**
 * $ID:  $
 *
 * Changes
 * -------
 * 09-Nov-2007 : Version 1.0, contributed by Niyi Gbodimowo;
 * 28-Feb-2008 : Version 2.0, contributed by Niyi Gbodimowo
 * $ID: $
 *
 * Trafficspaces Ad Server
 *
 * Copyright (c) 2007 Trafficspaces Inc. All rights reserved.
 */
date_default_timezone_set('UTC');

//define('Temp_Dir', 'file://C:/tmp/log/pfabackoffice/media/');
define('Temp_Dir', '/tmp');

define('Content_Type_JAVASCRIPT',	'application/javascript');
define('Content_Type_JSON',	'application/json');
define('Content_Type_PLAIN',	'text/plain');

// Database-related configuration
//define('Database_DSN', 'sqlsrv:server=192.168.1.52\SQLEXPRESS;Database=STANBICIBTC');
define('Database_DSN', 'dblib:host=10.234.240.48\SQLEXPRESS;dbname=STANBICIBTC');
define('Database_Username', 'sa');
define('Database_Password', 'redc0dec!');

define('Employees_Table_Catalog', 'STANBICIBTC');
define('Employees_Table_Schema', 'dbo');
define('Employees_Table_Name', 'EMPLOYEES');

define('Verity_Image_Webroot', 'http://10.234.240.48/apps/');
define('Verity_Documents_Webroot', 'http://localhost/uploads/documents/');

define('Employee_Media_Webroot', 'http://localhost/uploads/media/');

define('Employee_Media_Passport_Width', 120);
define('Employee_Media_Passport_Height', 140);
define('Employee_Media_Signature_Width', 140);
define('Employee_Media_Signature_Height', 120);
define('Employee_Media_Biometrics_Width', 120);
define('Employee_Media_Biometrics_Height', 140);

define('API_Username', 'sipml');
define('API_Password', 'abcdefghijklmnopqwsrtuvwzyz');
define('API_Endpoint_Employees', 'http://sipml.backoffice.ng/api/contributor-registrations');
//define('API_Endpoint_Employees', 'http://requestb.in/r677v6r6');
define('API_PageSize', 25);
define('Database_BatchSize', 100);

define('Error_Log', '/tmp/php-error.log');
