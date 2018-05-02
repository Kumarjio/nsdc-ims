<?php
/*	
	Date :- 12-09-2012
	Author :- Raju Rajpurohit - Codoffer Infotech
*/
// set the level of error reporting
	error_reporting(0);
	ini_set('display_errors', 'off');

// include server parameters
	require('includes/configure.php');
	
// define the project version
	define('PROJECT_VERSION', 'IMS Proschool');

//	define site name
	define(SITE_NAME,'proschoolonline.com');

	//define admin email address
	define(ADMIN_EMAIL,'codoffer@gmail.com');

// define$MD5CryptKey encryption key;
	define(MD5CryptKey,'MD5');
	
// set tep_self in the local scope
	if (!isset($tep_SELF)) $tep_SELF = $HTTP_SERVER_VARS['tep_SELF'];

// include the list of project filenames
	require(DIR_WS_INCLUDES . 'filenames.php');

// include the list of project database tables
	require(DIR_WS_INCLUDES . 'database_tables.php');

// include the database functions
	require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
	tep_db_connect() or die('Unable to connect to database server!');

// define general functions used application-wide
	require(DIR_WS_FUNCTIONS . 'general.php');

	require(DIR_WS_FUNCTIONS . 'html_output.php');

	require(DIR_WS_FUNCTIONS . 'sessions.php');

// define how the crypt classes will be used
	require(DIR_WS_CLASSES . 'MD5Crypt.php');

// split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

// define current page 
	define('CURRENT_PAGE',basename($_SERVER['PHP_SELF']));
// start session only if sunnrent page is not index.php or index_process.php


tep_session_start();

$dont_redirect = array(FILENAME_DEFAULT,FILENAME_INDEX_PROCESS,FILENAME_LOGOUT);
if(!in_array( CURRENT_PAGE, $dont_redirect ))	{
	if (!tep_session_is_registered("sess_status")){
		tep_redirect(tep_href_link(FILENAME_DEFAULT));
	}
}

// define company name
	define('COMPANY_NAME','IMS Proschool');

// define title for pages
	define('TITLE', COMPANY_NAME);

  // setting of the NONSSL or SSL after error on talent dated on 02/03/2007 "unable to determine connection type"
	if( ENABLE_SSL == false )	{
		$request_type = 'NONSSL';
	}else {
		$request_type = 'SSL';
	}

	$defRecPerPage = "5";
	$defPageUpto = "1";

	$arr_status = array('0'=>'Inactive','1'=>'Active');
	
	include('static_data.php');

	$arr_maritial_status = array('UN_MARRIED'=>'Un Married', 'MARRIED'=>'Married', 'WIDOWER_WIDOW'=>'Widower-Widow', 'DIVORCEE'=>'Divorcee');
	$arr_family_type = array('BPL'=>'Below Poverty Line', 'APL'=>'Above Poverty Line');
	$arr_category = array('SC'=>'SC', 'ST'=>'ST', 'BC'=>'BC', 'OTHERS'=>'OTHERS', 'GENERAL'=>'General');
	$arr_religion = array('HINDU'=>'Hindu', 'MUSLIM'=>'Muslim', 'SIKH'=>'Sikh', 'CHRISTIAN'=>'Christian', 'JAIN'=>'Jain', 'BUDH'=>'Budh', 'OTHERS'=>'Others');
	$arr_status = array('1'=>'Yes', '0'=>'No');
	$arr_gender = array('MALE'=>'Male', 'FEMALE'=>'Female');

	$arr_qualification = array('UP_TO_5TH_PASS'=>'Upto 5th Pass', '6TH_TO_9TH_PASS'=>'6th to 9th Pass', 'SSC'=>'SSC', 'HSC'=>'HSC', 'GRADUATE'=>'Graduate', 'POST_GRADUATE'=>'Post Graduate', 'OTHERS'=>'Others');
	
	$employee_detail_array = array('ON JOB' => 'On Job','SELF-EMPLOYED' => 'Self-Employed');
	$occupation_array = array('ON JOB' => 'On Job','SELF-EMPLOYED' => 'Self-Employed','NOT WORKING' => 'Not Working');

	$placement_type_array = array('ON_JOB' => 'On Job', 'SELF_EMPLOYED' => 'Self-Employed', 'OPTED_HIGHER_STUDIES' => 'Opted for Higher Studies', 'UP_SKILLED' => 'Up Skilled');

	$arr_payment_type = array('CASH'=>'Cash', 'CHEQUE'=>'Cheque', 'DD'=>'Demand Draft', 'CREDIT_CARD'=>'Credit Card', 'NEFT_RTGS'=>'NEFT/RTGS');
	$arr_deposit_payment_type = array('CASH'=>'Cash', 'CHEQUE'=>'Cheque', 'DD'=>'Demand Draft');
	$arr_exam_result = array('PASS'=>'Pass', 'FAIL'=>'Fail', 'ABSENT'=>'Not Recieved',);
	$arr_placement_status = array('WORKING'=>'Working', 'DROP_OUT'=>'Drop Out');
	$arr_contact_mode = array('PHONE'=>'Phone', 'VISIT'=>'Visit');
	$arr_job_status = array('SAME_JOB'=>'Same Job', 'JOB_CHANGED'=>'Job Changed');

	$arr_course_option = array('RESIDENTIAL'=>'Residential', 'NON_RESIDENTIAL'=>'Non Residential');
	$arr_aadhar_status = array('APPLIED'=>'Applied for', 'RECEIVED'=>'Received');
	$arr_bank_ac_status = array('APPLIED'=>'Applied for', 'OPENED'=>'Opened');
	$arr_course_level = array('BASIC'=>'Basic', 'INTERMEDIATE'=>'Intermediate', 'HIGH'=>'High');

	$arr_document_type = array('BPL Card', 'BPL Certificate Copy', 'Caste Certificate', 'Age Proof', 'Address Proof', 'Photo ID Proof', 'Education Proof');

	$arr_batch_status = array('COMPLETED'=>'Completed', 'IN_PROGRESS'=>'In Progress', 'TO_BE_STARTED'=>'To Be Started');

	$media_type_array = array('IMAGE'=>'Photo', 'VIDEO'=>'Video');
	$media_category_array = array('Mobilization', 'In Training', 'Lodging & Boarding', 'Field Visits', 'Placements', 'Infrastructure', 'Activities & Events', 'Others');

	$arr_student_area = array('RURAL'=>'Rural area', 'URBAN'=>'Urban area');
	$disp_stud_payment_type_array = array('INSTALLMENT_PAYMENT'=>'Installment Payment', 'DOWN_PAYMENT'=>'Down Payment');
	$disp_stud_payment_status_array = array('NOT_DEPOSITED'=>'Not Deposited', 'DEPOSITED'=>'Deposited', 'BOUNCE'=>'Bounce', 'SETTLEMENT' => 'Settlement');

	$refund_reason_array = array('Excess fees', 'Discount', 'Course Cancellation');
	$enq_type_array = array('WALK_IN' => 'Walk In', 'TELEPHONIC' => 'Telephonic', 'EMAIL' => 'Email', 'OTHERS' => 'Others');
	$arr_status_pros = array('INTERESTED'=>'Interested', 'UNDECIDED'=>'Undecided', 'NOT_INTERESTED'=>'Not Interested');
?>