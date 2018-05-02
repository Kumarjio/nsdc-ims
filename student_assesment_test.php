<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$is_apeared_for_test = tep_db_prepare_input($_POST['is_apeared_for_test']);
		$test_result = tep_db_prepare_input($_POST['test_result']);
		$is_certificate_recieved = tep_db_prepare_input($_POST['is_certificate_recieved']);
		$assessor_name = tep_db_prepare_input($_POST['assessor_name']);
		$certificate_number = tep_db_prepare_input($_POST['certificate_number']);
		$certificate_date = tep_db_prepare_input($_POST['certificate_date']);
		$certificate_name = tep_db_prepare_input($_POST['certificate_name']);
		$certificate_body_name = tep_db_prepare_input($_POST['certificate_body_name']);
		$test_absent_reason = $_POST['test_absent_reason'];

		$test_allotted_date = tep_db_prepare_input($_POST['test_allotted_date']);
		$test_abn_no = tep_db_prepare_input($_POST['test_abn_no']);
		$test_agency = tep_db_prepare_input($_POST['test_agency']);
		$is_form_uploaded_sdi_web = tep_db_prepare_input($_POST['is_form_uploaded_sdi_web']);
		$certificate_date = input_valid_date($certificate_date);
		$test_allotted_date = input_valid_date($test_allotted_date);

		$arr_db_values = array(
			'is_apeared_for_test' => $is_apeared_for_test,
			'test_result' => $test_result,
			'is_certificate_recieved' => $is_certificate_recieved,
			'assessor_name' => $assessor_name,
			'certificate_number' => $certificate_number,
			'certificate_date' => $certificate_date,
			'certificate_name' => $certificate_name,
			'certificate_body_name' => $certificate_body_name,
			'test_absent_reason' => $test_absent_reason,
			'test_allotted_date' => $test_allotted_date,
			'test_agency' => $test_agency,
			'test_abn_no' => $test_abn_no,
			'is_form_uploaded_sdi_web' => $is_form_uploaded_sdi_web
		);

		if($_FILES['certificate_copy']['name'] != ''){
			$ext = get_extension($_FILES['certificate_copy']['name']);
			$src = $_FILES['certificate_copy']['tmp_name'];

			$dest_filename = 'certificate_copy_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['certificate_copy'] = $dest_filename;
			}
		}

		switch($action){
			case 'edit':
				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");

				change_student_status($student_id, '1');

				$msg = 'asses_edited';
			break;
			case 'delete':
				$arr_db_values = array(
					'is_apeared_for_test' => '0',
					'test_result' => '',
					'is_certificate_recieved' => '',
					'certificate_copy' => '',
					'assessor_name' => '',
					'certificate_number' => '',
					'certificate_date' => '',
					'certificate_name' => '',
					'certificate_body_name' => '',
					'test_absent_reason' => '',
					'test_allotted_date' => '',
					'test_agency' => '',
					'test_abn_no' => '',
					'is_form_uploaded_sdi_web' => ''
				);

				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	if($_GET['actionType'] == "edit")
	{
		$int_id = $_GET['int_id'];

		$info_query_raw = "select student_id, centre_id, course_id, is_apeared_for_test, test_result, is_certificate_recieved, certificate_copy, assessor_name, certificate_number, date_format(certificate_date, '%d-%m-%Y') as certificate_date,certificate_name,certificate_body_name, test_absent_reason, batch_id, date_format(test_allotted_date, '%d-%m-%Y') as test_allotted_date, test_abn_no, test_agency, is_form_uploaded_sdi_web, student_status from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and is_deactivated != '1'";

		if($_SESSION['sess_adm_type'] != 'ADMIN'){
			$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
		}

		$info_query = tep_db_query($info_query_raw);

		$info = tep_db_fetch_array($info_query);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Assesment Test Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function toggle_element(source_element, target_element, is_reverse){
				var check_value = (is_reverse == '1' ? '0' : '1');
				if($('input[id="'+source_element+'"]:checked').val() == check_value){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#test_allotted_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});
				$('#certificate_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				<?php if($info['student_status'] == '1'){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
			});

			function delete_record(objForm){
				if(confirm("Are you want to delete assesment test details?")){
					objForm.action_type.value = 'delete';
					objForm.submit();
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
												if($_GET['actionType'] == "edit")
												{
													$action_type = 'edit';
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Student Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType')) . '&actionType=preview'); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>"> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Assessment Test</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Apeared for the Test&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_apeared_for_test" id="is_apeared_for_test" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_apeared_for_test'] == $k_status ? 'checked="checked"' : '');?>  onclick="javascript: toggle_element('is_apeared_for_test', 'test_reason', '1');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="test_reason">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Reason for Absence&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="test_absent_reason" id="test_absent_reason" class="required" rows="5" cols="40"><?php echo $info['test_absent_reason'];?></textarea>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_apeared_for_test', 'test_reason', '1');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Test Result&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="test_result" id="test_result" class="required" style="width: 140px;">
																											<option value="">Please choose</option>
																											<?php foreach($arr_exam_result as $k_result=>$v_result){ ?>
																											<option value="<?php echo $k_result;?>" <?php echo($info['test_result'] == $k_result ? 'selected="selected"' : '');?>><?php echo $v_result;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Assessor Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="assessor_name" id="assessor_name" value="<?php echo  ($dupError ? $_POST['assessor_name'] : $info['assessor_name']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Certificate Recieved&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_certificate_recieved" id="is_certificate_recieved" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_certificate_recieved'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_certificate_recieved', 'blk_certificate');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="blk_certificate">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Certificate Copy&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php if($info['certificate_copy']!=''){?>
																										<a href="<?php echo DIR_WS_UPLOAD . $info['certificate_copy'];?>" target="_blank"><?php echo $info['certificate_copy'];?></a><br><br>
																										<?php } ?>
																										<input type="file" name="certificate_copy" id="certificate_copy">
																									</td>
																								</tr>
																								<tr class="blk_certificate">
																									<td class="arial12LGrayBold" width="15%">&nbsp;Certificate Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="certificate_number" id="certificate_number" value="<?php echo  ($dupError ? $_POST['certificate_number'] : $info['certificate_number']) ?>">
																									</td>
																								</tr>
																								<tr class="blk_certificate">
																									<td class="arial12LGrayBold" width="15%">&nbsp;Certificate Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="certificate_date" id="certificate_date" value="<?php echo  ($dupError ? $_POST['certificate_date'] : $info['certificate_date']) ?>">
																									</td>
																								</tr>
																								<tr class="blk_certificate">
																									<td class="arial12LGrayBold" width="15%">&nbsp;Certificate Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="certificate_name" id="certificate_name" value="<?php echo  ($dupError ? $_POST['certificate_name'] : $info['certificate_name']) ?>">
																									</td>
																								</tr>
																								<tr class="blk_certificate">
																									<td class="arial12LGrayBold" width="15%">&nbsp;Certifying Body Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="certificate_body_name" id="certificate_body_name" value="<?php echo  ($dupError ? $_POST['certificate_body_name'] : $info['certificate_body_name']) ?>">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_certificate_recieved', 'blk_certificate');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Test Alloted Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="test_allotted_date" id="test_allotted_date" value="<?php echo  ($dupError ? $_POST['test_allotted_date'] : $info['test_allotted_date']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;ABN No.&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="test_abn_no" id="test_abn_no" maxlength="20" value="<?php echo  ($dupError ? $_POST['test_abn_no'] : $info['test_abn_no']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Testing Agency&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="test_agency" id="test_agency" maxlength="100" value="<?php echo  ($dupError ? $_POST['test_agency'] : $info['test_agency']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Form Uploaded on SDI Website&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_form_uploaded_sdi_web" id="is_form_uploaded_sdi_web" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_form_uploaded_sdi_web'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
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
																<?php if($info['student_status'] == '0'){ ?>
																<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																	<tr>
																		<td>&nbsp;<input type="submit" value="UPADTE" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton">
																		<?php if($info['student_id'] != ''){ ?>
																		&nbsp;&nbsp;&nbsp;<input type="button" value="DELETE" name="cmdDel" id="cmdDel" class="groovybutton" onclick="javascript: delete_record(document.frmDetails);">
																		<?php } ?>
																		</td>
																		<td >&nbsp;</td>
																	<tr>
																</table>
																<?php } ?>
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