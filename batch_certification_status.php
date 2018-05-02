<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$test_allotted_date = tep_db_prepare_input($_POST['test_allotted_date']);
		$test_abn_no = tep_db_prepare_input($_POST['test_abn_no']);
		$test_agency = tep_db_prepare_input($_POST['test_agency']);
		$is_form_uploaded_sdi_web = tep_db_prepare_input($_POST['is_form_uploaded_sdi_web']);

		$test_allotted_date = input_valid_date($test_allotted_date);

		$arr_db_values = array(
			'test_allotted_date' => $test_allotted_date,
			'test_agency' => $test_agency,
			'test_abn_no' => $test_abn_no,
			'is_form_uploaded_sdi_web' => $is_form_uploaded_sdi_web
		);

		switch($action){
			case 'edit':

				tep_db_perform(TABLE_BATCHES, $arr_db_values, "update", "batch_id = '" . $batch_id . "'");
				$msg = 'cert_edited';

			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_BATCHES, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Batch Certification Status</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#test_allotted_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
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
													$int_id = $_GET['int_id'];

													$info_query_raw = "select batch_id, test_allotted_date, test_abn_no, test_agency, is_form_uploaded_sdi_web from " . TABLE_BATCHES . " where batch_id = '" . $int_id . "' ";

													if($_SESSION['sess_adm_type'] != 'ADMIN'){
														$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
													}

													$info_query = tep_db_query($info_query_raw);

													$info = tep_db_fetch_array($info_query);

													$action_type = 'edit';
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Batch Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_BATCHES, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Batch Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType')) . '&actionType=preview'); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="batch_id" id="batch_id" value="<?php echo $info['batch_id']; ?>"> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Batch Certification Status</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
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