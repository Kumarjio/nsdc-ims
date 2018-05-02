<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	//$arr_overall_proj_cols = array('S. No', 'Center', 'District', 'Course', 'Batch Title', 'Batch Start Date', 'Batch End Date', 'Batch Handholding Date', 'Total No of Students Trained', 'Total No of Students Certified', 'Total No of Students Placed', 'Total Male Trained', 'Total Female Trained', 'Total Male Placed', 'Total Female Placed', 'No of Residential Candidates', 'No of Non Residential Candidates', 'No of SC', 'No of ST', 'No of Others', 'No of Minority', 'No of Physically Handicapped', 'No of Placement Allowance Instalment 1', 'No of Placement Allowance Instalment 2', 'No of Non Residential Allowance');

	$arr_overall_proj_cols = array('S.No', 'Centre ID', 'Centre Location', 'Centre District', 'Batch Code', 'Batch District', 'Course Name', 'Batch Start Dt', 'Batch End Dt:', 'Handholding End Date', 'Batch Status', 'Total No of Candidates Trained', 'No of Male Trained', 'No of Female Trained', 'SC Trained', 'ST Trained', 'BC Trained', 'Others Trained', 'Minority Trained', 'General Trained', 'Number Candidates Certified', 'Total No of Candidates Placed ', 'Male Placed', 'Female Placed', 'SC Placed', 'ST Placed', 'BC Placed', 'Others Placed', 'Minority Placed', 'General Placed', 'No of Residential Candidates', 'No of Non Residential Candidates', 'No of Placement Allowance Instalment 1', 'No of Placement Allowance Instalment 2', 'No of Non Residential Allowance', 'No of Aadhar Card Receipts', 'No of Aadhar Card' , 'No of Physical Handicapped', 'No of Bank Account Opened', 'Total Under Training', 'No of Male Under Training', 'No of Female Under Training', 'No of SC Under Training', 'No of ST Under Training', 'No of BC Under Training', 'No of Others Under Training', 'No of Minorities Under Training', 'No of General Under Training', 'Total Uploaded to NSDC Stage 1', 'Total Uploaded to NSDC Stage 2');

	//'Total No of Students Working', , 

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

		$excelsheet_name = 'proschool_sgsy_overall_project_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		//$arr_alphabet = range('A', 'Z');
		$start_alpha = 'A';

		$rows = 1;
		$cnt_cols = 0;

		foreach($arr_overall_proj_cols as $column){
			$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $column);
			$cnt_cols++;
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $start_alpha . '1')->applyFromArray($heading_bold);

		$center_query_raw = " select cn.centre_id, cn.district_id, cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

		if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";

		$center_query_raw .= " order by cn.centre_name";

		$center_query = tep_db_query($center_query_raw);

		$sr_no = 1;
		$rows = 2;

		while($centre = tep_db_fetch_array($center_query)){

			$courses_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_code, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id ";

			if($_POST['course_id'] != '')$courses_query_raw .= " and c.course_id = '" . $_POST['course_id'] . "'";

			$courses_query_raw .= "  order by c.course_name";

			$courses_query = tep_db_query($courses_query_raw);

			while($courses = tep_db_fetch_array($courses_query)){

				$batches_query_raw = "select b.*, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id) where b.course_id = '"  . $courses['course_id'] . "'";

				if(tep_not_null($start_month) && tep_not_null($start_year) && !tep_not_null($end_month) && !tep_not_null($end_year)){
					$start_date = $start_year . "-" . $start_month . "-01";
					$batches_query_raw .= " and batch_start_date >= '" . $start_date . "'";
				}else if(!tep_not_null($start_month) && !tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

					$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
					$end_date = $end_year . "-" . $end_month . "-" . $last_day;

					$batches_query_raw .= " and batch_end_date <= '" . $end_date . "'";

				}else if(tep_not_null($start_month) && tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

					$start_date = $start_year . "-" . $start_month . "-01";
					$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
					$end_date = $end_year . "-" . $end_month . "-" . $last_day;

					$batches_query_raw .= " and (batch_start_date >= '" . $start_date . "' and batch_end_date <= '" . $end_date . "')";
				}

				$batches_query_raw .= " and centre_id = '" . $centre['centre_id'] . "'";

				$batches_query = tep_db_query($batches_query_raw);
				while($batches = tep_db_fetch_array($batches_query)){
					$total_cert_student_query = tep_db_query("select count(student_id) as total_certified from " . TABLE_STUDENTS . " where test_result = 'PASS' and batch_id = '" . $batches['batch_id'] . "' and is_deactivated != '1'");
					$total_cert_student = tep_db_fetch_array($total_cert_student_query);

					$total_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '"  . $batches['batch_id'] . "' and s.is_deactivated != '1'");
					$total_placed = tep_db_fetch_array($total_placed_query);

					$total_working_query = tep_db_query("select count(handholding_id) as total_works from " . TABLE_HANDHOLDING . " hh, " . TABLE_STUDENTS . " s where s.student_id = hh.student_id and s.batch_id = '"  . $batches['batch_id'] . "' and hh.student_status = 'WORKING' and s.is_deactivated != '1' group by s.student_id");
					$total_working = tep_db_fetch_array($total_working_query);

					$total_trained_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and is_deactivated != '1'");
					$total_trained_student = tep_db_fetch_array($total_trained_student_query);

					$total_male_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'");
					$total_male_trained = tep_db_fetch_array($total_male_trained_query);

					$total_female_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'");
					$total_female_trained = tep_db_fetch_array($total_female_trained_query);

					$total_sc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc_trained = tep_db_fetch_array($total_sc_trained_query);

					$total_st_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st_trained = tep_db_fetch_array($total_st_trained_query);

					$total_bc_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'BC' and is_deactivated != '1'");
					$total_bc_trained = tep_db_fetch_array($total_bc_trained_query);

					$total_other_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other_trained = tep_db_fetch_array($total_other_trained_query);

					$total_trained_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and is_minority_category = '1' and is_deactivated != '1'");
					$total_trained_minority = tep_db_fetch_array($total_trained_minority_query);

					$total_trained_general_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'");
					$total_trained_general = tep_db_fetch_array($total_trained_general_query);

					/*Placement*/

					$total_male_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.course_id = '"  . $courses['course_id'] . "' and p.centre_id = '" . $centre['centre_id'] . "' and student_gender = 'MALE' and s.batch_id = '" . $batches['batch_id'] . "' and s.is_deactivated != '1'");
					$total_male_placed = tep_db_fetch_array($total_male_placed_query);

					$total_female_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.course_id = '"  . $courses['course_id'] . "' and p.centre_id = '" . $centre['centre_id'] . "' and student_gender = 'FEMALE' and s.batch_id = '" . $batches['batch_id'] . "' and s.is_deactivated != '1'");
					$total_female_placed = tep_db_fetch_array($total_female_placed_query);

					$total_sc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and student_category = 'SC' and s.is_deactivated != '1'");
					$total_sc_placed = tep_db_fetch_array($total_sc_placed_query);

					$total_st_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and student_category = 'ST' and s.is_deactivated != '1'");
					$total_st_placed = tep_db_fetch_array($total_st_placed_query);

					$total_bc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and student_category = 'BC' and s.is_deactivated != '1'");
					$total_bc_placed = tep_db_fetch_array($total_bc_placed_query);

					$total_other_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and student_category = 'OTHERS' and s.is_deactivated != '1'");
					$total_other_placed = tep_db_fetch_array($total_other_placed_query);

					$total_minority_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and s.is_minority_category = '1' and s.is_deactivated != '1'");
					$total_minority_placed = tep_db_fetch_array($total_minority_placed_query);

					$total_general_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.batch_id = '" . $batches['batch_id'] . "' and s.student_category = 'GENERAL' and s.is_minority_category = '0' and s.is_deactivated != '1'");
					$total_general_placed = tep_db_fetch_array($total_general_placed_query);

					$total_res_student_query = tep_db_query("select count(student_id) as total_res from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and course_option = 'RESIDENTIAL' and is_deactivated != '1'");
					$total_res_student = tep_db_fetch_array($total_res_student_query);

					$total_nonres_student_query = tep_db_query("select count(student_id) as total_res from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and course_option = 'NON_RESIDENTIAL' and is_deactivated != '1'");
					$total_nonres_student = tep_db_fetch_array($total_nonres_student_query);

					//New fields

					$total_sc_query = tep_db_query("select count(student_id) as total_student from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc = tep_db_fetch_array($total_sc_query);

					$total_st_query = tep_db_query("select count(student_id) as total_student from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st = tep_db_fetch_array($total_st_query);

					$total_other_query = tep_db_query("select count(student_id) as total_student from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other = tep_db_fetch_array($total_other_query);

					$total_minority_query = tep_db_query("select count(student_id) as total_student from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and is_minority_category = '1' and is_deactivated != '1'");
					$total_minority = tep_db_fetch_array($total_minority_query);

					//Total handicarft Receipt
					$total_handicraft_query = tep_db_query("select count(student_id) as total_aadhar from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and is_physical_disability = '1' and is_deactivated != '1'");
					$total_handicraft = tep_db_fetch_array($total_handicraft_query);

					$total_installment1_query_raw = "select count(inst.installment_id) as total_installment from " . TABLE_INSTALLMENTS . " inst, " . TABLE_STUDENTS . " s where inst.student_id = s.student_id and s.batch_id = '" . $batches['batch_id'] . "' and installment_no = '1' and inst.installment_type = 'PLACEMENT_ALLOWANCE' and s.is_deactivated != '1'";
					$total_installment1_query = tep_db_query($total_installment1_query_raw);
					$total_installment1 = tep_db_fetch_array($total_installment1_query);

					$total_installment2_query_raw = "select count(inst.installment_id) as total_installment from " . TABLE_INSTALLMENTS . " inst, " . TABLE_STUDENTS . " s where inst.student_id = s.student_id and s.batch_id = '" . $batches['batch_id'] . "' and installment_no = '2	' and inst.installment_type = 'PLACEMENT_ALLOWANCE' and s.is_deactivated != '1'";
					$total_installment2_query = tep_db_query($total_installment2_query_raw);
					$total_installment2 = tep_db_fetch_array($total_installment2_query);

					$total_non_res_query = tep_db_query("select count(student_id) as total_students from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and non_res_alw_amt_paid > '0' and is_deactivated != '1'");
					$total_non_res = tep_db_fetch_array($total_non_res_query);

					//Bank account opened
					$total_bank_ac_opened_query = tep_db_query("select count(student_id) as total_bank_ac_opened from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and is_bank_account = '1' and is_deactivated != '1'");
					$total_bank_ac_opened = tep_db_fetch_array($total_bank_ac_opened_query);

					$total_aadhar_rec_query = tep_db_query("select count(student_id) as total_aadhar from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and is_student_aadhar_card = '0' and student_aadhar_card_receipt != '' and is_deactivated != '1'");
					$total_aadhar_rec = tep_db_fetch_array($total_aadhar_rec_query);

					//Total Aadhar
					$total_aadhar_query = tep_db_query("select count(student_id) as total_aadhar from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and is_student_aadhar_card = '1' and student_aadhar_card != '' and is_deactivated != '1'");
					$total_aadhar = tep_db_fetch_array($total_aadhar_query);

					//Total Physical disablity
					$total_physical_dis_query = tep_db_query("select count(student_id) as total_physical_dis from " . TABLE_STUDENTS . " s where 1 and s.batch_id = '" . $batches['batch_id'] . "' and is_physical_disability = '1' and is_deactivated != '1'");
					$total_physical_dis = tep_db_fetch_array($total_physical_dis_query);

					//Under Training

					$total_under_training_student_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and is_deactivated != '1'");
					$total_under_training_student = tep_db_fetch_array($total_under_training_student_query);

					$total_male_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_gender = 'MALE' and is_deactivated != '1'");
					$total_male_under_training = tep_db_fetch_array($total_male_under_training_query);

					$total_female_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_gender = 'FEMALE' and is_deactivated != '1'");
					$total_female_under_training = tep_db_fetch_array($total_female_under_training_query);

					$total_sc_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'SC' and is_deactivated != '1'");
					$total_sc_under_training = tep_db_fetch_array($total_sc_under_training_query);

					$total_st_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'ST' and is_deactivated != '1'");
					$total_st_under_training = tep_db_fetch_array($total_st_under_training_query);

					$total_bc_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'BC' and is_deactivated != '1'");
					$total_bc_under_training = tep_db_fetch_array($total_bc_under_training_query);

					$total_other_under_training_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'OTHERS' and is_deactivated != '1'");
					$total_other_under_training = tep_db_fetch_array($total_other_under_training_query);

					$total_under_training_minority_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and is_minority_category = '1' and is_deactivated != '1'");
					$total_under_training_minority = tep_db_fetch_array($total_under_training_minority_query);

					$total_under_training_general_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed != '1' and batch_id = '" . $batches['batch_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and is_deactivated != '1'");
					$total_under_training_general = tep_db_fetch_array($total_under_training_general_query);

					$total_nsdc_stage_1_query = tep_db_query("select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and stage1_uploaded = '1' and is_deactivated != '1'");
					$total_nsdc_stage_1 = tep_db_fetch_array($total_nsdc_stage_1_query);

					$total_nsdc_stage_2_query = tep_db_query("select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where batch_id = '" . $batches['batch_id'] . "' and stage2_uploaded = '1' and is_deactivated != '1'");
					$total_nsdc_stage_2 = tep_db_fetch_array($total_nsdc_stage_2_query);

					$cnt_innter = 0;
					$start_alpha = 'A';

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $sr_no);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $centre['centre_unq_id']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $centre['centre_name']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, ucwords(strtolower($centre['district_name'])));
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $batches['batch_title']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $batches['district_name']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $courses['course_name']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, display_valid_date($batches['batch_start_date']));
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, display_valid_date($batches['batch_end_date']));
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, display_valid_date($batches['handholding_end_date']));

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $arr_batch_status[$batches['batch_status']]);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_trained_student['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_male_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_female_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_sc_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_st_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_bc_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_other_trained['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_trained_minority['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_trained_general['total_trained']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_cert_student['total_certified']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_male_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_female_placed['total_placement']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_sc_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_st_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_bc_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_other_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_minority_placed['total_placement']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_general_placed['total_placement']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_res_student['total_res']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_nonres_student['total_res']);

					/*$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_sc['total_student']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_st['total_student']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_other['total_student']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_minority['total_student']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_handicraft['total_student']);*/
					//$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_aadhar_rec['total_aadhar']);
					//$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_bank_ac_opened['total_bank_ac_opened']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_installment1['total_installment']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_installment2['total_installment']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_non_res['total_students']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_aadhar_rec['total_aadhar']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_aadhar['total_aadhar']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_physical_dis['total_physical_dis']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_bank_ac_opened['total_bank_ac_opened']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_under_training_student['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_male_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_female_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_sc_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_st_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_bc_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_other_under_training['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_under_training_minority['total_trained']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_under_training_general['total_trained']);

					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_nsdc_stage_1['total_uploaded']);
					$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $total_nsdc_stage_2['total_uploaded']);

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
													<td colspan="2" class="arial18BlueN">Report - Project Overall</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="export_report">
														<table cellpadding="2" cellspacing="0" border="0" width="60%">
															<tr>
																<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
																<td valign="top" class="arial14LGrayBold" width="13%">
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
																<td valign="top" class="arial14LGrayBold"  width="15%">
																	Course<br>
																	<select name="course_id" id="course_id" class="required" style="width:150px">
																		<option value="">All</option>
																		<?php
																			$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id order by course_name";
																			$course_query = tep_db_query($course_query_raw);
																			
																			while($course = tep_db_fetch_array($course_query)){
																		?>
																		<option value="<?php echo $course['course_id'];?>"><?php echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';?></option>
																		<?php } ?>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold"  width="15%">
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