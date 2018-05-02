<?php	
	include('includes/application_top.php');

	if(isset($_POST['form_action']) && $_POST['form_action'] != '')
	{
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_' . $_POST['form_action'] . '_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		$arr_alphabet = range('A', 'Z');

		if($_POST['form_action'] == 'full_batch_report'){

			$arr_batch_cols = array('S.No', 'Centre Id', 'Centre Location', 'Centre District', 'Batch Code', 'Batch District', 'Course Name', 'Batch Start Dt', 'Batch End Dt:', 'Handholding End Date', 'Batch Status', 'Total No of Candidates Trained', 'No of Male Trained', 'No of Female Trained', 'SC Trained', 'ST Trained', 'BC Trained', 'Others Trained', 'Minority Trained', 'General Trained', 'Number Candidates Certified', 'Total No of Candidates Placed ', 'Male Placed', 'Female Placed', 'SC Placed', 'ST Placed', 'BC Placed', 'Others Placed', 'Minority Placed', 'General Placed', 'No of Bank Account Opened', 'No of Aadhar Card Receipts', 'No of Aadhar Card' , 'No of Physical Handicapped', 'No of Residential Candidates', 'No of Non Residential Candidates', 'No of Placement Allowance Instalment 1', 'No of Placement Allowance Instalment 2', 'No of Non Residential Allowance', 'Total Under Training', 'No of Male Under Training', 'No of Female Under Training', 'No of SC Under Training', 'No of ST Under Training', 'No of BC Under Training', 'No of Others Under Training', 'No of Minorities Under Training', 'No of General Under Training', 'Total Uploaded to NSDC Stage 1', 'Total Uploaded to NSDC Stage 2');

			//'Assessment Test Date', 'Testing Agency', 'AADHAR Process Completed', 'Bank Account Opened', 'Offer Letters & Salary Slips Collected', 'No of Candidates Working', 'No of Candidates Dropped Out',

			$rows = 1;
			$cnt_cols = 0;

			$alphabet = 'A';

			foreach($arr_batch_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}


			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

			$center_query_raw = " select cn.centre_id, cn.district_id, cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

			//if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";
			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$center_query_raw .= " and cn.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}

			$center_query_raw .= " order by cn.centre_name";

			$center_query = tep_db_query($center_query_raw);

			$sr_no = 1;
			$rows = 2;

			while($centre = tep_db_fetch_array($center_query)){

				$batch_query_raw = " select b.batch_id, b.centre_id, b.section_id, b.course_id, b.batch_title, b.batch_start_date, b.batch_end_date, b.handholding_end_date, b.test_allotted_date, b.test_agency, b.batch_status, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id) where b.centre_id='" . $centre['centre_id'] . "' ";
				$batch_query_raw .= "  order by b.batch_title";

				//if($_POST['district_id'] != '')$batch_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";

				$batch_query = tep_db_query($batch_query_raw);

				while($batch = tep_db_fetch_array($batch_query)){

					$course_query_raw = " select course_id, section_id, course_name, course_desc, course_code, course_duration, course_status from " . TABLE_COURSES . " where course_id='" . $batch['course_id'] . "' ";
					$course_query = tep_db_query($course_query_raw);

					$course = tep_db_fetch_array($course_query);

					/*$aadhar_card_status = 'Y';

					$check_addhar_status_query = tep_db_query("select student_id from " . TABLE_STUDENTS . " where student_aadhar_card_status != 'RECEIVED' and batch_id = '" . $batch['batch_id'] . "'");
					if(tep_db_num_rows($check_addhar_status_query)){
						$aadhar_card_status = 'N';
					}

					$bank_account_status = 'Y';

					$check_bank_account_query = tep_db_query("select student_id from " . TABLE_STUDENTS . " where bank_account_status != 'OPENED' and batch_id = '" . $batch['batch_id'] . "'");
					if(tep_db_num_rows($check_bank_account_query)){
						$bank_account_status = 'N';
					}

					$ss_ol_status = 'Y';

					$total_sal_offer_query = tep_db_query("select handholding_id from " . TABLE_HANDHOLDING . " hh, " . TABLE_STUDENTS . " s where s.student_id = hh.student_id and s.course_id = '"  . $courses['course_id'] . "' and hh.centre_id = '" . $centre['centre_id'] . "' and 	hh.student_status = 'WORKING' and batch_id = '" . $batch['batch_id'] . "' and hh.is_salary_slip_collected = '0' and hh.is_offer_letter_collected = '0'");
					if(tep_db_num_rows($total_sal_offer_query)){
						$ss_ol_status = 'N';
					}*/

					//Trained

					$total_trained_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and is_deactivated != '1'");
					$total_trained_student = tep_db_fetch_array($total_trained_student_query);

					$total_male_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'");
					$total_male_trained = tep_db_fetch_array($total_male_trained_query);

					$total_female_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'");
					$total_female_trained = tep_db_fetch_array($total_female_trained_query);

					$total_sc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc_trained = tep_db_fetch_array($total_sc_trained_query);

					$total_st_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st_trained = tep_db_fetch_array($total_st_trained_query);

					$total_bc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'BC' and is_deactivated != '1'");
					$total_bc_trained = tep_db_fetch_array($total_bc_trained_query);

					$total_other_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other_trained = tep_db_fetch_array($total_other_trained_query);

					$total_trained_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and is_minority_category = '1' and is_deactivated != '1'");
					$total_trained_minority = tep_db_fetch_array($total_trained_minority_query);

					$total_trained_general_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'");
					$total_trained_general = tep_db_fetch_array($total_trained_general_query);

					//Certified

					$total_cert_student_query = tep_db_query("select count(student_id) as total_certified from " . TABLE_STUDENTS . " where test_result = 'PASS' and batch_id = '" . $batch['batch_id'] . "' and is_deactivated != '1'");
					$total_cert_student = tep_db_fetch_array($total_cert_student_query);

					//Placement

					$total_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and is_deactivated != '1'");
					//echo "select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' <br /><br />	";
					$total_placed = tep_db_fetch_array($total_placed_query);

					$total_male_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'");
					$total_male_placed = tep_db_fetch_array($total_male_placed_query);

					$total_female_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'");
					$total_female_placed = tep_db_fetch_array($total_female_placed_query);

					$total_sc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc_placed = tep_db_fetch_array($total_sc_placed_query);

					$total_st_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st_placed = tep_db_fetch_array($total_st_placed_query);

					$total_bc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_category = 'BC' and is_deactivated != '1'");
					$total_bc_placed = tep_db_fetch_array($total_bc_placed_query);

					$total_other_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other_placed = tep_db_fetch_array($total_other_placed_query);

					$total_minority_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and s.is_minority_category = '1' and is_deactivated != '1'");
					$total_minority_placed = tep_db_fetch_array($total_minority_placed_query);

					$total_general_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batch['batch_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'");
					$total_general_placed = tep_db_fetch_array($total_general_placed_query);

					//Residencial

					$total_res_student_query = tep_db_query("select count(student_id) as total_res from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and course_option = 'RESIDENTIAL' and is_deactivated != '1'");
					$total_res_student = tep_db_fetch_array($total_res_student_query);

					$total_nonres_student_query = tep_db_query("select count(student_id) as total_res from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and course_option = 'NON_RESIDENTIAL' and is_deactivated != '1'");
					$total_nonres_student = tep_db_fetch_array($total_nonres_student_query);

					/*$total_working_query = tep_db_query("select count(handholding_id) as total_works from " . TABLE_HANDHOLDING . " hh, " . TABLE_STUDENTS . " s where s.student_id = hh.student_id and s.batch_id = '" . $batch['batch_id'] . "' and hh.student_status = 'WORKING' group by s.student_id");
					$total_working = tep_db_fetch_array($total_working_query);

					$total_dropout_query = tep_db_query("select count(handholding_id) as total_dropout from " . TABLE_HANDHOLDING . " hh, " . TABLE_STUDENTS . " s where s.student_id = hh.student_id and s.batch_id = '" . $batch['batch_id'] . "' and hh.student_status = 'DROP_OUT' group by s.student_id");
					$total_dropout = tep_db_fetch_array($total_dropout_query);*/

					//Bank account opened
					$total_bank_ac_opened_query = tep_db_query("select count(student_id) as total_bank_ac_opened from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and is_bank_account = '1' and is_deactivated != '1'");
					$total_bank_ac_opened = tep_db_fetch_array($total_bank_ac_opened_query);

					//Total Aadhar Receipt
					$total_aadhar_rec_query = tep_db_query("select count(student_id) as total_aadhar from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and is_student_aadhar_card = '0' and student_aadhar_card_receipt != '' and is_deactivated != '1'");
					$total_aadhar_rec = tep_db_fetch_array($total_aadhar_rec_query);

					//Total Aadhar
					$total_aadhar_query = tep_db_query("select count(student_id) as total_aadhar from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and is_student_aadhar_card = '1' and student_aadhar_card != '' and is_deactivated != '1'");
					$total_aadhar = tep_db_fetch_array($total_aadhar_query);

					//Total Physical disablity
					$total_physical_dis_query = tep_db_query("select count(student_id) as total_physical_dis from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and is_physical_disability = '1' and is_deactivated != '1'");
					$total_physical_dis = tep_db_fetch_array($total_physical_dis_query);


					$total_installment1_query_raw = "select count(inst.installment_id) as total_installment from " . TABLE_INSTALLMENTS . " inst, " . TABLE_STUDENTS . " s where inst.student_id = s.student_id and s.batch_id = '" . $batch['batch_id'] . "' and installment_no = '1' and inst.installment_type = 'PLACEMENT_ALLOWANCE' and is_deactivated != '1'";
					$total_installment1_query = tep_db_query($total_installment1_query_raw);
					$total_installment1 = tep_db_fetch_array($total_installment1_query);

					$total_installment2_query_raw = "select count(inst.installment_id) as total_installment from " . TABLE_INSTALLMENTS . " inst, " . TABLE_STUDENTS . " s where inst.student_id = s.student_id and s.batch_id = '" . $batch['batch_id'] . "' and installment_no = '2	' and inst.installment_type = 'PLACEMENT_ALLOWANCE' and is_deactivated != '1'";
					$total_installment2_query = tep_db_query($total_installment2_query_raw);
					$total_installment2 = tep_db_fetch_array($total_installment2_query);

					$total_non_res_query = tep_db_query("select count(student_id) as total_students from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batch['batch_id'] . "' and non_res_alw_amt_paid > 0 and is_deactivated != '1'");
					$total_non_res = tep_db_fetch_array($total_non_res_query);

					//Under training
					$total_under_training_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and is_deactivated != '1'");
					$total_under_training_student = tep_db_fetch_array($total_under_training_student_query);

					$total_male_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'");
					$total_male_under_training = tep_db_fetch_array($total_male_under_training_query);

					$total_female_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'");
					$total_female_under_training = tep_db_fetch_array($total_female_under_training_query);

					$total_sc_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc_under_training = tep_db_fetch_array($total_sc_under_training_query);

					$total_st_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st_under_training = tep_db_fetch_array($total_st_under_training_query);

					$total_bc_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'BC' and is_deactivated != '1'");
					$total_bc_under_training = tep_db_fetch_array($total_bc_under_training_query);

					$total_other_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other_under_training = tep_db_fetch_array($total_other_under_training_query);

					$total_under_training_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and is_minority_category = '1' and is_deactivated != '1'");
					$total_under_training_minority = tep_db_fetch_array($total_under_training_minority_query);

					$total_under_training_general_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batch['batch_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'");
					$total_under_training_general = tep_db_fetch_array($total_under_training_general_query);

					$total_nsdc_stage_1_query = tep_db_query("select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "' and stage1_uploaded = '1' and is_deactivated != '1'");
					$total_nsdc_stage_1 = tep_db_fetch_array($total_nsdc_stage_1_query);

					$total_nsdc_stage_2_query = tep_db_query("select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "' and stage2_uploaded = '1' and is_deactivated != '1'");
					$total_nsdc_stage_2 = tep_db_fetch_array($total_nsdc_stage_2_query);
					
					$cnt_innter = 1;

					$alphabet = 'A';
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $sr_no);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $centre['centre_unq_id']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $centre['centre_name'] . ', ' . $centre['centre_address']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ucwords(strtolower($centre['district_name'])));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['batch_title']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['district_name']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $course['course_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_start_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_end_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['handholding_end_date']));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_batch_status[$batch['batch_status']]);
					$alphabet++;

					/*$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $aadhar_card_status);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $bank_account_status);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $ss_ol_status);
					$alphabet++;*/
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_trained_student['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_male_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_female_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_sc_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_st_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_bc_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_other_trained['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_trained_minority['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_trained_general['total_trained']);
					$alphabet++;

					/*$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['test_allotted_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['test_agency']);
					$alphabet++;*/

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_cert_student['total_certified']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_male_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_female_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_sc_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_st_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_bc_placed['total_placement']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_other_placed['total_placement']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_minority_placed['total_placement']);//Discussion pending

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_general_placed['total_placement']);

					/*$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_working['total_works']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_dropout['total_dropout']);*/

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_bank_ac_opened['total_bank_ac_opened']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_aadhar_rec['total_aadhar']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_aadhar['total_aadhar']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_physical_dis['total_physical_dis']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_res_student['total_res']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_nonres_student['total_res']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_installment1['total_installment']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_installment2['total_installment']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_non_res['total_students']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_under_training_student['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_male_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_female_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_sc_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_st_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_bc_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_other_under_training['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_under_training_minority['total_trained']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_under_training_general['total_trained']);

					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_nsdc_stage_1['total_uploaded']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_nsdc_stage_2['total_uploaded']);

					$sr_no++;
					$rows++;
				}
			}
		}else if($_POST['form_action'] == 'batch_info_report'){
			$arr_batch_cols = array('S. No.', 'Batch No/Code', 'Batch Disctrict', 'MES Sector', 'Course Name', 'Course Code', 'Course Duration', 'Course Type Residential/Non Residential', 'Batch Start Date', 'Batch End Date', 'Batch Handholding Date', 'Batch Status', 'Candidate Name', 'Candidate Father\'s Name', 'Candidate Father\'s Occupation', 'Candidate Father\'s Monthly Income', 'Candidate Father\'s Yearly Income', 'Candidate Mother\'s Name', 'Candidate Mother\'s Occupation', 'Candidate Mother\'s Monthly Income', 'Candidate Mother\'s Yearly Income', 'Address', 'Dist.', 'Taluka ', 'Village ', 'Pin Code', 'State', 'Mobile No 1', 'Mobile No 2', 'Mobile No 3', 'Alternate Contact No', 'Email ID', 'Gender', 'DOB (DD/MM/YYYY)', 'Age', 'Marital Status', 'Candidate Spouse Name', 'Candidate Spouse Occupation', 'Candidate Spouse Monthly Income', 'Candidate Spouse Yearly Income', 'Family Type (BPL/APL)', 'BPL CARD Submitted (Y/N)', 'BPL CARD NO', 'Family ID ', 'Candidate Total Family', 'Candidate Total Family Yearly Income', 'Candidate Total Family Source Income', 'Category SC/ST/BC/OTHERS', 'Minority (Y/N)', 'Religion', 'Physical Disability (Y/N)', 'AADHAR CARD Submitted (Y/N)', 'AADHAR RECEIPT NUMBER', 'AADHAR CARD NO', 'PAN Card Submitted NO (Y/N)', 'PAN Card NO','Other Proof Submitted NO (Y/N)', 'Other Proof Name','Other Proof No', 'Language  Known', 'Literacy Status/Education', 'Student Stream', 'student_inst_name', 'student_passing_year', 'student_marks', 'student_other_skill', 'Basic Computer Literacy (Y/N)', 'Employed (Y/N)', 'Current Source of Income', 'Student Current Emp','Student Designation','Student Total Exp', 'Bank Account (Y/N)', 'Name of the Bank', 'Bank Branch', 'Bank Account No', 'Bank IFSC Code', 'Height', 'Weight', 'Blood Group', 'Photographs Submitted (Y/N)', 'Caste Certificate Submitted (Y/N)', 'Age Proof Submitted(Y/N) ', 'Document Submitted as Age Proof', 'Address Proof Submitted(Y/N) ', 'Document Submitted as Address Proof', 'Photo Identification Submitted(Y/N) ', 'Document Submitted as Photo ID', 'Education Proof Submitted (Y/N) ', 'Document Submitted as Education Proof','CED Portal Stage 1','CED Portal Stage 1 Date','CED Portal Stage 2','CED Portal Stage 2 Date');

			$rows = 1;
			$cnt_cols = 0;

			$alphabet = 'A';
			foreach($arr_batch_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}


			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

			$batch_query_raw = " select b.batch_id, b.centre_id, b.section_id, b.course_id, b.batch_title, batch_start_date, batch_end_date, handholding_end_date, test_allotted_date, b.test_agency, b.batch_status, s.section_name, c.course_name, c.course_code, c.course_duration, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id), " . TABLE_SECTIONS . " s, " . TABLE_COURSES . " c where b.course_id = c.course_id and s.section_id = b.section_id ";

			if($_POST['batch_id'] != '')$batch_query_raw .= " and b.batch_id = '" . $_POST['batch_id'] . "'";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}else{
				if($_POST['centre_id'] != '')$batch_query_raw .= " and b.centre_id = '" . $_POST['centre_id'] . "'";
			}

			if($_POST['course_id'] != '')$batch_query_raw .= " and b.course_id = '" . $_POST['course_id'] . "'";
			if($_POST['section_id'] != '')$batch_query_raw .= " and b.section_id = '" . $_POST['section_id'] . "'";

			if($_POST['district_id'] != '')$batch_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";

			$batch_query_raw .= "  order by b.batch_title";

			$batch_query = tep_db_query($batch_query_raw);

			$sr_no = 1;
			$rows = 2;

			while($batch = tep_db_fetch_array($batch_query)){

				$students_query_raw = "select student_id, course_option, student_full_name, student_middle_name, student_surname, student_father_name, father_middle_name, father_surname, student_father_occupation,student_father_mincome,student_father_yincome, mother_first_name, mother_middle_name, mother_surname, student_mother_occupation,student_mother_mincome,student_mother_yincome, student_address, student_district, student_taluka, student_village, student_pincode, student_state, student_mobile, student_mobile_2, student_mobile_3, student_phone_std, student_phone, student_email, student_gender, student_dob, student_age, student_maritial, student_spouse_first_name, student_spouse_middle_name, student_spouse_last_name, student_spouse_occupation, student_spouse_mincome, student_spouse_yincome, student_family_type, is_bpl_card, bpl_card_no, family_id, student_total_family,student_total_family_yincome,student_family_source_income, student_category, is_minority_category, student_religion, is_physical_disability, is_student_aadhar_card, student_aadhar_card_receipt, student_aadhar_card, is_student_pan_card, student_pan_card, is_student_other_proof,student_other_proof_name,student_other_proof_number, student_language_known, student_qualification, student_other_qualification, student_stream,student_inst_name,student_passing_year,student_marks,student_other_skill, is_unemployed, student_income_source, student_current_emp,student_designation,student_total_exp, is_bank_account, student_bank_name, student_branch, student_account_number, bank_ifsc_code, student_height, student_weight, student_blood_group, student_photo, is_computer_primary_knowledge, stage1_ced_portal, stage1_ced_portal_date,stage2_ced_portal,stage2_ced_portal_date from " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "' and is_deactivated != '1'";
				$students_query = tep_db_query($students_query_raw);

				while($students = tep_db_fetch_array($students_query)){

					$student_documents_query_raw = "select student_document_id, document, document_title, document_type from " . TABLE_STUDENT_DOCUMENTS . " where student_id = '" . $students['student_id'] . "'";
					$student_documents = array();
					$student_documents_query = tep_db_query($student_documents_query_raw);
					if(tep_db_num_rows($student_documents_query)){
						while($student_documents_temp = tep_db_fetch_array($student_documents_query)){
							$student_documents[$student_documents_temp['document_type']] = $student_documents_temp['document_title'];
						}
					}

					$alphabet = 'A';
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $sr_no);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['batch_title']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['district_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['section_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_code']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_duration']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_course_option[$students['course_option']]);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_start_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_end_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['handholding_end_date']));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_batch_status[$batch['batch_status']]);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_occupation']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_mincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_yincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['mother_first_name'] . ' ' . $students['mother_middle_name'] . ' ' . $students['mother_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mother_occupation']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mother_mincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mother_yincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_address']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_district']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_taluka']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_village']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_pincode']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_state']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile_2']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile_3']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_phone_std'] . ' ' . $students['student_phone']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_email']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_gender[$students['student_gender']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['student_dob']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_age']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_maritial_status[$students['student_maritial']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_spouse_first_name'] . ' ' . $students['student_spouse_middle_name'] . ' ' . $students['student_spouse_last_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_spouse_occupation']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_spouse_mincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_spouse_yincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_family_type[$students['student_family_type']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_bpl_card'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['bpl_card_no']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['family_id']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_total_family']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_total_family_yincome']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_family_source_income']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_category[$students['student_category']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_minority_category'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_religion[$students['student_religion']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_physical_disability'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_student_aadhar_card'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_aadhar_card_receipt']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_aadhar_card']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_student_pan_card'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_pan_card']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_student_other_proof'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_other_proof_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_other_proof_number']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_language_known']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['student_qualification'] == 'OTHERS' ? $students['student_qualification'] : $arr_qualification[$students['student_qualification']]));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_stream']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_inst_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_passing_year']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_marks']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_other_skill']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_computer_primary_knowledge'] == '1' ? 'Y' : 'N'));//Pending
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_unemployed'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_income_source']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_current_emp']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_designation']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_total_exp']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_bank_account'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_bank_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_branch']);
					$alphabet++;
					//$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_account_number']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($alphabet . $rows, $students['student_account_number'], PHPExcel_Cell_DataType::TYPE_STRING);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['bank_ifsc_code']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_height']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_weight']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_blood_group']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['student_photo'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($student_documents['Caste Certificate'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($student_documents['Age Proof'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_documents['Age Proof']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($student_documents['Address Proof'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_documents['Address Proof']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($student_documents['Photo ID Proof'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_documents['Photo ID Proof']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($student_documents['Education Proof'] != '' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_documents['Education Proof']);
					$alphabet++;
					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage1_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage1_ced_portal_date']));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage2_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage2_ced_portal_date']));

					$sr_no++;
					$rows++;
				}
			}

		}else if($_POST['form_action'] == 'batch_overview_report'){
			$excelsheet_name = 'proschool_sgsy_batch_overview_report_' . time();

			$training_condition = ($_POST['training_status'] == 'COMPLETED' ? " and is_training_completed = '1'" : " and is_training_completed != '1'");

			$sections_query = tep_db_query(" select section_id, section_name from ". TABLE_SECTIONS ." where 1");
			$sections = array();

			while($sections_temp = tep_db_fetch_array($sections_query)){
				$sections[] = $sections_temp;
			}

			$reports_cols = array('S. No.', 'State', 'Districts');
			foreach($sections as $section_info){
				$reports_cols[] = $section_info['section_name'];
			}

			$reports_cols = array_merge($reports_cols, array('Total', 'Male', 'Female', 'SC', 'ST', 'BC', 'Others', 'General', 'Minority'));

			$sheet_col = 'A';
			$sheet_row = '1';

			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($sheet_col . $sheet_row, $column);
				$sheet_col++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $sheet_col . $sheet_row)->applyFromArray($heading_bold);

			$sheet_row = 2;

			$districts_query_raw = "select d.district_id, d.state, district_name from " . TABLE_DISTRICTS . " d, " . TABLE_BATCHES . " b  where b.district_id = d.district_id";
			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$districts_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}
			$districts_query_raw .= " group by d.district_id order by d.district_name";

			$districts_query = tep_db_query($districts_query_raw);

			$cnt_sn = 1;
			while($districts = tep_db_fetch_array($districts_query)){

				$sheet_col = 'A';

				$total_trained_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and is_deactivated != '1' " . $training_condition);
				$total_trained_student = tep_db_fetch_array($total_trained_student_query);

				$total_male_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'" . $training_condition);
				$total_male_trained = tep_db_fetch_array($total_male_trained_query);

				$total_female_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'" . $training_condition);
				$total_female_trained = tep_db_fetch_array($total_female_trained_query);

				$total_sc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'SC' and is_deactivated != '1'" . $training_condition);
				$total_sc_trained = tep_db_fetch_array($total_sc_trained_query);

				$total_st_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'ST' and is_deactivated != '1'" . $training_condition);
				$total_st_trained = tep_db_fetch_array($total_st_trained_query);

				$total_bc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'BC' and is_deactivated != '1'" . $training_condition);
				$total_bc_trained = tep_db_fetch_array($total_bc_trained_query);

				$total_other_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'" . $training_condition);
				$total_other_trained = tep_db_fetch_array($total_other_trained_query);

				$total_general_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and s.student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'" . $training_condition);
				$total_general_trained = tep_db_fetch_array($total_general_trained_query);

				$total_trained_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and is_minority_category = '1' and is_deactivated != '1'" . $training_condition);
				$total_trained_minority = tep_db_fetch_array($total_trained_minority_query);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $cnt_sn);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, ucwords(strtolower($districts['state'])));
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $districts['district_name']);

				foreach($sections as $section_info){

					$total_section_tranined_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.district_id = '" . $districts['district_id'] . "' and b.section_id = '" . $section_info['section_id'] . "' and is_deactivated != '1'" . $training_condition);
					$total_section_tranined = tep_db_fetch_array($total_section_tranined_query);

					$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_section_tranined['total_trained']);
				}

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_trained_student['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_male_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_female_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_sc_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_st_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_bc_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_other_trained['total_trained']);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_general_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_trained_minority['total_trained']);

				$cnt_sn++;
				$sheet_row++;
			}
		}else if($_POST['form_action'] == 'sector_batch_report'){
			$excelsheet_name = 'proschool_sgsy_sector_batch_report_' . time();

			$training_condition = ($_POST['training_status'] == 'COMPLETED' ? " and is_training_completed = '1'" : " and is_training_completed != '1'");

			$reports_cols = array('S. No.', 'Sectors', 'Total', 'Male', 'Female', 'SC', 'ST', 'BC', 'Others', 'General', 'Minority');

			$sheet_col = 'A';
			$sheet_row = '1';

			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($sheet_col . $sheet_row, $column);
				$sheet_col++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $sheet_col . $sheet_row)->applyFromArray($heading_bold);

			$sheet_row = 2;

			$sections_query = tep_db_query(" select section_id, section_name from ". TABLE_SECTIONS ." where 1 order by section_name");

			$cnt_sn = 1;
			while($sections = tep_db_fetch_array($sections_query)){

				$sheet_col = 'A';

				$total_trained_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and is_deactivated != '1' " . $training_condition);
				$total_trained_student = tep_db_fetch_array($total_trained_student_query);

				$total_male_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'" . $training_condition);
				$total_male_trained = tep_db_fetch_array($total_male_trained_query);

				$total_female_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'" . $training_condition);
				$total_female_trained = tep_db_fetch_array($total_female_trained_query);

				$total_sc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'SC' and is_deactivated != '1'" . $training_condition);
				$total_sc_trained = tep_db_fetch_array($total_sc_trained_query);

				$total_st_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'ST' and is_deactivated != '1'" . $training_condition);
				$total_st_trained = tep_db_fetch_array($total_st_trained_query);

				$total_bc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'BC' and is_deactivated != '1'" . $training_condition);
				$total_bc_trained = tep_db_fetch_array($total_bc_trained_query);

				$total_other_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s , " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'" . $training_condition);
				$total_other_trained = tep_db_fetch_array($total_other_trained_query);

				$total_general_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and s.student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'" . $training_condition);
				$total_general_trained = tep_db_fetch_array($total_general_trained_query);

				$total_trained_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and b.section_id = '" . $sections['section_id'] . "' and is_minority_category = '1' and is_deactivated != '1'" . $training_condition);
				$total_trained_minority = tep_db_fetch_array($total_trained_minority_query);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $cnt_sn);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $sections['section_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_trained_student['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_male_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_female_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_sc_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_st_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_bc_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_other_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_general_trained['total_trained']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_trained_minority['total_trained']);

				$cnt_sn++;
				$sheet_row++;
			}
		}else if($_POST['form_action'] == 'nsdc_report'){
			$isds_cols_array = array('S. No.', 'center', 'CentreID', 'NSDC SDMS Number', 'Batch No/Code', 'Batch Disctrict', 'sector', 'Course Name', 'CourseID', 'CourseFee', 'Course Type Residential/Non Residential', 'TrainerID', 'Batch Start Date', 'Batch End Date', 'Batch Status Completed/ In Progress', 'Candidate First Name', 'Candidate Last Name', 'Date of Birth (DD-MM-YYYY)', 'Father First Name', 'Father Last Name', 'Aadhaar Enrollment Number', 'Aadhaar Number', 'Gender', 'Category SC/ST/BC/OTHERS', 'Physical Disability (Y/N)', 'Religion', 'Candidate State', 'Candidate District', 'Candidate Pin Code', 'Mobile No of Candidate', 'Pre Training Status Employed (Y/N)', 'Total Work Experience', 'Prior Training Earning', 'Education Qualification', 'Total Days Attended', '% Day of Attended','Certified (Y/N)', 'Certification Date (DD-MM-YYYY)', 'Certificate name', 'Certificate no', 'Test Date', 'Testing Agency', 'Assessor', 'Certifying Agency', 'Placement Status (Working/Dropout)', 'Placement Type', 'Letter of Offer/ Declaration Collected (Y/N)', 'Date of Joining (DD-MM-YYYY)', 'Employer Name Or Self Employed', 'Employer Contact Person Name', 'Employer Contact Person Designation', 'Employer Contact No', 'Location of employer State', 'Location of employer District', 'Gross Salary', 'Candidate Bank Name', 'Candidate Branch Address', 'Candidate Ifsc Code', 'Candidate Bank Account Number', 'NSDC Stage 1 uploaded (Y/N)','NSDC Stage 2 uploaded (Y/N)','CED Portal Stage 1','CED Portal Stage 1 Date','CED Portal Stage 2','CED Portal Stage 2 Date');

			$rows = 1;
			$cnt_cols = 0;
			$alphabet = 'A';

			foreach($isds_cols_array as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);
			
			$batch_query_raw = " select b.batch_id, b.centre_id, b.section_id, b.course_id, b.batch_title, batch_start_date, batch_end_date, handholding_end_date, test_allotted_date, b.test_agency, b.batch_status, cn.centre_name,cn.centre_unq_id, s.section_name, c.course_name, c.course_fee, c.course_unq_id, c.course_code, c.course_duration, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id)  LEFT JOIN  " . TABLE_CENTRES . " cn ON (cn.centre_id = b.centre_id), " . TABLE_SECTIONS . " s, " . TABLE_COURSES . " c where b.course_id = c.course_id and s.section_id = b.section_id ";

			if($_POST['batch_id'] != '')$batch_query_raw .= " and b.batch_id = '" . $_POST['batch_id'] . "'";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}else{
				if($_POST['centre_id'] != '')$batch_query_raw .= " and b.centre_id = '" . $_POST['centre_id'] . "'";
			}

			if($_POST['course_id'] != '')$batch_query_raw .= " and b.course_id = '" . $_POST['course_id'] . "'";
			if($_POST['section_id'] != '')$batch_query_raw .= " and b.section_id = '" . $_POST['section_id'] . "'";

			if($_POST['district_id'] != '')$batch_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";

			$batch_query_raw .= "  order by b.batch_title";

			$batch_query = tep_db_query($batch_query_raw);


			$sr_no = 1;
			$rows = 2;
			
			while($batch = tep_db_fetch_array($batch_query)){
				$students_query_raw = "select student_id, course_id, course_option, stage1_uploaded, stage2_uploaded, sdms_number, student_full_name, student_surname, student_dob, student_father_name,father_surname, is_student_aadhar_card, student_aadhar_card, student_gender, student_category, is_physical_disability, student_religion, student_state, student_district, student_pincode, student_mobile, is_unemployed, student_total_exp, student_income, student_qualification, is_certificate_recieved, certificate_date, certificate_name, certificate_number, test_allotted_date, test_agency, assessor_name, certificate_body_name, student_bank_name, student_branch, bank_ifsc_code, student_account_number, stage1_ced_portal, stage1_ced_portal_date, stage2_ced_portal, stage2_ced_portal_date from  " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "'";

				$students_query = tep_db_query($students_query_raw);

				while($students = tep_db_fetch_array($students_query)){

					$course_query = "select course_duration from " . TABLE_COURSES . " where course_id = '" . $students['course_id'] . "'";
					$course_query = tep_db_query($course_query);
					$courses = tep_db_fetch_array($course_query);

					$placement_query = "select p.company_id ,job_status, placement_type, offer_letter_collected, job_joining_date, gross_salary, co.district_id, d.state, d.	district_name , co.company_name, co.company_contact_person, co.company_contact_person_designation, co.company_phone_std, co.company_phone, co.company_email, co.company_address, co.company_pincode from " . TABLE_PLACEMENTS . " p LEFT JOIN ". TABLE_COMPANIES . " co ON (co.company_id = p.company_id)  LEFT JOIN ". TABLE_DISTRICTS . " d ON (d.district_id = co.district_id) where student_id = ".$students['student_id']." and job_status = 'WORKING'";
					$placement_query = tep_db_query($placement_query);

					$placements = tep_db_fetch_array($placement_query);

					$student_abs_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where student_id = '" . $students['student_id'] . "' and attendance = 'ABSENT'");
					$student_abs = tep_db_fetch_array($student_abs_query);

					$student_attend_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where student_id = '" . $students['student_id'] . "' and attendance = 'ATTEND'");
					$student_attend = tep_db_fetch_array($student_attend_query);

					$day_attended = 0;
					if((int)$student_abs['count'] > 0 || (int)$student_attend['count'] > 0){
						$day_attended = (($student_attend['count'] * 100) / ($student_abs['count'] + $student_attend['count']));
					}
					
					/*$student_attend_query = tep_db_query("select count(attendance_id) as count_attend from " . TABLE_ATTENDANCE . " where student_id = " . $students['student_id'] . " and attendance = 'ATTEND'");

					$student_attend = tep_db_fetch_array($student_attend_query);*/
					$course_duration = $courses['course_duration'];
					//$day_attended = (($student_attend['count_attend']*100)/$course_duration);
					//(A/(B*100))

					$faculty_query_raw = " select faculty_unq_id from " . TABLE_FACULTIES . " where course_id = '" . $students['course_id'] . "'";
					$faculty_query = tep_db_query($faculty_query_raw);

					$faculty = tep_db_fetch_array($faculty_query);

					/*$handholding_query_raw = "select handholding_id, current_company_name,current_contact_person_name,current_contact_person_designation, current_company_phone,gross_salary, gross_salary from " . TABLE_HANDHOLDING. " where student_id = '" . $students['student_id'] . "'";
					$handholding_query = tep_db_query($handholding_query_raw);
					$handholding = tep_db_fetch_array($handholding_query);*/

					$alphabet = 'A';
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $sr_no);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['centre_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['centre_unq_id']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['sdms_number']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['batch_title']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['district_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['section_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_unq_id']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_fee']);
					$alphabet++;
						
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_course_option[$students['course_option']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $faculty['faculty_unq_id']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_start_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_end_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_batch_status[$batch['batch_status']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_full_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['student_dob']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['father_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_student_aadhar_card'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_aadhar_card']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_gender[$students['student_gender']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_category[$students['student_category']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_physical_disability'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_religion']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_state']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_district']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_pincode']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_unemployed'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_total_exp']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_income']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['student_qualification'] == 'OTHERS' ? $students['student_qualification'] : $arr_qualification[$students['student_qualification']]));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_attend['count']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $day_attended);
					$alphabet++;

					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_certificate_recieved'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['certificate_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['certificate_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['certificate_number']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['test_allotted_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['test_agency']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['assessor_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['certificate_body_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['job_status']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['placement_type']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($placements['offer_letter_collected'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($placements['job_joining_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_contact_person']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_contact_person_designation']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_phone_std'] . ' ' . $placements['company_phone']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['state']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['district_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['gross_salary']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_bank_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_branch']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['bank_ifsc_code']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_account_number']);
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
					$alphabet++;

					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage1_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage1_ced_portal_date']));
					$alphabet++;
					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage2_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage2_ced_portal_date']));

					$sr_no++;
					$rows++;
				}
			}
	}else if($_POST['form_action'] == '20_col_report'){
			$arr_batch_cols = array('SR NO', 'Centre', 'Batch No/Code', 'Course Name', 'Batch Start Date (DD-MM-YYYY)', 'Batch End Date (DD-MM-YYYY)', 'User Id ', 'User Id&Password ', 'Name', 'Student Name as per Adhaar', 'Father\'s / Husband\'s Name', 'Mother\'s Name', 'Date Of Birth', 'Religion', 'Nationality', 'Gender', 'Category', 'Languages known', 'General Qualification', 'Professional Qualification', 'Other Professional Qualification', 'Address', 'State', 'District', 'City', 'PIN Code', 'Mobile Number', 'Photo');

			$rows = 1;
			$cnt_cols = 0;

			$alphabet = 'A';
			foreach($arr_batch_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);

				$alphabet++;
				$cnt_cols++;
			}


			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

			$batch_query_raw = " select b.batch_id, b.centre_id, b.section_id, b.course_id, b.batch_title, b.batch_start_date, b.batch_end_date, b.handholding_end_date, test_allotted_date, b.test_agency, b.batch_status, cn.centre_name, s.section_name, c.course_name, c.course_code, c.course_duration, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id) LEFT JOIN " . TABLE_CENTRES . " cn ON (cn.centre_id = b.centre_id), " . TABLE_SECTIONS . " s, " . TABLE_COURSES . " c where b.course_id = c.course_id and s.section_id = b.section_id ";

			if($_POST['batch_id'] != '')$batch_query_raw .= " and b.batch_id = '" . $_POST['batch_id'] . "'";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}else{
				if($_POST['centre_id'] != '')$batch_query_raw .= " and b.centre_id = '" . $_POST['centre_id'] . "'";
			}

			if($_POST['course_id'] != '')$batch_query_raw .= " and b.course_id = '" . $_POST['course_id'] . "'";
			if($_POST['section_id'] != '')$batch_query_raw .= " and b.section_id = '" . $_POST['section_id'] . "'";

			if($_POST['district_id'] != '')$batch_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";

			$batch_query_raw .= "  order by b.batch_title";

			$batch_query = tep_db_query($batch_query_raw);

			$sr_no = 1;
			$rows = 2;

			while($batch = tep_db_fetch_array($batch_query)){

				$students_query_raw = "select student_id, course_option, student_full_name, student_middle_name, student_surname, student_father_name, father_middle_name, father_surname, mother_first_name, mother_middle_name, mother_surname, student_dob, student_age, student_gender, student_category, student_language_known, student_village, student_religion, student_qualification, is_physical_disability, student_family_type, student_qualification, student_address, student_state, student_district, student_pincode, student_mobile, student_photo, student_name_as_aadhar from " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "'";
				$students_query = tep_db_query($students_query_raw);

				while($students = tep_db_fetch_array($students_query)){
					$alphabet = 'A';
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $sr_no);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['centre_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['batch_title']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_start_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_end_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, "");
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, "");
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_name_as_aadhar']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['mother_first_name'] . ' ' . $students['mother_middle_name'] . ' ' . $students['mother_surname']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['student_dob']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_religion[$students['student_religion']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, "INDIAN");
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_gender[$students['student_gender']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_category[$students['student_category']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_language_known']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_qualification']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ""); //Professional Qualification
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ""); //Other Professional Qualification
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_address']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_state']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_district']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_village']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_pincode']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile']);
					$alphabet++;

					if(trim($students['student_photo']) != '' && file_exists(DIR_FS_UPLOAD . $students['student_photo'])){
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						$objDrawing->setName('Customer Signature');
						$objDrawing->setDescription('Customer Signature');
						//Path to signature .jpg file
						$signature = DIR_FS_UPLOAD . $students['student_photo'];
						$objDrawing->setPath($signature);
						//$objDrawing->setOffsetX(18.41);//setOffsetX works properly
						$objDrawing->setCoordinates($alphabet . $rows);             //set image to cell E38
						//$objDrawing->setHeight(296); //signature height 
						$objDrawing->setWidthAndHeight(90,114);
						$objDrawing->setResizeProportional(true);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());  //save      
					}

					$alphabet++;

					$sr_no++;
					$rows++;
				}
			}
		}else if($_POST['form_action'] == 'detaile_nadc_project_report'){
			$isds_cols_array = array('S. No.', 'center', 'Batch No/Code', 'Batch Disctrict', 'sector', 'Course Name', 'Course Type Residential/Non Residential', 'Batch Start Date', 'Batch End Date', 'Batch Status Completed/ In Progress', 'Candidate Name', 'Student Name as per Adhaar', 'Date of Birth (DD-MM-YYYY)', 'Age', 'Gender', 'Category SC/ST/BC/OTHERS', 'Minority (Y/N)',  'Religion', 'Physical Disability (Y/N)', 'Family Type (APL/BPL)', 'Education Qualification', 'ID Proof (Y/N)', 'Mobile No', 'Training Completed (Y/N)', 'No of days Attended', 'Certificate (Y/N)','Test Result', 'Testing Agency', 'Working/ Dropout', 'Placement Type', 'Letter of Offer/Declaration Collected Y/N', 'Name of Employer', 'Employer City', 'Employer Contact No', 'Date of Joining (DD-MM-YYYY)', 'Gross Salary', 'Salary Slip 1', 'Salary Slip 2', 'Salary Slip 3', 'Salary Slip 4', 'Salary Slip 5', 'Salary Slip 6', 'Salary Slip 7', 'Salary Slip 8', 'Salary Slip 9', 'Salary Slip 10', 'Salary Slip 11', 'Salary Slip 12', 'Candidate Bank Name', 'Candidate Bank Branch', 'Candidate Bank Account No', 'Candidate Bank IFSC Code', 'Fee Paid', 'Mode of Payment', 'Instrument No', 'NSDC Stage 1 uploaded (Y/N)', 'NSDC Stage 2 uploaded (Y/N)', 'NSDC SDMS Number' ,'CED Portal Stage 1','CED Portal Stage 1 Date','CED Portal Stage 2','CED Portal Stage 2 Date');
			$rows = 1;
			$cnt_cols = 0;
			$alphabet = 'A';
			
			foreach($isds_cols_array as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);
			
			$batch_query_raw = " select b.batch_id, b.centre_id, b.section_id, b.course_id, b.batch_title, batch_start_date, batch_end_date, handholding_end_date, test_allotted_date, b.test_agency, b.batch_status, cn.centre_name, s.section_name, c.course_name, c.course_code, c.course_duration, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id)  LEFT JOIN  " . TABLE_CENTRES . " cn ON (cn.centre_id = b.centre_id), " . TABLE_SECTIONS . " s, " . TABLE_COURSES . " c where b.course_id = c.course_id and s.section_id = b.section_id ";
			if($_POST['batch_id'] != '')$batch_query_raw .= " and b.batch_id = '" . $_POST['batch_id'] . "'";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}else{
				if($_POST['centre_id'] != '')$batch_query_raw .= " and b.centre_id = '" . $_POST['centre_id'] . "'";
			}

			if($_POST['course_id'] != '')$batch_query_raw .= " and b.course_id = '" . $_POST['course_id'] . "'";
			if($_POST['section_id'] != '')$batch_query_raw .= " and b.section_id = '" . $_POST['section_id'] . "'";

			if($_POST['district_id'] != '')$batch_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";

			$batch_query_raw .= "  order by b.batch_title";

			$batch_query = tep_db_query($batch_query_raw);
			

			$sr_no = 1;
			$rows = 2;

			while($batch = tep_db_fetch_array($batch_query)){
				$students_query_raw = "select student_id, centre_id, course_option, student_full_name, student_middle_name, student_surname, student_dob, student_age, student_gender, student_category, is_minority_category, student_religion, is_physical_disability, student_family_type, student_qualification, is_student_other_proof, student_mobile, is_training_completed, is_certificate_recieved, student_bank_name, student_branch, student_account_number, bank_ifsc_code, student_payable_fee, student_course_fee, stage1_uploaded, stage2_uploaded, sdms_number, stage1_ced_portal, stage1_ced_portal_date, stage2_ced_portal, stage2_ced_portal_date, is_apeared_for_test, test_result, student_name_as_aadhar from  " . TABLE_STUDENTS . " where batch_id = '" . $batch['batch_id'] . "'";
				$students_query = tep_db_query($students_query_raw);
				
				while($students = tep_db_fetch_array($students_query)){
					
					$placement_query = "select p.company_id, job_status, placement_type, offer_letter_collected, job_joining_date, gross_salary, co.district_id, d.state, d.district_name , co.company_name, co.company_contact_person, co.company_contact_person_designation, co.company_phone_std, co.company_phone, co.company_email, co.company_address, co.company_pincode, cty.city_name from " . TABLE_PLACEMENTS . " p LEFT JOIN ". TABLE_COMPANIES . " co ON (co.company_id = p.company_id)  LEFT JOIN ". TABLE_DISTRICTS . " d ON (d.district_id = co.district_id) left join " . TABLE_CITIES . " cty on (cty.city_id = co.city_id) where student_id = ".$students['student_id']." and job_status = 'WORKING'";
					$placement_query = tep_db_query($placement_query);
					
					$placements = tep_db_fetch_array($placement_query);
					
					$installment_query = " Select instrument_no from " . TABLE_INSTALLMENTS . " where student_id = ".$students['student_id']." and installment_mop = 'CHEQUE'";
					$installment_query = tep_db_query($installment_query);
					$installment = tep_db_fetch_array($installment_query);
				
					$handholding_query_raw = "select h.handholding_id, h.gross_salary, h.current_joining_date, h.salary_slip, comp.company_id, comp.centre_id, comp.city_id, comp.company_name, comp.company_address, comp.company_phone, comp.company_contact_person, comp.company_contact_person_designation, comp.company_email, comp.company_phone_std, comp.company_phone, c.city_name, d.district_name, d.state from " . TABLE_HANDHOLDING . " h left join " . TABLE_COMPANIES . " comp on (comp.company_id = h.company_id), " . TABLE_CITIES . " c, " . TABLE_DISTRICTS . " d where comp.city_id = c.city_id and d.district_id = c.district_id and student_id = '" . $students['student_id'] . "' ORDER BY handholding_id ASC";
					$handholding_query = tep_db_query($handholding_query_raw);
					$handholding = array();
					$salary_slips = array();
	
					$cnt_hh = 1;
					if(tep_db_num_rows($handholding_query)){
						while($handholding_temp = tep_db_fetch_array($handholding_query)){
							$handholding = $handholding_temp;
							$salary_slips[$cnt_hh] = $handholding_temp['salary_slip'];
							$cnt_hh++;
						}
					}
					$student_attend_query = tep_db_query("select count(attendance_id) as count_attend from " . TABLE_ATTENDANCE . " where student_id = " . $students['student_id'] . " and attendance = 'ATTEND'");
	
					$student_attend = tep_db_fetch_array($student_attend_query);
					
					$test_result = ($students['is_apeared_for_test'] == 1 ? $students['test_result'] : 'ABSENT');
					
					$cnt_innter = 0;
	
					$student_waivers_query_raw = "select student_waiver_id, stud_payment_id, student_id, waiver_id, waiver_title, waiver_desc, course_fee, waiver_amount, waiver_reason, waiver_added_by, waiver_added from " . TABLE_STUDENT_WAIVERS . " where student_id='" . $students['student_id'] . "'";
					$student_waivers_query = tep_db_query($student_waivers_query_raw);
					$student_waivers = array();
					$waiver_amounts = 0;
					while($student_waivers_temp = tep_db_fetch_array($student_waivers_query)){
											
						$waiver_amounts += $student_waivers_temp['waiver_amount'];
					}
					
					$student_payments_query_raw = "select sp.stud_payment_id, sp.student_id, sp.deposit_id, sp.stud_payment_type, sp.stud_payment_mode, sp.stud_payment_cheque_no, sp.stud_payment_bank_name, sp.stud_payment_bank_branch, date_format(sp.stud_payment_deposit_date, '%d-%m-%Y') as stud_payment_deposit_date, sp.stud_payment_amount, sp.stud_payment_receipt_no, sp.stud_payment_status, sp.stud_payment_added, sp.cheque_cleared from " . TABLE_STUDENT_PAYMENTS . " sp where sp.student_id='" . $students['student_id'] . "' ";
					$student_payments_query_raw .= 'ORDER BY sp.stud_payment_type, sp.stud_payment_status DESC';
							
					$non_deposited = 0;
					$deposited = 0;
					$total_paid = 0;
					$student_payments_query = tep_db_query($student_payments_query_raw);
					
					$student_payments = array();
					while($student_payments_temp = tep_db_fetch_array($student_payments_query)){
						if($student_payments_temp['stud_payment_status'] == 'NOT_DEPOSITED'){
							$non_deposited += $student_payments_temp['stud_payment_amount'];
						}else if($student_payments_temp['stud_payment_status'] == 'DEPOSITED'){
							$deposited += $student_payments_temp['stud_payment_amount'];
						}
					}
	
					$refund_query_raw = "select refund_id, centre_id, student_id, refund_amount, refund_mode, refund_inst_no, refund_bank_name, refund_branch_name, refund_reason, refund_review, date_format(refund_added, '%d-%m-%Y') as refund_added from " . TABLE_REFUNDS . " where student_id='" . $students['student_id'] . "'";
					$refund_query = tep_db_query($refund_query_raw);
	
					$refund_array = array();
					$refund_amount = 0;
					while($refund_array_temp = tep_db_fetch_array($refund_query)){
						$refund_amount += $refund_array_temp['refund_amount'];
					}
					$total_paid = ($deposited - $refund_amount);
					$total_paid = ($total_paid > 0 ? $total_paid : 0); 		
									
					//code ends 24-08-2017 
					
					$alphabet = 'A';
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $sr_no);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['centre_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['batch_title']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['district_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['section_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $batch['course_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_course_option[$students['course_option']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_start_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($batch['batch_end_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_batch_status[$batch['batch_status']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_full_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_name_as_aadhar']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['student_dob']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_age']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_gender[$students['student_gender']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_category[$students['student_category']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_minority_category'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_religion']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_physical_disability'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $arr_family_type[$students['student_family_type']]);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['student_qualification'] == 'OTHERS' ? $students['student_qualification'] : $arr_qualification[$students['student_qualification']]));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_student_other_proof'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_mobile']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_training_completed'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $student_attend['count_attend']);
					$alphabet++;
						
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['is_certificate_recieved'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					//test Result 24-08-2017
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $test_result);
					$alphabet++;
					//test Result Ends here 24-08-2017  
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['test_agency']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['job_status']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['placement_type']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($placements['offer_letter_collected'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['city_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['company_phone']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($placements['job_joining_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $placements['gross_salary']);
					$alphabet++;
	
					for($cnt_ss = 1; $cnt_ss <= 12; $cnt_ss++){
						$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, (isset($salary_slips[$cnt_ss]) && $salary_slips[$cnt_ss] != '' ? "Y" : 'N'));
						$alphabet++;
					}
	
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_bank_name']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_branch']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['student_account_number']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['bank_ifsc_code']);
					$alphabet++;
					//Total Paid 24-08-2017 //$students['student_payable_fee'] replace with $total_paid
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $total_paid); 
					$alphabet++;
				
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, '');//$students['student_course_fee']
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $installment['instrument_no']);
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $students['sdms_number']);
					$alphabet++;
	
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage1_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage1_ced_portal_date']));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, ($students['stage2_ced_portal'] == '1' ? 'Y' : 'N'));
					$alphabet++;
					$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, display_valid_date($students['stage2_ced_portal_date']));
					
					$sr_no++;
					$rows++;
	
				}
			}
		}
		
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
		<title><?php echo TITLE ?>: Overall Project Report</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
		<script type="text/javascript">
		<!--
			function get_courses(default_course){
				var section = $('#section_id').val();

				$('#course_id').empty();
				$('#course_id').append($("<option></option>").attr("value",'').text('All'));

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
				$('#batch_id').append($("<option></option>").attr("value",'').text('All'));

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
			function batch_info_report(){
				document.frmSigleBatch.form_action.value = 'batch_info_report';
				document.frmSigleBatch.submit();
			}
			function nsdc_report(){
				document.frmSigleBatch.form_action.value = 'nsdc_report';
				document.frmSigleBatch.submit();
			}
			function detaile_nadc_project_report(){
				document.frmSigleBatch.form_action.value = 'detaile_nadc_project_report';
				document.frmSigleBatch.submit();
			}
			function twenty_col_report(){
				document.frmSigleBatch.form_action.value = '20_col_report';
				document.frmSigleBatch.submit();
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
													<td colspan="2" class="arial18BlueN">Batch Report</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td align="center" colspan="2">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="full_batch_report">
														<table cellpadding="2" cellspacing="0" border="0" width="100%" align="center">
															<tr>
																<td><br>
																	&nbsp;<input type="submit" value="Full Batch Report" name="cmdExcel" id="cmdExcel" class="groovybutton"></td>
																</td>
																<td>&nbsp;<td>
															</tr>
														</table>
														</form><br>
													</td>
												</tr>
												<tr>
													<td align="center" colspan="2" style="border-top: dashed 1px #000000;">
														<form name="frmSigleBatch" id="frmSigleBatch" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="batch_info_report">
														<table cellpadding="5" cellspacing="0" border="0" width="100%" align="center">
															<tr>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Batch District<br>
																	<select name="district_id" id="district_id" title="Choose district">
																		<option value="">All</option>
																		<?php
																			$disctrict_query_raw = " select d.district_id, d.district_name from ". TABLE_DISTRICTS ." d, " . TABLE_BATCHES . " b where b.district_id = d.district_id ";
																			if($_SESSION['sess_adm_type'] != 'ADMIN'){
																				$disctrict_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																			}
																			$disctrict_query_raw .= " group by d.district_id order by d.district_name";
																			$disctrict_query = tep_db_query($disctrict_query_raw);
																			
																			while($disctrict = tep_db_fetch_array($disctrict_query)){
																		?>
																		<option value="<?php echo $disctrict['district_id'];?>"><?php echo $disctrict['district_name'];?></option>
																		<?php } ?>
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
																		<option value="">All</option>
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
																		<option value="">All</option>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold">
																	Batch<br>
																	<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																		<option value="">All</option>
																	</select>
																</td>
																<!-- <td class="arial14LGrayBold">Batch<br><br>
																	<select name="batch_id" id="batch_id">
																	<option value="">All</option>
																	<?php
																		/*$batch_query_raw = "select b.batch_id, b.batch_title from " . TABLE_BATCHES . " b where 1";

																		if($_SESSION['sess_adm_type'] != 'ADMIN'){
																			$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																		}

																		$batch_query_raw .= " order by b.batch_title";

																		$batch_query = tep_db_query($batch_query_raw);

																		while($batch = tep_db_fetch_array($batch_query)){*/
																	?>
																		<option value="<?php //echo $batch['batch_id'];?>"><?php //echo $batch['batch_title'];?></option>
																	<?php
																		//}
																	?>
																	</select>
																</td> -->
															</tr>
															<!-- <tr>
																<td class="arial14LGrayBold">Batch Code<br><br>
																	<input type="text" name="batch_code" id="batch_code" value="">
																</td>
															</tr> -->
															<tr>
																<td colspan="4"><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
															</tr>
															<tr>
																<td colspan="4">
																	<table cellpadding="5" cellspacing="0" border="0" width="100%">
																		<tr>
																			<td width="25%"><button type="button" value="Batch Report" name="cmdExcel" id="cmdExcel" class="groovybutton" onclick="batch_info_report();">Batch Report</button>
																			<td width="25%"><button type="button" value="NSDC Report" name="cmdExcel" id="cmdExcel" class="groovybutton" onclick="nsdc_report();">NSDC Report</button></td>
																			<td width="25%"><button type="button" value="Detailed NSDC Project Report" name="cmdExcel" id="cmdExcel" class="groovybutton" onclick="detaile_nadc_project_report();">Detailed NSDC Project Report</button></td>
																			<td width="25%"><button type="button" value="20 Column Report" name="cmdExcel" id="cmdExcel" class="groovybutton" onclick="twenty_col_report();">20 Column Report</button></td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														</form>
													</td>
												</tr>
												<tr>
													<td align="center" colspan="2">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="batch_overview_report">
														<table cellpadding="2" cellspacing="0" border="0" width="100%" align="center">
															<tr>
																<td><br>
																	<select name="training_status">
																		<option value="COMPLETED">Training Completed</option>
																		<option value="PROGRESS">Training In Progress</option>
																	</select>
																	&nbsp;<input type="submit" value="Batch Overview Report" name="cmdExcel" id="cmdExcel" class="groovybutton"></td>
																</td>
																<td>&nbsp;<td>
															</tr>
														</table>
														</form><br>
													</td>
												</tr>
												<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
												<tr>
													<td colspan="2">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="sector_batch_report">
														<select name="training_status">
															<option value="COMPLETED">Training Completed</option>
															<option value="PROGRESS">Training In Progress</option>
														</select>
														&nbsp;<input type="submit" value="Batch Sector Report" name="cmdExcel" id="cmdExcel" class="groovybutton">
														</form><br>
													</td>
												</tr>
												<?php } ?>
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