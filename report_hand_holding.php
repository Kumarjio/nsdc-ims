<?php	
	include('includes/application_top.php');

	if($_POST['form_action'] == 'export_report'){
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$start_month = tep_db_input(tep_db_prepare_input($_POST['start_month']));
		$start_month = ($start_month <= 9 ? '0' : '') . $start_month;
		$start_year = tep_db_input(tep_db_prepare_input($_POST['start_year']));
		$end_month = tep_db_input(tep_db_prepare_input($_POST['end_month']));
		$end_month = ($end_month <= 9 ? '0' : '') . $end_month;
		$end_year = tep_db_input(tep_db_prepare_input($_POST['end_year']));

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_sgsy_hand_holding_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		if($_POST['report_type'] == 'DETAILED_REPORT'){

			$reports_cols = array('S. No.','Centre Id','Centre Location', 'MES Sector', 'Course Name', 'Course Code', 'Batch No/Code', 'Course Duration', 'Batch Start Dt', 'Batch End Dt', 'Handholding End Date', 'Training Completed (Y/N)', 'Course Type', 'Candidate Name', 'Candidate Father\'s Name', 'Gender', 'Category', 'Minority', 'Mobile No', 'Candidate\'s District', 'Prev Company Name', 'Gross Salary', 'Net Salary', 'Contact Date', 'Mode of Contact', 'Was Student Contactable Y/N', 'Name of Person Contacted', 'Relationship of Contact Person with the Candidate', 'Contact No of the Person Contacted', 'Student Status Working/ Dropout ', 'Reason for Not Working/Dropping out', 'Date of Dropout', 'Job Status Same Job/Job Changed', 'Date of Leaving Previous Employer', 'Reason for Changing Job', 'Date Of Joining Current Employer', 'Name of the Current Company/ Employer', 'Designation of  Candidate', 'Gross Salary of Candidate', 'In Hand Salary of Candidate', 'Any Other Benefits', 'Name of the Contact Person in the company', 'Designation of Contact Person in Company', 'Contact Number of Company Personnel', 'Email Id of the Company Personnel', 'Company/Employer Address', 'Company/Employer CITY', 'Company/Employer PIN CODE', 'Salary Slip Collected', 'Name of the Bank of Student', 'Bank Branch of Student', 'Bank Account No of Student', 'Bank IFSC Code of Student');

			$alphabet = 'A';
			for($cntAlpha=0;$cntAlpha<=count($reports_cols);$cntAlpha++){
				$arr_alphabet[$cntAlpha] = $alphabet;
				$alphabet = get_rand_fix_type($alphabet);
			}

			$rows = 1;
			$cnt_cols = 0;

			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_cols] . $rows, $column);
				$cnt_cols++;
			}

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $arr_alphabet[count($reports_cols)-1] . '1')->applyFromArray($heading_bold);

			$center_query_raw = " select cn.centre_id, cn.district_id,cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

			if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";

			$center_query_raw .= " order by cn.centre_name";

			$center_query = tep_db_query($center_query_raw);

			$sr_no = 1;
			$rows = 2;

			if(tep_db_num_rows($center_query)){
				while($centre = tep_db_fetch_array($center_query)){

					$courses_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_code, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id ";

					if($_POST['course_id'] != '')$courses_query_raw .= " and c.course_id = '" . $_POST['course_id'] . "'";
					if($_POST['section_id'] != '')$courses_query_raw .= " and c.section_id = '" . $_POST['section_id'] . "'";

					$courses_query_raw .= "  order by c.course_name";

					$courses_query = tep_db_query($courses_query_raw);

					while($courses = tep_db_fetch_array($courses_query)){

						$batches_query_raw = "select batch_id, batch_title, batch_start_date, batch_end_date, handholding_end_date, test_allotted_date, test_abn_no, test_agency from " . TABLE_BATCHES . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "'";

						if($_POST['batch_id'] != '')$batches_query_raw .= " and batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

						$batches_query = tep_db_query($batches_query_raw);

						while($batches = tep_db_fetch_array($batches_query)){
							$students_query_raw = "select student_id, student_full_name, student_middle_name, student_surname, student_father_name, father_middle_name, father_surname, student_gender, student_mobile, student_district, if(is_training_completed = '1', 'Y', 'N') as is_training_completed, training_dropout_reason, if(is_apeared_for_test = '1', 'Y', 'N') as is_apeared_for_test, test_result, if(is_certificate_recieved = '1', 'Y', 'N') as is_certificate_recieved, student_category, if(is_minority_category = '1', 'Y', 'N') as is_minority_category, student_aadhar_card, student_bank_name, student_branch, student_account_number, bank_ifsc_code, course_option from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "' and batch_id = '" . $batches['batch_id'] . "' and is_deactivated != '1'";
							$students_query = tep_db_query($students_query_raw);

							while($students = tep_db_fetch_array($students_query)){

								$placement_query_raw = "select p.placement_id, p.student_id, p.company_id, p.centre_id, p.job_status, job_joining_date, p.job_designation, p.gross_salary, p.in_hand_salary, p.job_other_benifits, p.post_palacement_allowance, p.offer_letter_collected, p.offer_letter, p.salary_slip_collected, p.salary_slip, c.company_name, c.company_contact_person, c.company_contact_person_designation, c.company_phone_std, c.company_phone, c.company_email, c.company_address, c.company_pincode, cty.city_name from " . TABLE_PLACEMENTS . " p, " . TABLE_COMPANIES . " c, " . TABLE_CITIES . " cty where cty.city_id = c.city_id and c.company_id = p.company_id and p.student_id = '" . $students['student_id'] . "' ";
								$placement_query = tep_db_query($placement_query_raw);
								$placement = tep_db_fetch_array($placement_query);

								$handholding_query_raw = "select handholding_id, student_id, centre_id, company_id, contact_date, contact_mode, is_student_contable, contact_person_name, contact_person_relation, contact_person_phone, student_status, drop_out_reason, drop_out_date, job_status, leave_date, leave_reason, current_joining_date, current_company_name, candidate_designation, gross_salary, in_hand_salary, other_benifits, current_contact_person_name, current_contact_person_designation, current_company_phone, current_company_email, current_company_address, current_company_city, current_company_pincode, is_offer_letter_collected, is_salary_slip_collected, contact_made_by, created_date from " . TABLE_HANDHOLDING . " where student_id = '" . $students['student_id'] . "' ";

								if(tep_not_null($start_month) && tep_not_null($start_year) && !tep_not_null($end_month) && !tep_not_null($end_year)){
									$start_date = $start_year . "-" . $start_month . "-01";
									$handholding_query_raw .= " and contact_date >= '" . $start_date . "'";
								}else if(!tep_not_null($start_month) && !tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

									$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
									$end_date = $end_year . "-" . $end_month . "-" . $last_day;

									$handholding_query_raw .= " and contact_date <= '" . $end_date . "'";

								}else if(tep_not_null($start_month) && tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

									$start_date = $start_year . "-" . $start_month . "-01";
									$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
									$end_date = $end_year . "-" . $end_month . "-" . $last_day;

									$handholding_query_raw .= " and (contact_date >= '" . $start_date . "' and contact_date <= '" . $end_date . "')";
								}

								$handholding_query_raw .= " order by contact_date";

								$handholding_query = tep_db_query($handholding_query_raw);

								$cnt_handholding = 1;

								$last_company = '';
								$gross_salary = '';
								$in_hand_salary = '';

								while($handholding = tep_db_fetch_array($handholding_query)){
									$cnt_innter = 0;

									$current_company_info_query_raw = " select company_id, centre_id, city_id, company_name, branch_name, company_address, company_phone_std, company_phone, company_contact_person, company_contact_person_designation, company_email, company_pincode from " . TABLE_COMPANIES . " where company_id='" . $handholding['company_id'] . "' ";
									$current_company_info_query = tep_db_query($current_company_info_query_raw);
									$current_company_info = tep_db_fetch_array($current_company_info_query);

									if($cnt_handholding == 1){
										$last_company = $placement['company_name'];
										$gross_salary = $placement['gross_salary'];
										$in_hand_salary = $placement['in_hand_salary'];
									}

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $sr_no);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_unq_id']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['section_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_code']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_title']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_duration']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($batches['batch_start_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($batches['batch_end_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($batches['handholding_end_date']));

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['is_training_completed']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_course_option[$students['course_option']]);

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_gender']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_category[$students['student_category']]);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['is_minority_category']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_mobile']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_district']);

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $last_company);
									//$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $gross_salary);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $in_hand_salary);

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($handholding['contact_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_contact_mode[$handholding['contact_mode']]);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, ($handholding['is_student_contable'] == '1' ? 'Y' : 'N'));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['contact_person_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['contact_person_relation']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['contact_person_phone']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_placement_status[$handholding['student_status']]);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['drop_out_reason']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($handholding['drop_out_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_job_status[$handholding['job_status']]);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($handholding['leave_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['leave_reason']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, display_valid_date($handholding['current_joining_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['candidate_designation']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['gross_salary']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['in_hand_salary']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['other_benifits']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_contact_person_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_contact_person_designation']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_phone']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_email']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_address']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_city']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $handholding['current_company_pincode']);

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, ($handholding['is_salary_slip_collected'] == '1' ? 'Y' : 'N'));

									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_bank_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_branch']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_account_number']);
									$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['bank_ifsc_code']);

									$last_company = $current_company_info['company_name'];
									$gross_salary = $handholding['gross_salary'];
									$in_hand_salary = $handholding['in_hand_salary'];

									$sr_no++;
									$rows++;
									$cnt_handholding++;
								}
							}
						}
					}
				}
			}
		}else if($_POST['report_type'] == 'OVERVIEW_REPORT'){
			$reports_cols = array('S. No', 'Centre', 'MES Sector', 'Course Name', 'Batch No/Code', 'Batch ', 'Dt', 'Batch End Dt', 'Handholding End Date', 'Training Completed (Y/N)', 'Course Type', 'Candidate First Name', 'Candidate Father\'s Name', 'Candidate\'s Surname', 'Gender', 'Category.', 'Minority', 'Mobile No', 'Candidate District', 'No of Handholding Record', 'Salary Slip 1 (Y/N)', 'Salary Slip 2(Y/N)', 'Salary Slip 3(Y/N)', 'Salary Slip 4 (Y/N)', 'Salary Slip 5 (Y/N)', 'Salary Slip 6 (Y/N)', 'Salary Slip 7 (Y/N)', 'Salary Slip 8 (Y/N)', 'Salary Slip 9 (Y/N)', 'Salary Slip 10 (Y/N)', 'Salary Slip 11 (Y/N)', 'Salary Slip 12 (Y/N)', 'Name of the Bank of Student', 'Bank Branch of Student', 'Bank Account No of Student', 'Bank IFSC Code of Student', 'NSDC Stage 1 uploaded (Y/N)', 'NSDC Stage 2 uploaded (Y/N)', 'NSDC SDMS Number');

			$sheet_column = 'A';
			$sheet_row = '1';

			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($sheet_column . $sheet_row, $column);
				$sheet_column++;
			}

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $sheet_column . $sheet_row)->applyFromArray($heading_bold);

			$center_query_raw = " select cn.centre_id, cn.district_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

			if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";

			$center_query_raw .= " order by cn.centre_name";

			$center_query = tep_db_query($center_query_raw);

			$sr_no = 1;
			$sheet_row = 2;

			if(tep_db_num_rows($center_query)){
				while($centre = tep_db_fetch_array($center_query)){

					$courses_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_code, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id ";

					if($_POST['course_id'] != '')$courses_query_raw .= " and c.course_id = '" . $_POST['course_id'] . "'";
					if($_POST['section_id'] != '')$courses_query_raw .= " and c.section_id = '" . $_POST['section_id'] . "'";

					$courses_query_raw .= "  order by c.course_name";

					$courses_query = tep_db_query($courses_query_raw);

					while($courses = tep_db_fetch_array($courses_query)){

						$batches_query_raw = "select batch_id, batch_title, batch_start_date, batch_end_date, handholding_end_date, test_allotted_date, test_abn_no, test_agency from " . TABLE_BATCHES . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "'";

						if($_POST['batch_id'] != '')$batches_query_raw .= " and batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

						$batches_query = tep_db_query($batches_query_raw);

						while($batches = tep_db_fetch_array($batches_query)){
							$students_query_raw = "select student_id, student_full_name, student_father_name, student_surname, student_gender, student_mobile, student_district, if(is_training_completed = '1', 'Y', 'N') as is_training_completed, training_dropout_reason, if(is_apeared_for_test = '1', 'Y', 'N') as is_apeared_for_test, test_result, if(is_certificate_recieved = '1', 'Y', 'N') as is_certificate_recieved, student_category, if(is_minority_category = '1', 'Y', 'N') as is_minority_category, student_aadhar_card, student_bank_name, student_branch, student_account_number, bank_ifsc_code, course_option, stage1_uploaded, stage2_uploaded, sdms_number from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "' and batch_id = '" . $batches['batch_id'] . "'";
							$students_query = tep_db_query($students_query_raw);

							while($students = tep_db_fetch_array($students_query)){

								$sheet_column = 'A';

								$placement_query_raw = "select p.placement_id, p.student_id, p.company_id, p.centre_id, p.job_status, job_joining_date, p.job_designation, p.gross_salary, p.in_hand_salary, p.job_other_benifits, p.post_palacement_allowance, p.offer_letter_collected, p.offer_letter, p.salary_slip_collected, p.salary_slip, c.company_name, c.company_contact_person, c.company_contact_person_designation, c.company_phone_std, c.company_phone, c.company_email, c.company_address, c.company_pincode, cty.city_name from " . TABLE_PLACEMENTS . " p, " . TABLE_COMPANIES . " c, " . TABLE_CITIES . " cty where cty.city_id = c.city_id and c.company_id = p.company_id and p.student_id = '" . $students['student_id'] . "' ";
								$placement_query = tep_db_query($placement_query_raw);
								$placement = tep_db_fetch_array($placement_query);

								$handholding_query_raw = "select handholding_id, student_id, centre_id, company_id, contact_date, contact_mode, is_student_contable, contact_person_name, contact_person_relation, contact_person_phone, student_status, drop_out_reason, drop_out_date, job_status, leave_date, leave_reason, current_joining_date, current_company_name, candidate_designation, gross_salary, in_hand_salary, other_benifits, current_contact_person_name, current_contact_person_designation, current_company_phone, current_company_email, current_company_address, current_company_city, current_company_pincode, is_offer_letter_collected, is_salary_slip_collected, contact_made_by, created_date from " . TABLE_HANDHOLDING . " where student_id = '" . $students['student_id'] . "'";

								if(tep_not_null($start_month) && tep_not_null($start_year) && !tep_not_null($end_month) && !tep_not_null($end_year)){
									$start_date = $start_year . "-" . $start_month . "-01";
									$handholding_query_raw .= " and contact_date >= '" . $start_date . "'";
								}else if(!tep_not_null($start_month) && !tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

									$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
									$end_date = $end_year . "-" . $end_month . "-" . $last_day;

									$handholding_query_raw .= " and contact_date <= '" . $end_date . "'";

								}else if(tep_not_null($start_month) && tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

									$start_date = $start_year . "-" . $start_month . "-01";
									$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
									$end_date = $end_year . "-" . $end_month . "-" . $last_day;

									$handholding_query_raw .= " and (contact_date >= '" . $start_date . "' and contact_date <= '" . $end_date . "')";
								}

								$handholding_query_raw .= " order by contact_date";

								$handholding_query = tep_db_query($handholding_query_raw);
								$no_of_handholding = tep_db_num_rows($handholding_query);

								$cnt_hand_holding = 1;
								$salary_slip_array = array();

								while($handholding_array = tep_db_fetch_array($handholding_query)){
									$salary_slip_array[$cnt_hand_holding] = $handholding_array['is_salary_slip_collected'];
									$cnt_hand_holding++;
								}

								if($no_of_handholding){
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $sr_no);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $centre['centre_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $courses['section_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $courses['course_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $courses['course_code']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $batches['batch_title']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, display_valid_date($batches['batch_start_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, display_valid_date($batches['batch_end_date']));
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, display_valid_date($batches['handholding_end_date']));

									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['is_training_completed']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $arr_course_option[$students['course_option']]);

									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_full_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_father_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_surname']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_gender']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $arr_category[$students['student_category']]);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['is_minority_category']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_mobile']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_district']);

									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $no_of_handholding);

									for($i=1;$i<=12;$i++){
										$salary_slip_status = (isset($salary_slip_array[$i]) && $salary_slip_array[$i] != '' && $salary_slip_array[$i] == '1' ? 'Y' : 'N');
										$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $salary_slip_status);
									}

									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_bank_name']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_branch']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['student_account_number']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['bank_ifsc_code']);
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, ($students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, ($students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
									$objPHPExcel->getActiveSheet()->setCellValue($sheet_column++ . $sheet_row, $students['sdms_number']);

									$sr_no++;
									$sheet_row++;
								}
							}
						}
					}
				}
			}
		}

		//$arr_alphabet = range('A', 'Z');
		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $excelsheet_name . date("Ymd") . '.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?> : Hand Holding Report</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
		<script type="text/javascript">
		<!--
			function get_courses(default_course){
				var section = $('#section_id').val();

				$('#course_id').empty();
				$('#course_id').append($("<option></option>").attr("value",'').text('Please choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_courses&section='+section,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_course == values.course_id){
								$('#course_id').append($("<option></option>").attr("value",values.course_id).attr('selected', 'selected').text(values.frm_course_name));
							}else{
								$('#course_id').append($("<option></option>").attr("value",values.course_id).text(values.frm_course_name));
							}
						});

						get_batch('');
					}
				});
			}

			function get_batch(default_batch){
				var course = $('#course_id').val();
				var centre = $('#centre_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course+'&centre='+centre,
					type: 'POST',
					async: false,
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_batch == values.batch_id){
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).attr('selected', 'selected').text(values.batch_title));
							}else{
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).text(values.batch_title));
							}
						})
					}
				});
			}
		//-->
		</script>
	</head>
	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
		<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			<tr>
				<td><?php include( DIR_WS_MODULES . 'header.php' ); ?></td>
			</tr>
			<tr>
				<td valign="top" colspan="2">
					<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">
						<tr>
							<td valign="top" colspan="2"><?php include( DIR_WS_MODULES . 'top_menu.php' ); ?></td>
						</tr>
						<tr>
							<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
						</tr>
						<tr>
							<td class="backgroundBgMain" valign="top">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									<tr>
										<td valign="top">
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">Report - Hand Holding</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td>
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="export_report">
														<table cellpadding="5" cellspacing="0" border="0" width="100%">
															<tr>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Report Type<br>
																	<select name="report_type" id="report_type" class="required">
																		<option value="DETAILED_REPORT">Detailed Report</option>
																		<option value="OVERVIEW_REPORT">Overview Report</option>
																	</select>
																</td>
																<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Center<br>
																	<select name="centre_id" id="centre_id" class="required">
																		<option value="">All</option>
																		<?php
																			$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " order by centre_name";
																			$centre_query = tep_db_query($centre_query_raw);
																			
																			while($centre = tep_db_fetch_array($centre_query)){
																		?>
																		<option value="<?php echo $centre['centre_id'];?>" <?php echo($info['centre_id'] == $centre['centre_id'] ? 'selected="selected"' : '');?>><?php echo $centre['centre_name'];?></option>
																		<?php } ?>
																	</select>
																</td>
																<?php }else { ?>
																<input type="hidden" name="centre_id" id="centre_id" value="<?php echo $_SESSION['sess_centre_id'];?>">
																<?php } ?>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Sector<br>
																	<select name="section_id" id="section_id" title="Please select sector" class="required" onchange="javascript: get_courses('');">
																		<option value="">Please choose</option>
																		<?php
																			$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																			$section_query = tep_db_query($section_query_raw);
																			
																			while($section = tep_db_fetch_array($section_query)){
																		?>
																		<option value="<?php echo $section['section_id'];?>" <?php echo($info['section_id'] == $section['section_id'] ? 'selected="selected"' : '');?>><?php echo $section['section_name'];?></option>
																		<?php } ?>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold"  width="15%">
																	Course<br>
																	<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch('');" style="width: 120px;">
																		<option value="">Please choose</option>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold"  width="15%">
																	Batch<br>
																	<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																		<option value="">Please choose</option>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Start Month<br>
																	<select name="start_month">
																		<?php for($cnt_month=1;$cnt_month<=12;$cnt_month++){?>
																		<option value="<?php echo $cnt_month;?>"><?php echo date("M", mktime(0,0,0,$cnt_month,date("d"), date("Y")));?></option>
																		<?php } ?>
																	</select>
																	<select name="start_year">
																		<?php for($cnt_year=date("Y");$cnt_year>=date("Y")-5;$cnt_year--){?>
																		<option value="<?php echo $cnt_year;?>"><?php echo $cnt_year;?></option>
																		<?php } ?>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	End Month<br>
																	<select name="end_month">
																		<?php for($cnt_month=1;$cnt_month<=12;$cnt_month++){?>
																		<option value="<?php echo $cnt_month;?>"><?php echo date("M", mktime(0,0,0,$cnt_month,date("d"), date("Y")));?></option>
																		<?php } ?>
																	</select>
																	<select name="end_year">
																		<?php for($cnt_year=date("Y");$cnt_year>=date("Y")-5;$cnt_year--){?>
																		<option value="<?php echo $cnt_year;?>"><?php echo $cnt_year;?></option>
																		<?php } ?>
																	</select>
																</td>
															</tr>
															<tr>
																<td><br>
																	&nbsp;<input type="submit" value="Export to Excel" name="cmdExcel" id="cmdExcel" class="groovybutton"></td>
																</td>
																<td>&nbsp;<td>
															</tr>
															<script type="text/javascript">
															<!--
																get_courses('<?php echo $_GET['course_id'] ?>');
															//-->
															</script>
														</table>
														</form>
													</td>
												</tr>
											</table>	
										</td>
									</tr>
									<?php include( DIR_WS_MODULES . 'footer.php' ); ?>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>