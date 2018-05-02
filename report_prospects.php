<?php	
	include('includes/application_top.php');

	if($_POST['form_action'] == 'export_report'){
		include(DIR_WS_CLASSES . 'PHPExcel.php');
		$objPHPExcel = new PHPExcel();

		$enq_from_date = tep_db_input(tep_db_prepare_input($_POST['enq_from_date']));
		$enq_from_date = date("Y-m-d", strtotime($enq_from_date));
		$enq_to_date = tep_db_input(tep_db_prepare_input($_POST['enq_to_date']));
		$enq_to_date = date("Y-m-d", strtotime($enq_to_date));

		$objPHPExcel->getProperties()->setCreator("Proschool SGSY")
									 ->setLastModifiedBy("Proschool SGSY")
									 ->setTitle("Proschool SGSY")
									 ->setSubject("Proschool SGSY")
									 ->setDescription("Proschool SGSY")
									 ->setKeywords("Proschool SGSY")
									 ->setCategory("Proschool SGSY");

		$excelsheet_name = 'proschool_sgsy_contact_log_' . time();
		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		$reports_cols = array('Name of Candidate',' Mobile No','Email Id', 'Enquiry for','Status', 'Last Follow up date','Next Contact Date', 'Remark', 'Date of Enquiry','Gender','Education','Employed','District');

		$alphabet = 'A';
		$rows = 1;
		foreach($reports_cols as $column){
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $column);
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $alphabet . '1')->applyFromArray($heading_bold);

		$get_student_query_raw = "select s.student_id, s.student_full_name, s.student_mobile, s.student_email, s.enq_date, s.student_gender, s.student_qualification, s.is_unemployed, s.student_district, s.student_type, s.course_id, pcl.contact_log_date, pcl.contact_log_next_date, pcl.contact_log_status, pcl.contact_log_remark from " . TABLE_STUDENTS . " s left join " . TABLE_PROS_CONTACT_LOGS . " pcl on (pcl.student_id = s.student_id) where s.student_type = 'PROSPECT'";

		if((int)$_POST['course_id']){
			$get_student_query_raw .= " and s.course_id = '" . $_POST['course_id'] . "'";
		}

		if($_SESSION['sess_adm_type'] != 'ADMIN'){
			$get_student_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
		}else{
			if($_POST['centre_id'] != '')$get_student_query_raw .= " and s.centre_id = '" . $_POST['centre_id'] . "'";
		}

		if($_POST['enq_type'] != ''){
			$get_student_query_raw .= " and pcl.contact_log_status = '" . $_POST['enq_type'] . "'";
		}

		if(tep_not_null($enq_from_date) && !tep_not_null($enq_to_date)){
			$get_student_query_raw .= " and date(s.enq_date) >= '" . $enq_from_date . "'";
		}else if(!tep_not_null($enq_from_date) && tep_not_null($enq_to_date)){
			$get_student_query_raw .= " and date(s.enq_date) <= '" . $enq_to_date . "'";
		}else if(tep_not_null($enq_from_date) && tep_not_null($enq_to_date)){
			$get_student_query_raw .= " and (s.enq_date >= '" . $enq_from_date . "' and s.enq_date <= '" . $enq_to_date . "')";
		}

		$get_student_query_raw .= " ORDER BY pcl.contact_log_id DESC";
	
		$get_student_query = tep_db_query($get_student_query_raw);	

		$rows = 2;
		while($get_student_array = tep_db_fetch_array($get_student_query)){
			$alphabet = 'A';
			$cource_query_raw = "SELECT course_name from courses where course_id = '" . $get_student_array['course_id'] . "'";
			$course = tep_db_query($cource_query_raw);
			$course_array= tep_db_fetch_array($course);
			
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['student_full_name']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['student_mobile']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['student_email']);
			
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $course_array['course_name']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['contact_log_status']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, display_valid_date($get_student_array['contact_log_date']));
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, display_valid_date($get_student_array['contact_log_next_date']));
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['contact_log_remark']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['enq_date']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['student_gender']);
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($get_student_array['student_qualification'] == 'OTHERS' ? $get_student_array['student_qualification'] :$arr_qualification[$get_student_array['student_qualification']]));
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, ($get_student_array['is_unemployed'] == '1' ? 'Y' : 'N'));
			$objPHPExcel->getActiveSheet()->setCellValue(($alphabet++) . $rows, $get_student_array['student_district']);

			$rows++;
			
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
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery.labelify.js';?>"></script>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#enq_from_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true,
				});
				$('#enq_to_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true,
				});
			});

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
													<td class="arial18BlueN">Report - Prospects</td>
												</tr>
												<tr>
													<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
												</tr>
												<tr>
													<td>
														<form name="frm_action" id="frm_action" method="post" action="">
														<input type="hidden" name="form_action" id="form_action" value="export_report">
														<table cellpadding="5" cellspacing="0" border="0" width="100%">
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
																	Course<br>
																	<select name="course_id" id="course_id" class="required" style="width:200px;"  onchange="javascript: get_batch('');">
																	<option value="">Please choose</option>
																	<?php
																		$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id order by course_name";
																		$course_query = tep_db_query($course_query_raw);
																		
																		while($course = tep_db_fetch_array($course_query)){
																	?>
																
																	<option value="<?php echo $course['course_id'];?>" <?php echo($info['course_id'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';?></option>
																	<?php }?>
																</select>
																</td>
																<td valign="top" class="arial14LGrayBold" width="15%">Status<br>
																	<select name="enq_type" id="enq_type" class="required" style="width:200px;"  onchange="javascript: get_batch('');">
																		<option value="">Please choose</option>
																		<?php foreach($arr_status_pros as $enq_key => $enq_value){ ?>
																		<option value="
																		<?php echo $enq_key;?>" <?php echo($info['enq_type'] == $enq_key? 'selected="selected"' : '');?>><?php echo $enq_value;?></option>
																	<?php } ?>
																	</select>
																</td>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	From<br>
																	<input type="text" name="enq_from_date" id="enq_from_date" value="<?php echo  ($dupError ? $_POST['enq_from_date'] : date("d-m-Y", strtotime("-1 month"))) ?>">
																</td>
																<td valign="top" class="arial14LGrayBold" width="15%">
																	To<br>
																	<input type="text" name="enq_to_date" id="enq_to_date" value="<?php echo  ($dupError ? $_POST['enq_to_date'] : date("d-m-Y")) ?>">
																		
																</td>
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