<?php	
	include('includes/application_top.php');
	check_valid_type('ADMIN');
	
	$ass_cols_values = array('Student name','Parent name','Center','Course','Batch','Cheque no','Cheque date','Amount','Bank name','Invoice No','Invoice Date','Assesment body');

	if($_POST['action_type'] == 'export_student')
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

		$excelsheet_name = 'proschool_assement' . time();

		$heading_bold = array(
			'font' => array(
				'bold' => true
			)
		);

		$arr_alphabet = range('A', 'Z');
		$start_alpha = 'A';
		
		$rows = 1;
		$cnt_cols = 0;

		foreach($ass_cols_values as $column){
			$objPHPExcel->getActiveSheet()->setCellValue($start_alpha++ . $rows, $column);
			$cnt_cols++;
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $start_alpha . '1')->applyFromArray($heading_bold);

		$keywords = tep_db_input(tep_db_prepare_input($_POST['keywords']));

		$stud_payments_query_raw = "select a.assessment_id, a.cheque_no, a.cheque_date, a.amount, a.bank_name, a.invoice_no, a.invoice_date, a.assessment_body, a.assessment_students, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.father_middle_name, s.father_surname, cn.centre_name,b.batch_title,c.course_name from " .TABLE_ASSESSMENTS . " a INNER JOIN " . TABLE_STUDENTS . " s ON FIND_IN_SET(s.student_id,  a.assessment_students) JOIN ". TABLE_COURSES ." c ON (s.course_id = c.course_id) JOIN ". TABLE_BATCHES ." b ON (s.batch_id = b.batch_id) JOIN ". TABLE_CENTRES ." cn ON (s.centre_id = cn.centre_id)";
		if($keywords != ''){
			$stud_payments_query_raw .= " WHERE (cheque_no LIKE '%" . $keywords . "%' OR bank_name LIKE '%" . $keywords . "%' OR a.invoice_no LIKE '%" . $keywords . "%' OR s.student_full_name LIKE '%" . $keywords . "%' OR s.student_middle_name LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' OR cn.centre_name LIKE '%" . $keywords . "%' OR c.course_name LIKE '%" . $keywords . "%' OR b.batch_title LIKE '%" . $keywords . "%')";
		}

		$stud_assesment_query = tep_db_query($stud_payments_query_raw);

		$rows = 2;
		if(tep_db_num_rows($stud_assesment_query)){
			while($stud_assesment_array = tep_db_fetch_array($stud_assesment_query)){
				$col_alpha = 'A';
				$assessment_id = $stud_assesment_array['assessment_id'];

				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, $stud_assesment_array['student_full_name'] . ' ' . $stud_assesment_array['student_middle_name'] . ' ' . $stud_assesment_array['student_surname']);
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, $stud_assesment_array['student_father_name'] . ' ' . $stud_assesment_array['father_middle_name'] . ' ' . $stud_assesment_array['father_surname']);
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, $stud_assesment_array['centre_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, $stud_assesment_array['batch_title']);
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, $stud_assesment_array['course_name']);
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, (isset($stud_assesment_array['cheque_no']) && $stud_assesment_array['cheque_no'] != '' ? $stud_assesment_array['cheque_no'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, date("d-m-Y", strtotime($stud_assesment_array['cheque_date'])));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, (isset($stud_assesment_array['amount']) && $stud_assesment_array['amount'] != '' ? $stud_assesment_array['amount'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, (isset($stud_assesment_array['bank_name']) && $stud_assesment_array['bank_name'] != '' ? $stud_assesment_array['bank_name'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, (isset($stud_assesment_array['invoice_no']) && $stud_assesment_array['invoice_no'] != '' ? $stud_assesment_array['invoice_no'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, date("d-m-Y", strtotime($stud_assesment_array['invoice_date'])));
				$objPHPExcel->getActiveSheet()->setCellValue(($col_alpha++) . $rows, (isset($stud_assesment_array['assessment_body']) && $stud_assesment_array['assessment_body'] != '' ? $stud_assesment_array['assessment_body'] : '-'));

				$rows++;
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
		<title><?php echo TITLE ?>: Assessement Payments</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
			<script type="text/javascript">
			<!--
				function export_stdnt_sheet(){
					document.frmstudentSearch.method = 'POST';
					document.frmstudentSearch.action_type.value = 'export_student';
					document.frmstudentSearch.submit();
				}

				function do_search(){
					document.frmstudentSearch.method = 'GET';
					document.frmstudentSearch.submit();
				}

				function get_courses(default_course){
					var section = $('#section_id').val();

					$('#course_id').empty();
					$('#course_id').append($("<option></option>").attr("value",'').text('Choose Course'));

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
					$('#batch_id').append($("<option></option>").attr("value",'').text('Choose Batch'));

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
			-->
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
													<td class="arial18BlueN">Assesment Payments <small class="verdana11GrayB"></small></td>
												</tr>
												<tr>
													<td colspan="2">
														<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
															<tr>
																<td>
																	<?php
																		$keywords = tep_db_input(tep_db_prepare_input($_GET['keywords']));
																	?>
																	<form name="frmstudentSearch" id="frmstudentSearch" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="get" enctype="multipart/form-data">
																	<input type="hidden" name="action_type" id="action_type" value="get_student_search">
																	<table cellpadding="0" cellspacing="0" border="0" width="100%">
																		<tr>
																			<td class="arial12LGray" width="20%">
																				&nbsp;<input type="text" name="keywords" id="keywords" class="required" placeholder="Please enter your Keywords" value="<?php echo $keywords;?>">
																			</td>
																			<td valign="top" class="arial14LGrayBold">
																				<select name="centre_id" id="centre_id" style="width:150px;">
																					<option value="">All Center</option>
																					<?php
																						$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " order by centre_name";
																						$centre_query = tep_db_query($centre_query_raw);
																						
																						while($centre = tep_db_fetch_array($centre_query)){
																					?>
																					<option value="<?php echo $centre['centre_id'];?>" <?php echo(isset($_GET['centre_id']) && $_GET['centre_id'] == $centre['centre_id'] ? 'selected="selected"' : '');?>><?php echo $centre['centre_name'];?></option>
																					<?php } ?>
																				</select>&nbsp;
																				<select name="section_id" id="section_id" title="Please select sector" class="required" onchange="javascript: get_courses('');" style="width:150px;">
																					<option value="">Choose Sector</option>
																					<?php
																						$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																						$section_query = tep_db_query($section_query_raw);
																						
																						while($section = tep_db_fetch_array($section_query)){
																					?>
																					<option value="<?php echo $section['section_id'];?>" <?php echo($info['section_id'] == $section['section_id'] ? 'selected="selected"' : '');?>><?php echo $section['section_name'];?></option>
																					<?php } ?>
																				</select>&nbsp;
																				<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch('');" style="width: 120px;">
																					<option value="">Choose Course</option>
																				</select>&nbsp;
																				<select name="batch_id" id="batch_id" title="Please select batch" class="required" style="width:150px;">
																					<option value="">Choose Batch</option>
																				</select>
																			</td>
																			<td width="10%">
																				<input type="button" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" onclick="javascript: do_search();" class="groovybutton">
																			</td>
																			<td width="10%">
																				<input type="button" value="Export to Excel" name="cmdSubmit" id="cmdSubmit" class="groovybutton" onclick="javascript: export_stdnt_sheet();">
																			</td>
																			<td>&nbsp;</td>
																		</tr>
																	</table>
																	</form>
																	<br/><br/>
																	<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_filter" class="display">
																		<thead>
																			<tr>
																				<th>Student Name</th>
																				<th>Parent Name</th>
																				<th>Center</th>
																				<th>Course</th>
																				<th>Batch</th>
																				<th>Cheque No</th>
																				<th>Cheque Date</th>
																				<th>Amount</th>
																				<th>Bank Name</th>
																				<th>Invoice No</th>
																				<th>Invoice Date</th>
																				<th>Assessment Body</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																				if((isset($keywords))){
																				
																				$stud_payments_query_raw = "select a.assessment_id, a.cheque_no, a.cheque_date, a.amount, a.bank_name, a.invoice_no, a.invoice_date, a.assessment_body, a.assessment_students, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.father_middle_name, s.father_surname, cn.centre_name,b.batch_title,c.course_name from " .TABLE_ASSESSMENTS . " a INNER JOIN " . TABLE_STUDENTS . " s ON FIND_IN_SET(s.student_id,  a.assessment_students) JOIN ". TABLE_COURSES ." c ON (s.course_id = c.course_id) JOIN ". TABLE_BATCHES ." b ON (s.batch_id = b.batch_id) JOIN ". TABLE_CENTRES ." cn ON (s.centre_id = cn.centre_id)";
																				//print_r($stud_payments_query_raw); exit;
																				
																				if($keywords != ''){
																					$stud_payments_query_raw .= " WHERE (cheque_no LIKE '%" . $keywords . "%' OR bank_name LIKE '%" . $keywords . "%' OR a.invoice_no LIKE '%" . $keywords . "%' OR s.student_full_name LIKE '%" . $keywords . "%' OR s.student_middle_name LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' OR cn.centre_name LIKE '%" . $keywords . "%' OR c.course_name LIKE '%" . $keywords . "%' OR b.batch_title LIKE '%" . $keywords . "%')";
																				}

																				if(isset($_GET['centre_id']) && $_GET['centre_id'] != ""){
																					$centre_id = (int)$_GET['centre_id'];
																					$stud_payments_query_raw .= " and s.centre_id = '" . $centre_id . "'";
																				}

																				if(isset($_GET['course_id']) && $_GET['course_id'] != ""){
																					$course_id = (int)$_GET['course_id'];
																					$stud_payments_query_raw .= " and s.course_id = '" . $course_id . "'";
																				}

																				if(isset($_GET['batch_id']) && $_GET['batch_id'] != ""){
																					$batch_id = (int)$_GET['batch_id'];
																					$stud_payments_query_raw .= " and s.batch_id = '" . $batch_id . "'";
																				}

																				$stud_assesment_query = tep_db_query($stud_payments_query_raw);

																				if(tep_db_num_rows($stud_assesment_query)){
																					while($stud_assesment_array = tep_db_fetch_array($stud_assesment_query)){
																							 $assessment_id = $stud_assesment_array['assessment_id'];
																			?>
																			<tr>
																				<td>
																					<?php 
																					echo $stud_assesment_array['student_full_name'] . ' ' . $stud_assesment_array['student_middle_name'] . ' ' . $stud_assesment_array['student_surname'];?>
																				</td>
																				<td>
																					<?php echo $stud_assesment_array['student_father_name'] . ' ' . $stud_assesment_array['father_middle_name'] . ' ' . $stud_assesment_array['father_surname'];?>
																				</td>
																				<td>
																					<?php echo $stud_assesment_array['centre_name'];?>
																				</td>
																				<td>
																					<?php echo $stud_assesment_array['batch_title'];?>
																				</td>
																				<td>
																					<?php echo $stud_assesment_array['course_name'];?>
																				</td>

																				<td>
																					<?php echo (isset($stud_assesment_array['cheque_no']) && $stud_assesment_array['cheque_no'] != '' ? $stud_assesment_array['cheque_no'] : '-');?>
																				</td>

																				<td>
																					<?php echo date("d-m-Y", strtotime($stud_assesment_array['cheque_date']));?>
																				</td>

																				<td>
																					<?php echo (isset($stud_assesment_array['amount']) && $stud_assesment_array['amount'] != '' ? $stud_assesment_array['amount'] : '-');?>
																				</td>

																				<td>
																					<?php echo (isset($stud_assesment_array['bank_name']) && $stud_assesment_array['bank_name'] != '' ? $stud_assesment_array['bank_name'] : '-');?>
																				</td>

																				<td>
																					<?php echo (isset($stud_assesment_array['invoice_no']) && $stud_assesment_array['invoice_no'] != '' ? $stud_assesment_array['invoice_no'] : '-');?>
																				</td>

																				<td>
																					<?php echo date("d-m-Y", strtotime($stud_assesment_array['invoice_date']));?>
																				</td>

																				<td>
																					<?php echo (isset($stud_assesment_array['assessment_body']) && $stud_assesment_array['assessment_body'] != '' ? $stud_assesment_array['assessment_body'] : '-');?>
																				</td>

																				
																			</tr>
																			<?php
																					}
																			?>
																			<script type="text/javascript" charset="utf-8">
																				$(document).ready(function() {
																					$('#table_filter').dataTable({
																						"aoColumns": [
																							{ "bSortable": false}, //Checkbox
																							null, //Student Name
																							null, //Parent Name
																							null, //Centre
																							null, //Cheque
																							null, //Bank
																							null, // Branch
																							null, // Payment Mode
																							null, // Payment Date
																							null, // Amount
																						],
																						"bFilter": false,
																						"paging": false,
																						"bPaginate": false,
																						 "oLanguage": {
																							 "sInfo": "",
																							 "sInfoEmpty": ""
																						  }
																					});
																				});
																			</script>
																			<?php
																				}else{
																			?>
																			<tr>
																				<td colspan="12" align="center">No payment found.</td>
																			</tr>
																			<?php
																				}
																			?>
																		</tbody>
																	</table>
																	<?php } ?>
																</td>
															</tr>
														</table>
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