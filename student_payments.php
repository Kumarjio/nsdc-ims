<?php	
	include('includes/application_top.php');

	ini_set("display_errors", "On");

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];

		$course_waiver = tep_db_prepare_input($_POST['course_waiver']);
		$course_waiver_reason = tep_db_prepare_input($_POST['course_waiver_reason']);

		$stud_payment_amount = tep_db_prepare_input($_POST['stud_payment_amount']);
		$stud_payment_deposit_date = tep_db_prepare_input($_POST['stud_payment_deposit_date']);
		$stud_payment_deposit_date = date("Y-m-d", strtotime($stud_payment_deposit_date));
		$stud_payment_mode = tep_db_prepare_input($_POST['stud_payment_mode']);
		$stud_payment_cheque_no = tep_db_prepare_input($_POST['stud_payment_cheque_no']);
		$stud_payment_bank_name = tep_db_prepare_input($_POST['stud_payment_bank_name']);
		$stud_payment_bank_branch = tep_db_prepare_input($_POST['stud_payment_bank_branch']);
		$student_course_fee = tep_db_prepare_input($_POST['student_course_fee']);
		$stud_payment_receipt_no = tep_db_prepare_input($_POST['stud_payment_receipt_no']);
		$generate_receipt = tep_db_prepare_input($_POST['generate_receipt']);
		$inst_generate_receipt_array = tep_db_prepare_input($_POST['inst_generate_receipt']);

		$no_installment = tep_db_prepare_input($_POST['no_installment']);

		$placement_alw_date = input_valid_date($placement_alw_date);

		switch($action){
			case 'edit':
				$stud_payment_db_values = array(
					'student_id' => $student_id,
					'centre_id' => $centre_id,
					'stud_payment_amount' => $stud_payment_amount,
					'stud_payment_deposit_date' => 'now()',
					'stud_payment_mode' => $stud_payment_mode,
					'stud_payment_receipt_no' => $stud_payment_receipt_no,
					'stud_payment_status' => 'NOT_DEPOSITED',
					'stud_payment_type' => 'DOWN_PAYMENT',
					'stud_payment_added' => 'now()'
				);

				if(in_array($stud_payment_mode, array('CHEQUE', 'DD', 'NEFT_RTGS'))){
					$stud_payment_db_values['stud_payment_bank_name'] = $stud_payment_bank_name;
					$stud_payment_db_values['stud_payment_cheque_no'] = $stud_payment_cheque_no;
					$stud_payment_db_values['stud_payment_bank_branch'] = $stud_payment_bank_branch;
				}

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values);
				$stud_payment_id = tep_db_insert_id();

				$student_payable_fee = $student_course_fee;

				if((int)$course_waiver > 0){
					$course_waiver_info_query_raw = "select waiver_id, course_id, waiver_title, waiver_desc, waiver_amount, waiver_expiry from " . TABLE_COURSES_WAIVERS . " where waiver_id = '" . $course_waiver . "'";
					$course_waiver_info_query = tep_db_query($course_waiver_info_query_raw);
					$course_waiver_info = tep_db_fetch_array($course_waiver_info_query);

					$stud_waivers_db_array = array(
						'stud_payment_id' => $stud_payment_id,
						'student_id' => $student_id,
						'waiver_id' => $course_waiver,
						'course_id' => $course_waiver_info['course_id'],
						'waiver_title' => $course_waiver_info['waiver_title'],
						'waiver_desc' => $course_waiver_info['waiver_desc'],
						'course_fee' => $student_course_fee,
						'waiver_amount' => $course_waiver_info['waiver_amount'],
						'waiver_reason' => $course_waiver_reason,
						'waiver_added_by' => $_SESSION['sess_admin_id'],
						'waiver_added' => 'now()'
					);

					tep_db_perform(TABLE_STUDENT_WAIVERS, $stud_waivers_db_array);

					$student_payable_fee = $student_course_fee - $course_waiver_info['waiver_amount'];
				}

				$stud_db_values = array(
					'student_course_fee' => $student_course_fee,
					'student_payable_fee' => $student_payable_fee,
					'student_outstanding_fee' => 0
				);

				tep_db_perform(TABLE_STUDENTS, $stud_db_values, "update", "student_id = '" . $student_id . "'");

				if($generate_receipt == '1'){
					$invoice_number = latest_invoice_number();
					$receipts_db_values = array(
						'stud_payment_id' => $stud_payment_id,
						'centre_id' => $centre_id,
						'receipt_number' => $invoice_number,
						'receipt_amount' => $stud_payment_amount,
						'receipt_created' => 'now()'
					);

					tep_db_perform(TABLE_RECEIPTS, $receipts_db_values);

					tep_db_perform(TABLE_STUDENT_PAYMENTS, array('stud_payment_receipt_no' => $invoice_number, 'is_auto_generated_reciept' => '1'), "update", "stud_payment_id = '" . $stud_payment_id . "'");
				}

				for($cnt_inst = 1; $cnt_inst<=$no_installment; $cnt_inst++){
					$installment_amount = $_POST['installment_amount'][$cnt_inst];
					$is_receipt_collected = $_POST['is_receipt_collected'][$cnt_inst];
					$installment_date = $_POST['installment_date'][$cnt_inst];
					$installment_date = input_valid_date($installment_date);
					$installment_mop = $_POST['installment_mop'][$cnt_inst];
					$instrument_no = $_POST['instrument_no'][$cnt_inst];
					$bank_name = $_POST['instl_bank'][$cnt_inst];
					$bank_branch = $_POST['instl_branch'][$cnt_inst];
					$receipt_no = $_POST['receipt_no'][$cnt_inst];
					$inst_generate_receipt = $inst_generate_receipt_array[$cnt_inst];

					$arr_db_values = array(
						'student_id' => $student_id,
						'installment_type' => 'COURSE_FEE',
						'installment_no' => $cnt_inst,
						'installment_date' => $installment_date,
						'installment_mop' => $installment_mop,
						'instrument_no' => $instrument_no,
						'bank_name' => $bank_name,
						'bank_branch' => $bank_branch,
						'receipt_no' => $receipt_no,
						'installment_amount' => $installment_amount,
						'receipt_filename' => "",
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
					$stud_payment_id = tep_db_insert_id();

					if($inst_generate_receipt == '1'){
						$invoice_number = latest_invoice_number();
						$receipts_db_values = array(
							'stud_payment_id' => $stud_payment_id,
							'centre_id' => $centre_id,
							'receipt_number' => $invoice_number,
							'receipt_amount' => $installment_amount,
							'receipt_created' => 'now()'
						);

						tep_db_perform(TABLE_RECEIPTS, $receipts_db_values);

						tep_db_perform(TABLE_STUDENT_PAYMENTS, array('stud_payment_receipt_no' => $invoice_number), "update", "stud_payment_id = '" . $stud_payment_id . "'");
					}
				}

				$msg = 'added';
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	$int_id = $_GET['int_id'];

	$info_query_raw = "select student_id, centre_id, course_id, student_full_name from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' ";

	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}

	$info_query = tep_db_query($info_query_raw);

	$info = tep_db_fetch_array($info_query);

	$course_info_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_desc, c.course_code, c.course_duration, c.course_fee, c.course_installments, c.course_instl_duration, c.course_status, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id and course_id='" . $info['course_id'] . "' ";
	$course_info_query = tep_db_query($course_info_query_raw);
	$course_info = tep_db_fetch_array($course_info_query);

	$total_installment = $course_info['course_installments'];

	$action_type = 'edit';

	$installment_query_raw = "select installment_id, student_id, installment_type, installment_no, date_format(installment_date, '%d-%m-%Y') as installment_date, installment_mop, installment_amount, instrument_no, is_receipt_collected, receipt_filename from " . TABLE_INSTALLMENTS . " where student_id='" . $int_id . "' and installment_type = 'COURSE_FEE' order by installment_no";
	$installment_query = tep_db_query($installment_query_raw);
	$installment = array();
	while($installment_temp = tep_db_fetch_array($installment_query)){
		$installment[$installment_temp['installment_no']] = $installment_temp;
	}

	$course_waivers_query_raw = "select waiver_id, course_id, waiver_title, waiver_desc, waiver_amount, waiver_expiry from " . TABLE_COURSES_WAIVERS . " where course_id = '" . $info['course_id'] . "' AND waiver_expiry >= CURRENT_DATE()";
	$course_waivers_query = tep_db_query($course_waivers_query_raw);

	$course_waivers_array = array();

	while($course_waivers_temp = tep_db_fetch_array($course_waivers_query)){
		$course_waivers_array[] = $course_waivers_temp;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Student Payments</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>
		<style type="text/css">
			.blk_manual_receipt{
				display:none;
			}
		</style>

		<script language="javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
					$('#installment_date_<?php echo $cntInstallment;?>').datepicker({
						dateFormat: "dd-mm-yy",
						changeMonth: true,
						changeYear: true,
						onSelect: function(){
							var selected_date = this.value;
							var selected_date_array = new Array();
							selected_date_array = selected_date.split("-");

							if(check_valid_inst_date(selected_date_array[0], selected_date_array[1], selected_date_array[2]) == false){
								$(this).val("");
							}
						}
					});
				<?php } ?>

				$('#stud_payment_deposit_date').datepicker({
					dateFormat: "dd-mm-yy"
				});

				<?php if($info['student_status'] == '1'){ ?>
				/*$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);*/
				<?php } ?>
			});

			var course_validity = '<?php echo $course_info['course_instl_duration']; ?>';
			function check_valid_inst_date(inst_dd, inst_mm, inst_yy){
				if(eval(course_validity) > 0){
					var inst_date = new Date(inst_mm+"/"+inst_dd+"/"+inst_yy);
					var today_date = new Date();

					if(inst_date > today_date){
						var timeDiff = Math.abs(inst_date.getTime() - today_date.getTime());
						var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
						if(diffDays > course_validity){
							alert("Invalid Installment date. Date duration should be " + course_validity + " Days");
							return false;
						}
					}else{
						alert("Please check the installment date.");
						return false;
					}
				}

				return true;
			}

			function calculate_balance(){
				var waiver_amount = $('select[name="course_waiver"] option:selected').attr('amount');
				waiver_amount = (typeof(waiver_amount) != 'undefined' ? waiver_amount : 0);
				var course_fee = '<?php echo $course_info['course_fee']; ?>';

				var stud_payment_amount = $('input[name="stud_payment_amount"]').val();

				var installment_amount = 0;
				var no_installment = $('#no_installment').val();
				for(var inst_cnt = 1; inst_cnt <= no_installment; inst_cnt++){
					var inst_amt = $('input[name="installment_amount[' + inst_cnt + ']"]').val();
					inst_amt = (typeof(inst_amt) != 'undefined' ? inst_amt : 0);
					if(inst_amt > 0){
						installment_amount += eval(inst_amt);
					}
				}

				installment_amount = (typeof(installment_amount) != 'undefined' && isNaN(installment_amount) == false ? installment_amount : 0);

				var balance_amount = course_fee - (eval(stud_payment_amount) + eval(installment_amount) + eval(waiver_amount));

				$('#payable_amount').html(course_fee - eval(waiver_amount));

				$('#balance_amount').html(balance_amount);
				$('#student_balance').val(balance_amount);

				if(balance_amount < 0){
					alert("Please check the payment.");
					//$('input[name="stud_payment_amount"]').focus();
					return false;
				}else{
					return true;
				}
			}

			function change_waiver(){
				var waiver_amount = $('select[name="course_waiver"] option:selected').attr('amount');
				waiver_amount = (waiver_amount != '' && typeof(waiver_amount) != 'undefined' ? waiver_amount : 0);
				var course_fee = '<?php echo $course_info['course_fee']; ?>';

				var net_fee = course_fee - waiver_amount;

				$('input[name="stud_payment_amount"]').val(net_fee);

				calculate_balance();
			}

			function check_inst_amount(){
				calculate_balance();
			}

			function toggle_installment(){
				var no_installment = $('#no_installment').val();
				$('.blk_installment').hide();
				for(var cnt_inst=1;cnt_inst<=no_installment; cnt_inst++){
					$('.inst_'+cnt_inst).show();

					var mop = $('#installment_mop_'+cnt_inst).val();
					if(mop == 'CHEQUE' || mop == 'DD' || mop == 'NEFT_RTGS'){
						$('.instrument_no_'+cnt_inst).show();
					}else{
						$('.instrument_no_'+cnt_inst).hide();
					}
				}
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function toggle_instr_no(){
				var no_installment = $('#no_installment').val();
				for(var cnt_inst=1;cnt_inst<=no_installment; cnt_inst++){
					var mop = $('#installment_mop_'+cnt_inst).val();
					if(mop == 'CHEQUE' || mop == 'DD' || mop == 'NEFT_RTGS'){
						$('.instrument_no_'+cnt_inst).show();
					}else{
						$('.instrument_no_'+cnt_inst).hide();
					}
				}
			}

			function toggle_bank_info(){
				var payment_type = $('#stud_payment_mode').val();
				if(payment_type == 'CHEQUE' || payment_type == 'DD' || payment_type == 'NEFT_RTGS'){
					$('#cheque_fields').show();
				}else{
					$('#cheque_fields').hide();
				}
			}

			function toggle_manual_receipt(){
				if($('input[name="generate_receipt"]').prop('checked')){
					$(".blk_manual_receipt").hide();
				}else{
					$(".blk_manual_receipt").show();
				}
			}

			function toggle_inst_receipt(inst_no){
				if($('input[id="inst_generate_receipt_' + inst_no + '"]').prop('checked')){
					$(".blk_inst_receipt_" + inst_no).hide();
				}else{
					$(".blk_inst_receipt_" + inst_no).show();
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

			function check_payment_instance(){
				var payment_type = $('#stud_payment_mode').val();
				if(payment_type == 'CHEQUE' || payment_type == 'DD' || payment_type == 'NEFT_RTGS'){
					if($('#stud_payment_cheque_no').val() == ""){
						alert("Please enter cheque no");
						$('#stud_payment_cheque_no').focus();
						return false;
					}else if($('#stud_payment_bank_name').val() == ""){
						alert("Please enter bank name");
						$('#stud_payment_bank_name').focus();
						return false;
					}else if($('#stud_payment_bank_branch').val() == ""){
						alert("Please enter branch name");
						$('#stud_payment_bank_branch').focus();
						return false;
					}else{
						return true;
					}
				}else{
					return true;
				}
			}

			function submit_payment(objForm){
				calculate_balance();
				if($('#student_balance').val() != 0){
					alert("Invalid amount. Please check the amount.");
					return false;
				}else if($('#stud_payment_mode').val() == ""){
					alert("Please select payment type");
					return false;
				}else if($('#stud_payment_deposit_date').val() == ""){
					alert("Please enter fees collected date");
					return false;
				}else if(check_payment_instance() == false){
					return false;
				}else{
					return true;
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
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">Student Fee - <?php echo $info['student_full_name']; ?></td>
													<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType')) . '&actionType=preview'); ?>" onsubmit="javascript: return submit_payment(this);" method="post" enctype="multipart/form-data">
															<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
															<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>">
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
																										echo $course_info['course_name'] . ' - ' . $course_info['section_name'] . ' ( ' . $course_info['course_code'] . ' ) ';		
																									?>
																								</td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Course Fee&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<?php echo display_currency($course_info['course_fee']);?>
																									<input type="hidden" name="student_course_fee" id="student_course_fee" value="<?php echo $course_info['course_fee'];?>">
																								</td>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Payable Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<span id="payable_amount"><?php echo display_currency($course_info['course_fee']);?></span>
																								</td>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Outstanding&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<span id="balance_amount"><?php echo display_currency($course_info['course_fee']);?></span>
																									<input type="hidden" name="student_balance" id="student_balance" value="0">
																								</td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Waivers&nbsp;:</td>
																								<td width="10%">
																									<select name="course_waiver" id="course_waiver" onchange="javascript: change_waiver();">
																										<option value="">Please choose</option>
																										<?php foreach($course_waivers_array as $waiver_info){?>
																										<option value="<?php echo $waiver_info['waiver_id'];?>" <?php echo($info['course_waiver'] == $waiver_info['waiver_id'] ? 'selected="selected"' : '');?> amount="<?php echo $waiver_info['waiver_amount'];?>"><?php echo $waiver_info['waiver_title'] . ' ( ' . display_currency($waiver_info['waiver_amount']) . ' ) ';?></option>
																										<?php } ?>
																									</select>
																								</td>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Waiver Reason&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<input type="text" name="course_waiver_reason" id="course_waiver_reason" maxlength="255" value="">
																								</td>
																							</tr>
																							<tr>
																								<td colspan="6" style="border:none;"><strong>Down Payment Details</strong></td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Fees amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<input type="text" name="stud_payment_amount" id="stud_payment_amount" maxlength="10" value="<?php echo  ($dupError ? $_POST['stud_payment_amount'] : $course_info['course_fee']) ?>" onblur="javascript:calculate_balance();" class="required">
																								</td>
																								<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Payment Type&nbsp;:</td>
																								<td width="10%">
																									<select name="stud_payment_mode" id="stud_payment_mode" onchange="javascript: toggle_bank_info();">
																										<option value="">Please choose</option>
																										<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																										<option value="<?php echo $k_payment_type;?>" <?php echo($info['stud_payment_mode'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																										<?php } ?>
																									</select>
																								</td>
																								<td class="arial12LGrayBold" width="15%" colspan="3">
																									<input type="checkbox" onclick="toggle_manual_receipt();" name="generate_receipt" id="generate_receipt" value="1" <?php echo (($dupError && $_POST['stud_payment_deposit_date'] == '1') || !isset($_POST['stud_payment_deposit_date']) ? 'checked="checked"' : '') ?>>&nbsp;<label for="generate_receipt">Generate Receipt</label>
																								</td>
																								<td class="arial12LGrayBold blk_manual_receipt" width="12%" align="right">&nbsp;Receipt Number&nbsp;:</td>
																								<td class="arial12LGray blk_manual_receipt" width="15%" colspan="3">
																									<input type="text" name="stud_payment_receipt_no" id="stud_payment_receipt_no" value="<?php echo  ($dupError ? $_POST['stud_payment_deposit_date'] : '') ?>">
																								</td>
																							</tr>
																							<tr id="cheque_fields" style="display:none;">
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Cheque No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<input type="text" name="stud_payment_cheque_no" id="stud_payment_cheque_no" maxlength="50" value="" class="required">
																								</td>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Bank Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<input type="text" name="stud_payment_bank_name" id="stud_payment_bank_name" maxlength="50" value="" class="required">
																								</td>
																								<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Bank Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold" width="15%">
																									<input type="text" name="stud_payment_bank_branch" id="stud_payment_bank_branch" maxlength="50" value="" class="required">
																								</td>
																							</tr>
																							<tr>
																								<td colspan="6" style="border:none;"><strong>Installment Details</strong></td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" align="right">&nbsp;Installment:</td>
																								<td class="arial12LGray" colspan="5">
																									<select name="no_installment" id="no_installment" style="width:80px;" onchange="javascript: toggle_installment();">
																										<option value="">Choose</option>
																										<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
																										<option value="<?php echo $cntInstallment;?>"><?php echo $cntInstallment;?></option>
																										<?php } ?>
																									</select>
																								</td>
																							</tr>
																							<tr>
																								<td colspan="8">
																									<table cellpadding="5" cellspacing="5" border="0" width="100%" class="arial12LGray">
																									<?php for($cntInstallment=1; $cntInstallment<=$total_installment; $cntInstallment++){?>
																									<tr class="inst_<?php echo $cntInstallment;?> blk_installment">
																										<td colspan="6" valign="middle"><b>Installment <?php echo $cntInstallment;?></b></td>
																									</tr>
																									<tr class="inst_<?php echo $cntInstallment;?> blk_installment">
																										<td>Installment Date :</td>
																										<td><input type="text" name="installment_date[<?php echo $cntInstallment;?>]" id="installment_date_<?php echo $cntInstallment;?>" value="<?php echo $installment[$cntInstallment]['installment_date'];?>" class="required" style="width: 100px;"></td>
																										<td>&nbsp;Mode of Payment :</td>
																										<td>
																											<select name="installment_mop[<?php echo $cntInstallment;?>]" id="installment_mop_<?php echo $cntInstallment;?>" class="required" onchange="javascript: toggle_instr_no();">
																												<option value="">Please choose</option>
																												<option value="UNKNOWN">Unknown</option>
																												<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																												<option value="<?php echo $k_payment_type;?>" <?php echo($installment[$cntInstallment]['installment_mop'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																												<?php } ?>
																											</select>
																										</td>
																									</tr>
																									<tr class="instrument_no_<?php echo $cntInstallment;?> inst_<?php echo $cntInstallment;?> blk_installment">
																										<td>&nbsp;Instrument / Cheque No :</td>
																										<td>
																											<input type="text" name="instrument_no[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['instrument_no'];?>">
																										</td>
																										<td>&nbsp;Bank Name :</td>
																										<td>
																											<input type="text" name="instl_bank[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['bank_name'];?>">
																										</td>
																										<td>&nbsp;Branch :</td>
																										<td>
																											<input type="text" name="instl_branch[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['bank_branch'];?>">
																										</td>
																									</tr>
																									<tr class="inst_<?php echo $cntInstallment;?> blk_installment">
																										<td width="12%">Installment Amount :</td>
																										<td width="10%"><input type="text" name="installment_amount[<?php echo $cntInstallment;?>]" value="<?php echo $installment[$cntInstallment]['installment_amount'];?>" onblur="javascript: check_inst_amount();" style="width: 100px;"></td>
																										<td width="12%">Receipt Issued :</td>
																										<td width="10%">
																											<?php foreach($arr_status as $k_status=>$v_status){?>
																												<input type="radio" name="is_receipt_collected[<?php echo $cntInstallment;?>]" id="is_receipt_collected_<?php echo $cntInstallment;?>" value="<?php echo $k_status;?>" class="required" <?php echo ($installment[$cntInstallment]['is_receipt_collected'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: toggle_element('is_receipt_collected_<?php echo $cntInstallment;?>', 'receipt_<?php echo $cntInstallment;?>');">&nbsp;<?php echo $v_status;?>&nbsp;
																											<?php } ?>
																										</td>
																										<td><input type="checkbox" class="receipt_<?php echo $cntInstallment;?>" onclick="toggle_inst_receipt('<?php echo $cntInstallment;?>');" name="inst_generate_receipt[<?php echo $cntInstallment;?>]" id="inst_generate_receipt_<?php echo $cntInstallment;?>" value="1">&nbsp;<label for="inst_generate_receipt_<?php echo $cntInstallment;?>" class="receipt_<?php echo $cntInstallment;?>">Generate Receipt</label></td>
																										<td width="8%"><span class="receipt_<?php echo $cntInstallment;?> blk_inst_receipt_<?php echo $cntInstallment;?>">Receipt :</span></td>
																										<td>
																											<span class="blk_inst_receipt_<?php echo $cntInstallment;?> receipt_<?php echo $cntInstallment;?>">
																											<input type="text" name="receipt_no[<?php echo $cntInstallment;?>]" value="" placeholder="Receipt No."></td>
																											</span>
																										<td>
																										<?php 
																											$file_tag_display = '';
																											if($installment[$cntInstallment]['receipt_filename'] != '' && file_exists(DIR_FS_UPLOAD . $installment[$cntInstallment]['receipt_filename'])){ 
																												$file_tag_display = 'none;';
																										?>
																										<span><a href="<?php echo DIR_WS_UPLOAD . $installment[$cntInstallment]['receipt_filename'];?>" target="_blank"><?php echo $installment[$cntInstallment]['receipt_filename'];?></a></span>&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;<a href="javascript: void(0);" onclick="javascript: change_receipt('<?php echo $cntInstallment;?>')" id="txt_change_<?php echo $cntInstallment;?>">Change</a>&nbsp;]&nbsp;&nbsp;&nbsp;
																										<?php } ?>
																										<span class="blk_inst_receipt_<?php echo $cntInstallment;?> receipt_<?php echo $cntInstallment;?>"><input type="file" name="receipt_filename[<?php echo $cntInstallment;?>]" value="" class="required" id="receipt_file_<?php echo $cntInstallment;?>" style="display: <?php echo $file_tag_display;?>">&nbsp;</span>
																										</td>
																									</tr>
																									<script type="text/javascript">
																									<!--
																										toggle_element('is_receipt_collected_<?php echo $cntInstallment;?>', 'receipt_<?php echo $cntInstallment;?>');

																										toggle_instr_no();
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
																						</table>
																					</fieldset>
																					<script type="text/javascript">
																					<!--
																						toggle_element('is_fees_deposit', 'fees_deposit');
																						toggle_element('is_due_balance', 'due_balance');
																					//-->
																					</script>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
															<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																<tr>
																	<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton">
																	</td>
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