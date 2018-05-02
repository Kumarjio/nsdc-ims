<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$bank_account_status = tep_db_prepare_input($_POST['bank_account_status']);
		$is_bank_account = ($bank_account_status == 'OPENED' ? '1' : '0');
		$student_bank_name = tep_db_prepare_input($_POST['student_bank_name']);
		$student_branch = tep_db_prepare_input($_POST['student_branch']);
		$student_account_number = tep_db_prepare_input($_POST['student_account_number']);
		$bank_ifsc_code = tep_db_prepare_input($_POST['bank_ifsc_code']);

		$arr_db_values = array(
			'is_bank_account' => $is_bank_account,
			'student_bank_name' => $student_bank_name,
			'student_branch' => $student_branch,
			'student_account_number' => $student_account_number,
			'bank_ifsc_code' => $bank_ifsc_code,
			'bank_account_status' => $bank_account_status
		);

		switch($action){
			case 'edit':
				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "' and centre_id = '" . $centre_id . "'");

				change_student_status($student_id, '1');

				$msg = 'ac_status_edited';
			break;
			case 'delete':
				$arr_db_values = array(
					'is_bank_account' => '0',
					'student_bank_name' => '',
					'student_branch' => '',
					'student_account_number' => '',
					'bank_ifsc_code' => '',
					'bank_account_status' => '0'
				);

				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "' and centre_id = '" . $centre_id . "'");
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	if($_GET['actionType'] == "edit")
	{
		$int_id = $_GET['int_id'];

		$info_query_raw = "select student_id, centre_id, course_id, bank_account_status, is_bank_account, bank_account_status, student_bank_name, student_branch, student_account_number, bank_ifsc_code, student_status from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and is_deactivated != '1'";

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
		<title><?php echo TITLE ?>: Bank Account Status</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				<?php if($info['student_status'] == '1'){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
			});

			function toggle_bank_account(){
				var bank_account_status = $('#bank_account_status').val();
				if(bank_account_status == 'OPENED'){
					$('.bank_account').show();
				}else{
					$('.bank_account').hide();
				}
			}

			function delete_record(objForm){
				if(confirm("Are you want to delete bank account status details?")){
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
														<td class="arial18BlueN">Bank Account Status</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType')) . '&actionType=preview'); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>"> 
																<input type="hidden" name="document_id" id="document_id" value=""> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Bank Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Bank Account Status<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="bank_account_status" id="bank_account_status" class="required" onchange="javascript: toggle_bank_account();">
																											<option value="">Please choose</option>
																											<?php foreach($arr_bank_ac_status as $k_bank_ac_status=>$v_bank_ac_status){?>
																											<option value="<?php echo $k_bank_ac_status;?>" <?php echo($info['bank_account_status'] == $k_bank_ac_status ? 'selected="selected"' : '');?>><?php echo $v_bank_ac_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" width="13%" align="right">&nbsp;Name of the Bank&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										<input type="text" name="student_bank_name" id="student_bank_name" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_bank_name'] : $info['student_bank_name']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%">
																										<input type="text" name="student_branch" id="student_branch" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_branch'] : $info['student_branch']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Account Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_account_number" id="student_account_number" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_account_number'] : $info['student_account_number']) ?>" class="required number">
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" align="right">&nbsp;Bank IFSC Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="bank_ifsc_code" id="bank_ifsc_code" maxlength="20" value="<?php echo  ($dupError ? $_POST['bank_ifsc_code'] : $info['bank_ifsc_code']) ?>" class="required">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_bank_account();
																								//-->
																								</script>
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