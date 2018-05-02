<?php	
	include('includes/application_top.php');

	$reports_cols = array('S. No.', 'Centre Id', 'Centre Location', 'MES Sector', 'Course Name ', 'Course Code', 'Batch No/Code', 'Course Duration (Month)', 'Batch Start Dt', 'Batch End Dt:', 'Handholding End Date', 'Candidate Name', 'Candidate Father\'s Name', 'Gender',  'Category', 'Minority', 'Training Completed (Y/N)', 'Course Type', 'Mobile No', 'Candidate\'s District', 'Amount Paid (Rs) to Student Installment 1', 'Amount Paid (Rs)to student - Installment 2', 'Mode of Payment  - Installment 1 ', 'Mode of Payment  - Installment 2', 'Instrument No - Installment 1 ', 'Instrument No  - Installment 2', 'Date of Payment - Installment 1', 'Date of Payment -  Installment 2', 'Reciept Obtained (Y/N) - Installment 1', 'Reciept Obtained (Y/N) - Installment 2', 'Name of the Bank of Student', 'Bank Branch of Student', 'Bank Account No of Student', 'Bank IFSC Code of Student');

	if($_POST['form_action'] == 'export_report'){
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_sgsy_non_res_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		//$arr_alphabet = range('A', 'Z');
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

		$center_query_raw = " select cn.centre_id, cn.district_id, cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

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

				$batches_query_raw = "select batch_id, batch_title, date_format(batch_start_date, '%d %b %Y') as batch_start_date, date_format(batch_end_date, '%d %b %Y') as batch_end_date, date_format(handholding_end_date, '%d %b %Y') as handholding_end_date from " . TABLE_BATCHES . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "'";

				if($_POST['batch_id'] != '')$batches_query_raw .= " and batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

				$batches_query = tep_db_query($batches_query_raw);

				while($batches = tep_db_fetch_array($batches_query)){
					$students_query_raw = "select student_id, student_full_name, student_middle_name, student_surname, student_father_name, father_middle_name, father_surname, student_gender, student_category, is_minority_category, student_mobile, student_district, if(is_training_completed = '1', 'Y', 'N') as is_training_completed, training_dropout_reason, date_format(training_dropout_date, '%d %b %Y') as training_dropout_date, if(is_bank_account = '1', 'Y', 'N') as is_bank_account, student_bank_name, student_branch, student_account_number, bank_ifsc_code, course_option from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "' and batch_id = '" . $batches['batch_id'] . "' and is_deactivated != '1'";
					$students_query = tep_db_query($students_query_raw);

					while($students = tep_db_fetch_array($students_query)){
						$cnt_innter = 0;

						$installment_query_raw = "select installment_id, student_id, installment_type, installment_no, date_format(installment_date, '%d %b %Y') as installment_date, installment_mop, instrument_no, installment_amount, if(is_receipt_collected = '1', 'Y', 'N') as is_receipt_collected, receipt_filename from " . TABLE_INSTALLMENTS . " where student_id='" . $students['student_id'] . "' and installment_type = 'NON_RES_ALLOWANCE' order by installment_no";
						$installment_query = tep_db_query($installment_query_raw);
						$installment = array();
						while($installment_temp = tep_db_fetch_array($installment_query)){
							$installment[$installment_temp['installment_no']] = $installment_temp;
						}

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $sr_no);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_unq_id']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_name']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['section_name']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_name']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_code']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_title']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_duration']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_start_date']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_end_date']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['handholding_end_date']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_father_name'] . ' ' . $students['father_middle_name'] . ' ' . $students['father_surname']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_gender']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_category[$students['student_category']]);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, ($students['is_minority_category'] == '1' ? 'Y' : 'N'));

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['is_training_completed']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_course_option[$students['course_option']]);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_mobile']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_district']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[1]['installment_amount']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[2]['installment_amount']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_payment_type[$installment[1]['installment_mop']]);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_payment_type[$installment[2]['installment_mop']]);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[1]['instrument_no']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[2]['instrument_no']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[1]['installment_date']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[2]['installment_date']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[1]['is_receipt_collected']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $installment[2]['is_receipt_collected']);

						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_bank_name']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_branch']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_account_number']);
						$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['bank_ifsc_code']);


						$sr_no++;
						$rows++;
					}
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
													<td class="arial18BlueN">Report - Non Res Allowance</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td align="center">
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="export_report">
														<table cellpadding="2" cellspacing="0" border="0" width="100%" align="center">
															<tr>
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