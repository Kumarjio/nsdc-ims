<?php	
	include('includes/application_top.php');

	if($_POST['form_action'] == 'export_report'){

		$start_month = tep_db_input(tep_db_prepare_input($_POST['start_month']));
		$start_month = ($start_month <= 9 ? '0' : '') . $start_month;
		$start_year = tep_db_input(tep_db_prepare_input($_POST['start_year']));
		$end_month = tep_db_input(tep_db_prepare_input($_POST['end_month']));
		$end_month = ($end_month <= 9 ? '0' : '') . $end_month;
		$end_year = tep_db_input(tep_db_prepare_input($_POST['end_year']));

		$reports_cols = array('S. No.', 'Centre Id', 'Centre', 'Sector', 'Course Name', 'Course Code', 'Batch No/Code', 'Batch Start Date', 'Batch End Date', 'Course Type Residential/Non Residential', 'Candidate Name', 'Candidate Mobile', 'Cheque Amount', 'Cheque Number', 'Bank Name', 'Bank Branch', 'Date of Deposit', 'Date of Bounced', 'Reason for Bounced');

		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")->setLastModifiedBy("Proschool SGSY")->setTitle("Proschool SGSY")->setSubject("Proschool SGSY")->setDescription("Proschool SGSY")->setKeywords("Proschool SGSY")->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_bounce_' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		$background_color = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFFFFF')
			)
		);

		$arr_alphabet = range('A', 'Z');

		$rows = 3;
		$cnt_cols = 0;

		foreach($reports_cols as $column){
			$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_cols] . $rows, $column);
			$cnt_cols++;
		}

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Bounce Report');
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $arr_alphabet[count($reports_cols)-1] . '1')->applyFromArray($background_color)->applyFromArray($heading_bold)->getFont()->setSize(16);;

		$objPHPExcel->getActiveSheet()->getStyle('A3:' . $arr_alphabet[count($reports_cols)-1] . '3')->applyFromArray($heading_bold);

		$center_query_raw = " select cn.centre_id, cn.district_id, cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id ";

		if($_POST['centre_id'] != '')$center_query_raw .= " and cn.centre_id = '" . $_POST['centre_id'] . "'";

		$center_query_raw .= " order by cn.centre_name";

		$center_query = tep_db_query($center_query_raw);

		$sr_no = 1;
		$rows = 4;

		while($centre = tep_db_fetch_array($center_query)){

			$courses_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_fee, c.course_code, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id ";

			if($_POST['course_id'] != '')$courses_query_raw .= " and c.course_id = '" . $_POST['course_id'] . "'";
			if($_POST['section_id'] != '')$courses_query_raw .= " and c.section_id = '" . $_POST['section_id'] . "'";

			$courses_query_raw .= "  order by c.course_name";

			$courses_query = tep_db_query($courses_query_raw);

			while($courses = tep_db_fetch_array($courses_query)){

				$batches_query_raw = "select batch_id, batch_title, date_format(batch_start_date, '%d %b %Y') as batch_start_date, date_format(batch_end_date, '%d %b %Y') as batch_end_date, date_format(handholding_end_date, '%d %b %Y') as handholding_end_date from " . TABLE_BATCHES . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "'";

				if($_POST['batch_id'] != '')$batches_query_raw .= " and batch_id = '" . tep_db_input($_POST['batch_id']) . "'";

				$batches_query = tep_db_query($batches_query_raw);

				while($batches = tep_db_fetch_array($batches_query)){
					$students_query_raw = "select student_id, student_full_name, student_middle_name, student_surname, is_deactivated, student_father_name, student_surname, student_gender, student_category, course_option, student_payable_fee, student_course_fee, student_mobile from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and course_id = '" . $courses['course_id'] . "' and batch_id = '" . $batches['batch_id'] . "'";
					$students_query = tep_db_query($students_query_raw);

					while($students = tep_db_fetch_array($students_query)){
						$cnt_innter = 0;

						$student_payments_query_raw = "select sp.stud_payment_id, sp.student_id, sp.deposit_id, sp.stud_payment_type, sp.stud_payment_mode, sp.stud_payment_cheque_no, sp.stud_payment_bank_name, sp.stud_payment_bank_branch, date_format(sp.stud_payment_deposit_date, '%d-%m-%Y') as stud_payment_deposit_date, date_format(sp.bounce_date, '%d-%m-%Y') as bounce_date, sp.stud_payment_amount, sp.stud_payment_receipt_no, sp.stud_payment_status, sp.stud_payment_added, sp.cheque_cleared, sp.bounce_reason from " . TABLE_STUDENT_PAYMENTS . " sp where sp.student_id='" . $students['student_id'] . "' AND sp.stud_payment_status =  'BOUNCE'";

						if(tep_not_null($start_month) && tep_not_null($start_year) && !tep_not_null($end_month) && !tep_not_null($end_year)){
							$start_date = $start_year . "-" . $start_month . "-01";
							$student_payments_query_raw .= " and bounce_date >= '" . $start_date . "'";
						}else if(!tep_not_null($start_month) && !tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

							$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
							$end_date = $end_year . "-" . $end_month . "-" . $last_day;

							$student_payments_query_raw .= " and bounce_date <= '" . $end_date . "'";

						}else if(tep_not_null($start_month) && tep_not_null($start_year) && tep_not_null($end_month) && tep_not_null($end_year)){

							$start_date = $start_year . "-" . $start_month . "-01";
							$last_day = date('t', mktime(0,0,0, $end_month, 1, $end_year));
							$end_date = $end_year . "-" . $end_month . "-" . $last_day;

							$student_payments_query_raw .= " and (bounce_date >= '" . $start_date . "' and bounce_date <= '" . $end_date . "')";
						}

						$student_payments_query_raw .= ' ORDER BY sp.stud_payment_type, sp.stud_payment_status DESC';

						$student_payments_query = tep_db_query($student_payments_query_raw);

						while($student_payments_array = tep_db_fetch_array($student_payments_query)){
							$cnt_innter = 0;
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $sr_no);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_unq_id']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $centre['centre_name']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['section_name']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_name']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $courses['course_code']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_title']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_start_date']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $batches['batch_end_date']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $arr_course_option[$students['course_option']]);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_full_name'] . ' ' . $students['student_middle_name'] . ' ' . $students['student_surname']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $students['student_mobile']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['stud_payment_amount']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['stud_payment_cheque_no']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['stud_payment_bank_name']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['stud_payment_bank_branch']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['stud_payment_deposit_date']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['bounce_date']);
							$objPHPExcel->getActiveSheet()->setCellValue($arr_alphabet[$cnt_innter++] . $rows, $student_payments_array['bounce_reason']);

							$sr_no++;
							$rows++;
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
		<title><?php echo TITLE ?> : Bounce Report</title>
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
													<td class="arial18BlueN">Report - Bounce Cheque Report</td>
												</tr>
												<tr>
													<td><small>TO view the bounce records of the students</small></td>
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