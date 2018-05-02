<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Student Attendance has been deleted successfully!!!", 'added'=>'Student Attendance has been added successfully',"edited"=>"Student Attendance  has been updated successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$ad = tep_db_prepare_input($_POST['ad']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'batch_id' => $batch_id,
			'attendance_date' => $ad
		);

		switch($action){
			case 'save_attendace':
				$attendance_delete_query = tep_db_query("delete from " . TABLE_ATTENDANCE . " where course_id = '" . $course_id . "' and batch_id = '" . $batch_id . "' and attendance_date = '" . $ad . "'");

				if(is_array($_POST['attend']) && count($_POST['attend'])){
					foreach($_POST['attend'] as $attend_student){
						$arr_db_values['student_id'] = $attend_student;
						$arr_db_values['attendance'] = 'ATTEND';
						tep_db_perform(TABLE_ATTENDANCE, $arr_db_values);
					}
				}

				if(is_array($_POST['absent']) && count($_POST['absent'])){
					foreach($_POST['absent'] as $attend_student){
						$arr_db_values['student_id'] = $attend_student;
						$arr_db_values['attendance'] = 'ABSENT';
						tep_db_perform(TABLE_ATTENDANCE, $arr_db_values);
					}
				}

				$msg = 'edited';
			break;
			case 'delete_attendance':
				$attendance_id = tep_db_prepare_input($_POST['attendance_id']);

				tep_db_query("delete from " . TABLE_ATTENDANCE . " where attendance_id = '" . $attendance_id . "' limit 1");

				$msg = 'deleted';

				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg')) . 'msg=' . $msg));
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType', 'course', 'ad', 'batch', 'cmdSubmit', 'action_type')) . 'msg=' . $msg));
	}

	$action_type = 'get_attendace';

	$current_student_id = tep_db_input($_GET['stud_id']);
	$show_student_section = false;

	$student_query_raw = "select s.student_id, s.student_full_name, s.student_father_name, s.student_mobile, b.batch_title, date_format(b.batch_start_date, '%d %b %Y') as batch_start_date, date_format(b.batch_end_date, '%d %b %Y') as batch_end_date from " . TABLE_STUDENTS . " s left join " . TABLE_BATCHES . " b on b.batch_id = s.batch_id where is_deactivated != '1'";
	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$student_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}
	$student_query_raw .= " and s.student_id  = '" . $current_student_id . "'";

	$student_query = tep_db_query($student_query_raw);

	$student = array();

	if(tep_db_num_rows($student_query)){
		$show_student_section = true;
		$action_type = 'get_attendace_info';

		$student = tep_db_fetch_array($student_query);
	}

	$batch_info = array();

	if(isset($_GET['batch']) && $_GET['batch'] != ''){
		$batch_info_query_raw = "select batch_id, batch_title, date_format(batch_start_date, '%d-%m-%Y') as batch_start_date, date_format(batch_end_date, '%d-%m-%Y') as batch_end_date from " . TABLE_BATCHES . " where centre_id = '" . $_SESSION['sess_centre_id'] . "' and batch_id = '" . tep_db_input($_GET['batch']) . "'";
		$batch_info_query = tep_db_query($batch_info_query_raw);

		$batch_info = tep_db_fetch_array($batch_info_query);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Student Attendance Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'multiselect.js';?>"></script>

		<script language="javascript">
		<!--
			function get_batch(default_batch){
				var course = $('#course').val();

				$('#batch').empty();
				$('#batch').append($("<option></option>").attr("value",'').text('Please choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course,
					type: 'POST',
					async: false,
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_batch == values.batch_id){
								$('#batch').append($("<option></option>").attr("value",values.batch_id).attr('selected', 'selected').text(values.batch_title));
							}else{
								$('#batch').append($("<option></option>").attr("value",values.batch_id).text(values.batch_title));
							}
						})
					}
				});
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				<?php if($show_student_section == true){?>
					$('#sd').datepicker({
						dateFormat: "dd-mm-yy",
						changeMonth: true,
						changeYear: true
					});

					$('#ed').datepicker({
						dateFormat: "dd-mm-yy",
						changeMonth: true,
						changeYear: true
					});
				<?php }else{ ?>
					$('#ad').datepicker({
						dateFormat: "dd-mm-yy",
						changeMonth: true,
						changeYear: true
					});
				<?php } ?>
			});

			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this date from student attendance?")){
					objForm.action_type.value = action_type;
					objForm.attendance_id.value = int_id;
					objForm.submit();
				}
			}
		//-->
		</script>
			<script type="text/javascript">
			<!--
				function check_detail(objForm){
					multipleSelectOnSubmit();

					$('#attend option').each(function(){
						$(this).attr('selected', 'selected');
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
						<?php
							if(isset($_GET['msg']))
							{
						?>
							<tr>
								<td valign="middle" class="<?php echo ($_GET['msg'] == 'deleted' ? 'error_msg' : 'success_msg' );?>" align="center"><?php echo $arrMessage[$_GET['msg']]?></td>
							</tr>
							<tr>
								<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
							</tr>
						<?php
							}	
						?>
						<tr>
							<td class="backgroundBgMain" valign="top">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									<tr>
										<td valign="top">
											<?php
												if($_GET['actionType'] == "edit"){
													$int_id = $_GET['int_id'];

													$info_query_raw = " select subject_id, course_id, subject, subject_info from " . TABLE_SUBJECTS . " where subject_id = '" . $int_id . "' ";
													$info_query = tep_db_query($info_query_raw);

													$info = tep_db_fetch_array($info_query);
												}
											?>
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">
														<?php if($show_student_section == false){?>
														Student Attendance Management
														<?php }else{ ?>
															<?php echo $student['student_full_name']; ?><br>
															<?php echo $student['batch_title']; ?><br>
															<span style="color: red;">Start Date :</span> <?php echo $student['batch_start_date']; ?>&nbsp;<span style="color: red;">End Date :</span> <?php echo $student['batch_end_date']; ?><br>
														<?php } ?>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmDetails" id="frmDetails" method="get" enctype="multipart/form-data">
															<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
															<input type="hidden" name="stud_id" id="stud_id" value="<?php echo $current_student_id;?>">
															<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																<tr>
																	<?php if($show_student_section == false){?>
																	<td width="6%" class="arial12LGrayBold" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																	<td width="10%">
																		<select name="course" id="course" class="required" onchange="javascript: get_batch('');">
																			<option value="">Please choose</option>
																			<?php
																				$course_query_raw = " select course_id, course_name from " . TABLE_COURSES . " order by course_name";
																				$course_query = tep_db_query($course_query_raw);
																				
																				while($course = tep_db_fetch_array($course_query)){
																			?>
																			<option value="<?php echo $course['course_id'];?>" <?php echo($_GET['course'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'];?></option>
																			<?php } ?>
																		</select>
																	</td>
																	<td class="arial12LGrayBold" align="right" width="5%">&nbsp;Batch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																	<td width="10%">
																		<select name="batch" id="batch" class="required">
																			<option value="">Please choose</option>
																		</select>
																	</td>
																	<td class="arial12LGrayBold" width="5%" align="right">&nbsp;Date&nbsp;:</td>
																	<td class="arial12LGray" width="15%">
																		<input type="text" name="ad" id="ad" value="<?php echo  ($_GET['ad'] != '' ? $_GET['ad'] : date("d-m-Y")) ?>">
																	</td>
																	<?php }else{?>
																	<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Start Date&nbsp;:</td>
																	<td class="arial12LGray" width="10%">
																		<input type="text" name="sd" id="sd" value="<?php echo  ($_GET['sd'] != '' ? $_GET['sd'] : date("d-m-Y", strtotime("-1 month"))) ?>" style="width:100px;">
																	</td>
																	<td class="arial12LGrayBold" width="8%" align="right">&nbsp;End Date&nbsp;:</td>
																	<td class="arial12LGray" width="15%">
																		<input type="text" name="ed" id="ed" value="<?php echo  ($_GET['ed'] != '' ? $_GET['ed'] : date("d-m-Y")) ?>"  style="width:100px;">
																	</td>
																	<?php } ?>
																	<td>&nbsp;<input type="submit" value="Fetch Attendance" name="cmdSubmit" id="cmdSubmit" class="groovybutton" style="width:auto;"></td>
																</tr>
																<script type="text/javascript">
																<!--
																	get_batch('<?php echo $_GET['batch'] ?>');
																//-->
																</script>
															</table>
														</form>
														<?php
															if(is_array($batch_info) && count($batch_info)){
														?>
														<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
															<tr>
																<td class="arial12LGray">
																	Attendance for <b><?php echo $batch_info['batch_title']; ?></b><br/><br/>
																	Start Date : <b><?php echo $batch_info['batch_start_date']; ?></b>&nbsp;&nbsp;
																	End Date : <b><?php echo $batch_info['batch_end_date']; ?></b>
																</td>
															</tr>
														</table>
														<?php 
															}

														if(isset($_GET['action_type']) && $_GET['action_type'] == 'get_attendace'){

															$course_id = tep_db_input($_GET['course']);
															$batch_id = tep_db_input($_GET['batch']);
															$ad = tep_db_input($_GET['ad']);
															$ad = input_valid_date($ad);

															$absent_student_query_raw = "select a.student_id, a.attendance_date, s.student_full_name, s.student_surname from " . TABLE_ATTENDANCE . " a, " . TABLE_STUDENTS . " s where a.course_id = '" . $course_id . "' and a.batch_id = '" . $batch_id . "' and a.attendance_date = '" . $ad . "' and s.student_id = a.student_id and attendance != 'ATTEND' and is_deactivated != '1'";
															if($_SESSION['sess_adm_type'] != 'ADMIN'){
																$absent_student_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
															}

															$absent_student_query = tep_db_query($absent_student_query_raw);

															$absent_students = '';

															while($absent_student_temp = tep_db_fetch_array($absent_student_query)){
																$absent_student[] = $absent_student_temp;

																if($absent_students != '')$absent_students .= "','";
																$absent_students .= $absent_student_temp['student_id'];
															}

															$students_query_raw = "select s.student_id, s.student_full_name, s.student_surname from " . TABLE_STUDENTS . " s where student_type = 'ENROLLED' and is_deactivated != '1'";

															if(tep_not_null($absent_students)){
																$students_query_raw .= " and s.student_id not in ('" . $absent_students . "')";
															}

															if($_SESSION['sess_adm_type'] != 'ADMIN'){
																$students_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
															}

															$students_query_raw .= " and s.course_id = '" . $course_id . "' and s.batch_id = '" . $batch_id . "'";

															$students_query_raw .= " order by s.student_id desc";

															$students_query = tep_db_query($students_query_raw);
														?>
														<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data" onsubmit="javascript: check_detail(this);">
														<input type="hidden" name="action_type" id="action_type" value="save_attendace">
														<input type="hidden" name="course_id" id="course_id" value="<?php echo $course_id;?>">
														<input type="hidden" name="batch_id" id="batch_id" value="<?php echo $batch_id;?>">
														<input type="hidden" name="ad" id="ad" value="<?php echo $ad;?>">
															<table cellpadding="5" cellspacing="5" border="0" width="100%" align="center" class="tabForm" style="border-top:none;">
																<tr>
																	<td>
																		<select name="attend[]" id="attend" multiple="multiple">
																			<?php 
																				while($students = tep_db_fetch_array($students_query)){
																					$last_attend_date = '';
																					$last_attend_query = tep_db_query("select date_format(a.attendance_date, '%d-%m-%Y') as last_attend_date  from " . TABLE_ATTENDANCE . " a where a.course_id = '" . $course_id . "' and a.batch_id = '" . $batch_id . "' and a.attendance_date != '" . $ad . "' and a.student_id = '" . $students['student_id'] . "' and a.attendance_date != '0000-00-00' order by a.attendance_date desc");
																					if(tep_db_num_rows($last_attend_query)){
																						$last_attend_array = tep_db_fetch_array($last_attend_query);
																						$last_attend_date = '&nbsp;&nbsp;(Last attend date&nbsp;' . $last_attend_array['last_attend_date'] . ')';
																					}
																			?>
																			<option value="<?php echo $students['student_id'];?>"><?php echo $students['student_full_name'] . ' ' . $students['student_surname'] . $last_attend_date;?></option>
																			<?php 
																				}
																			?>
																		</select>
																		<select multiple name="absent[]" id="absent" size="10">
																			<?php
																				if(is_array($absent_student) && count($absent_student)){
																					foreach($absent_student as $students){
																			?>
																			<option value="<?php echo $students['student_id'];?>"><?php echo $students['student_full_name'] . ' ' . $students['student_surname'];?></option>
																			<?php
																					}
																				}
																			?>
																		</select>
																		<script type="text/javascript">
																			createMovableOptions("attend","absent",1200,200,'Attend Student','Absent Student');
																		</script>
																	</td>
																</tr>
															</table>
															<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																<tr>
																	<td>&nbsp;<input type="submit" value="SAVE" name="cmdSubmit" id="cmdSubmit" class="groovybutton"></td>
																	<td >&nbsp;</td>
																<tr>
															</table>
														</form>
														<?php 
															}else if(isset($_GET['action_type']) && $_GET['action_type'] == 'get_attendace_info'){
																$sd = tep_db_input($_GET['sd']);
																$sd = input_valid_date($sd);

																$ed = tep_db_input($_GET['ed']);
																$ed = input_valid_date($ed);

																$attendace_count_query_raw = "select count(attendance) as attendance_count, attendance from " . TABLE_ATTENDANCE . " where student_id = '" . $current_student_id . "' and ( attendance_date between '" . $sd . "' and '" . $ed . "' ) group by attendance";
																$attendace_count_query = tep_db_query($attendace_count_query_raw);

																while($attendace_count_temp = tep_db_fetch_array($attendace_count_query)){
																	$attendace_count[$attendace_count_temp['attendance']] = $attendace_count_temp['attendance_count'];
																}

																$attendace_query_raw = "select attendance_id, attendance, attendance_date, date_format(attendance_date, '%d %b %Y') as frm_attendance_date from " . TABLE_ATTENDANCE . " where student_id = '" . $current_student_id . "' and ( attendance_date between '" . $sd . "' and '" . $ed . "' )";
																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$attendace_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$attendace_query_raw .= " order by attendance_date";

																$attendace_query = tep_db_query($attendace_query_raw);
														?>
														<table cellpadding="5" cellspacing="0" width="50%" align="center" border="1" class="display" style="width: 40%;">
															<tr>
																<td><b>TOTAL ATTEND : </b>&nbsp;<?php echo (tep_not_null($attendace_count['ATTEND']) ? $attendace_count['ATTEND'] : '0');?> Day(s)</td>
																<td><b>TOTAL ABSENT : </b>&nbsp;<?php echo (tep_not_null($attendace_count['ABSENT']) ? $attendace_count['ABSENT'] : '0');?> Day(s)</td>
															</tr>
														</table><br>
														<form name="frmAttendDetails" id="frmAttendDetails" method="post">
														<input type="hidden" name="action_type" id="action_type" value="">
														<input type="hidden" name="attendance_id" id="attendance_id" value="">
														<table cellpadding="5" cellspacing="0" width="50%" align="center" border="1" class="display" style="width: 40%;">
															<thead>
																<th>Date</th>
																<th>Day</th>
																<th>Attendance</th>
																<th>&nbsp;</th>
															</thead>
															<tbody>
															<?php
																if(tep_db_num_rows($attendace_query) ){
																	while( $attendace = tep_db_fetch_array($attendace_query)){
																		$day = date("l", strtotime($attendace['frm_attendance_date']));
															?>
																<tr bgcolor="<?php echo($attendace['attendance'] == 'ABSENT' ? '#FCE0E0' : '' );?>">
																	<td valign="top" align="center"><?php echo $attendace['frm_attendance_date']; ?></td>
																	<td valign="top" align="center"><?php echo $day; ?></td>
																	<td valign="top" align="center"><?php echo ucwords(strtolower($attendace['attendance'])); ?></td>
																	<td valign="top" align="center"><a href="javascript: delete_selected(document.frmAttendDetails, 'delete_attendance','<?php echo $attendace['attendance_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																</tr>
															<?php
																	}
															?>
															<?php
																}else{
															?>
																<tr>
																		<td align="center" colspan="6" class="verdana11Red">No Record Found !!</td>
																</tr>
															<?php } ?>
															</tbody>
														</table>
														</form>
														<?php } ?>
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