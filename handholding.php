<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];

	function salary_slip_upload() {
		if($_FILES['salary_slip']['name'] != ''){
			$ext = get_extension($_FILES['salary_slip']['name']);
			$src = $_FILES['salary_slip']['tmp_name'];

			$dest_filename = 'salray_slip_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				return $dest_filename;
			}
		}
	}
	
	if(isset($action) && tep_not_null($action))
	{
		$handholding_id = tep_db_prepare_input($_POST['handholding_id']);
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$company_id = tep_db_prepare_input($_POST['company_id']);
		$contact_date = tep_db_prepare_input($_POST['contact_date']);
		$contact_mode = tep_db_prepare_input($_POST['contact_mode']);
		$is_student_contable = tep_db_prepare_input($_POST['is_student_contable']);

		if($is_student_contable == '1'){
			$contact_person_name = tep_db_prepare_input($_POST['contact_person_name']);
			$contact_person_relation = tep_db_prepare_input($_POST['contact_person_relation']);
			$contact_person_phone = tep_db_prepare_input($_POST['contact_person_phone']);
			$student_status = tep_db_prepare_input($_POST['student_status']);

			if($student_status == 'DROP_OUT'){
				$drop_out_reason = tep_db_prepare_input($_POST['drop_out_reason']);
				$drop_out_date = tep_db_prepare_input($_POST['drop_out_date']);
			}else{
				$job_status = tep_db_prepare_input($_POST['job_status']);
				if($job_status == 'JOB_CHANGED'){
					$leave_date = tep_db_prepare_input($_POST['leave_date']);
					$leave_reason = tep_db_prepare_input($_POST['leave_reason']);
					$current_joining_date = tep_db_prepare_input($_POST['current_joining_date']);
					$current_company_name = tep_db_prepare_input($_POST['current_company_name']);
					$candidate_designation = tep_db_prepare_input($_POST['candidate_designation']);
					$gross_salary = tep_db_prepare_input($_POST['gross_salary']);
					$in_hand_salary = tep_db_prepare_input($_POST['in_hand_salary']);
					$other_benifits = tep_db_prepare_input($_POST['other_benifits']);
					$current_contact_person_name = tep_db_prepare_input($_POST['current_contact_person_name']);

					$current_contact_person_designation = tep_db_prepare_input($_POST['current_contact_person_designation']);
					$current_company_phone = tep_db_prepare_input($_POST['current_company_phone']);
					$current_company_email = tep_db_prepare_input($_POST['current_company_email']);
					$current_company_address = tep_db_prepare_input($_POST['current_company_address']);
					$current_company_city = tep_db_prepare_input($_POST['current_company_city']);

					$current_company_pincode = tep_db_prepare_input($_POST['current_company_pincode']);
					$is_offer_letter_collected = tep_db_prepare_input($_POST['is_offer_letter_collected']);
				}
			}
		}

		$contact_date = input_valid_date($contact_date);
		$leave_date = input_valid_date($leave_date);
		$current_joining_date = input_valid_date($current_joining_date);
		$drop_out_date = input_valid_date($drop_out_date);

		$is_salary_slip_collected = tep_db_prepare_input($_POST['is_salary_slip_collected']);
		$contact_made_by = tep_db_prepare_input($_POST['contact_made_by']);

		$arr_db_values = array(
			'student_id' => $student_id,
			'company_id' => $company_id,
			'centre_id' => $centre_id,
			'contact_date' => $contact_date,
			'contact_mode' => $contact_mode,
			'is_student_contable' => $is_student_contable,
			'contact_person_name' => $contact_person_name,
			'contact_person_relation' => $contact_person_relation,
			'contact_person_phone' => $contact_person_phone,
			'student_status' => $student_status,
			'drop_out_reason' => $drop_out_reason,
			'drop_out_date' => $drop_out_date,
			'job_status' => $job_status,
			'leave_date' => $leave_date,
			'leave_reason' => $leave_reason,
			'current_joining_date' => $current_joining_date,
			'current_company_name' => $current_company_name,
			'candidate_designation' => $candidate_designation,
			'gross_salary' => $gross_salary,
			'in_hand_salary' => $in_hand_salary,
			'other_benifits' => $other_benifits,
			'current_contact_person_name' => $current_contact_person_name,
			'current_contact_person_designation' => $current_contact_person_designation,
			'current_company_phone' => $current_company_phone,
			'current_company_email' => $current_company_email,
			'current_company_address' => $current_company_address,
			'current_company_city' => $current_company_city,
			'current_company_pincode' => $current_company_pincode,
			'is_offer_letter_collected' => $is_offer_letter_collected,
			'is_salary_slip_collected' => $is_salary_slip_collected,
			'contact_made_by' => $contact_made_by,
			'created_date' => 'now()'
		);

		$str_salary_slip = salary_slip_upload();

		if($str_salary_slip != ''){
			$arr_db_values['salary_slip'] = $str_salary_slip;
		}

		switch($action){
			case 'add':
				tep_db_perform(TABLE_HANDHOLDING, $arr_db_values);

				change_student_status($student_id, '1');

				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_HANDHOLDING, $arr_db_values, "update", "handholding_id = '" . $handholding_id . "'");

				change_student_status($student_id, '1');

				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_HANDHOLDING ." where handholding_id = '". $handholding_id ."'");
				$msg = 'deleted';
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_HANDHOLDING, tep_get_all_get_params(array('msg','int_id','actionType')) . '&msg=' . $msg));
	}

	$current_student_id = tep_db_input($_GET['stud_id']);

	$student_query_raw = "select s.student_id, s.student_full_name, s.student_father_name, s.student_mobile, s.student_status from " . TABLE_STUDENTS . " s where is_deactivated != '1'";
	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$student_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}
	$student_query_raw .= " and s.student_id  = '" . $current_student_id . "'";

	$student_query = tep_db_query($student_query_raw);

	if(!tep_db_num_rows($student_query)){
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType', 'stud_id'))));
	}

	$student = tep_db_fetch_array($student_query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Handholding</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function get_company_info(){
				var company_id = $('#company_id').val();
				var params = 'action=get_comp_info&company_id='+company_id;

				$('#comp_info_tr').hide();
				$('#comp_info_td').empty();

				if(company_id != ''){
					$.ajax({
						url: 'get_data.php',
						data: params,
						type: 'POST',
						success: function(response){
							$('#comp_info_tr').show();
							$('#comp_info_td').html(response);
						}
					});
				}
			}

			function toggle_dropout_reason(){
				var student_status = $('#student_status').val();

				//$('#job_status').val('');

				if(student_status == 'WORKING'){
					$('.dropout_reason').hide();
					$('.job_status').show();
				}else if(student_status == 'DROP_OUT'){
					$('.dropout_reason').show();
					$('.job_status').hide();
					$('.job_changed').hide();
				}else{
					$('.dropout_reason').hide();
					$('.job_status').hide();
					$('.job_changed').hide();
				}
			}

			function toggle_job_change(){
				var job_status = $('#job_status').val();

				if(job_status == 'JOB_CHANGED'){
					$('.job_changed').show();
				}else{
					$('.job_changed').hide();
				}
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this handholding?")){
					objForm.action_type.value = action_type;
					objForm.handholding_id.value = int_id;
					objForm.submit();
				}
			}

			function is_contactable(){
				if($('#is_student_contable:checked').val() == '1'){
					$('.contactable').show();
					$('.student_status').show();
				}else{
					$('#student_status').val('');
					$('#job_status').val('');

					$('.dropout_reason').hide();
					$('.contactable').hide();
					$('.student_status').hide();
					$('.job_status').hide();
					$('.job_changed').hide();
					//student_status
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#contact_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#leave_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#drop_out_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#current_joining_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				<?php if($student['student_status'] == '1' && (isset($_GET['actionType']) && $_GET['actionType'] != "add")){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
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
												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" )
												{
													if($_GET['actionType'] == "edit"){
														$int_id = $_GET['int_id'];

														$info_query_raw = "select handholding_id, student_id, centre_id, company_id, contact_date, contact_mode, is_student_contable, contact_person_name, contact_person_relation, contact_person_phone, student_status, drop_out_reason, date_format(drop_out_date, '%d-%m-%Y') as drop_out_date, job_status, leave_date, leave_reason, current_joining_date, current_company_name, candidate_designation, gross_salary, in_hand_salary, other_benifits, current_contact_person_name, current_contact_person_designation, current_company_phone, current_company_email, current_company_address, current_company_city, current_company_pincode, is_offer_letter_collected, is_salary_slip_collected, salary_slip, contact_made_by, created_date from " . TABLE_HANDHOLDING . " where handholding_id = '" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
													
													$action_type = $_GET['actionType'];
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN"><?php echo $student['student_full_name'] . ' ' . $student['student_father_name'];?> - Handholding</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Handholding Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $current_student_id; ?>"> 
																<input type="hidden" name="handholding_id" id="handholding_id" value="<?php echo $int_id; ?>"> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Handholding</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Student Name&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php
																											echo $student['student_full_name'] . ' ' . $student['student_father_name'];
																										?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Company&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php
																											$companies_query_raw = "select company_id, company_name from ". TABLE_COMPANIES ." where 1 ";
																											if($_SESSION['sess_adm_type'] != 'ADMIN'){
																												$companies_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																											}
																											$companies_query_raw .= " order by company_name";

																											$companies_query = tep_db_query($companies_query_raw);
																										?>
																										<select name="company_id" id="company_id" class="required" onchange="javascript: get_company_info();">
																											<option value="">Please choose</option>
																											<?php
																												while($companies = tep_db_fetch_array($companies_query)){
																											?>
																											<option value="<?php echo $companies['company_id'];?>" <?php echo($info['company_id'] == $companies['company_id'] ? 'selected="selected"' : '');?>><?php echo $companies['company_name'];?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr id="comp_info_tr">
																									<td>&nbsp;</td>
																									<td id="comp_info_td" class="arial12LGray" align="left">&nbsp;</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									get_company_info();
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Contact Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="contact_date" id="contact_date" value="<?php echo  ($info['contact_date'] != '' ? $info['contact_date'] : date("d-m-Y")) ?>" readonly>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Mode of Contact&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="contact_mode" id="contact_mode" class="required">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_contact_mode as $k_mode=>$v_mode){
																											?>
																											<option value="<?php echo $k_mode;?>" <?php echo($info['contact_mode'] == $k_mode ? 'selected="selected"' : '');?>><?php echo $v_mode;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Was the Student Contactable&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_student_contable" id="is_student_contable" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_student_contable'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: is_contactable()">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="contactable">
																									<td class="arial12LGrayBold" align="right">&nbsp;Name of Person Contacted&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="contact_person_name" id="contact_person_name" value="<?php echo  ($info['contact_person_name'] != '' ? $info['contact_person_name'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="contactable">
																									<td class="arial12LGrayBold" align="right">&nbsp;Relationship of the Contact Person with the Candidate&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="contact_person_relation" id="contact_person_relation" value="<?php echo  ($info['contact_person_relation'] != '' ? $info['contact_person_relation'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="contactable">
																									<td class="arial12LGrayBold" align="right">&nbsp;Contact No of the Person Contacted&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="contact_person_phone" id="contact_person_phone" maxlength="50" value="<?php echo  ($info['contact_person_phone'] != '' ? $info['contact_person_phone'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="student_status">
																									<td class="arial12LGrayBold">&nbsp;Student Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_status" id="student_status" class="required" onchange="javascript: toggle_dropout_reason();">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_placement_status as $k_status=>$v_status){
																											?>
																											<option value="<?php echo $k_status;?>" <?php echo($info['student_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr class="dropout_reason" style="display: none;">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Reason for Drop out&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea type="text" name="drop_out_reason" id="drop_out_reason" rows="5" cols="40"><?php echo  ($info['drop_out_reason'] != '' ? $info['drop_out_reason'] : '') ?></textarea>
																									</td>
																								</tr>
																								<tr class="dropout_reason" style="display: none;">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Drop out Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="drop_out_date" id="drop_out_date" value="<?php echo  ($info['drop_out_date'] != '' ? $info['drop_out_date'] : '') ?>" class="required">
																									</td>
																								</tr>
																								<tr class="job_status">
																									<td class="arial12LGrayBold">&nbsp;Job Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="job_status" id="job_status" class="required" onchange="javascript: toggle_job_change();">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_job_status as $k_status=>$v_status){
																											?>
																											<option value="<?php echo $k_status;?>" <?php echo($info['job_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold" width="15%">&nbsp;Date of leaving Previous Job&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="leave_date" id="leave_date" value="<?php echo  ($info['leave_date'] != '' ? $info['leave_date'] : date("d-m-Y")) ?>" readonly>
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Reason for leaving&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea type="text" name="leave_reason" id="leave_reason" rows="5" cols="40"><?php echo  ($info['leave_reason'] != '' ? $info['leave_reason'] : '') ?></textarea>
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Date Of Joining Current Job&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_joining_date" id="current_joining_date" value="<?php echo  ($info['current_joining_date'] != '' ? $info['current_joining_date'] : date("d-m-Y")) ?>" readonly>
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Name of Current Company/Employer&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_name" id="current_company_name" maxlength="100" value="<?php echo  ($info['current_company_name'] != '' ? $info['current_company_name'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Designation of Candidate&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="candidate_designation" id="candidate_designation" maxlength="50" value="<?php echo  ($info['candidate_designation'] != '' ? $info['candidate_designation'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Gross Salary of the Candidate&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="gross_salary" id="gross_salary" maxlength="13" value="<?php echo  ($info['gross_salary'] != '' ? $info['gross_salary'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;In Hand Salary of Candidate&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="in_hand_salary" id="in_hand_salary" maxlength="13" value="<?php echo  ($info['in_hand_salary'] != '' ? $info['in_hand_salary'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Any Other Benefits&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="other_benifits" id="other_benifits" rows="5" cols="40"><?php echo  ($info['other_benifits'] != '' ? $info['other_benifits'] : '') ?></textarea>
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Name of the Contact Person in the company&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_contact_person_name" id="current_contact_person_name" maxlength="150" value="<?php echo  ($info['current_contact_person_name'] != '' ? $info['current_contact_person_name'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Designation of Contact Person in Company&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_contact_person_designation" id="current_contact_person_designation" maxlength="50" value="<?php echo  ($info['current_contact_person_designation'] != '' ? $info['current_contact_person_designation'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Contact Number of Company Personnel&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_phone" id="current_company_phone" maxlength="50" value="<?php echo  ($info['current_company_phone'] != '' ? $info['current_company_phone'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Email Id of the Company Personnel&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_email" id="current_company_email" maxlength="50" value="<?php echo  ($info['current_company_email'] != '' ? $info['current_company_email'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Company/ Employer Address&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_address" id="current_company_address" maxlength="255" value="<?php echo  ($info['current_company_address'] != '' ? $info['current_company_address'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Company/Employer City&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_city" id="current_company_city" maxlength="255" value="<?php echo  ($info['current_company_city'] != '' ? $info['current_company_address'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold">&nbsp;Company/Employer Pin Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="current_company_pincode" id="current_company_pincode" maxlength="255" value="<?php echo  ($info['current_company_pincode'] != '' ? $info['current_company_pincode'] : '') ?>">
																									</td>
																								</tr>
																								<tr class="job_changed">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Copy of Offer Letter Collected&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_offer_letter_collected" id="is_offer_letter_collected" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_offer_letter_collected'] == $k_status ? 'checked="checked"' : '');?>>&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Salary Slip Collected&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_salary_slip_collected" id="is_salary_slip_collected" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_salary_slip_collected'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: toggle_element('is_salary_slip_collected', 'blk_salary_slip');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="blk_salary_slip">
																									<td class="arial12LGrayBold" width="10%" valign="top">&nbsp;Salary Slip&nbsp;:</td>
																									<td>
																										<?php if($info['salary_slip']!=''){?>
																										<a href="<?php echo DIR_WS_UPLOAD . $info['salary_slip'];?>" target="_blank"><?php echo $info['salary_slip'];?></a><br><br>
																										<?php } ?>
																										<input type="file" name="salary_slip" id="salary_slip">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_job_change();
																									toggle_dropout_reason();
																									is_contactable();
																									toggle_element('is_salary_slip_collected', 'blk_salary_slip');
																								//-->
																								</script>
																								<?php
																									$users_query_raw = " select u.adm_id, u.centre_id, u.adm_username, u.adm_password, u.adm_name, u.adm_email, u.adm_mobile, u.adm_status, u.adm_type, u.created_date from " . TABLE_ADMIN_MST . " u where 1";

																									$users_query_raw .= " and u.centre_id = '" . $_SESSION['sess_centre_id'] . "'";

																									$users_query = tep_db_query($users_query_raw);
																								?>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Contact Made By&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="contact_made_by" id="contact_made_by" class="required">
																											<option value="">Please choose</option>
																											<?php
																												while($users = tep_db_fetch_array($users_query)){
																											?>
																											<option value="<?php echo $users['adm_id'];?>" <?php echo($info['contact_made_by'] == $users['adm_id'] ? 'selected="selected"' : '');?>><?php echo $users['adm_name'];?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
																<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																	<tr>
																		<td>&nbsp;<input type="submit" value="UPADTE" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton"></td>
																		<td >&nbsp;</td>
																	<tr>
																</table>
															</form>
														</td>
													</tr>
												</table>
												<?php 
												}else{ 

													$order = "asc";
													$searchValue = tep_db_input($_GET['txtSearchValue']);
													$searchType = tep_db_input($_GET['cmbSearch']);
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN"><?php echo $student['student_full_name'] . ' ' . $student['student_father_name'];?> - Handholding</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."&actionType=add"); ?>" class="arial14LGrayBold">Add Handholding Record</a>&nbsp;&nbsp;&nbsp;<img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id', 'stud_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = " select handholding_id, date_format(created_date, '%d-%m-%Y') as created_date, date_format(contact_date, '%d-%m-%Y') as frm_contact_date, student_status, job_status, is_salary_slip_collected from ". TABLE_HANDHOLDING ." where student_id = '" . $current_student_id . "' ";
																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query_raw .= " order by contact_date";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="handholding_id" id="handholding_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Handholding Record</th>
																		<th>Date of Entry</th>
																		<th>Contact Date</th>
																		<th>Student Status</th>
																		<th>Job Status</th>
																		<th>Salary Slip Collected</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			$cntHold = 1;
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top">Handholding <?php echo $cntHold; ?></td>
																			<td valign="top"><?php echo $listing['created_date']; ?></td>
																			<td valign="top"><?php echo $listing['frm_contact_date']; ?></td>
																			<td valign="top"><?php echo $arr_placement_status[$listing['student_status']]; ?></td>
																			<td valign="top"><?php echo $arr_job_status[$listing['job_status']]; ?></td>
																			<td valign="top"><?php echo ($listing['is_salary_slip_collected'] == '1' ? 'Yes' : 'No'); ?></td>
																			<td valign="top">
																				<a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."&actionType=edit&int_id=".$listing['handholding_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>
																				<?php if($student['student_status'] == '0'){ ?>
																				&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['handholding_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a>
																				<?php } ?>
																			</td>
																		</tr>
																	<?php
																			$cntHold++;
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Name
																					null, // Date of Entry
																					null, // Contact Date
																					null, // Student Status
																					null, // Job Status
																					null, // Salary Slip Status
																					{ "bSortable": false}
																				],
																				"aaSorting": [[0,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bStateSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Handholding Found !!</td>
																		</tr>
																	<?php } ?>
																	</tbody>
																</table>
															</form>
														</td>
													</tr>
												</table>
											<?php 
												}
											?>
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