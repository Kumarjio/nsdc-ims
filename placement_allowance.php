<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	$total_installment = 10;
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$placement_alw_amt_paid = tep_db_prepare_input($_POST['placement_alw_amt_paid']);
		$placement_alw_date = tep_db_prepare_input($_POST['placement_alw_date']);
		$placement_alw_mop = tep_db_prepare_input($_POST['placement_alw_mop']);
		$placement_alw_installment = tep_db_prepare_input($_POST['placement_alw_installment']);
		$placement_alw_remarks = tep_db_prepare_input($_POST['placement_alw_remarks']);

		$placement_alw_date = input_valid_date($placement_alw_date);

		$arr_db_values = array(
			'placement_alw_amt_paid' => $placement_alw_amt_paid,
			'placement_alw_date' => $placement_alw_date,
			'placement_alw_mop' => $placement_alw_mop,
			'placement_alw_installment' => $placement_alw_installment,
			'placement_alw_remarks' => $placement_alw_remarks
		);

		switch($action){
			case 'edit':
				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "' and centre_id = '" . $centre_id . "'");

				$arr_old_installment = array();

				$old_installment_query_raw = "select installment_no, receipt_filename from " . TABLE_INSTALLMENTS . " where student_id = '" . $student_id . "' and installment_type = 'PLACEMENT_ALLOWANCE'";
				$old_installment_query = tep_db_query($old_installment_query_raw);

				while($old_installment_temp = tep_db_fetch_array($old_installment_query)){
					$arr_old_installment[$old_installment_temp['installment_no']] = $old_installment_temp;
				}

				tep_db_query("delete from " . TABLE_INSTALLMENTS . " where student_id = '" . $student_id . "' and installment_type = 'PLACEMENT_ALLOWANCE'");

				for($cnt_inst = 1; $cnt_inst<=$placement_alw_installment; $cnt_inst++){
					$installment_amount = $_POST['installment_amount'][$cnt_inst];
					$is_receipt_collected = $_POST['is_receipt_collected'][$cnt_inst];
					$installment_date = $_POST['installment_date'][$cnt_inst];
					$installment_date = input_valid_date($installment_date);
					$installment_mop = $_POST['installment_mop'][$cnt_inst];
					$instrument_no = $_POST['instrument_no'][$cnt_inst];

					$arr_db_values = array(
						'student_id' => $student_id,
						'installment_type' => 'PLACEMENT_ALLOWANCE',
						'installment_no' => $cnt_inst,
						'installment_date' => $installment_date,
						'installment_mop' => $installment_mop,
						'instrument_no' => $instrument_no,
						'installment_amount' => $installment_amount,
						'is_receipt_collected' => $is_receipt_collected
					);

					if($_FILES['receipt_filename']['name'][$cnt_inst] != ''){
						$ext = get_extension($_FILES['receipt_filename']['name'][$cnt_inst]);
						$src = $_FILES['receipt_filename']['tmp_name'][$cnt_inst];

						$dest_filename = 'receipt_' . time() . $ext;
						$dest = DIR_FS_UPLOAD . $dest_filename;

						if(file_exists($dest))
						{
							@unlink($dest);
						}

						if(move_uploaded_file($src, $dest))	
						{
							$arr_db_values['receipt_filename'] = $dest_filename;
						}
					}else{
						if(isset($arr_old_installment[$cnt_inst]['receipt_filename']) && tep_not_null($arr_old_installment[$cnt_inst]['receipt_filename'])){
							$arr_db_values['receipt_filename'] = $arr_old_installment[$cnt_inst]['receipt_filename'];
						}
					}

					tep_db_perform(TABLE_INSTALLMENTS, $arr_db_values);
				}

				change_student_status($student_id, '1');

				$msg = 'update_place_allow';

			break;
			case 'delete':
				$arr_db_values = array(
					'placement_alw_amt_paid' => '',
					'placement_alw_date' => '',
					'placement_alw_mop' => '',
					'placement_alw_installment' => '0',
					'placement_alw_remarks' => '',
				);

				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "' and centre_id = '" . $centre_id . "'");
				tep_db_query("delete from " . TABLE_INSTALLMENTS . " where student_id = '" . $student_id . "' and installment_type = 'PLACEMENT_ALLOWANCE'");
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	if($_GET['actionType'] == "edit")
	{
		$int_id = $_GET['int_id'];

		$info_query_raw = "select student_id, centre_id, course_id, placement_alw_amt_paid, date_format(placement_alw_date, '%d-%m-%Y') as placement_alw_date, placement_alw_mop, placement_alw_installment, placement_alw_remarks, student_status from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and is_deactivated != '1' ";

		if($_SESSION['sess_adm_type'] != 'ADMIN'){
			$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
		}

		$info_query = tep_db_query($info_query_raw);

		$info = tep_db_fetch_array($info_query);

		$action_type = 'edit';

		$installment_query_raw = "select installment_id, student_id, installment_type, installment_no, date_format(installment_date, '%d-%m-%Y') as installment_date, installment_mop, installment_amount, instrument_no, is_receipt_collected, receipt_filename from " . TABLE_INSTALLMENTS . " where student_id='" . $int_id . "' and installment_type = 'PLACEMENT_ALLOWANCE' order by installment_no";
		$installment_query = tep_db_query($installment_query_raw);
		$installment = array();
		while($installment_temp = tep_db_fetch_array($installment_query)){
			$installment[$installment_temp['installment_no']] = $installment_temp;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Placement Allowance</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
					$('#installment_date_<?php echo $cntInstallment;?>').datepicker({
						dateFormat: "dd-mm-yy",
						changeMonth: true,
						changeYear: true
					});
				<?php } ?>

				<?php if($info['student_status'] == '1'){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
			});

			function toggle_installment(){
				var installment = $('#placement_alw_installment').val();
				$('table tr[class^="inst_"]').hide();
				for(var cnt_inst=1;cnt_inst<=installment; cnt_inst++){
					$('.inst_'+cnt_inst).show();
				}
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function toggle_instr_no(instl_no){
				var mop = $('#installment_mop_'+instl_no).val();
				if(mop == 'CHEQUE' || mop == 'DD' || mop == 'NEFT_RTGS'){
					$('.instrument_no_'+instl_no).show();
				}else{
					$('.instrument_no_'+instl_no).hide();
				}
			}

			function change_receipt(installment){
				if($('#receipt_file_'+installment).attr('style') == 'display: none;'){
					$('#receipt_file_'+installment).show();
					$('#txt_change_'+installment).html('Cancel');
				}else{
					$('#receipt_file_'+installment).hide();
					$('#txt_change_'+installment).html('Change');
				}
			}

			function delete_record(objForm){
				if(confirm("Are you want to delete placement allowance details?")){
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
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Placement Allowance</td>
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
																							<legend>Placement Allowance</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="15%">&nbsp;Total Payable Amount<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<input type="text" name="placement_alw_amt_paid" id="placement_alw_amt_paid" maxlength="11" value="<?php echo  ($dupError ? $_POST['placement_alw_amt_paid'] : $info['placement_alw_amt_paid']) ?>" class="required number">
																									</td>
																								</tr>
																								<!-- <tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Date<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<input type="text" name="placement_alw_date" id="placement_alw_date" value="<?php //echo  ($dupError ? $_POST['placement_alw_date'] : $info['placement_alw_date']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Mode of Payment<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<select name="placement_alw_mop" id="placement_alw_mop" class="required">
																											<option value="">Please choose</option>
																											<?php //foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																											<option value="<?php //echo $k_payment_type;?>" <?php //echo($info['placement_alw_mop'] == $k_payment_type ? 'selected="selected"' : '');?>><?php //echo $v_payment_type;?></option>
																											<?php //} ?>
																										</select>
																									</td>
																								</tr> -->
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Installment<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<select name="placement_alw_installment" id="placement_alw_installment" class="required" style="width:80px;" onchange="javascript: toggle_installment();">
																											<option value="">Choose</option>
																											<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
																											<option value="<?php echo $cntInstallment;?>" <?php echo($info['placement_alw_installment'] == $cntInstallment ? 'selected="selected"' : '');?>><?php echo $cntInstallment;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td colspan="2">
																										<table cellpadding="5" cellspacing="5" border="0" width="100%" class="arial12LGray">
																										<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
																										<tr class="inst_<?php echo $cntInstallment;?>">
																											<td width="8%" rowspan="2" valign="top"><b>Installment <?php echo $cntInstallment;?></b></td>
																											<td width="12%">Installment Date :</td>
																											<td width="10%"><input type="text" name="installment_date[<?php echo $cntInstallment;?>]" id="installment_date_<?php echo $cntInstallment;?>" value="<?php echo $installment[$cntInstallment]['installment_date'];?>" class="required" style="width: 100px;"></td>
																											<td width="12%">&nbsp;Mode of Payment :</td>
																											<td width="10%">
																												<select name="installment_mop[<?php echo $cntInstallment;?>]" id="installment_mop_<?php echo $cntInstallment;?>" class="required" onchange="javascript: toggle_instr_no('<?php echo $cntInstallment;?>');">
																													<option value="">Please choose</option>
																													<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																													<option value="<?php echo $k_payment_type;?>" <?php echo($installment[$cntInstallment]['installment_mop'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																													<?php } ?>
																												</select>
																											</td>
																											<td width="12%" class="instrument_no_<?php echo $cntInstallment;?>">&nbsp;Instrument No :</td>
																											<td class="instrument_no_<?php echo $cntInstallment;?>">
																												<input type="text" name="instrument_no[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['instrument_no'];?>">
																											</td>
																										</tr>
																										<tr class="inst_<?php echo $cntInstallment;?>">
																											<td width="12%">Installment Amount :</td>
																											<td width="10%"><input type="text" name="installment_amount[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['installment_amount'];?>" style="width: 100px;"></td>
																											<td width="12%">Receipt collected :</td>
																											<td width="10%">
																												<?php foreach($arr_status as $k_status=>$v_status){?>
																													<input type="radio" name="is_receipt_collected[<?php echo $cntInstallment;?>]" id="is_receipt_collected_<?php echo $cntInstallment;?>" value="<?php echo $k_status;?>" class="required" <?php echo ($installment[$cntInstallment]['is_receipt_collected'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: toggle_element('is_receipt_collected_<?php echo $cntInstallment;?>', 'receipt_<?php echo $cntInstallment;?>');">&nbsp;<?php echo $v_status;?>&nbsp;
																												<?php } ?>
																											</td>
																											<td width="8%"><span class="receipt_<?php echo $cntInstallment;?>">Receipt :</span></td>
																											<td>
																											<?php 
																												$file_tag_display = '';
																												if($installment[$cntInstallment]['receipt_filename'] != '' && file_exists(DIR_FS_UPLOAD . $installment[$cntInstallment]['receipt_filename'])){ 
																													$file_tag_display = 'none;';
																											?>
																											<span><a href="<?php echo DIR_WS_UPLOAD . $installment[$cntInstallment]['receipt_filename'];?>" target="_blank"><?php echo $installment[$cntInstallment]['receipt_filename'];?></a></span>&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;<a href="javascript: void(0);" onclick="javascript: change_receipt('<?php echo $cntInstallment;?>')" id="txt_change_<?php echo $cntInstallment;?>">Change</a>&nbsp;]&nbsp;&nbsp;&nbsp;
																											<?php } ?>
																											<span class="receipt_<?php echo $cntInstallment;?>"><input type="file" name="receipt_filename[<?php echo $cntInstallment;?>]" value="" class="required" id="receipt_file_<?php echo $cntInstallment;?>" style="display: <?php echo $file_tag_display;?>">&nbsp;</span></td>
																										</tr>
																										<script type="text/javascript">
																										<!--
																											toggle_element('is_receipt_collected_<?php echo $cntInstallment;?>', 'receipt_<?php echo $cntInstallment;?>');

																											toggle_instr_no('<?php echo $cntInstallment;?>');
																										//-->
																										</script>
																										<?php } ?>
																										</table>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_installment();
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right" valign="top">&nbsp;Remarks:</td>
																									<td class="arial12LGray">
																										<textarea name="placement_alw_remarks" id="placement_alw_remarks" cols="40" rows="6"><?php echo  ($dupError ? $_POST['placement_alw_remarks'] : $info['placement_alw_remarks']) ?></textarea>
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