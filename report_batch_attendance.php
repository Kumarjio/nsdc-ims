<?php	
	include('includes/application_top.php');

	$arr_batch_status = array('COMPLETED'=>'Completed', 'IN_PROGRESS'=>'In Progress', 'TO_BE_STARTED'=>'To Be Started');

	if($_POST['form_action'] == 'export_report'){
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$batch_id = tep_db_input(tep_db_prepare_input($_POST['batch_id']));

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_sgsy_attendance_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		if($_POST['report_type'] == 'STUDENT_WISE'){
			$reports_cols = array('S. No.', 'Centre', 'MES Sector', 'Course Name', 'Batch No/Code', 'Batch Size', 'Batch Start Dt', 'Batch End Dt', 'Handholding End Date', 'Batch Status', 'Candidate District', 'Student Name', 'Training Completed (Y/N)', 'Gender', 'Category', 'Minority', 'Course Type', 'Name of the Bank', 'Bank Branch', 'Bank Account No', 'Bank IFSC Code', 'Total Days Present', 'Total Absent', 'Percentage Of Days Persents', 'NSDC Stage 1 uploaded (Y/N)', 'NSDC Stage 2 uploaded (Y/N)', 'NSDC SDMS Number');

			$arr_alphabet = range('A', 'Z');

			$rows = 1;
			$cnt_cols = 0;

			$alphabet = 'A';
			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($alphabet . $rows, $column);
				$alphabet++;
				$cnt_cols++;
			}

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

			$batch_students_query_raw = "select s.student_id, s.student_full_name, s.student_middle_name, s.student_surname, b.batch_title, b.batch_size, b.batch_start_date, b.batch_end_date, b.handholding_end_date, b.batch_status, cn.centre_name, c.course_name, c.course_code, sc.section_name, s.student_district, s.student_gender, s.student_category, s.is_minority_category, s.course_option, if(is_training_completed = '1', 'Y', 'N') as is_training_completed, s.student_bank_name, s.student_branch, s.student_account_number, s.bank_ifsc_code, stage1_uploaded, stage2_uploaded, sdms_number from " . TABLE_STUDENTS . " s, ". TABLE_CENTRES ." cn, " . TABLE_BATCHES . " b, ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." sc where c.course_id = s.course_id and sc.section_id = b.section_id and cn.centre_id = s.centre_id and b.batch_id = s.batch_id and s.is_deactivated != '1'";

			if($_POST['batch_id'] != '')$batch_students_query_raw .= " and s.batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

			if($_POST['centre_id'] != '')$batch_students_query_raw .= " and s.centre_id = '" . tep_db_input($_POST['centre_id']) . "'";
			if($_POST['course_id'] != '')$batch_students_query_raw .= " and s.course_id = '" . tep_db_input($_POST['course_id']) . "'";
			if($_POST['section_id'] != '')$batch_students_query_raw .= " and b.section_id = '" . tep_db_input($_POST['section_id']) . "'";

			$batch_students_query_raw .= " order by s.student_full_name";

			$batch_students_query = tep_db_query($batch_students_query_raw);

			$sr_no = 1;
			$rows = 2;

			while($batch_students = tep_db_fetch_array($batch_students_query)){

				$cnt_innter = 0;
				$alphabet = 'A';

				$student_abs_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where student_id = '" . $batch_students['student_id'] . "' and attendance = 'ABSENT'");
				$student_abs = tep_db_fetch_array($student_abs_query);

				$student_attend_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where student_id = '" . $batch_students['student_id'] . "' and attendance = 'ATTEND'");
				$student_attend = tep_db_fetch_array($student_attend_query);

				$attend_percentage = 0;
				if((int)$student_abs['count'] > 0 || (int)$student_attend['count'] > 0){
					$attend_percentage = (($student_attend['count'] * 100) / ($student_abs['count'] + $student_attend['count']));
				}

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $sr_no);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['centre_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['section_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['course_name']);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['batch_title']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['batch_size']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, display_valid_date($batch_students['batch_start_date']));
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, display_valid_date($batch_students['batch_end_date']));
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, display_valid_date($batch_students['handholding_end_date']));

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_batch_status[$batch_students['batch_status']]);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['student_district']);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['student_full_name'] . ' ' . $batch_students['student_middle_name'] . $batch_students['student_surname']);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['is_training_completed']);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_gender[$batch_students['student_gender']]);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_category[$batch_students['student_category']]);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($batch_students['is_minority_category'] == '1' ? 'Y' : 'N'));
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $arr_course_option[$batch_students['course_option']]);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['student_bank_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['student_branch']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['student_account_number']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['bank_ifsc_code']);

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $student_attend['count']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $student_abs['count']);
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, round($attend_percentage, 2));

				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($batch_students['stage1_uploaded'] == '1' ? 'Y' : 'N'));
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($batch_students['stage2_uploaded'] == '1' ? 'Y' : 'N'));
				$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $batch_students['sdms_number']);


				$sr_no++;
				$rows++;
			}
		}else if($_POST['report_type'] == 'DATE_WISE' || $_POST['report_type'] == 'BATCH_WISE'){
			$reports_cols = array('S. No', 'Centre', 'MES Sector', 'Course Name', 'Batch No/Code', 'Batch District', 'Batch Size', 'Batch Start Dt', 'Batch End Dt', 'Handholding End Date', 'Batch Status', 'Date', 'Day', 'Total Present', 'Total Absent', 'Percentage Of Days Persents');

			$sheet_col = 'A';
			$sheet_row = '1';

			foreach($reports_cols as $column){
				$objPHPExcel->getActiveSheet()->setCellValue($sheet_col . $sheet_row, $column);
				$sheet_col++;
			}

			$from_date = $_POST['from_date'];
			$to_date = $_POST['to_date'];

			$objPHPExcel->getActiveSheet()->getStyle('A1:' . $sheet_col . $sheet_row)->applyFromArray($heading_bold);

			$batch_students_query_raw = "select b.batch_id, b.batch_title, b.batch_size, b.batch_start_date, b.batch_end_date, b.handholding_end_date, b.batch_status, cn.centre_name, c.course_name, c.course_code, sc.section_name, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id), ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." sc, ". TABLE_CENTRES ." cn where c.course_id = b.course_id and sc.section_id = b.section_id and cn.centre_id = b.centre_id";

			if($_POST['batch_id'] != '')$batch_students_query_raw .= " and b.batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

			if($_POST['centre_id'] != '')$batch_students_query_raw .= " and b.centre_id = '" . tep_db_input($_POST['centre_id']) . "'";

			if($_POST['course_id'] != '')$batch_students_query_raw .= " and b.course_id = '" . tep_db_input($_POST['course_id']) . "'";
			if($_POST['section_id'] != '')$batch_students_query_raw .= " and b.section_id = '" . tep_db_input($_POST['section_id']) . "'";

			$batch_students_query_raw .= " order by b.batch_title";

			$batch_students_query = tep_db_query($batch_students_query_raw);

			$sheet_row = 2;

			$stamp_from_date = strtotime($from_date);
			$stamp_to_date = strtotime($to_date);

			while($batch_students = tep_db_fetch_array($batch_students_query)){

				if($_POST['report_type'] == 'BATCH_WISE' && $_POST['batch_id'] != ''){

					$from_date = $batch_students['batch_start_date'];
					$to_date = $batch_students['batch_end_date'];

					$stamp_from_date = strtotime($batch_students['batch_start_date']);
					$stamp_to_date = strtotime($batch_students['batch_end_date']);
				}

				if($from_date != '' && $to_date != ''){

					$stamp_from_date = strtotime($from_date);

					if($stamp_to_date > $stamp_from_date){

						$sr_no = 1;

						while($stamp_from_date <= $stamp_to_date){

							if(date("N", $stamp_from_date) == 7){
								$stamp_from_date = strtotime("+1 day", $stamp_from_date);
								continue;
							}

							$sheet_col = 'A';

							$student_abs_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where batch_id = '" . $batch_students['batch_id'] . "' and attendance = 'ABSENT' and attendance_date = '" . date("Y-m-d", $stamp_from_date) . "'");
							$student_abs = tep_db_fetch_array($student_abs_query);

							$student_attend_query = tep_db_query("select count(attendance_id) as count from " . TABLE_ATTENDANCE . " where batch_id = '" . $batch_students['batch_id'] . "' and attendance = 'ATTEND' and attendance_date = '" . date("Y-m-d", $stamp_from_date) . "'");
							$student_attend = tep_db_fetch_array($student_attend_query);

							$attend_percentage = 0;
							if((int)$student_abs['count'] > 0 || (int)$student_attend['count'] > 0){
								$attend_percentage = (($student_attend['count'] * 100) / ($student_abs['count'] + $student_attend['count']));
							}
							

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $sr_no);

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['centre_name']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['section_name']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['course_name']);

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['batch_title']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['district_name']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $batch_students['batch_size']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, display_valid_date($batch_students['batch_start_date']));
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, display_valid_date($batch_students['batch_end_date']));
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, display_valid_date($batch_students['handholding_end_date']));

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $arr_batch_status[$batch_students['batch_status']]);

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, date("d-M-Y", $stamp_from_date));
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, date("l", $stamp_from_date));

							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $student_attend['count']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, $student_abs['count']);
							$objPHPExcel->getActiveSheet()->setCellValue(($sheet_col++) . $sheet_row, round($attend_percentage, 2));

							$sr_no++;
							$sheet_row++;

							$stamp_from_date = strtotime("+1 day", $stamp_from_date);
						}
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
		<title><?php echo TITLE ?> : Hand Holding Report</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$('#from_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#to_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});
			});

			function get_centre_batches(default_centre){
				var centre = $('#centre_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_centre_batch&centre='+centre,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_centre == values.batch_id){
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).attr('selected', 'selected').text(values.batch_title));
							}else{
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).text(values.batch_title));
							}
						})
					}
				});
			}

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

			function toggle_day_wise_cren(){
				var report_type = $('#report_type').val();

				if(report_type == 'DATE_WISE'){
					$('.daywise').show();
				}else{
					$('.daywise').hide();
				}
			}

			function check_detail(objForm){
				var report_type = objForm.report_type.value;

				if(report_type == 'BATCH_WISE'){
					if(objForm.batch_id.value == ''){
						alert("Please choose the batch.");
						return false;
					}else{
						return true;
					}
				}else{
					return true;
				}
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
													<td class="arial18BlueN">Report - Batch Attendance</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td align="center">
														<form name="frm_action" id="frm_action" method="post" action="" onsubmit="javascript: return check_detail(this);">
														<input type="hidden" name="form_action" id="form_action" value="export_report">
														<table cellpadding="2" cellspacing="0" border="0" width="60%" align="left">
															<tr>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Report Type<br>
																	<select name="report_type" id="report_type" class="required" onchange="javascript: toggle_day_wise_cren();">
																		<option value="STUDENT_WISE">Student Wise</option>
																		<option value="DATE_WISE">Date Wise</option>
																		<option value="BATCH_WISE">Batch Wise</option>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold daywise" width="15%">
																	From<br>
																	<input type="text" name="from_date" id="from_date" style="width: 120px;">
																</td>
																<td valign="top" class="arial14LGrayBold daywise"  width="15%">
																	To<br>
																	<input type="text" name="to_date" id="to_date" style="width: 120px;">
																</td>
															</tr>
															<tr>
																<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	Center<br>
																	<select name="centre_id" id="centre_id" class="required" onchange="javascript: get_centre_batches('');">
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
																<script type="text/javascript">
																<!--
																	//get_centre_batches('');
																//-->
																</script>
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
																<td valign="top" class="arial14LGrayBold" width="15%">
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
																toggle_day_wise_cren();
																get_centre_batches('<?php echo $_GET['batch_id'] ?>');
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