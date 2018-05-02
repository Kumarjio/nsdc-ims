<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("edited"=>"Batch has been updated successfully");
	$action = $_POST['action_type'];

	$arr_batch_status = array('COMPLETED'=>'Completed', 'IN_PROGRESS'=>'In Progress', 'TO_BE_STARTED'=>'To Be Started');
	
	if(isset($action) && tep_not_null($action))
	{
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$batch_status = tep_db_prepare_input($_POST['batch_status']);

		$arr_db_values = array(
			'batch_status' => $batch_status
		);

		tep_db_perform(TABLE_BATCHES, $arr_db_values, "update", "batch_id = '" . $batch_id . "'");

		tep_redirect(tep_href_link(FILENAME_BATCHES, tep_get_all_get_params(array('msg','batch_id','actionType'))));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
	<head>
		<title><?php echo TITLE ?>: Batch Status Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();
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
										<?php
											$int_id = $_GET['batch_id'];

											$info_query_raw = " select batch_id, centre_id, batch_title, batch_status from " . TABLE_BATCHES . " where batch_id='" . $int_id . "' ";

											if($_SESSION['sess_adm_type'] != 'ADMIN'){
												$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
											}

											$info_query = tep_db_query($info_query_raw);

											$info = tep_db_fetch_array($info_query);
										?>
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN"><?php echo $info['batch_title']; ?> - Batch Status</td>
													<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','batch_id'))); ?>" class="arial14LGrayBold">Batch Listing</a></td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
															<input type="hidden" name="action_type" id="action_type" value="update_batch_status">
															<input type="hidden" name="batch_id" id="batch_id" value="<?php echo $info['batch_id']; ?>"> 
															<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																<tr>
																	<td class="arial12LGrayBold" valign="top" align="right" width="15%">&nbsp;Batch Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																	<td>
																		<select name="batch_status" id="batch_status" title="Please select batch status" class="required">
																			<option value=""></option>
																			<?php
																				foreach($arr_batch_status as $k_status=>$v_status){
																			?>
																			<option value="<?php echo $k_status;?>" <?php echo($info['batch_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																			<?php } ?>
																		</select>
																	</td>
																</tr>
															</table>
															<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																<tr>
																	<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton"></td>
																	<td >&nbsp;</td>
																<tr>
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