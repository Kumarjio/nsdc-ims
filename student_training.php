<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$is_training_completed = tep_db_prepare_input($_POST['is_training_completed']);
		$training_dropout_reason = $_POST['training_dropout_reason'];
		$training_dropout_date = tep_db_prepare_input($_POST['training_dropout_date']);

		$training_dropout_date = input_valid_date($training_dropout_date);

		$arr_db_values = array(
			'is_training_completed' => $is_training_completed,
			'training_dropout_date' => $training_dropout_date,
			'training_dropout_reason' => $training_dropout_reason
		);

		switch($action){
			case 'edit':
				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");
				
				change_student_status($student_id, '1');

				$msg = 'asses_edited';

			break;
			case 'delete':
				$arr_db_values = array(
					'is_training_completed' => '0',
					'training_dropout_date' => '',
					'training_dropout_reason' => ''
				);

				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	if($_GET['actionType'] == "edit")
	{
		$int_id = $_GET['int_id'];

		$info_query_raw = "select student_id, centre_id, course_id, is_training_completed, training_dropout_reason, date_format(training_dropout_date, '%d-%m-%Y') as training_dropout_date, student_status from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and is_deactivated != '1'";

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
		<title><?php echo TITLE ?>: Student Training</title>
		
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

				$('#training_dropout_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				<?php if($info['student_status'] == '1'){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
			});

			function delete_record(objForm){
				if(confirm("Are you sure want to delete training details?")){
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
																<input type="hidden" name="document_id" id="document_id" value=""> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Training Management</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Training Completed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_training_completed" id="is_training_completed" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_training_completed'] == $k_status ? 'checked="checked"' : '');?>  onclick="javascript: toggle_element('is_training_completed', 'drop_reason', '1');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="drop_reason">
																									<td class="arial12LGrayBold" valign="top">&nbsp;Reason for Drop Out&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="training_dropout_reason" id="training_dropout_reason" rows="5" cols="40"  class="required"><?php echo $info['training_dropout_reason'];?></textarea>
																									</td>
																								</tr>
																								<tr class="drop_reason">
																									<td class="arial12LGrayBold">&nbsp;Date of Drop Out&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="training_dropout_date" id="training_dropout_date" value="<?php echo $info['training_dropout_date'];?>" style="width: 120px"  class="required">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_training_completed', 'drop_reason', '1');
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