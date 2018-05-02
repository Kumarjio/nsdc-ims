<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$action = $_POST['action_type'];

	$arrMessage = array('refund_added'=>'Refund has been added successfully', 'updated' => 'Payment has been updated succssfully.', 'settled' => 'Settlement has been made succssfully.');
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$stud_payment_id = tep_db_prepare_input($_POST['pid']);
		$stud_payment_mode = tep_db_prepare_input($_POST['stud_payment_mode']);
		$stud_payment_cheque_no = tep_db_prepare_input($_POST['stud_payment_cheque_no']);
		$stud_payment_bank_name = tep_db_prepare_input($_POST['stud_payment_bank_name']);
		$stud_payment_bank_branch = tep_db_prepare_input($_POST['stud_payment_bank_branch']);
		$stud_payment_receipt_no = tep_db_prepare_input($_POST['stud_payment_receipt_no']);
		$stud_payment_deposit_date = tep_db_prepare_input($_POST['stud_payment_deposit_date']);
		$stud_payment_deposit_date = date("Y-m-d", strtotime($stud_payment_deposit_date));
		$stud_payment_amount = tep_db_prepare_input($_POST['stud_payment_amount']);
		

		switch($action){
			case 'update_payment_mode':
				$stud_payment_db_values = array(
					'stud_payment_amount' => $stud_payment_amount,
					'stud_payment_deposit_date' => $stud_payment_deposit_date,
					'stud_payment_modified' => 'now()'
				);

				if($stud_payment_mode != ''){
					$stud_payment_db_values['stud_payment_mode'] = $stud_payment_mode;
				}

				if($stud_payment_receipt_no != ''){
					$stud_payment_db_values['stud_payment_receipt_no'] = $stud_payment_receipt_no;
				}

				if(in_array($stud_payment_mode, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
					$stud_payment_db_values['stud_payment_bank_name'] = $stud_payment_bank_name;
					$stud_payment_db_values['stud_payment_cheque_no'] = $stud_payment_cheque_no;
					$stud_payment_db_values['stud_payment_bank_branch'] = $stud_payment_bank_branch;
				}

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

				$sp_info_query_raw = "select stud_payment_id, student_id, installment_id, deposit_id, stud_payment_type, stud_payment_mode, stud_payment_cheque_no, stud_payment_bank_name, stud_payment_bank_branch, date_format(stud_payment_deposit_date, '%d-%m-%Y') as stud_payment_deposit_date, stud_payment_receipt_no, stud_payment_amount, stud_payment_status, stud_payment_added from " . TABLE_STUDENT_PAYMENTS . " where stud_payment_id='" . $stud_payment_id . "' ";
				$sp_info_query = tep_db_query($sp_info_query_raw);

				$sp_info_array = tep_db_fetch_array($sp_info_query);

				if($sp_info_array['installment_id'] > 0 && $sp_info_array['stud_payment_type'] == 'INSTALLMENT_PAYMENT'){
					$instl_db_values = array(
						'installment_amount' => $stud_payment_amount,
						'installment_mop' => $stud_payment_mode,
						'installment_date' => $stud_payment_deposit_date,
						'installment_modified' => 'now()'
					);

					if(in_array($stud_payment_mode, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
						$instl_db_values['bank_name'] = $stud_payment_bank_name;
						$instl_db_values['instrument_no'] = $stud_payment_cheque_no;
						$instl_db_values['bank_branch'] = $stud_payment_bank_branch;
					}

					tep_db_perform(TABLE_INSTALLMENTS, $instl_db_values, "update", " installment_id = '" . $sp_info_array['installment_id'] . "'");
				}

				$msg = 'updated';
			break;
			case 'settle_payment_mode':
				$sp_info_query_raw = "select stud_payment_id, centre_id, student_id, installment_id, deposit_id, stud_payment_type, stud_payment_mode, stud_payment_cheque_no, stud_payment_bank_name, stud_payment_bank_branch, date_format(stud_payment_deposit_date, '%d-%m-%Y') as stud_payment_deposit_date, stud_payment_receipt_no, stud_payment_amount, stud_payment_status, stud_payment_added from " . TABLE_STUDENT_PAYMENTS . " where stud_payment_id='" . $stud_payment_id . "' ";
				$sp_info_query = tep_db_query($sp_info_query_raw);

				$sp_info_array = tep_db_fetch_array($sp_info_query);

				if($sp_info_array['installment_id'] > 0 && $sp_info_array['stud_payment_type'] == 'INSTALLMENT_PAYMENT'){
					$instl_db_values = array(
						'installment_amount' => $stud_payment_amount,
						'installment_mop' => $stud_payment_mode,
						'installment_modified' => 'now()'
					);

					if(in_array($stud_payment_mode, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
						$instl_db_values['bank_name'] = $stud_payment_bank_name;
						$instl_db_values['instrument_no'] = $stud_payment_cheque_no;
						$instl_db_values['bank_branch'] = $stud_payment_bank_branch;
					}

					tep_db_perform(TABLE_INSTALLMENTS, $instl_db_values, "update", " installment_id = '" . $sp_info_array['installment_id'] . "'");
				}

				$stud_payment_db_values = array(
					'student_id' => $student_id,
					'centre_id' => $sp_info_array['centre_id'],
					'installment_id' => $sp_info_array['installment_id'],
					'stud_payment_amount' => $stud_payment_amount,
					'stud_payment_deposit_date' => 'now()',
					'stud_payment_mode' => $stud_payment_mode,
					'stud_payment_bank_name' => $stud_payment_bank_name,
					'stud_payment_cheque_no' => $stud_payment_cheque_no,
					'stud_payment_bank_branch' => $stud_payment_bank_branch,
					'stud_payment_receipt_no' => $stud_payment_receipt_no,
					'stud_payment_status' => 'NOT_DEPOSITED',
					'stud_payment_type' => 'INSTALLMENT_PAYMENT',
					'stud_payment_added' => 'now()'
				);

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values);
				$new_stud_payment_id = tep_db_insert_id();

				$stud_payment_db_values = array(
					'stud_payment_amount' => $stud_payment_amount,
					'stud_payment_status' => 'SETTLEMENT',
					'settle_payment_id' => $new_stud_payment_id,
					'stud_payment_modified' => 'now()'
				);

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

				$msg = 'settled';
			break;
			case 'add_refund':
				$student_id = tep_db_prepare_input($_POST['student_id']);
				$centre_id = tep_db_prepare_input($_POST['centre_id']);
				$refund_amount = tep_db_prepare_input($_POST['refund_amount']);
				$refund_mode = tep_db_prepare_input($_POST['refund_mode']);
				$refund_inst_no = tep_db_prepare_input($_POST['refund_inst_no']);
				$refund_bank_name = tep_db_prepare_input($_POST['refund_bank_name']);
				$refund_branch_name = tep_db_prepare_input($_POST['refund_branch_name']);
				$refund_reason = tep_db_prepare_input($_POST['refund_reason']);
				$refund_review = tep_db_prepare_input($_POST['refund_review']);
				$refund_added = tep_db_prepare_input($_POST['refund_added']);
				$refund_added = input_valid_date($refund_added);
				

				$refund_db_values = array(
					'student_id' => $student_id,
					'centre_id' => $centre_id,
					'refund_amount' => $refund_amount,
					'refund_mode' => $refund_mode,
					'refund_bank_name' => $refund_bank_name,
					'refund_inst_no' => $refund_inst_no,
					'refund_branch_name' => $refund_branch_name,
					'refund_reason' => $refund_reason,
					'refund_review' => $refund_review,
					'refund_added' => $refund_added
				);

				tep_db_perform(TABLE_REFUNDS, $refund_db_values);
				$msg = 'refund_added';
			break;
		}
		
		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'int_id=' . $student_id . '&msg=' . $msg));
	}

	$int_id = $_GET['int_id'];

	$info_query_raw = "select student_id, centre_id, course_id, student_full_name, student_course_fee, student_payable_fee, student_outstanding_fee from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' ";
	$info_query = tep_db_query($info_query_raw);

	$info = tep_db_fetch_array($info_query);

	$student_course_fee = $info['student_course_fee'];

	$action_type = 'edit';

	$student_payments_query_raw = "select sp.stud_payment_id, sp.student_id, sp.deposit_id, sp.stud_payment_type, sp.stud_payment_mode, sp.stud_payment_cheque_no, sp.stud_payment_bank_name, sp.stud_payment_bank_branch, date_format(sp.stud_payment_deposit_date, '%d-%m-%Y') as stud_payment_deposit_date, sp.stud_payment_amount, sp.stud_payment_receipt_no, sp.is_auto_generated_reciept, sp.stud_payment_status, sp.stud_payment_added, sp.cheque_cleared, i.installment_no from " . TABLE_STUDENT_PAYMENTS . " sp left join " . TABLE_INSTALLMENTS . " i on (i.installment_id = sp.installment_id)  where sp.student_id='" . $int_id . "' ";//and stud_payment_type = 'DEPOSITED'
	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$student_payments_query_raw .= " and sp.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}

	$student_payments_query_raw .= 'ORDER BY sp.stud_payment_type, i.installment_no, sp.stud_payment_status DESC';

	$non_deposited = 0;
	$deposited = 0;
	$total_paid = 0;
	$student_payments_query = tep_db_query($student_payments_query_raw);
	$student_payments = array();
	while($student_payments_temp = tep_db_fetch_array($student_payments_query)){

		if($student_payments_temp['stud_payment_status'] == 'NOT_DEPOSITED'){
			$non_deposited += $student_payments_temp['stud_payment_amount'];
		}else if($student_payments_temp['stud_payment_status'] == 'DEPOSITED'){
			$deposited += $student_payments_temp['stud_payment_amount'];
		}

		$student_payments[] = $student_payments_temp;
	}

	$student_waivers_query_raw = "select student_waiver_id, stud_payment_id, student_id, waiver_id, waiver_title, waiver_desc, course_fee, waiver_amount, waiver_reason, waiver_added_by, waiver_added from " . TABLE_STUDENT_WAIVERS . " where student_id='" . $int_id . "'";
	$student_waivers_query = tep_db_query($student_waivers_query_raw);
	$student_waivers = array();
	$waiver_amounts = 0;
	while($student_waivers_temp = tep_db_fetch_array($student_waivers_query)){
		$waiver_amounts += $student_waivers_temp['waiver_amount'];
		$student_waivers[] = $student_waivers_temp;
	}

	$installment_query_raw = "select installment_id, student_id, installment_type, installment_no, date_format(installment_date, '%d-%m-%Y') as installment_date, installment_mop, installment_amount, instrument_no, is_receipt_collected, receipt_filename, receipt_no, bank_branch, receipt_no, bank_name from " . TABLE_INSTALLMENTS . " where student_id='" . $int_id . "' and installment_type = 'COURSE_FEE'";
	$installment_query_raw .= " order by installment_no";

	$installment_query = tep_db_query($installment_query_raw);
	$installment = array();
	while($installment_temp = tep_db_fetch_array($installment_query)){
		$installment[$installment_temp['installment_no']] = $installment_temp;
	}

	$refund_query_raw = "select refund_id, centre_id, student_id, refund_amount, refund_mode, refund_inst_no, refund_bank_name, refund_branch_name, refund_reason, refund_review, date_format(refund_added, '%d-%m-%Y') as refund_added from " . TABLE_REFUNDS . " where student_id='" . $int_id . "'";
	$refund_query = tep_db_query($refund_query_raw);

	$refund_array = array();
	$refund_amount = 0;
	while($refund_array_temp = tep_db_fetch_array($refund_query)){
		$refund_amount += $refund_array_temp['refund_amount'];
		$refund_array[] = $refund_array_temp;
	}

	$due_amount = ($student_course_fee - ($deposited + $waiver_amounts + $refund_amount));
	$total_paid = ($deposited - $refund_amount);
	$total_paid = ($total_paid > 0 ? $total_paid : 0);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Student Payments</title>
		
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
				var payment_type = $('#refund_mode').val();
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
													<td class="arial18BlueN">Student Payments - <?php echo $info['student_full_name']; ?></td>
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
																					<legend>Payments</legend>
																					<table cellpadding="5" cellspacing="0" border="0" width="100%" id="payments">
																						<thead>
																							<tr bgcolor="#FBB900">
																								<th class="arial12LGray brdr_right brdr_left">SN#</th>
																								<th class="arial12LGray brdr_right">Payment Type</th>
																								<th class="arial12LGray brdr_right">Payment Mode</th>
																								<th class="arial12LGray brdr_right">Cheque No</th>
																								<th class="arial12LGray brdr_right">Bank Name</th>
																								<th class="arial12LGray brdr_right">Bank Branch</th>
																								<th class="arial12LGray brdr_right">Deposit Date</th>
																								<th class="arial12LGray brdr_right">Amount</th>
																								<th class="arial12LGray brdr_right">Status</th>
																								<th class="arial12LGray brdr_right">Receipt</th>
																								<th class="arial12LGray brdr_right">&nbsp;</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																								$cnt_payments = 1;
																								foreach($student_payments as $student_payment_info){
																										$tr_color = ($cnt_payments % 2 == 0 ? '#F2F5F9' : '#FFFFFF');
																							?>
																							<tr bgcolor="<?php echo $tr_color;?>">
																								<td class="arial12LGray brdr_right brdr_left" style="border-bottom:none;" <?php echo($student_payment_info['stud_payment_status'] == 'NOT_DEPOSITED' ? 'rowspan="2"' : '');?>>
																									<?php
																										echo $cnt_payments;
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none;">
																									<?php 
																										echo $disp_stud_payment_type_array[$student_payment_info['stud_payment_type']];
																										if((int)$student_payment_info['installment_no'] > 0){
																											echo ' - Installment : ' . $student_payment_info['installment_no'];
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php
																										echo $student_payment_info['stud_payment_mode'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php
																										if(in_array($student_payment_info['stud_payment_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $student_payment_info['stud_payment_cheque_no'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php
																										if(in_array($student_payment_info['stud_payment_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $student_payment_info['stud_payment_bank_name'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php
																										if(in_array($student_payment_info['stud_payment_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $student_payment_info['stud_payment_bank_branch'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php
																										if($student_payment_info['stud_payment_status'] != 'NOT_DEPOSITED'){
																											echo $student_payment_info['stud_payment_deposit_date'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php echo $student_payment_info['stud_payment_amount'];?>
																								</td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;"><?php echo $disp_stud_payment_status_array[$student_payment_info['stud_payment_status']] . ($student_payment_info['cheque_cleared'] == '1' ? '&nbsp;<span style="color:green;"><b> - CLEARED</b></span>' : '');?></td>
																								<td class="arial12LGray brdr_right" style="border-bottom:none; border-left:none;">
																									<?php 
																										if(tep_not_null($student_payment_info['stud_payment_receipt_no'])){
																											if($student_payment_info['is_auto_generated_reciept'] == '1'){
																												echo '<a href="' . tep_href_link(FILENAME_RECEIPT_PRINT, 'rid=' . $student_payment_info['stud_payment_receipt_no']) . '" target="_blank">' . $student_payment_info['stud_payment_receipt_no'] . '</a>';
																											}else{
																												echo $student_payment_info['stud_payment_receipt_no'];
																											}
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td>
																									<?php
																										if($student_payment_info['stud_payment_status'] == 'BOUNCE'){
																									?>
																									<a href="javascript: void(0);" onclick="javascript: toggle_settlement('<?php echo $student_payment_info['stud_payment_id']; ?>')">Settle Payment</a>
																									<?php } ?>&nbsp;
																								</td>
																							</tr>
																							<?php
																								if($student_payment_info['stud_payment_status'] == 'NOT_DEPOSITED'){
																							?>
																							<tr bgcolor="<?php echo $tr_color;?>">
																								<td colspan="9">
																									<form name="frmDetails_<?php echo $student_payment_info['stud_payment_id']; ?>" action="" method="post" onsubmit="javascript: return submit_modified_payment();">
																									<input type="hidden" name="action_type" id="action_type" value="update_payment_mode">
																									<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>">
																									<input type="hidden" name="pid" id="pid" value="<?php echo $student_payment_info['stud_payment_id']; ?>">
																									<?php
																										$unq_class = 'extra_bank_info_' . $student_payment_info['stud_payment_id'];
																									?>
																									<table cellpadding="5" cellspacing="0">
																										<tr>
																											<td class="arial12LGrayBold" align="right">&nbsp;Amount&nbsp;:</td>
																											<td>
																												<input type="text" name="stud_payment_amount" id="stud_payment_amount" value="<?php echo $student_payment_info['stud_payment_amount'];?>" placeholder="Amount" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Date&nbsp;:</td>
																											<td>
																												<input type="text" name="stud_payment_deposit_date" id="stud_payment_deposit_date_<?php echo $student_payment_info['stud_payment_id']; ?>" value="<?php echo $student_payment_info['stud_payment_deposit_date'];?>" placeholder="Amount" class="required datepicker">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Payment Type&nbsp;:</td>
																											<td>
																												<select name="stud_payment_mode" id="stud_payment_mode_<?php echo $student_payment_info['stud_payment_id']; ?>" onchange="javascript: toggle_bank_info('<?php echo $student_payment_info['stud_payment_id']; ?>');">
																													<option value="UNKNOWN">Unknown</option>
																													<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																													<option value="<?php echo $k_payment_type;?>" <?php echo($student_payment_info['stud_payment_mode'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																													<?php } ?>
																												</select>
																											</td>
																										</tr>
																										<tr style="display:none;" class="<?php echo $unq_class;?>">
																											<td class="arial12LGrayBold" align="right">&nbsp;Cheque / Instrument No&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_cheque_no" id="stud_payment_cheque_no" maxlength="50" value="" placeholder="Cheque No" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Bank Name&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_bank_name" id="stud_payment_bank_name" maxlength="50" value="" placeholder="Bank Name" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Bank Branch&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_bank_branch" id="stud_payment_bank_branch" maxlength="50" value="" class="required" placeholder="Bank Branch">
																											</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_receipt_no" id="stud_payment_receipt_no" maxlength="50" value="" class="required" placeholder="Receipt Number">
																											</td>
																										</tr>
																										<tr>
																											<td>
																												<button type="submit" name="btnUpdate">Update</button>
																											</td>
																										</tr>
																									</table>
																									</form>
																								</td>
																							</tr>
																							<?php
																								}
																							?>
																							<?php
																								if($student_payment_info['stud_payment_status'] == 'BOUNCE'){
																							?>
																							<tr bgcolor="<?php echo $tr_color;?>" id="settle_<?php echo $student_payment_info['stud_payment_id']; ?>" style="display:none;">
																								<td colspan="11">
																									<form name="frmDetails_<?php echo $student_payment_info['stud_payment_id']; ?>" action="" method="post">
																									<input type="hidden" name="action_type" id="action_type" value="settle_payment_mode">
																									<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>">
																									<input type="hidden" name="pid" id="pid" value="<?php echo $student_payment_info['stud_payment_id']; ?>">
																									<?php
																										$unq_class = 'extra_bank_info_' . $student_payment_info['stud_payment_id'];
																									?>
																									<table cellpadding="5" cellspacing="0">
																										<tr>
																											<td class="arial12LGrayBold" align="right">&nbsp;Amount&nbsp;:</td>
																											<td>
																												<input type="text" name="stud_payment_amount" id="stud_payment_amount" value="<?php echo $student_payment_info['stud_payment_amount'];?>" placeholder="Amount" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Payment Type&nbsp;:</td>
																											<td>
																												<select name="stud_payment_mode" id="stud_payment_mode_<?php echo $student_payment_info['stud_payment_id']; ?>" onchange="javascript: toggle_bank_info('<?php echo $student_payment_info['stud_payment_id']; ?>');">
																													<option value="">Please choose</option>
																													<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																													<option value="<?php echo $k_payment_type;?>" <?php echo($info['stud_payment_mode'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																													<?php } ?>
																												</select>
																											</td>
																										</tr>
																										<tr style="display:none;" class="<?php echo $unq_class;?>">
																											<td class="arial12LGrayBold" align="right">&nbsp;Cheque / Instrument No&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_cheque_no" id="stud_payment_cheque_no" maxlength="50" value="" placeholder="Cheque No" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Bank Name&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_bank_name" id="stud_payment_bank_name" maxlength="50" value="" placeholder="Bank Name" class="required">
																											</td>
																											<td class="arial12LGrayBold" align="right">&nbsp;Bank Branch&nbsp;:</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_bank_branch" id="stud_payment_bank_branch" maxlength="50" value="" class="required" placeholder="Bank Branch">
																											</td>
																											<td class="arial12LGrayBold">
																												<input type="text" name="stud_payment_receipt_no" id="stud_payment_receipt_no" maxlength="50" value="" class="required" placeholder="Receipt Number">
																											</td>
																										</tr>
																										<tr>
																											<td>
																												<button type="submit" name="btnUpdate">Submit</button>
																											</td>
																										</tr>
																									</table>
																									</form>
																								</td>
																							</tr>
																							<?php
																								}
																							?>
																							<!-- <tr>
																								<td colspan="9" style="border:none;">&nbsp;</td>
																							</tr> -->
																							<?php
																									$cnt_payments++;
																								}
																							?>
																						</tbody>
																					</table>
																				</fieldset>
																				<fieldset>
																					<legend>Refund</legend>
																					<?php if(is_array($refund_array) && count($refund_array)){ ?>
																					<table cellpadding="5" cellspacing="0" border="0" width="100%" id="payments">
																						<thead>
																							<tr bgcolor="#FBB900">
																								<th class="arial12LGray brdr_right brdr_left">Refund Amount</th>
																								<th class="arial12LGray brdr_right">Mode</th>
																								<th class="arial12LGray brdr_right">Instrument / Cheque No</th>
																								<th class="arial12LGray brdr_right">Bank Name</th>
																								<th class="arial12LGray brdr_right">Branch Name</th>
																								<th class="arial12LGray brdr_right">Reason</th>
																								<th class="arial12LGray brdr_right">Review</th>
																								<th class="arial12LGray brdr_right">Added On</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																								foreach($refund_array as $refund_info){
																									$tr_color = ($cnt_inst % 2 == 0 ? '#F2F5F9' : '#FFFFFF');
																							?>
																							<tr bgcolor="<?php echo $tr_color;?>">
																								<td class="arial12LGray brdr_right brdr_left" style="border-bottom:none;">
																									<?php 
																										echo $refund_info['refund_amount'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $refund_info['refund_mode'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										if(in_array($refund_info['refund_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $refund_info['refund_inst_no'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php
																										if(in_array($refund_info['refund_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $refund_info['refund_bank_name'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										if(in_array($refund_info['refund_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																											echo $refund_info['refund_branch_name'];
																										}else{
																											echo '-';
																										}
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $refund_info['refund_reason'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $refund_info['refund_review'];
																									?>
																								</td>
																								<td class="arial12LGray brdr_right"  style="border-bottom:none; border-left:none;">
																									<?php 
																										echo $refund_info['refund_added'];
																									?>
																								</td>
																							</tr>
																							<?php 
																								$cnt_inst++;
																								}
																							?>
																						<tbody>
																					</table>
																					<h3>Add New Refund</h3>
																					<?php } ?>
																					<form name="frmRefund" id="frmRefund" action="" method="post">
																					<input type="hidden" name="action_type" id="action_type" value="add_refund">
																					<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>">
																					<input type="hidden" name="centre_id" id="centre_id" value="<?php echo $info['centre_id']; ?>">
																					<table cellpadding="5" cellspacing="0" border="0" width="100%">
																						<tr>
																							<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Reason&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<select name="refund_reason" id="refund_reason" class="required">
																									<option value="">Please choose</option>
																									<?php foreach($refund_reason_array as $refund_reason){?>
																									<option value="<?php echo $refund_reason;?>"><?php echo $refund_reason;?></option>
																									<?php } ?>
																								</select>
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="refund_amount" id="refund_amount" value="" class="required" max="<?php echo $total_paid;?>">
																								<small>(Maximum - <?php echo display_currency($total_paid);?>)</small>
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Payment Mode&nbsp;:</td>
																							<td>
																								<select name="refund_mode" id="refund_mode" onchange="javascript: toggle_cheque_info();">
																									<option value="">Please choose</option>
																									<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																									<option value="<?php echo $k_payment_type;?>"><?php echo $v_payment_type;?></option>
																									<?php } ?>
																								</select>
																							</td>
																						</tr>
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Cheque No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="refund_inst_no" id="refund_inst_no" maxlength="20" value="" class="required">
																							</td>
																						</tr>
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Bank Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="refund_bank_name" id="refund_bank_name" maxlength="50" value="" class="required">
																							</td>
																						</tr>
																						<tr class="cheque_fields" style="display:none;">
																							<td class="arial12LGrayBold" align="right">&nbsp;Bank Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="refund_branch_name" id="refund_branch_name" maxlength="50" value="" class="required">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Refund Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="refund_added" id="refund_added" value="<?php echo date("d-m-Y");?>" class="required datepicker">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right" valign="top">&nbsp;Review&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<textarea name="refund_review" id="refund_review" cols="30" rows="10"></textarea>
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