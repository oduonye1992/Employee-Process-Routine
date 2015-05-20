<?php
   
class EmployeeDataManager {
	

	protected $pdo;
	
	
	public function __construct() {
		$this->connect();
	}
	
   	private function connect() {
	   	
	   	try {
			$this->pdo = new PDO(Database_DSN, Database_Username, Database_Password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		} catch (PDOException $e) {
			echo '\n ERROR CONNECTING TO DATABASE: '.$e->getMessage();
			$this->pdo = null;
		}
   	}

	public function storeEmployee($employee, $metadata) {
		if (isUserValid($employee->registration_code)) {
			//echo 'UPDATE\n';
			//check if the record is migrated
			if (isMigrated($employee->registration_code)) {
				//log
				echo 'Record with REG CODE'. $employee->registration_code. ' is already Migrated. Skipping now. . .  \n';
			} else {
				doAction($employee, 'UPDATE', $metadata);	
			}
			
		} else {
			//echo 'INSERT\n';
			doAction($employee, 'INSERT', $metadata);
		}
	}


	private function doAction($userObject, $action, $metadata) {
			
		//print_r($userObject);
		/*$dataMap = array(
			array('media.identity.is_checked', 'IMAGE_PROCESSED', null),
			array('step', 'STEP', null)
		}; */

		$status = '';
		switch ($userObject->status) {
			case 'unprocessed':  $status = 0; break;
			case 'unauthorized': $status = 1; break;
			case 'authorized': 	 $status = 2; break;
			case 'accepted':	 $status = 3; break;
			case 'rejected':	 $status = 4; break;
			case 'migrated':	 $status = 5; break;
		}

		$map = array (
			//array('COLUMN'=>'SEQ_NUM_ID'										, 'VALUE'=> isset($userObject->id) ? ($userObject->id)  										: NULL),
			array('COLUMN'=>'XREALM_CODE'										, 'VALUE'=> isset($userObject->realm_code)? ($userObject->realm_code)                       	: NULL),
			array('COLUMN'=>'XUSERNAME'			                        		, 'VALUE'=> isset($userObject->assignee) ? ($userObject->assignee)                      		: NULL),
			//array('COLUMN'=>'IS_ACTIVE'                        					, 'VALUE'=> $userObject->is_active ? 'Y'  : 'N'),
			array('COLUMN'=>'USERID'                       					 	, 'VALUE'=> isset($userObject->user_id) ? ($userObject->user_id)                      		: NULL),
			array('COLUMN'=>'REGISTRATION_TYPE'									, 'VALUE'=> isset($userObject->registration_type) ? ($userObject->registration_type)          : NULL),
			array('COLUMN'=>'REGISTRATION_CODE'									, 'VALUE'=> isset($userObject->registration_code) ? ($userObject->registration_code)          : NULL),
			array('COLUMN'=>'PFA_CODE'											, 'VALUE'=> isset($userObject->pfa_code) ? ($userObject->pfa_code)                      		: NULL),
	
			array('COLUMN'=>'TRANSFER_PFA_CODE'									, 'VALUE'=> isset($userObject->transfer_pfa_code) ? ($userObject->transfer_pfa_code)                      		: NULL),
			array('COLUMN'=>'CURRENT_PFA_NAME'									, 'VALUE'=> isset($userObject->current_pfa_name) ? ($userObject->current_pfa_name)                      		: NULL),
			array('COLUMN'=>'STATUS'											, 'VALUE'=> $status),
			array('COLUMN'=>'PIN'												, 'VALUE'=> isset($userObject->pin) ? ($userObject->pin)                      		: NULL),
			array('COLUMN'=>'PIN_DATE'											, 'VALUE'=> isset($userObject->pin_date) ? ($userObject->pin_date)                      		: NULL),
			array('COLUMN'=>'DATE_SENT_TO_PENCOM'								, 'VALUE'=> isset($userObject->pencom_transmission_date) ? ($userObject->pencom_transmission_date)                      		: NULL),
			array('COLUMN'=>'RECORD_MIGRATION_DATE'								, 'VALUE'=> isset($userObject->record_migration_date) ? ($userObject->record_migration_date)                      		: NULL),
			array('COLUMN'=>'PIN_INVALID'										, 'VALUE'=> isset($userObject->is_pin_invalid) && $userObject->is_pin_invalid ? 'Y' : 'N'),
			array('COLUMN'=>'PIN_COMMENTS'										, 'VALUE'=> isset($userObject->pin_comments) ? ($userObject->pin_comments)                      		: NULL),
			array('COLUMN'=>'DUPLICATE_VALID'                        			, 'VALUE'=> isset($userObject->is_duplicate_valid) && $userObject->is_duplicate_valid ? 'Y' : 'N'),
	
			array('COLUMN'=>'TITLE'                        						, 'VALUE'=> isset($userObject->title) ? ($userObject->title)                      		: NULL),
			array('COLUMN'=>'FIRSTNAME'                        					, 'VALUE'=> isset($userObject->first_name) ? ($userObject->first_name)                      		: NULL),
			array('COLUMN'=>'OTHERNAME'                        					, 'VALUE'=> isset($userObject->other_name) ? ($userObject->other_name)                      		: NULL),
			array('COLUMN'=>'SURNAME'                        					, 'VALUE'=> isset($userObject->last_name) ? ($userObject->last_name)                      		: NULL),
			array('COLUMN'=>'GENDER'                        					, 'VALUE'=> isset($userObject->gender) ? ($userObject->gender == 'male' ? 'M' : 'F')                      		: NULL),
			array('COLUMN'=>'DATE_OF_BIRTH'                        				, 'VALUE'=> isset($userObject->birth_date) ? ($userObject->birth_date)                      		: NULL),
			array('COLUMN'=>'MARITAL_STATUS'                        			, 'VALUE'=> isset($userObject->marital_status) ? ($userObject->marital_status)                      		: NULL),
			array('COLUMN'=>'MOTHER_MAIDEN_NAME'                        		, 'VALUE'=> isset($userObject->mother_maiden_name) ? ($userObject->mother_maiden_name)                      		: NULL),
			array('COLUMN'=>'EMAIL'                        						, 'VALUE'=> isset($userObject->email_address) ? ($userObject->email_address)                      		: NULL),
			array('COLUMN'=>'NATIONALITY'                        				, 'VALUE'=> isset($userObject->nationality) ? ($userObject->nationality)                      		: NULL),
			array('COLUMN'=>'MOBILE_PHONE'                        				, 'VALUE'=> isset($userObject->mobile_phone_number) ? ($userObject->mobile_phone_number)                      		: NULL),
			array('COLUMN'=>'MOBILE_PHONE_NUMBER_TWO'                        	, 'VALUE'=> isset($userObject->mobile_phone_number_two) ? ($userObject->mobile_phone_number_two)                      		: NULL),
			array('COLUMN'=>'CORRESPONDENCE_ADDS'                        		, 'VALUE'=> isset($userObject->address_line_one) ? ($userObject->address_line_one)                      		: NULL),
			array('COLUMN'=>'CORRESPONDENCE_ADDS1'                        		, 'VALUE'=> isset($userObject->address_line_two) ? ($userObject->address_line_two)                      		: NULL),
			array('COLUMN'=>'LGA_CODE'                        					, 'VALUE'=> isset($userObject->lga_code) ? ($userObject->lga_code)                      		: NULL),
			array('COLUMN'=>'CITY'                        						, 'VALUE'=> isset($userObject->city) ? ($userObject->city)                      		: NULL),
			array('COLUMN'=>'STATE_CODE'                        				, 'VALUE'=> isset($userObject->state_code) ? ($userObject->state_code)                      		: NULL),
			array('COLUMN'=>'INTERNATIONAL_ZIP_CODE'                        	, 'VALUE'=> isset($userObject->international_zip_code) ? ($userObject->international_zip_code)                      		: NULL),
			array('COLUMN'=>'INTERNATIONAL_MOBILE_PHONE_NUMBER'                 , 'VALUE'=> isset($userObject->international_mobile_phone_number) ? ($userObject->international_mobile_phone_number)                      		: NULL),
			array('COLUMN'=>'REGION'                        					, 'VALUE'=> isset($userObject->region_code) ? ($userObject->region_code)                      		: NULL),
			array('COLUMN'=>'RECORD_SOURCE'                        				, 'VALUE'=> isset($userObject->region_source) ? ($userObject->region_source)                      		: NULL),
			array('COLUMN'=>'STATE_OF_ORIGIN'                        			, 'VALUE'=> isset($userObject->state_of_origin) ? ($userObject->state_of_origin)                      		: NULL),
			array('COLUMN'=>'STATE_OF_POSTING'                        			, 'VALUE'=> isset($userObject->state_of_posting) ? ($userObject->state_of_posting)                      		: NULL),
			array('COLUMN'=>'RELIGION'                        					, 'VALUE'=> isset($userObject->religion) ? ($userObject->religion)                      		: NULL),
			array('COLUMN'=>'EMPLOYEE_ID'                        				, 'VALUE'=> isset($userObject->employee_id) ? ($userObject->employee_id)                      		: NULL),
			array('COLUMN'=>'DESIGNATION'                        				, 'VALUE'=> isset($userObject->designation) ? ($userObject->designation)                      		: NULL),
			array('COLUMN'=>'STEP'                        						, 'VALUE'=> isset($userObject->step) ? ($userObject->step)                      		: NULL),
			array('COLUMN'=>'GRADE_LEVEL'                        				, 'VALUE'=> isset($userObject->grade_level) ? ($userObject->grade_level)                      		: NULL),
			array('COLUMN'=>'QUALIFICATION'                        				, 'VALUE'=> isset($userObject->qualification) ? ($userObject->qualification)                      		: NULL),
			array('COLUMN'=>'SALARY_SCALE'                        				, 'VALUE'=> isset($userObject->salary_scale) ? ($userObject->salary_scale)                      		: NULL),
			array('COLUMN'=>'WEB_USER_ID'                        				, 'VALUE'=> isset($userObject->web_user_id) ? ($userObject->web_user_id)                      		: NULL),
			array('COLUMN'=>'PERSONAL_EMAIL_ADDRESS'                        	, 'VALUE'=> isset($userObject->personal_email_address) ? ($userObject->personal_email_address)                      		: NULL),
			array('COLUMN'=>'OFFICIAL_EMAIL_ADDRESS'                        	, 'VALUE'=> isset($userObject->official_email_address) ? ($userObject->official_email_address)                      		: NULL),
			array('COLUMN'=>'ID_NUMBER'                        					, 'VALUE'=> isset($userObject->id_number) ? ($userObject->id_number)                      		: NULL),
			//array('COLUMN'=>'TYPE_OF_ID'                        				, 'VALUE'=> isset($userObject->type_of_id) ? ($userObject->type_of_id)                      		: NULL),
	
			array('COLUMN'=>'EMPLOYMENT_TYPE'                        			, 'VALUE'=> isset($userObject->employment_type) ? ($userObject->employment_type)                      		: NULL),
			array('COLUMN'=>'DATE_CONFIRMED'                        			, 'VALUE'=> isset($userObject->confirmation_date) ? ($userObject->confirmation_date)                      		: NULL),
			array('COLUMN'=>'DATE_CREATED'                        				, 'VALUE'=> isset($userObject->record_creation_date) ? ($userObject->record_creation_date)                      		: NULL),
			array('COLUMN'=>'DATE_EMPLOYED'                        				, 'VALUE'=> isset($userObject->employment_date) ? ($userObject->employment_date)                      		: NULL),
			array('COLUMN'=>'PENCOM_CODE'                        				, 'VALUE'=> isset($userObject->employer->pencom_code) ? ($userObject->employer->pencom_code)                      		: NULL),
			array('COLUMN'=>'PENCOM_NAME'                        				, 'VALUE'=> isset($userObject->employer->pencom_name) ? ($userObject->employer->pencom_name)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_CODE'                        				, 'VALUE'=> isset($userObject->employer->code) ? ($userObject->employer->code)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_NAME'                        				, 'VALUE'=> isset($userObject->employer->name) ? ($userObject->employer->name)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_RCNO'                        				, 'VALUE'=> isset($userObject->employer->rc_number) ? ($userObject->employer->rc_number)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_ADDRESS'                        			, 'VALUE'=> isset($userObject->employer->address_line_one) ? ($userObject->employer->address_line_one)                      		: NULL),
			//array('COLUMN'=>'EMPLOYER_LGA_CODE'                        			, 'VALUE'=> isset($userObject->employer->lga_code) ? ($userObject->employer->lga_code)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_CITY'                        				, 'VALUE'=> isset($userObject->employer->city) ? ($userObject->employer->city)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_STATECODE'                        		, 'VALUE'=> isset($userObject->employer->state_code) ? ($userObject->employer->state_code)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_EMAIL_ADDRESS'                        	, 'VALUE'=> isset($userObject->employer->email_address) ? ($userObject->employer->email_address)                      		: NULL),
			array('COLUMN'=>'EMPLOYER_INTERNATIONAL_ZIP_CODE'                   , 'VALUE'=> isset($userObject->employer->international_zip_code) ? ($userObject->employer->international_zip_code)                      		: NULL),
			//array('COLUMN'=>'SECTOR_CLASSFICATION'                        		, 'VALUE'=> isset($userObject->employer->sector_classification) ? ($userObject->employer->sector_classification)                      		: NULL),
	
			array('COLUMN'=>'NOK_TITLE'                        					, 'VALUE'=> isset($userObject->nok1->title) ? ($userObject->nok1->title)                      			: NULL),
			array('COLUMN'=>'NOK_FIRSTNAME'         	               			, 'VALUE'=> isset($userObject->nok1->first_name) ? ($userObject->nok1->first_name)                      	: NULL),
			array('COLUMN'=>'NOK_OTHERNAME'     	                   			, 'VALUE'=> isset($userObject->nok1->other_name) ? ($userObject->nok1->other_name)                     		: NULL),
			array('COLUMN'=>'NOK_SURNAME'                        				, 'VALUE'=> isset($userObject->nok1->last_name) ? ($userObject->nok1->last_name)                      		: NULL),
			array('COLUMN'=>'NOK_GENDER'                        				, 'VALUE'=> isset($userObject->nok1->gender) ? ($userObject->nok1->gender == 'male' ? 'M' : 'F')                      		: NULL),
			array('COLUMN'=>'NOK_RELATIONSHIP'                        			, 'VALUE'=> isset($userObject->nok1->relationship) ? ($userObject->nok1->relationship)                   	: NULL),
			array('COLUMN'=>'NOK_EMAILADDRESS'                        			, 'VALUE'=> isset($userObject->nok1->email_address) ? ($userObject->nok1->email_address)                   	: NULL),
			array('COLUMN'=>'NOK_MOBILE_PHONE'                        			, 'VALUE'=> isset($userObject->nok1->mobile_phone_number) ? ($userObject->nok1->mobile_phone_number)                 : NULL),
			array('COLUMN'=>'NOK_ADDRESS'                     		   			, 'VALUE'=> isset($userObject->nok1->address_line_one) ? ($userObject->nok1->address_line_one)                    : NULL),
			array('COLUMN'=>'NOK_CITY'                        					, 'VALUE'=> isset($userObject->nok1->city) ? ($userObject->nok1->city)                      			: NULL),
			array('COLUMN'=>'NOK_STATECODE'                        				, 'VALUE'=> isset($userObject->nok1->state_code) ? ($userObject->nok1->state_code)                      	: NULL),
			array('COLUMN'=>'NOK_COUNTRY'  	                      				, 'VALUE'=> isset($userObject->nok1->country_code) ? ($userObject->nok1->country_code)                      		: NULL),
			array('COLUMN'=>'NOK_INTERNATIONAL_ZIP_CODE'          				, 'VALUE'=> isset($userObject->nok1->international_zip_code) ? ($userObject->nok1->international_zip_code)              : NULL),
			array('COLUMN'=>'NOK_INTERNATIONAL_MOBILE_PHONE_NUMBER'             , 'VALUE'=> isset($userObject->nok1->international_mobile_phone_number) ? ($userObject->nok1->international_mobile_phone_number)   : NULL),
	
	
			array('COLUMN'=>'NOK2_TITLE'                        				, 'VALUE'=> isset($userObject->nok2->title) ? ($userObject->nok2->title)                      			: NULL),
			array('COLUMN'=>'NOK2_FIRSTNAME'         	               			, 'VALUE'=> isset($userObject->nok2->first_name) ? ($userObject->nok2->first_name)                      	: NULL),
			array('COLUMN'=>'NOK2_OTHERNAME'     	                   			, 'VALUE'=> isset($userObject->nok2->other_name) ? ($userObject->nok2->other_name)                     		: NULL),
			array('COLUMN'=>'NOK2_SURNAME'                        				, 'VALUE'=> isset($userObject->nok2->last_name) ? ($userObject->nok2->last_name)                      		: NULL),
			array('COLUMN'=>'NOK2_GENDER'                        				, 'VALUE'=> isset($userObject->nok2->gender) ? ($userObject->nok2->gender == 'male' ? 'M' : 'F')                      		: NULL),
			array('COLUMN'=>'NOK2_RELATIONSHIP'                        			, 'VALUE'=> isset($userObject->nok2->relationship) ? ($userObject->nok2->relationship)                   	: NULL),
			array('COLUMN'=>'NOK2_EMAILADDRESS'                        			, 'VALUE'=> isset($userObject->nok2->email_address) ? ($userObject->nok2->email_address)                   	: NULL),
			array('COLUMN'=>'NOK2_MOBILE_PHONE'                        			, 'VALUE'=> isset($userObject->nok2->mobile_phone_number) ? ($userObject->nok2->mobile_phone_number)                 : NULL),
			array('COLUMN'=>'NOK2_ADDRESS'                     		   			, 'VALUE'=> isset($userObject->nok2->address_line_one) ? ($userObject->nok2->address_line_one)                    : NULL),
			array('COLUMN'=>'NOK2_CITY'                        					, 'VALUE'=> isset($userObject->nok2->city) ? ($userObject->nok2->city)                      			: NULL),
			array('COLUMN'=>'NOK2_STATECODE'                        			, 'VALUE'=> isset($userObject->nok2->state_code) ? ($userObject->nok2->state_code)                      	: NULL),
			array('COLUMN'=>'NOK2_COUNTRY'  	                      			, 'VALUE'=> isset($userObject->nok2->country_code) ? ($userObject->nok2->country_code)                      		: NULL),
			array('COLUMN'=>'NOK2_INTERNATIONAL_ZIP_CODE'          				, 'VALUE'=> isset($userObject->nok2->international_zip_code) ? ($userObject->nok2->international_zip_code)              : NULL),
			array('COLUMN'=>'NOK2_INTERNATIONAL_MOBILE_PHONE_NUMBER'            , 'VALUE'=> isset($userObject->nok2->international_mobile_phone_number) ? ($userObject->nok2->international_mobile_phone_number)   : NULL),
	
			array('COLUMN'=>'AGENT_CODE'                        				, 'VALUE'=> isset($userObject->agent->code) ? ($userObject->agent->code)                      		: NULL),
			array('COLUMN'=>'AGENT_NAME'                        				, 'VALUE'=> isset($userObject->agent->name) ? ($userObject->agent->name)                      		: NULL),
			array('COLUMN'=>'AGENT_LOCATION'                        			, 'VALUE'=> isset($userObject->agent->location) ? ($userObject->agent->location)                      		: NULL),
			array('COLUMN'=>'AGENT_PHONE'                        				, 'VALUE'=> isset($userObject->agent->phone_number) ? ($userObject->agent->phone_number)                      		: NULL),
	
			array('COLUMN'=>'REPRESENTATIVE_NAME'                        		, 'VALUE'=> isset($userObject->representive_name) ? ($userObject->agent->representive_name)                      		: NULL),
			array('COLUMN'=>'REPRESENTATIVE_DESIGNATION'                        , 'VALUE'=> isset($userObject->representative_designation) ? ($userObject->representative_designation)                      		: NULL),
	
			array('COLUMN'=>'ANNUAL_BASIC'                        				, 'VALUE'=> isset($userObject->financials->annual_basic) ? doubleval($userObject->financials->annual_basic)    : NULL),
			array('COLUMN'=>'ANNUAL_RENT'                        				, 'VALUE'=> isset($userObject->financials->annual_rent) ? doubleval($userObject->financials->annual_rent)    : NULL),
			array('COLUMN'=>'ANNUAL_TRANSPORT'                        			, 'VALUE'=> isset($userObject->financials->annual_transport) ? doubleval($userObject->financials->annual_transport)    : NULL),
			array('COLUMN'=>'MONTHLY_EMPLOYEE_CONT'		                     	, 'VALUE'=> isset($userObject->financials->monthly_employee_contribution) ? doubleval($userObject->financials->monthly_employee_contribution)    : NULL),
			array('COLUMN'=>'MONTHLY_EMPLOYER_CONT'                     		, 'VALUE'=> isset($userObject->financials->monthly_employer_contribution) ? doubleval($userObject->financials->monthly_employer_contribution)    : NULL),
			array('COLUMN'=>'TOTAL_MONTHLY_CONT'                        		, 'VALUE'=> isset($userObject->financials->total_monthly_contribution) ? doubleval($userObject->financials->total_monthly_contribution)    : NULL),
			array('COLUMN'=>'VOLUNTARY_CONTRIBUTION'                      		, 'VALUE'=> isset($userObject->financials->voluntary_contribution) ? doubleval($userObject->financials->voluntary_contribution)    : NULL),
			array('COLUMN'=>'MONTHLY_CONTRIBUTION_INFORMAL'                     , 'VALUE'=> isset($userObject->financials->monthly_informal_contribution) ? doubleval($userObject->financials->monthly_informal_contribution)    : NULL),
	
			array('COLUMN'=>'OTHER_PENSIONABLE_ALLOWANCE'                       , 'VALUE'=> isset($userObject->financials->other_pensionable_allowance) ? doubleval($userObject->financials->other_pensionable_allowance)                      		: NULL),
			array('COLUMN'=>'INVEST_PERCENTAGE2'                        		, 'VALUE'=> isset($userObject->financials->investment_percentage_two) ? ($userObject->financials->investment_percentage_two)                      		: NULL),
			array('COLUMN'=>'INVEST_PRODUCT2'                        			, 'VALUE'=> isset($userObject->financials->investment_product_two) ? ($userObject->financials->investment_product_two)                      		: NULL),
			array('COLUMN'=>'CURRENT_RSA_BALANCE'                        		, 'VALUE'=> isset($userObject->financials->current_rsa_balance) ? doubleval($userObject->financials->current_rsa_balance)                      		: NULL),
			array('COLUMN'=>'SALARY_STRUCTURE_2004'                        		, 'VALUE'=> isset($userObject->financials->salary_structure_2004) ? ($userObject->financials->salary_structure_2004)                      		: NULL),
			array('COLUMN'=>'SALARY_STRUCTURE_2007'                        		, 'VALUE'=> isset($userObject->financials->salary_structure_2007) ? ($userObject->financials->salary_structure_2007)                      		: NULL),
			array('COLUMN'=>'SALARY_STRUCTURE_2010'                        		, 'VALUE'=> isset($userObject->financials->salary_structure_2010) ? ($userObject->financials->salary_structure_2010)                      		: NULL),
			array('COLUMN'=>'GL_2004'                        					, 'VALUE'=> isset($userObject->financials->gl_2004) ? ($userObject->financials->gl_2004)                      		: NULL),
			array('COLUMN'=>'GL_2007'                        					, 'VALUE'=> isset($userObject->financials->gl_2007) ? ($userObject->financials->gl_2007)                      		: NULL),
			array('COLUMN'=>'GL_2010'                        					, 'VALUE'=> isset($userObject->financials->gl_2010) ? ($userObject->financials->gl_2010)                      		: NULL),
			array('COLUMN'=>'STEP_2004'                        					, 'VALUE'=> isset($userObject->financials->step_2004) ? ($userObject->financials->step_2004)                      		: NULL),
			array('COLUMN'=>'STEP_2007'                        					, 'VALUE'=> isset($userObject->financials->step_2007) ? ($userObject->financials->step_2007)                      		: NULL),
			array('COLUMN'=>'STEP_2010'                        					, 'VALUE'=> isset($userObject->financials->STEP_2010) ? ($userObject->financials->STEP_2010)                      		: NULL),
	
			array('COLUMN'=>'STMT_OPTION'                        				, 'VALUE'=> isset($userObject->statement_option) ? ($userObject->statement_option)                      		: NULL),
			array('COLUMN'=>'SIGNUP_MODE'                        				, 'VALUE'=> isset($userObject->signup_mode) ? ($userObject->signup_mode)                      		: NULL),
	
			array('COLUMN'=>'PASSPORT_SOURCE_RAWDATA_URLS'                      , 'VALUE'=> isset($userObject->media->passport_source_url) ? ($userObject->media->passport_source_url)                      		: NULL),
			array('COLUMN'=>'SIGNATURE_SOURCE_RAWDATA_URLS'                     , 'VALUE'=> isset($userObject->media->signature_source_url) ? ($userObject->media->signature_source_url)                      		: NULL),
			array('COLUMN'=>'LEFT_THUMB_SOURCE_RAWDATA_URLS'                    , 'VALUE'=> isset($userObject->media->left_thumb_source_url) ? ($userObject->media->left_thumb_source_url)                      		: NULL),
			array('COLUMN'=>'LEFT_INDEX_SOURCE_RAWDATA_URLS'                    , 'VALUE'=> isset($userObject->media->left_index_source_url) ? ($userObject->media->left_index_source_url)                      		: NULL),
			array('COLUMN'=>'LEFT_MIDDLE_SOURCE_RAWDATA_URLS'                   , 'VALUE'=> isset($userObject->media->left_middle_source_url) ? ($userObject->media->left_middle_source_url)                      		: NULL),
			array('COLUMN'=>'LEFT_RING_SOURCE_RAWDATA_URLS'                     , 'VALUE'=> isset($userObject->media->left_ring_source_url) ? ($userObject->media->left_ring_source_url)                      		: NULL),
			array('COLUMN'=>'LEFT_SMALL_SOURCE_RAWDATA_URLS'                    , 'VALUE'=> isset($userObject->media->left_small_source_url) ? ($userObject->media->left_small_source_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_THUMB_SOURCE_RAWDATA_URLS'                  	, 'VALUE'=> isset($userObject->media->right_thumb_source_url) ? ($userObject->media->right_thumb_source_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_INDEX_SOURCE_RAWDATA_URLS'                   , 'VALUE'=> isset($userObject->media->right_index_source_url) ? ($userObject->media->right_index_source_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_MIDDLE_SOURCE_RAWDATA_URLS'                  , 'VALUE'=> isset($userObject->media->right_middle_source_url) ? ($userObject->media->right_middle_source_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_RING_SOURCE_RAWDATA_URLS'                    , 'VALUE'=> isset($userObject->media->right_ring_source_url) ? ($userObject->media->right_ring_source_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_SMALL_SOURCE_RAWDATA_URLS'                   , 'VALUE'=> isset($userObject->media->right_small_source_url) ? ($userObject->media->right_small_source_url)                      		: NULL),
			array('COLUMN'=>'RSA_FORM_IMAGE_SOURCE_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->rsa_form_source_url) ? ($userObject->media->rsa_form_source_url)                      		: NULL),
			array('COLUMN'=>'TRANSFER_FORM_IMAGE_SOURCE_RAWDATA_URLS'           , 'VALUE'=> isset($userObject->media->transfer_form_source_url) ? ($userObject->media->transfer_form_source_url)                      		: NULL),
			array('COLUMN'=>'OFFICIAL_ID_SOURCE_RAWDATA_URLS'                   , 'VALUE'=> isset($userObject->media->official_id_source_url) ? ($userObject->media->official_id_source_url)                      		: NULL),
			array('COLUMN'=>'PROOF_OF_AGE_SOURCE_RAWDATA_URLS'                  , 'VALUE'=> isset($userObject->media->proof_of_age_source_url) ? ($userObject->media->proof_of_age_source_url)                      		: NULL),
			array('COLUMN'=>'PROOF_OF_SALARY_SOURCE_RAWDATA_URLS'               , 'VALUE'=> isset($userObject->media->proof_of_salary_source_url) ? ($userObject->media->proof_of_salary_source_url)                      		: NULL),
			array('COLUMN'=>'APPOINTMENT_LETTER_SOURCE_RAWDATA_URLS'            , 'VALUE'=> isset($userObject->media->appointment_letter_source_url) ? ($userObject->media->appointment_letter_source_url)                      		: NULL),
			array('COLUMN'=>'WELCOME_LETTER_SOURCE_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->welcome_letter_source_url) ? ($userObject->media->welcome_letter_source_url)                      		: NULL),
			array('COLUMN'=>'WITHDRAWAL_LETTER_SOURCE_RAWDATA_URLS'             , 'VALUE'=> isset($userObject->media->withdrawal_letter_source_url) ? ($userObject->media->withdrawal_letter_source_url)                      		: NULL),
			array('COLUMN'=>'RETIREMENT_LETTER_SOURCE_RAWDATA_URLS'             , 'VALUE'=> isset($userObject->media->retirement_letter_source_url) ? ($userObject->media->retirement_letter_source_url)                      		: NULL),
			array('COLUMN'=>'STATEMENT_HISTORY_SOURCE_RAWDATA_URLS'             , 'VALUE'=> isset($userObject->media->statement_history_source_url) ? ($userObject->media->statement_history_source_url)                      		: NULL),
			array('COLUMN'=>'AUTHORIZED_SIGNATURE_A_SOURCE_RAWDATA_URLS'        , 'VALUE'=> isset($userObject->media->authorized_signature_a_source_url) ? ($userObject->media->authorized_signature_a_source_url)                      		: NULL),
			array('COLUMN'=>'AUTHORIZED_SIGNATURE_B_SOURCE_RAWDATA_URLS'        , 'VALUE'=> isset($userObject->media->authorized_signature_b_source_url) ? ($userObject->media->authorized_signature_b_source_url)                      		: NULL),
			array('COLUMN'=>'SIGNUP_MODE_SIGNATURE_SOURCE_RAWDATA_URLS'         , 'VALUE'=> isset($userObject->media->signup_mode_signature_source_url) ? ($userObject->media->signup_mode_signature_source_url)                      		: NULL),
			array('COLUMN'=>'NOTIFICATION_MODE_SIGNATURE_SOURCE_RAWDATA_URLS'   , 'VALUE'=> isset($userObject->media->notification_mode_signature_source_url) ? ($userObject->media->notification_mode_signature_source_url)                      		: NULL),
			array('COLUMN'=>'PASSPORT_PROCESSED_RAWDATA_URLS'                   , 'VALUE'=> isset($userObject->media->passport_processed_url) ? ($userObject->media->passport_processed_url)                      		: NULL),
			array('COLUMN'=>'SIGNATURE_PROCESSED_RAWDATA_URLS'                  , 'VALUE'=> isset($userObject->media->signature_processed_url) ? ($userObject->media->signature_processed_url)                      		: NULL),
			array('COLUMN'=>'LEFT_THUMB_PROCESSED_RAWDATA_URLS'                 , 'VALUE'=> isset($userObject->media->left_thumb_processed_url) ? ($userObject->media->left_thumb_processed_url)                      		: NULL),
			array('COLUMN'=>'LEFT_INDEX_PROCESSED_RAWDATA_URLS'                 , 'VALUE'=> isset($userObject->media->left_index_processed_url) ? ($userObject->media->left_index_processed_url)                      		: NULL),
			array('COLUMN'=>'LEFT_MIDDLE_PROCESSED_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->left_middle_processed_url) ? ($userObject->media->left_middle_processed_url)                      		: NULL),
			array('COLUMN'=>'LEFT_RING_PROCESSED_RAWDATA_URLS'                  , 'VALUE'=> isset($userObject->media->left_ring_processed_url) ? ($userObject->media->left_ring_processed_url)                      		: NULL),
			array('COLUMN'=>'LEFT_SMALL_PROCESSED_RAWDATA_URLS'                 , 'VALUE'=> isset($userObject->media->left_small_processed_url) ? ($userObject->media->left_small_processed_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_THUMB_PROCESSED_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->right_thumb_processed_url) ? ($userObject->media->right_thumb_processed_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_INDEX_PROCESSED_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->right_index_processed_url) ? ($userObject->media->right_index_processed_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_MIDDLE_PROCESSED_RAWDATA_URLS'               , 'VALUE'=> isset($userObject->media->right_middle_processed_url) ? ($userObject->media->right_middle_processed_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_RING_PROCESSED_RAWDATA_URLS'                 , 'VALUE'=> isset($userObject->media->right_ring_processed_url) ? ($userObject->media->right_ring_processed_url)                      		: NULL),
			array('COLUMN'=>'RIGHT_SMALL_PROCESSED_RAWDATA_URLS'                , 'VALUE'=> isset($userObject->media->right_small_processed_url) ? ($userObject->media->right_small_processed_url)                      		: NULL),
	
			array('COLUMN'=>'IMAGES_PROCESSED'                     				, 'VALUE'=> isset($userObject->media->identity->is_checked) && $userObject->media->identity->is_checked ? 'Y'                      		: 'N'),
			array('COLUMN'=>'DOCUMENTS_PROCESSED'                        		, 'VALUE'=> isset($userObject->media->documentation->is_checked) && $userObject->media->documentation->is_checked ? 'Y'                      		: 'N'),
	
			array('COLUMN'=>'CALL_OVER_CHECKED'                     			, 'VALUE'=> isset($userObject->is_callover_checked) && $userObject->is_callover_checked ? 'Y'                      		: 'N'),
			array('COLUMN'=>'LAST_MAKER_DATE'                        			, 'VALUE'=> isset($userObject->media->last_maker_date) ? ($userObject->media->last_maker_date)                      		: NULL),
			array('COLUMN'=>'LAST_MAKER'                        				, 'VALUE'=> isset($userObject->media->last_maker) ? ($userObject->media->last_maker)                      		: NULL),
			array('COLUMN'=>'LAST_CHECKER_DATE'                        			, 'VALUE'=> isset($userObject->media->last_checker_date) ? ($userObject->media->last_checker_date)                      		: NULL),
			array('COLUMN'=>'LAST_CHECKER'                        				, 'VALUE'=> isset($userObject->media->last_checker) ? ($userObject->media->last_checker)                      		: NULL),
			//array('COLUMN'=>'DATE_CREATED'                        				, 'VALUE'=> isset($userObject->media->pfa_code) ? ($userObject->media->created_date)                      		: NULL),
			array('COLUMN'=>'LAST_MODIFIED_DATE'								, 'VALUE'=> isset($userObject->media->pfa_code) ? ($userObject->media->last_modified_date)                      		: NULL)
		);

		//print_r($map); exit;
		//	$metadatap[]
		//echo $metadata[$map[1]['COLUMN']]; exit;
		if ($action == 'INSERT') {
			$insertSql = 'INSERT INTO EMPLOYEES (';
			for ($i=0; $i < count($map) ; $i++) { 
				$insertSql .= $map[$i]['COLUMN'];
				if ($i != count($map) - 1) {
					$insertSql .= ',';		
				}
			}
			$insertSql .= ')' ;
			$insertSql .= 'VALUES (';
			for ($i=0; $i < count($map) ; $i++) { 
				if (empty($map[$i]['VALUE'])) {
					if ($metadata[$map[$i]['COLUMN']]['IS_NULLABLE']) {
						$formatted = 'NULL';
					} else if ($metadata[$map[$i]['COLUMN']]['IS_NUMERIC']) {
						$formatted = 0;
					} else if ($metadata[$map[$i]['COLUMN']]['IS_DATE']) {
						$formatted = '\'1753-01-01\'';
					} else {
						$formatted = '\'\'';
					}
					$insertSql .= $formatted;		
	
				} else {
					if ($metadata[$map[$i]['COLUMN']]['IS_NUMERIC']) {
						$formatted = $map[$i]['VALUE'];
					} else {
						$formatted = $this->pdo->quote($map[$i]['VALUE']);
					}
					$insertSql .= $formatted;		
				}
				if ($i != count($map) - 1) {
					$insertSql .= ',';		
				}
			}
			$insertSql .= ')';  //echo $qq;exit;
			//try {
				$this->pdo->exec($insertSql);	
			/*} catch (Exception $e) {
				echo $e->getMessage();
				echo $insertSql;	
				exit;		
			}*/
			
			echo '\nRecord inserted:'. $userObject->registration_code;
	
		} else if  ($action == 'UPDATE') {
			//update
			$updateSql = 'UPDATE EMPLOYEES SET ';
			for ($i=0; $i < count($map) ; $i++) { 
				if (empty($map[$i]['VALUE'])) {
					if ($metadata[$map[$i]['COLUMN']]['IS_NULLABLE']) {
						$formatted = 'NULL';
					} else if ($metadata[$map[$i]['COLUMN']]['IS_NUMERIC']) {
						$formatted = 0;
					} else if ($metadata[$map[$i]['COLUMN']]['IS_DATE']) {
						$formatted = '\'1753-01-01\'';
					} else {
						$formatted = '\'\'';
					}
					$updateSql .= $map[$i]['COLUMN'] .' = '. $formatted;
				} else {
					if ($metadata[$map[$i]['COLUMN']]['IS_NUMERIC']) {
						$formatted = $map[$i]['VALUE'];
					} else {
						$formatted = $this->pdo->quote($map[$i]['VALUE']);
					}
					$updateSql .= $map[$i]['COLUMN'] .' = '. $formatted;	
				}
			
				if ($i != count($map) - 1) {
					$updateSql .= ',';		
				}
			}
			$updateSql .= ' WHERE REGISTRATION_CODE = '. $this->pdo->quote($userObject->registration_code);
			
			//try {
			$this->pdo->exec($updateSql);	
	
			/*} catch (Exception $e) {
				echo $e->getMessage();
				echo $updateSql;
				exit;
			
			}*/
			echo '\nRecord updated:'. $userObject->registration_code;
		
		}
	
		/*UPDATE EMPLOYEES SET AD = 'AS'
		
			for($i = 0; $i < count($dataMap); $i++) {
				$jsonKey		= $dataMap[$i][0];
				$columnName		= $dataMap[$i][1];
		
				$currentVal		= &$userObject;
				$parts = explode($jsonKey, '.');
				for ($j = 0; $j < $num; $j++) {
					$currentVal = &$currentVal[$parts[$j]];
				}
				switch ($parts[$numParts - 1]) {
					case 'is_duplicate_valid':
					case 'is_checked':
				}
				$dataMap[$i][2]	= $currentVal;
			}	
			*/
	}  
	
	public function getLastInsertID() {
		return $this->pdo->lastInsertId();
	}
	
	private function getRemoteIDsWithSyncErrors($limit = 1) {
	
		$sql = 'SELECT TOP :limit REMOTE_ID ' .
				' FROM EMPLOYEE_SYNC_ERRORS ' .
				' WHERE IS_ACTIVE = :is_active ' .
				' ORDER BY LAST_CHECKED_TIME ';
	
		$params = array(':is_active' => 'Y');
	
		$remoteIDs = null;
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetchAll();
			
			$remoteIDs= array();
			
			for ($i = 0; $i < count($resultSet) ; $i++) {
				array_push($remoteIDs, $resultSet[$i]['REMOTE_ID']);
			}
		}
	
		return $remoteIDs;
	}

	public function logSyncError($employee, $exception) {
		if ($this->hasError($employee)) {
			$this->updateSyncError($employee, true, $$exception->getMessage());
		} else {
			$this->createSyncError($employee, $exception->getMessage());
		}
	}
	
	public function resolveSyncError($employee) {
		$this->updateSyncError($employee, false);
	}
	
	private function hasSyncError($employee) {
	
		$sql = 'SELECT COUNT(SEQ_NUM_ID) as NUM_RECORDS ' .
				' FROM EMPLOYEE_SYNC_ERRORS ' .
				' WHERE REMOTE_ID = :remote_id';
	
		$params = array(':remote_id' => $employee->id);
	
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetch();
		}
	
		return isset($resultSet) && intval($resultSet['NUM_RECORDS']) > 0;
	}
	
	private function createSyncError($employee, $errorMessage = null) {
	
		$sql = 'INSERT INTO EMPLOYEE_SYNC_ERRORS (REMOTE_ID, IS_ACTIVE, REGISTRATION_CODE, LAST_CHECKED_TIME, ERROR_MESSAGE) ' .
					'  (:remote_id, :is_active, :registration_code, :last_date, :error_message) ';
	
		$params = array(':remote_id' => $employee->id,
						':is_active' => 'Y',
						':registration_code' => $employee->registration_code,
						':last_date' => date('Y-m-d H:i:s'),
						':error_message' => $errorMessage);
	
		$statement = $this->pdo->prepare($sql);
		return $statement->execute($params);
	}

	private function updateSyncError($employee, $isActive = true, $errorMessage = null) {
	
		$sql = 'UPDATE EMPLOYEE_SYNC_ERRORS SET ' .
				'  IS_ACTIVE = :is_active ' .
				'  LAST_CHECKED_TIME = :last_date ' .
				($errorMessage != null ? ' ERROR_MESSAGE = :error_message ' : '') .
				' WHERE REMOTE_ID = :remote_id ';
	
		$params = array(':is_active' => $isActive ? 'Y' : 'N',
				':last_date' => date('Y-m-d H:i:s'),
				':error_message' => $errorMessage,
				':remote_id' => $employee->id);
	
		$statement = $this->pdo->prepare($sql);
		return $statement->execute($params);
	}
	
	
	public function updateSyncTime($lastDate, $lastPage) {
	
		$sql = 'UPDATE EMPLOYEE_SYNC_STATS SET ' .
					'  LAST_CHECKED_TIME = :last_date, ' .
					'  LAST_CHECKED_PAGE = :last_page ';
	
		$params = array(':last_date' => $lastDate,
						':last_page' => $lastPage);
	
		$statement = $this->pdo->prepare($sql);
		return $statement->execute($params);
	}

	public function getLastChangedStats($regCode) {
	
		$sql = 'SELECT LAST_CHECKED_TIME, LAST_CHECKED_PAGE ' .
				' FROM EMPLOYEE_SYNC_STATS ';
	
		$statement = $this->pdo->prepare($sql)->execute();
	
		$resultSet = $statement->fetch();
	
		return $resultSet;
	}

	private function isUserValid($regCode) {
	
		$sql = 'SELECT COUNT(SEQ_NUM_ID) as NUM_RECORDS ' .
					' FROM EMPLOYEES ' .
					' WHERE REGISTRATION_CODE = :registration_code';
	
		$params = array(':registration_code' => $regCode);
	
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetch();
		}
	
		return isset($resultSet) && intval($resultSet['NUM_RECORDS']) > 0;
	}
	
	private function isMigrated($regCode) {
		
		$sql = 'SELECT STATUS ' .
					' FROM EMPLOYEES ' .
					' WHERE REGISTRATION_CODE = :registration_code';
		
		$params = array(':registration_code' => $regCode);
		
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetch();
		}

		return isset($resultSet) && $resultSet['STATUS'] == 5;
	}
	
	private function getMetadata() {
		$sql = 'SELECT COLUMN_NAME, IS_NULLABLE, NUMERIC_PRECISION, DATETIME_PRECISION ' .
					' FROM INFORMATION_SCHEMA.COLUMNS ' .
						' WHERE TABLE_CATALOG = :table_catalog AND '. 
							' TABLE_SCHEMA = :table_schema AND ' .
							' TABLE_NAME = :table_name ';
		
		$params = array(':table_catalog' => Employees_Table_Catalog, 
						':table_schema' => Employees_Table_Schema, 
						':table_name' => Employees_Table_Name);
		
		$metadata = null;
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetchAll();
			$metadata = array();
	
			for ($i = 0; $i < count($resultSet); $i++) { 
				$metadata[$resultSet[$i]['COLUMN_NAME']] = array(
					'IS_NULLABLE' => $resultSet[$i]['IS_NULLABLE'] == 'YES', 
					'IS_NUMERIC' => is_numeric($resultSet[$i]['NUMERIC_PRECISION']), 
					'IS_DATE' => is_numeric($resultSet[$i]['DATETIME_PRECISION'])
				);
			}
		}
			
		return $metadata;
	}
	

	public function getUnprocessedEmployeeImages($limit = 1) {
		$sql = 'SELECT TOP ' . $limit . ' SEQ_NUM_ID, REGISTRATION_CODE, PASSPORT, SIGNATURE ' .
					' LEFT_THUMB, LEFT_INDEX, LEFT_MIDDLE, LEFT_RING, LEFT_SMALL, ' .
					' RIGHT_THUMB, RIGHT_INDEX, RIGHT_MIDDLE, RIGHT_RING, RIGHT_SMALL ' .
				' FROM EMPLOYEES ' .
				' WHERE IMAGES_PROCESSED = :images_processed ';
	
		$params = array(':images_processed' => 'N');

        //echo 'sql=' . $sql;
		$statement = $this->pdo->prepare($sql);
        //print_r($statement);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetchAll();
		}
			
		return $resultSet;
	}
	
	public function getUnprocessedEmployeeDocuments($limit = 1) {
		$sql = 'SELECT TOP ' . $limit . ' SEQ_NUM_ID, REGISTRATION_CODE, RSA_FORM_IMAGE, TRANSFER_FORM_IMAGE, OFFICIAL_ID, PROOF_OF_AGE, PROOF_OF_SALARY, ' .
					' APPOINTMENT_LETTER, WELCOME_LETTER, WITHDRAWAL_LETTER, RETIREMENT_LETTER, STATEMENT_HISTORY, ' .
					' AUTHORIZED_SIGNATURE_A, AUTHORIZED_SIGNATURE_B ' .
				' FROM EMPLOYEES ' .
				' WHERE DOCUMENTS_PROCESSED = :documents_processed ';
	
		$params = array(':limit' => $limit,
				':documents_processed' => 'N');
	
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetchAll();
		}
			
		return $resultSet;
	}
	
	public function storeRawData($username, $title, $filename, $content_type, $content_length, $data) {

		//$sql = 'INSERT INTO GT_RAW_DATA (XUSERNAME, TITLE, FILENAME, CONTENT_TYPE, CONTENT_LENGTH, DOWNLOAD_COUNT, KEYWORDS, DESCRIPTION, DATA, EVENT_TIME, LAST_MODIFIED_TIME) VALUES ' .
				//'  (:xusername, :title, :filename, :content_type, :content_length, :download_count, :keywords, :description, :data, :event_time, :last_modified_time) ';
        $sql = 'INSERT INTO GT_RAW_DATA (XUSERNAME, TITLE, FILENAME, CONTENT_TYPE, CONTENT_LENGTH, DOWNLOAD_COUNT, KEYWORDS, DESCRIPTION, EVENT_TIME, LAST_MODIFIED_TIME) VALUES ' .
            '  (:xusername, :title, :filename, :content_type, :content_length, :download_count, :keywords, :description, :event_time, :last_modified_time) ';
            //'  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';

		$now = date('Y-m-d H:i:s');

        $statement = $this->pdo->prepare($sql);
        //print_r($statement);
        $params = array(':xusername' => $username,
            ':title' => $title,
            ':filename' => $filename,
            ':content_type' => $content_type,
            ':content_length' => $content_length,
            ':download_count' => 0,
            ':keywords' => '',
            ':description' => '',
            //':data' => $data,
            ':event_time' => $now,
            ':last_modified_time' => $now);
/*
		$statement->bindParam(':xusername', 		$username,		PDO::PARAM_STR);
		$statement->bindParam(':title',				$title,			PDO::PARAM_STR);
		$statement->bindParam(':filename', 			$filename, 		PDO::PARAM_STR);
		$statement->bindParam(':content_type', 		$content_type,	PDO::PARAM_STR);
		$statement->bindParam(':content_length', 	$content_length, PDO::PARAM_INT);
		$statement->bindParam(':download_count', 	0, 				PDO::PARAM_INT);
		$statement->bindParam(':keywords', 			'', 			PDO::PARAM_STR);
		$statement->bindParam(':description', 		'', 			PDO::PARAM_STR);
		//$statement->bindParam(':data', 				$data, 			PDO::PARAM_LOB);
		$statement->bindParam(':event_time', 		$now, 			PDO::PARAM_STR);
		$statement->bindParam(':last_modified_time', $now, 			PDO::PARAM_STR);
*/
		//print_r($statement);
		//$statement = $this->pdo->prepare($sql);
		//return $statement->execute();

        return $statement->execute($params);
	}

	public function getRawData($id) {
		$sql = 'SELECT XUSERNAME, TITLE, FILENAME, CONTENT_TYPE, CONTENT_LENGTH, DOWNLOAD_COUNT, KEYWORDS, DESCRIPTION, DATA, EVENT_TIME, LAST_MODIFIED_TIME ' .
				' FROM GT_RAW_DATA ' .
				' WHERE ID = :id ';

		$params = array(':id' => $id);

        $lob = '';
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {

			//$statement->bindColumn('TITLE', $type, PDO::PARAM_STR, 256);
			//$statement->bindColumn(2, $lob, PDO::PARAM_LOB);
            $statement->fetch(PDO::FETCH_BOUND);
			
			$resultSet = $statement->fetchAll();
		}
			
		return $resultSet;
	}

	public function getRawDataIDByTitle($title) {
		$sql = 'SELECT ID ' .
				' FROM GT_RAW_DATA ' .
				' WHERE TITLE = :title ';
	
		$params = array(':title' => $title);
	
		$statement = $this->pdo->prepare($sql);
		if ($statement->execute($params)) {
			$resultSet = $statement->fetchAll();
		}
			
		return $resultSet;
	}
	


	public function updateEmployeeColumns($id, $columns) {
	    //echo "$id, $columns";
		$sqlPrefix = 'UPDATE EMPLOYEES SET ';
		$sqlSuffix = 	' WHERE REGISTRATION_CODE = :id ';
	
		$params = array(':id' => $id);
		
		$count = count($columns);
		
		$sql = $sqlPrefix;
		foreach ($columns as $columnName => $columnValue) {
			$columnPlaceholder = ':' . strtolower($columnName);
			$sql .= "$columnName = $columnPlaceholder";
			$params[$columnPlaceholder] = $columnValue;
			if (--$count > 0) {
				$sql .= ', ';
			}  
		}
		$sql .= $sqlSuffix;
	
		$statement = $this->pdo->prepare($sql);

		return $statement->execute($params);
	}
	
}
