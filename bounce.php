<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array('added'=>'Bounce cheque has been marked successfully');

	$action = $_POST['action_type'];

	if(isset($action) && tep_not_null($action)){
		switch($action){
			case 'do_bounce':
				$stud_payment_id = tep_db_prepare_input(tep_db_input($_POST['sp_id']));

				$stud_payment_db_values = array(
					'stud_payment_status' => 'BOUNCE',
					'stud_payment_modified' => 'now()'
				);

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

				$msg = 'added';
				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
			break;
			case 'do_clear':
				$stud_payment_id = tep_db_prepare_input(tep_db_input($_POST['sp_id']));
				$cheque_cleared_date = tep_db_prepare_input(tep_db_input($_POST['clearance_date']));
				$cheque_cleared_date = date("Y-m-d", strtotime($cheque_cleared_date));
			

				$stud_payment_db_values = array(
					'cheque_cleared' => '1',
					'cheque_cleared_date' => $cheque_cleared_date,
					'stud_payment_modified' => 'now()'
				);

				tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

				$msg = 'added';
				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
			break;
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Bounce</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery.form.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('.datepicker').datepicker({
					dateFormat: "dd-mm-yy"
				});
			});

			function create_bounce(sp_id){
				if(confirm("Are you sure want to mark as bounce?")){
					$('#frmBounce_' + sp_id).ajaxSubmit({
						url: 'get_data.php',
						data: {'action':'do_bounce_cheque','sp_id':sp_id},
						type: 'POST',
						success: function(response){
							$('.blk_bounce_extra_'+sp_id).hide();
							$('.blk_clearance_extra_'+sp_id).hide();
							$('#link_marks_'+sp_id).html(response);
						}
					});
				}
			}

			function hide_bounce_form(sp_id){
				$('.blk_bounce_extra_'+sp_id).hide();
				$('.blk_clearance_extra_'+sp_id).hide();
			}

			function toggle_bounce(sp_id){
				$('.blk_bounce_extra_'+sp_id).show();
				$('.blk_clearance_extra_'+sp_id).hide();
			}

			function create_clear(sp_id){
				if(confirm("Are you sure want to mark as cleared?")){
					$('#frmBounce_' + sp_id).ajaxSubmit({
						url: 'get_data.php',
						data: {'action':'do_clear_cheque','sp_id':sp_id},
						type: 'POST',
						success: function(response){
							$('.blk_bounce_extra_'+sp_id).hide();
							$('.blk_clearance_extra_'+sp_id).hide();
							$('#link_marks_'+sp_id).html(response);
						}
					});
				}
			}

			function toggle_clear(sp_id){
				$('.blk_clearance_extra_'+sp_id).show();
				$('.blk_bounce_extra_'+sp_id).hide();
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
													<td class="arial18BlueN">Reconciliation <small class="verdana11GrayB"> - Mark cheque as bounce or clear</small>	</td>
												</tr>
												<tr>
													<td colspan="2">
														<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
															<tr>
																<td>
																	<?php
																		$keywords = tep_db_input(tep_db_prepare_input($_GET['keywords']));
																	?>
																	<form name="frmBounceSearch" id="frmBounceSearch" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="get" enctype="multipart/form-data">
																	<input type="hidden" name="action_type" id="action_type" value="get_bounce">
																	<table cellpadding="0" cellspacing="0" border="0" width="100%">
																		<tr>
																			<td class="arial12LGrayBold" width="5%" align="right">&nbsp;Keywords&nbsp;:</td>
																			<td class="arial12LGray" width="20%">
																				&nbsp;<input type="text" name="keywords" id="keywords" class="required" value="<?php echo $keywords;?>">
																			</td>
																			<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton"></td>
																		</tr>
																	</table>
																	</form>
																	<br/><br/>
																	<table cellpadding="0" cellspacing="0" border="0" width="100%" id="table_filter" class="display">
																		<thead>
																			<tr>
																				<th>&nbsp;</th>
																				<th>Student Name</th>
																				<th>Parent Name</th>
																				<th>Centre</th>
																				<th>Cheque/Inst. No</th>
																				<th>Bank</th>
																				<th>Branch</th>
																				<th>Payment Mode</th>
																				<th>Payment Date</th>
																				<th>Amount</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																				if((isset($keywords) && tep_not_null($keywords))){

																				$stud_payments_query_raw = "select sp.stud_payment_id, sp.stud_payment_type, sp.stud_payment_mode, sp.stud_payment_cheque_no, sp.stud_payment_bank_name, sp.stud_payment_bank_branch, sp.stud_payment_deposit_date, sp.stud_payment_amount, sp.stud_payment_status, sp.stud_payment_added, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.father_middle_name, s.father_surname, cn.centre_name from " . TABLE_STUDENT_PAYMENTS . " sp JOIN " . TABLE_STUDENTS . " s ON (sp.student_id = s.student_id) JOIN ". TABLE_CENTRES ." cn ON (s.centre_id = cn.centre_id) where sp.stud_payment_status = 'DEPOSITED' AND ( cheque_cleared IS NULL OR cheque_cleared != '1')";

																				if($keywords != ''){
																					$stud_payments_query_raw .= " AND ( sp.stud_payment_cheque_no LIKE '%" . $keywords . "%' OR sp.stud_payment_bank_name LIKE '%" . $keywords . "%' OR sp.stud_payment_bank_branch LIKE '%" . $keywords . "%' OR s.student_full_name LIKE '%" . $keywords . "%' OR s.student_middle_name LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' OR s.student_surname LIKE '%" . $keywords . "%' )";
																				}

																				$stud_payments_query_raw .= " order by sp.stud_payment_deposit_date";
																				$stud_payments_query = tep_db_query($stud_payments_query_raw);

																				if(tep_db_num_rows($stud_payments_query)){
																					while($stud_payments_array = tep_db_fetch_array($stud_payments_query)){
																						$payment_id = $stud_payments_array['stud_payment_id'];
																			?>
																			<tr>
																				<td>
																					<span id="link_marks_<?php echo $payment_id;?>">
																						<?php
																							if(in_array($stud_payments_array['stud_payment_mode'], array('CHEQUE', 'DD', 'NEFT_RTGS'))){
																						?>
																						<a href="javascript:void(0);" onclick="javascript: toggle_bounce('<?php echo $payment_id;?>');">Mark As Bounce</a>&nbsp;|&nbsp;
																						<?php } ?>
																						<a href="javascript:void(0);" onclick="javascript: toggle_clear('<?php echo $payment_id;?>');">Mark As Clear</a>
																					</span>
																					<form name="frmBounce_<?php echo $payment_id;?>" id="frmBounce_<?php echo $payment_id;?>" action="" method="post">
																					<table cellpadding="0" cellspacing="0" border="0" width="100%">
																						<tr class="blk_bounce_extra_<?php echo $payment_id;?>" style="display:none;">
																							<td><input type="text" name="bounce_reason" placeholder="Reason for Bounce" value=""></td>
																							<td><input type="text" name="bounce_date" value="" placeholder="Date of Bounce" class="datepicker"></td>
																						</tr>
																						<tr class="blk_bounce_extra_<?php echo $payment_id;?>" style="display:none;">
																							<td colspan="2">
																								<input type="button" value="Save" onclick="javascript: create_bounce('<?php echo $payment_id;?>');">
																								<input type="button" value="Cancel" onclick="javascript: hide_bounce_form('<?php echo $payment_id;?>');">
																							</td>
																						</tr>
																						<tr class="blk_clearance_extra_<?php echo $payment_id;?>" style="display:none;">
																							<td colspan="2"><input type="text" name="clearance_date" placeholder="Date of Clearance" value="" class="datepicker"></td>
																						</tr>
																						<tr class="blk_clearance_extra_<?php echo $payment_id;?>" style="display:none;">
																							<td colspan="2">
																								<input type="button" value="Save" onclick="javascript: create_clear('<?php echo $payment_id;?>');">
																								<input type="button" value="Cancel" onclick="javascript: hide_bounce_form('<?php echo $payment_id;?>');">
																							</td>
																						</tr>
																					</table>
																					</form>
																				</td>
																				<td>
																					<?php echo $stud_payments_array['student_full_name'] . ' ' . $stud_payments_array['student_middle_name'] . ' ' . $stud_payments_array['student_surname'];?>
																				</td>
																				<td>
																					<?php echo $stud_payments_array['student_father_name'] . ' ' . $stud_payments_array['father_middle_name'] . ' ' . $stud_payments_array['father_surname'];?>
																				</td>
																				<td>
																					<?php echo $stud_payments_array['centre_name'];?>
																				</td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_cheque_no']) && $stud_payments_array['stud_payment_cheque_no'] != '' ? $stud_payments_array['stud_payment_cheque_no'] : '-');?></td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_bank_name']) && $stud_payments_array['stud_payment_bank_name'] != '' ? $stud_payments_array['stud_payment_bank_name'] : '-');?></td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_bank_branch']) && $stud_payments_array['stud_payment_bank_branch'] != '' ? $stud_payments_array['stud_payment_bank_branch'] : '-');?></td>
																				<td><?php echo (isset($stud_payments_array['stud_payment_mode']) && $stud_payments_array['stud_payment_mode'] != '' ? $stud_payments_array['stud_payment_mode'] : '-');?></td>
																				<td><?php echo date("d-m-Y", strtotime($stud_payments_array['stud_payment_deposit_date']));?></td>
																				<td><?php echo display_currency($stud_payments_array['stud_payment_amount']);?></td>
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
																							null, //Centre
																							null, //Cheque
																							null, //Bank
																							null, // Branch
																							null, // Payment Mode
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
																				<td colspan="9" align="center">No Payment Found.</td>
																			</tr>
																			<?php
																				}
																			?>
																		</tbody>
																	</table>
																	<?php } ?>
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