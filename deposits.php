<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array('added'=>'Deposit has been added successfully');

	$action = $_POST['action_type'];

	if(isset($action) && tep_not_null($action)){
		switch($action){
			case 'add_deposit_detail':
				$total_amount = tep_db_prepare_input($_POST['hid_total_amount']);
				$deposit_slip_no = tep_db_prepare_input($_POST['hid_deposit_slip_no']);
				$deposit_date = tep_db_prepare_input($_POST['deposit_date']);
				$deposit_date = date("Y-m-d", strtotime($deposit_date));
				$deposit_remark = tep_db_prepare_input($_POST['deposit_remark']);
				$deposit_file = tep_db_prepare_input($_POST['deposit_file ']);
				$bank_id = tep_db_prepare_input($_POST['bank_account']);
				$deposit_type = tep_db_prepare_input($_POST['deposit_type']);
				
				$stud_payment_array = $_POST['stud_payment_id'];
				$stud_amount_array = $_POST['stud_amount'];

				$total_amount = 0;
				foreach($stud_amount_array as $stud_amount){
					$total_amount += $stud_amount;
				}

				$bc_info_query_raw = " select bank_account_id, centre_id, bank_name, branch_name, bank_account_no, bank_address, bank_ifsc_code from " . TABLE_BANK_ACCOUNTS . " where bank_account_id = '" . $bank_id . "' ";
				$bc_info_query = tep_db_query($bc_info_query_raw);
				$bc_info = tep_db_fetch_array($bc_info_query);

				$db_deposit_array = array(
					'user_id' => $_SESSION['sess_admin_id'],
					'centre_id' => $_SESSION['sess_centre_id'],
					'bank_id' => $bank_id,
					'deposit_slip_no' => $deposit_slip_no,
					'deposit_date' => $deposit_date,
					'deposit_type' => $deposit_type,
					'bank_name' => $bc_info['bank_name'],
					'bank_branch' => $bc_info['branch_name'],
					'bank_account' => $bc_info['bank_account_no'],
					'deposit_amount' => $total_amount,
					'deposit_remark' => $deposit_remark,
					'deposit_file' => $deposit_file,
					'deposit_added' => 'now()'
				);

				if($_FILES['deposit_slip_file']['name'] != ''){
					
					
					$ext = get_extension($_FILES['deposit_slip_file']['name']);

					$src = $_FILES['deposit_slip_file']['tmp_name'];

					$dest_filename = 'deposit_slip_' . time() . date("His") . $ext;
					
					$dest = DIR_FS_UPLOAD . $dest_filename;
					if(file_exists($dest))
					{
						@unlink($dest);
					}
					if(move_uploaded_file($src, $dest))	
					{
						$db_deposit_array['deposit_file'] = $dest_filename;
					}

				}

				tep_db_perform(TABLE_DEPOSITS, $db_deposit_array);

				$deposit_id = tep_db_insert_id();

				if(is_array($stud_payment_array) && count($stud_payment_array)){
					foreach($stud_payment_array as $stud_payment_id){
						$db_stud_payment_array = array(
							'deposit_id' => $deposit_id,
							'stud_payment_status' => 'DEPOSITED',
							'stud_payment_deposit_date' => $deposit_date
						);

						tep_db_perform(TABLE_STUDENT_PAYMENTS, $db_stud_payment_array, 'update', "stud_payment_id = '" . $stud_payment_id . "'");
					}
				}

				$msg = 'added';

				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));

			break;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Deposits</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#date_to').datepicker({
					dateFormat: "dd-mm-yy"
				});

				$('#date_from').datepicker({
					dateFormat: "dd-mm-yy"
				});

				$('#deposit_date').datepicker({
					dateFormat: "dd-mm-yy"
				});

				$('input[name="chkSelectAll"]').on('click', function(){
					var select_all = $(this).prop('checked');
					$('input[name^="stud_payment_id["]').each(function(){
						if(select_all == true){
							$(this).attr('checked', true);
						}else{
							$(this).attr('checked', false);
						}

						calculate_amount();
					});
				});
			});


			function calculate_amount(){
				var total_amount = 0;
				$('input[name^="stud_payment_id["]').each(function(){
					if($(this).prop('checked') == true){
						total_amount += eval($(this).attr('amount'));
					}
				});

				$('#total_amount').html(total_amount);
				$('#hid_total_amount').val(total_amount);
			}

			function check_details(){
				if($('#hid_total_amount').val() <= 0){
					alert("Please check amount");
					return false;
				}else{
					if(confirm("Are you sure want to deposit selected amount?")){
						return true;
					}else{
						return false;
					}
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
													<td class="arial18BlueN">Deposits</td>
												</tr>
												<tr>
													<td colspan="2">
														<?php
															if(isset($action) && $action == 'get_non_deposits_payments'){
																$new_deposit_no = generate_deposit_no();
														?>
														<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="post" enctype="multipart/form-data" onsubmit="javascript: return check_details();">
														<input type="hidden" name="action_type" id="action_type" value="add_deposit_detail">
														<input type="hidden" name="deposit_type" id="deposit_type" value="<?php echo $payment_mode;?>">
														<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
															<tr>
																<td align="left">
																	<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_filter" class="display">
																		<thead>
																			<tr>
																				<th><input type="checkbox" name="chkSelectAll" id="chkSelectAll" value="1"></th>
																				<th>Student Name</th>
																				<th>Parent Name</th>
																				<th>Cheque/Inst. No</th>
																				<th>Bank</th>
																				<th>Branch</th>
																				<th>Payment Date</th>
																				<th>Amount</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																				$payment_mode = $_POST['payment_mode'];
																				$date_to = date("Y-m-d", strtotime($_POST['date_to']));
																				$date_from = date("Y-m-d", strtotime($_POST['date_from']));

																				$stud_payments_query_raw = "select sp.stud_payment_id, sp.stud_payment_type, sp.stud_payment_mode, sp.stud_payment_cheque_no, sp.stud_payment_bank_name, sp.stud_payment_bank_branch, sp.stud_payment_deposit_date, sp.stud_payment_amount, sp.stud_payment_status, sp.stud_payment_added, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.father_middle_name, s.father_surname from " . TABLE_STUDENT_PAYMENTS . " sp JOIN " . TABLE_STUDENTS . " s ON (sp.student_id = s.student_id) where ( sp.stud_payment_deposit_date between '" . $date_to . "' AND '" . $date_from . "') AND sp.stud_payment_mode = '" . $payment_mode . "' AND sp.stud_payment_status = 'NOT_DEPOSITED' order by sp.stud_payment_deposit_date";
																				$stud_payments_query = tep_db_query($stud_payments_query_raw);

																				if(tep_db_num_rows($stud_payments_query)){
																					while($stud_payments_array = tep_db_fetch_array($stud_payments_query)){
																			?>
																			<tr>
																				<td><input type="checkbox" name="stud_payment_id[<?php echo $stud_payments_array['stud_payment_id'];?>]" value="<?php echo $stud_payments_array['stud_payment_id'];?>" amount="<?php echo $stud_payments_array['stud_payment_amount'];?>" onclick="javascript: calculate_amount();"></td>
																				<td>
																					<?php echo $stud_payments_array['student_full_name'] . ' ' . $stud_payments_array['student_middle_name'] . ' ' . $stud_payments_array['student_surname'];?>
																				</td>
																				<td>
																					<?php echo $stud_payments_array['student_father_name'] . ' ' . $stud_payments_array['father_middle_name'] . ' ' . $stud_payments_array['father_surname'];?>
																				</td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_cheque_no']) && $stud_payments_array['stud_payment_cheque_no'] != '' ? $stud_payments_array['stud_payment_cheque_no'] : '-');?></td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_bank_name']) && $stud_payments_array['stud_payment_bank_name'] != '' ? $stud_payments_array['stud_payment_bank_name'] : '-');?></td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_bank_branch']) && $stud_payments_array['stud_payment_bank_branch'] != '' ? $stud_payments_array['stud_payment_bank_branch'] : '-');?></td>
																				<td><?php echo date("d-m-Y", strtotime($stud_payments_array['stud_payment_deposit_date']));?></td>
																				<td>
																					<?php echo display_currency($stud_payments_array['stud_payment_amount']);?>
																					<input type="hidden" name="stud_amount[<?php echo $stud_payments_array['stud_payment_id'];?>]" value="<?php echo $stud_payments_array['stud_payment_amount'];?>" />
																				</td>
																			</tr>
																			<?php
																					}
																			?>
																			<script type="text/javascript" charset="utf-8">
																				$(document).ready(function() {
																					$('#table_filter').dataTable({
																						"aoColumns": [
																							{ "bSortable": false}, //Checkbox
																							null, //Student Name
																							null, //Parent Name
																							null, //Cheque
																							null, //Bank
																							null, // Branch
																							null, // Payment Date
																							null, // Amount
																						],
																						"bFilter": false,
																						"paging": false,
																						"bPaginate": false,
																						 "oLanguage": {
																							 "sInfo": "",
																							 "sInfoEmpty": ""
																						  }
																					});
																				});
																			</script>
																			<?php
																				}else{
																			?>
																			<tr>
																				<td colspan="7" align="center">No Payment Found.</td>
																			</tr>
																			<?php
																				}
																			?>
																		</tbody>
																	</table>
																	<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																		<tr>
																			<td class="arial12LGrayBold" align="right">&nbsp;Total Amount&nbsp;:</td>
																			<td class="arial12LGray">
																				&nbsp;<span id="total_amount">0</span>
																				<input type="hidden" name="hid_total_amount" id="hid_total_amount" value="0"/>
																			</td>
																		</tr>
																		<tr>
																			<td class="arial12LGrayBold" align="right">&nbsp;Deposit Slip No&nbsp;:</td>
																			<td class="arial12LGray">
																				&nbsp;<input type="text" name="deposit_slip_no" id="deposit_slip_no" value="<?php echo $new_deposit_no;?>" disabled="disabled">
																				<input type="hidden" name="hid_deposit_slip_no" id="hid_deposit_slip_no" value="<?php echo $new_deposit_no;?>"/>
																			</td>
																		</tr>
																		<tr>
																			<td class="arial12LGrayBold" align="right">&nbsp;Deposit Date&nbsp;:</td>
																			<td class="arial12LGray">
																				<!-- <select name="deposit_date" id="deposit_date" class="required">
																					<?php
																						//for($str_date = strtotime("-2 days"); $str_date <= time(); $str_date=$str_date+(24*60*60)){
																					?>
																					<option value="<?php //echo date("Y-m-d", $str_date);?>"><?php //echo date("d M Y", $str_date);?></option>
																					<?php //} ?>
																				</select> -->
																				<input type="text" name="deposit_date" id="deposit_date" class="required" value="<?php echo date("d-m-Y") ?>">
																			</td>
																		</tr>
																		<tr>
																			<td class="arial12LGrayBold" align="right">&nbsp;Bank Account&nbsp;:</td>
																			<td class="arial12LGray">
																				<select name="bank_account" id="bank_account" class="required">
																					<?php
																						$bank_accounts_query_raw = "select ba.bank_account_id, ba.bank_name, ba.branch_name, ba.bank_account_no from " . TABLE_BANK_ACCOUNTS . " ba where ba.centre_id = '" . $_SESSION['sess_centre_id'] . "' order by ba.bank_name";
																						$bank_accounts_query = tep_db_query($bank_accounts_query_raw);
																						while($bank_accounts_array = tep_db_fetch_array($bank_accounts_query)){
																					?>
																					<option value="<?php echo $bank_accounts_array['bank_account_id'];?>"><?php echo $bank_accounts_array['bank_name'] . ' - ' . $bank_accounts_array['branch_name'] . ' (' . $bank_accounts_array['bank_account_no'] . ')';?></option>
																					<?php } ?>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td class="arial12LGrayBold" align="right" valign="top">&nbsp;Remark&nbsp;:</td>
																			<td class="arial12LGray">
																				<textarea name="deposit_remark" id="deposit_remark" cols="30" rows="10"></textarea>
																			</td>
																		</tr>
																		<tr>
																			<td class="arial12LGrayBold" align="right" valign="top">&nbsp;Deposit&nbsp;:</td>
																			<td class="arial12LGray">
																				<input type="file" name="deposit_slip_file" id="deposit_slip_file" class="required">
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
																</td>
															</tr>
														</table>
														</form>
														<?php 
															}else{
														?>
														<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="post" enctype="multipart/form-data">
															<input type="hidden" name="action_type" id="action_type" value="get_non_deposits_payments">
															<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																<tr>
																	<td>
																		<table cellpadding="0" cellspacing="0" border="0" width="100%">
																			<tr>
																				<td class="arial12LGrayBold" align="right">&nbsp;From&nbsp;:</td>
																				<td class="arial12LGray">
																					&nbsp;<input type="text" name="date_to" id="date_to" class="required" value="<?php echo date("d-m-Y") ?>">
																				</td>
																				<td class="arial12LGrayBold" align="right">&nbsp;To&nbsp;:</td>
																				<td class="arial12LGray">
																					&nbsp;<input type="text" name="date_from" id="date_from" class="required" value="<?php echo date("d-m-Y") ?>">
																				</td>
																				<td class="arial12LGrayBold" align="right">&nbsp;Payment Mode&nbsp;:</td>
																				<td class="arial14LGrayBold">
																					&nbsp;<select name="payment_mode" id="payment_mode" class="required">
																						<option value="">Please choose</option>
																						<?php foreach($arr_deposit_payment_type as $k_payment_type=>$v_payment_type){?>
																						<option value="<?php echo $k_payment_type;?>"><?php echo $v_payment_type;?></option>
																						<?php } ?>
																					</select>
																				</td>
																				<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton">
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</form>
														<?php } ?>
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