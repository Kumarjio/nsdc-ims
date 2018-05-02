<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$action = $_POST['action_type'];

	$arrMessage = array('refund_added'=>'Refund has been added successfully', 'updated' => 'Payment has been updated succssfully.', 'settled' => 'Settlement has been made succssfully.');
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$installment_no = tep_db_prepare_input($_POST['installment_no']);
		$installment_date = tep_db_prepare_input($_POST['installment_date']);
		$installment_date = date("Y-m-d", strtotime($installment_date));
		$installment_mop = tep_db_prepare_input($_POST['installment_mop']);
		$instrument_no = tep_db_prepare_input($_POST['instrument_no']);
		$bank_name = tep_db_prepare_input($_POST['bank_name']);
		$bank_branch = tep_db_prepare_input($_POST['bank_branch']);
		$installment_amount = tep_db_prepare_input($_POST['installment_amount']);
		$is_receipt_collected = tep_db_prepare_input($_POST['is_receipt_collected']);
		$receipt_no = tep_db_prepare_input($_POST['receipt_no']);
				
		switch($action){
			case 'add_installment':
				$arr_db_values = array(
					'student_id' => $student_id,
					'installment_type' => 'COURSE_FEE',
					'installment_no' => $installment_no,
					'installment_date' => $installment_date,
					'installment_mop' => $installment_mop,
					'receipt_no' => $receipt_no,
					'installment_amount' => $installment_amount,
					'is_receipt_collected' => $is_receipt_collected
				);

				if(in_array($installment_mop, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
					$arr_db_values['bank_name'] = $bank_name;
					$arr_db_values['instrument_no'] = $instrument_no;
					$arr_db_values['bank_branch'] = $bank_branch;
				}

				if($_FILES['receipt_filename'] != ''){
					$ext = get_extension($_FILES['receipt_filename']['name']);
					$src = $_FILES['receipt_filename']['tmp_name'];

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
				}

				tep_db_perform(TABLE_INSTALLMENTS, $arr_db_values);
				$installment_id = tep_db_insert_id();

				$stud_payment_db_values = array(
					'student_id' => $student_id,
					'centre_id' => $centre_id,
					'installment_id' => $installment_id,
					'stud_payment_amount' => $installment_amount,
					'stud_payment_deposit_date' => $installment_date,
					'stud_payment_mode' => $installment_mop,
					'stud_payment_receipt_no' => $receipt_no,
					'stud_payment_status' => 'NOT_DEPOSITED',
					'stud_payment_type' => 'INSTALLMENT_PAYMENT',
					'stud_payment_added' => 'now()'
				);

				if(in_array($stud_payment_mode, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
					$stud_payment_db_values['stud_payment_bank_name'] = $bank_name;
					$stud_payment_db_values['stud_payment_cheque_no'] = $instrument_no;
					$stud_payment_db_values['stud_payment_bank_branch'] = $bank_branch;
				}

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values);
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_VIEW_STUD_PAYMENT_HISTORY, tep_get_all_get_params(array('msg','int_id','actionType')) . 'int_id=' . $student_id));
	}

	$int_id = $_GET['int_id'];

	$info_query_raw = "select student_id, centre_id, course_id, student_full_name, student_course_fee, student_payable_fee, student_outstanding_fee from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' ";
	$info_query = tep_db_query($info_query_raw);

	$info = tep_db_fetch_array($info_query);

	$student_course_fee = $info['student_course_fee'];

	$action_type = 'edit';

	$installment_query_raw = "select installment_id, student_id, installment_type, installment_no, date_format(installment_date, '%d-%m-%Y') as installment_date, installment_mop, installment_amount, instrument_no, is_receipt_collected, receipt_filename, receipt_no, bank_branch, receipt_no, bank_name from " . TABLE_INSTALLMENTS . " where student_id='" . $int_id . "' and installment_type = 'COURSE_FEE'";
	$installment_query_raw .= " order by installment_no";

	$installment_query = tep_db_query($installment_query_raw);
	$installment = array();
	while($installment_temp = tep_db_fetch_array($installment_query)){
		$installment[$installment_temp['installment_no']] = $installment_temp;
	}

	$next_inst_query_raw = "select (max(installment_no) + 1) as next_intallment_no from " . TABLE_INSTALLMENTS . " where student_id='" . $int_id . "'";
	$next_inst_query = tep_db_query($next_inst_query_raw);

	$next_inst_array = tep_db_fetch_array($next_inst_query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Student Installment</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			function toggle_bank_info(pid){
				if($('select[id="stud_payment_mode_' + pid + '"]').val() == 'CHEQUE' || $('select[id="stud_payment_mode_' + pid + '"]').val() == 'DD' || $('select[id="stud_payment_mode_' + pid + '"]').val() == 'NEFT_RTGS'){
					$('.extra_bank_info_'+pid).show();
				}else{
					$('.extra_bank_info_'+pid).hide();
				}
			}

			function toggle_cheque_info(){
				var payment_type = $('#installment_mop').val();
				if(payment_type == 'CHEQUE' || payment_type == 'DD' || payment_type == 'NEFT_RTGS'){
					$('.cheque_fields').show();
				}else{
					$('.cheque_fields').hide();
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmRefund").validate();

				$('.datepicker').datepicker({
					dateFormat: "dd-mm-yy"
				});
			});

			function submit_modified_payment(){
				return confirm("Are you sure want to update the payment details?");
			}

			function toggle_settlement(pid){
				$('#settle_' + pid).toggle();
			}
			function toggle_receipt_collected(){
				if($('#is_receipt_collected').prop("checked") == true){
					$('.receipt_collected').show();
				}else{
					$('.receipt_collected').hide();
				}
			}			
		//-->
		</script>

		<style type="text/css">
			table#payments{
				border-bottom: solid 1px #D1CFCF;
			}
			table#payments tr td, table#payments tr th{
				/*border-top: solid 1px #D1CFCF;*/
			}

			.brdr_right{
				border-right: solid 1px #D1CFCF;
			}

			.brdr_left{
				border-left: solid 1px #D1CFCF;
			}

			.brdr_bottom{
				border-bottom: solid 1px #D1CFCF;
			}
		</style>
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
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">Add New Installment - <?php echo $info['student_full_name']; ?></td>
													<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
												</tr>
												<tr>
													<td colspan="2">
														<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
															<tr>
																<td>
																	<table cellpadding="0" cellspacing="0" border="0" width="100%">
																		<tr>
																			<td class="arial14LGrayBold" colspan="2">
																				<fieldset>
																					<legend>Fees</legend>
																					<table cellpadding="5" cellspacing="5" border="0" width="100%" id="fees">
																						<tr>
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Course Name&nbsp;:</td>
																							<td class="arial12LGray" colspan="5">
																								<?php
																									$course_info_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id and course_id = '" . $info['course_id'] . "'";

																									$course_info_query = tep_db_query($course_info_query_raw);
																									$course_info = tep_db_fetch_array($course_info_query);

																									echo $course_info['course_name'] . ' - ' . $course_info['section_name'] . ' ( ' . $course_info['course_code'] . ' ) ';		
																								?>
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Course Fee&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGray" width="15%">
																								<?php echo display_currency($info['student_course_fee']);?>
																							</td>
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Payable Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGray" width="15%">
																								<span id="payable_amount"><?php echo display_currency($info['student_payable_fee']);?></span>
																							</td>
																							<!-- <td class="arial12LGrayBold" width="12%" align="right">&nbsp;Outstanding&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGray" width="15%">
																								<span id="balance_amount"><?php //echo display_currency($info['student_outstanding_fee']);?></span>
																							</td> -->
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Due Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGray" width="15%">
																								<span id="balance_amount"><?php echo display_currency($due_amount);?></span>
																							</td>
																						</tr>
																						<?php
																							if(is_array($student_waivers) && count($student_waivers)){
																								foreach($student_waivers as $student_waiver_info){
																						?>
																						<tr>
																							<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Waivers&nbsp;:</td>
																							<td width="10%" class="arial12LGray">
																								<?php echo display_currency($student_waiver_info['waiver_amount']);?>
																								&nbsp;( <?php echo display_currency($student_waiver_info['waiver_title']);?> )
																							</td>
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Waiver Reason&nbsp;:</td>
																							<td class="arial12LGray" width="15%">
																								<?php echo ($student_waiver_info['waiver_reason'] != '' ? $student_waiver_info['waiver_reason'] : 'N/A');?>
																							</td>
																						</tr>
																						<?php
																								}
																							}
																						?>
																					</table>
																				</fieldset>
																				<fieldset>
																					<legend>Installments</legend>
																					<table cellpadding="5" cellspacing="0" border="0" width="100%" id="payments">
																						<thead>
																							<tr bgcolor="#FBB900">
																								<th class="arial12LGray brdr_right brdr_left">Installment No</th>
																								<th class="arial12LGray brdr_right">Installment Date</th>
																								<th class="arial12LGray brdr_right">Installment Mode</th>
																								<th class="arial12LGray brdr_right">Instrument / Cheque No</th>
																								<th class="arial12LGray brdr_right">Bank Name</th>
																								<th class="arial12LGray brdr_right">Branch Name</th>
																								<th class="arial12LGray brdr_right">Amount</th>
																								<th class="arial12LGray brdr_right">Receipt</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																								$cnt_inst = 1;
																								foreach($installment as $installment_info){
																									$tr_color = ($cnt_inst % 2 == 0 ? '#F2F5F9' : '#FFFFFF');
																							?>
																							<tr bgcolor="<?php echo $tr_color;?>">
																								<td class="arial12LGray brdr_right brdr_left" style="border-bottom:none;">
																									<?php 
																										echo $installment_info['installment_no'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $installment_info['installment_date'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $installment_info['installment_mop'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										if(in_array($installment_info['installment_mop'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $installment_info['instrument_no'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php
																										if(in_array($installment_info['installment_mop'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $installment_info['bank_name'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										if(in_array($installment_info['installment_mop'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $installment_info['bank_branch'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $installment_info['installment_amount'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										if($installment_info['is_receipt_collected'] == '1'){
																											if($installment_info['receipt_filename'] != ''){
																												echo '<a href="' . DIR_WS_UPLOAD . $installment_info['receipt_filename'] . '" target="_blank">' .  $installment_info['receipt_no'] . '</a>';
																											}else{
																												echo $installment_info['receipt_no'];
																											}
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																							</tr>
																							<?php 
																								$cnt_inst++;
																								}
																							?>
																						<tbody>
																					</table>
																				</fieldset>	
																				<fieldset>
																					<legend>Add New Installment</legend>
																					<form name="frmRefund" id="frmRefund" action="" method="post" enctype="multipart/form-data">
																					<input type="hidden" name="action_type" id="action_type" value="add_installment">
																					<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>">
																					<table cellpadding="5" cellspacing="0" border="0" width="100%">
																						<tr>
																							<td class="arial12LGrayBold" width="20%" align="right">&nbsp;Installment Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<?php echo $next_inst_array['next_intallment_no'];?>
																								<input type="hidden" name="installment_no" id="installment_no" value="<?php echo $next_inst_array['next_intallment_no'];?>">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Installment Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="installment_date" id="installment_date" value="<?php echo date("d-m-Y");?>" class="required datepicker">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Payment Mode&nbsp;:</td>
																							<td>
																								<select name="installment_mop" id="installment_mop" onchange="javascript: toggle_cheque_info();">
																									<option value="">Please choose</option>
																									<option value="UNKNOWN">Unknown</option>
																									<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																									<option value="<?php echo $k_payment_type;?>"><?php echo $v_payment_type;?></option>
																									<?php } ?>
																								</select>
																							</td>
																						</tr>																						
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Cheque No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="instrument_no" id="instrument_no" maxlength="20" value="" class="required">
																							</td>
																						</tr>
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Bank Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="bank_name" id="bank_name" maxlength="50" value="" class="required">
																							</td>
																						</tr>
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Bank Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="bank_branch" id="bank_branch" maxlength="50" value="" class="required">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="installment_amount" id="installment_amount" value="" class="required">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Receipt Collected&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="checkbox" id="is_receipt_collected" name="is_receipt_collected" value="1" <?php echo (isset($businesses_info['business_allow_session']) && 	$businesses_info['business_allow_session'] == '1' ? 'checked="checked"' : ''); ?> onclick="toggle_receipt_collected();" >&nbsp;														
																							</td>
																						</tr>
																						<tr class="receipt_collected" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Installment Receipt No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="receipt_no" id="receipt_no" value="" class="required">
																							</td>
																						</tr>
																						<tr class="receipt_collected" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Installment Receipt File&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td>
																								<input type="file" name="receipt_filename" id="receipt_filename" />
																							</td>
																						</tr>		
																						<tr>
																							<td colspan="2">
																								<button type="submit" name="btnSubmit">Submit</button>
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