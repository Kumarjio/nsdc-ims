<?php	
	include('includes/application_top.php');

	if($_POST['form_action'] != ''){
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		if($_POST['form_action'] == 'export_report'){

			$reports_cols = array('S. No.','Centre Id','Centre Location','MES Sector','Course Name ','Course Code','Batch No/Code','Course Duration','Batch Start Dt','Batch End Dt:','Handholding End Date', 'Batch District', 'Candidate Name','Candidate Father\'s Name','Gender','Category','Minority (Y/N)','Mobile No', 'Candidate\'s District', 'Training Completed (Y/N)', 'Course Type','AADHAR CARD NO','Student\'s Bank Name ','Student Bank  Branch','Student Bank Account  No','Students Bank IFSC Code','NSDC Stage 1 uploaded (Y/N)', 'NSDC Stage 2 uploaded (Y/N)','NSDC SDMS Number','Placed/ Dropout','Name of the Company/ Employer','Date Of Joining ','Designation of  Candidate','Gross Salary of Candidate', 'Placement Type','Offer Letter/Declaration Collected Y/N','In Hand Salary of Candidate','Any Other Benefits','Name of the Contact Person in the company','Designation of Contact Person in Company','Contact Number of Company Personnel','Email Id of the Company Personnel','Company/ Employer Address','Company/Employer CITY','Company/Employer PIN CODE');

			$excelsheet_name = 'proschool_sgsy_batch_placement_' . time();

			//$arr_alphabet = range('A', 'Z');
			$alphabet = 'A';
			for($cntAlpha=0;$cntAlpha<=count($reports_cols);$cntAlpha++){
				$arr_alphabet[$cntAlpha] = $alphabet;
				$alphabet = get_rand_fix_type($alphabet);
			}

			$rows = 1;
			$cnt_cols = 0;

			$alphabet = 'A';
			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

			$center_query_raw = " select cn.centre_id, cn.district_id,cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

			if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";

			$center_query_raw .= " order by cn.centre_name";

			$center_query = tep_db_query($center_query_raw);

			$sr_no = 1;
			$rows = 2;

			while($centre = tep_db_fetch_array($center_query)){

				$courses_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_code, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id ";

				if($_POST['course_id'] != '')$courses_query_raw .= " and c.course_id = '" . $_POST['course_id'] . "'";
				if($_POST['section_id'] != '')$courses_query_raw .= " and c.section_id = '" . $_POST['section_id'] . "'";

				$courses_query_raw .= "  order by c.course_name";

				$courses_query = tep_db_query($courses_query_raw);

				while($courses = tep_db_fetch_array($courses_query)){

					$batches_query_raw = "select b.batch_id, b.district_id, b.batch_title, date_format(b.batch_start_date, '%d %b %Y') as batch_start_date, date_format(b.batch_end_date, '%d %b %Y') as batch_end_date, date_format(b.handholding_end_date, '%d %b %Y') as handholding_end_date, date_format(b.test_allotted_date, '%d %b %Y') as test_allotted_date, b.test_abn_no, b.test_agency, d.district_name from " . TABLE_BATCHES . " b left join ". TABLE_DISTRICTS ." d on d.district_id = b.district_id where b.centre_id = '" . $centre['centre_id'] . "' and b.course_id = '" . $courses['course_id'] . "'";

					if($_POST['district_id'] != '')$batches_query_raw .= " and b.district_id = '" . tep_db_input($_POST['district_id']) . "'";
					if($_POST['batch_id'] != '')$batches_query_raw .= " and b.batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

					$batches_query = tep_db_query($batches_query_raw);

					while($batches = tep_db_fetch_array($batches_query)){
						$students_query_raw = "select student_id, student_full_name, student_middle_name, student_surname, student_father_name, father_middle_name, father_surname, student_gender, student_mobile, student_district, if(is_training_completed = '1', 'Y', 'N') as is_training_completed, training_dropout_reason, if(is_apeared_for_test = '1', 'Y', 'N') as is_apeared_for_test, test_result, if(is_certificate_recieved = '1', 'Y', 'N') as is_certificate_recieved, student_category, if(is_minority_category = '1', 'Y', 'N') as is_minority_category, student_aadhar_card, student_bank_name, student_branch, student_account_number, bank_ifsc_code, stage1_uploaded, stage2_uploaded, sdms_number, course_option from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "' and batch_id = '" . $batches['batch_id'] . "'  and is_deactivated != '1'";
						$students_query = tep_db_query($students_query_raw);

						while($students = tep_db_fetch_array($students_query)){

							$placement_query_raw = "select p.placement_id, p.student_id, p.company_id, p.centre_id, p.job_status, date_format(p.job_joining_date, '%d %b %Y') as frm_job_joining_date, p.job_designation, p.gross_salary, p.placement_type, p.offer_letter_collected, p.in_hand_salary, p.job_other_benifits, p.post_palacement_allowance, p.offer_letter, p.salary_slip_collected, p.salary_slip, c.company_name, c.company_contact_person, c.company_contact_person_designation, c.company_phone_std, c.company_phone, c.company_email, c.company_address, c.company_pincode, cty.city_name from " . TABLE_PLACEMENTS . " p left join " . TABLE_COMPANIES . " c on c.company_id = p.company_id left join " . TABLE_CITIES . " cty on cty.city_id = c.city_id where p.student_id = '" . $students['student_id'] . "' AND p.job_status = 'WORKING' ";

							if(isset($_POST['month_wise']) && $_POST['month_wise'] == '1'){
								$placement_query_raw .= " and p.job_joining_date like '%" . $_POST['year'] . '-' . ($_POST['month'] <= 9 ? '0' : '') . $_POST['month']  . "%'";
							}

							$placement_query = tep_db_query($placement_query_raw);

							if(tep_db_num_rows($placement_query)){
								$placement = tep_db_fetch_array($placement_query);

								$cnt_innter = 0;
								$alphabet = 'A';
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $sr_no);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $centre['centre_unq_id']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $centre['centre_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['section_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_code']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_title']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_duration']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_start_date']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_end_date']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['handholding_end_date']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['district_name']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_gender']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_category[$students['student_category']]);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['is_minority_category']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_mobile']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_district']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['is_training_completed']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_course_option[$students['course_option']]);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_aadhar_card']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_bank_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_branch']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_account_number']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['bank_ifsc_code']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['sdms_number']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_placement_status[$placement['job_status']]);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['frm_job_joining_date']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['job_designation']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['gross_salary']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['placement_type']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['offer_letter_collected']);
									
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['in_hand_salary']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['job_other_benifits']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_contact_person']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_contact_person_designation']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($placement['company_phone_std'] != '' ? $placement['company_phone_std'] . ' ' : '') . $placement['company_phone']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_email']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_address']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['city_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $placement['company_pincode']);

								$sr_no++;
								$rows++;
							}else{
								$cnt_innter = 0;
								$alphabet = 'A';
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $sr_no);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $centre['centre_unq_id']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $centre['centre_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['section_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_code']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_title']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $courses['course_duration']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_start_date']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['batch_end_date']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['handholding_end_date']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batches['district_name']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_gender']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_category[$students['student_category']]);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['is_minority_category']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_mobile']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_district']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['is_training_completed']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_course_option[$students['course_option']]);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_aadhar_card']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_bank_name']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_branch']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['student_account_number']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['bank_ifsc_code']);
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $students['sdms_number']);

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');

								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');
								$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, '');

								$sr_no++;
								$rows++;
							}
						}
					}
				}
			}
		}else if($_POST['form_action'] == 'overview_report'){
			$excelsheet_name = 'proschool_sgsy_placement_overview_' . time();

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

				$total_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and  s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and s.is_deactivated != '1'");
				$total_placed = tep_db_fetch_array($total_placed_query);

				$total_male_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_gender = 'MALE' and s.is_deactivated != '1'");
				$total_male_placed = tep_db_fetch_array($total_male_placed_query);

				$total_female_placed_query = tep_db_query("select count(p.placement_id) as total_placement, b.batch_id from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_gender = 'FEMALE' and s.is_deactivated != '1'");
				$total_female_placed = tep_db_fetch_array($total_female_placed_query);

				$total_sc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'SC' and s.is_deactivated != '1'");
				$total_sc_placed = tep_db_fetch_array($total_sc_placed_query);

				$total_st_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'ST' and s.is_deactivated != '1'");
				$total_st_placed = tep_db_fetch_array($total_st_placed_query);

				$total_bc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'BC' and s.is_deactivated != '1'");
				$total_bc_placed = tep_db_fetch_array($total_bc_placed_query);

				$total_other_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'OTHERS' and s.is_deactivated != '1'");
				$total_other_placed = tep_db_fetch_array($total_other_placed_query);

				$total_general_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and s.is_deactivated != '1'");
				$total_general_placed = tep_db_fetch_array($total_general_placed_query);

				$total_minor_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and is_minority_category = '1' and s.is_deactivated != '1'");
				$total_minor_placed = tep_db_fetch_array($total_minor_placed_query);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $cnt_sn);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, ucwords(strtolower($districts['state'])));
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $districts['district_name']);

				foreach($sections as $section_info){

					$total_section_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.district_id = '" . $districts['district_id'] . "' and b.section_id = '" . $section_info['section_id'] . "' and s.is_deactivated != '1'");
					$total_section_placed = tep_db_fetch_array($total_section_placed_query);

					$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_section_placed['total_placement']);
				}

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_male_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_female_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_sc_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_st_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_bc_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_other_placed['total_placement']);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_general_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_minor_placed['total_placement']);

				$cnt_sn++;
				$sheet_row++;
			}
		}else if($_POST['form_action'] == 'sector_wise_report'){
			$excelsheet_name = 'proschool_sgsy_sector_wise_placement_report_' . time();

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

				$total_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and  s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and s.is_deactivated != '1'");
				$total_placed = tep_db_fetch_array($total_placed_query);

				$total_male_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_gender = 'MALE' and s.is_deactivated != '1'");
				$total_male_placed = tep_db_fetch_array($total_male_placed_query);

				$total_female_placed_query = tep_db_query("select count(p.placement_id) as total_placement, b.batch_id from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_gender = 'FEMALE' and s.is_deactivated != '1'");
				$total_female_placed = tep_db_fetch_array($total_female_placed_query);

				$total_sc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'SC' and s.is_deactivated != '1'");
				$total_sc_placed = tep_db_fetch_array($total_sc_placed_query);

				$total_st_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'ST' and s.is_deactivated != '1'");
				$total_st_placed = tep_db_fetch_array($total_st_placed_query);

				$total_bc_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'BC' and s.is_deactivated != '1'");
				$total_bc_placed = tep_db_fetch_array($total_bc_placed_query);

				$total_other_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'OTHERS' and s.is_deactivated != '1'");
				$total_other_placed = tep_db_fetch_array($total_other_placed_query);

				$total_general_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and student_category = 'GENERAL' and is_minority_category = '0' and s.is_deactivated != '1'");
				$total_general_placed = tep_db_fetch_array($total_general_placed_query);

				$total_minor_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and s.student_id = p.student_id and b.section_id = '" . $sections['section_id'] . "' and is_minority_category = '1' and s.is_deactivated != '1'");
				$total_minor_placed = tep_db_fetch_array($total_minor_placed_query);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $cnt_sn);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $sections['section_name']);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_male_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_female_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_sc_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_st_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_bc_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_other_placed['total_placement']);

				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_general_placed['total_placement']);
				$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $total_minor_placed['total_placement']);

				$cnt_sn++;
				$sheet_row++;
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
		<title><?php echo TITLE ?> : Batch Placement Report</title>
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

							get_batch('');
						})
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

			function check_detail(action_type){
				document.frm_action.form_action.value = action_type;
				document.frm_action.submit();
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
													<td class="arial18BlueN">Report - Batch Placement</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td align="center">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="">
														<table cellpadding="2" cellspacing="0" border="0" width="100%" align="center">
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
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Course<br>
																	<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch('');" style="width: 120px;">
																		<option value="">Please choose</option>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold">
																	Batch<br>
																	<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																		<option value="">Please choose</option>
																	</select>
																</td>
															</tr>
															<tr><td colspan="3">&nbsp;<td></tr>
															<tr>
																<td valign="top" class="arial14LGrayBold" colspan="3">
																	<input type="checkbox" name="month_wise" id="month_wise" value="1" onclick="javascript: $('.blk_month_wise').toggle();">&nbsp;Export only selected Month
																</td>
															</tr>
															<tr><td colspan="3">&nbsp;<td></tr>
															<tr>
																<td valign="top" class="arial14LGrayBold blk_month_wise" style="display: none;" colspan="3">
																	Month<br>
																	<select name="month">
																		<?php for($cnt_month=1;$cnt_month<=12;$cnt_month++){?>
																		<option value="<?php echo $cnt_month;?>"><?php echo date("M", mktime(0,0,0,$cnt_month,date("d"), date("Y")));?></option>
																		<?php } ?>
																	</select>
																	<select name="year">
																		<?php for($cnt_year=date("Y");$cnt_year>=date("Y")-5;$cnt_year--){?>
																		<option value="<?php echo $cnt_year;?>"><?php echo $cnt_year;?></option>
																		<?php } ?>
																	</select>
																</td>
															</tr>
															<tr>
																<td colspan="3"><br>
																	&nbsp;<input type="button" value="Export to Excel" name="cmdExcel" id="cmdExcel" class="groovybutton"  onclick="javascript: check_detail('export_report');">
																</td>
															</tr>
															<script type="text/javascript">
															<!--
																get_courses('<?php echo $_GET['course_id'] ?>');
															//-->
															</script>
															<tr>
																<td colspan="3"><br>&nbsp;<input type="button" value="Placement Overview Report" name="cmdOverview" id="cmdOverview" class="groovybutton" onclick="javascript: check_detail('overview_report');"></td>
															</tr>
															<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
															<tr>
																<td colspan="3"><br>&nbsp;<input type="button" value="Sector wise Placement Report" name="cmdOverview" id="cmdOverview" class="groovybutton" onclick="javascript: check_detail('sector_wise_report');"></td>
															</tr>
															<?php } ?>
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