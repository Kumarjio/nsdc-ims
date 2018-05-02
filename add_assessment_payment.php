<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$action = $_POST['action_type'];

	$arrMessage = array('refund_added'=>'Refund has been added successfully', 'updated' => 'Payment has been updated succssfully.', 'settled' => 'Settlement has been made succssfully.');
	
	if(isset($action) && tep_not_null($action))
	{
		$bank_name = tep_db_prepare_input($_POST['bank_name']);
		$cheque_no = tep_db_prepare_input($_POST['cheque_no']);
		$cheque_date = tep_db_prepare_input($_POST['cheque_date']);
		$cheque_date = date("Y-m-d", strtotime($cheque_date));
		$amount = tep_db_prepare_input($_POST['amount']);
		$invoice_date = tep_db_prepare_input($_POST['invoice_date']);
		$invoice_date = date("Y-m-d", strtotime($invoice_date));
		$invoice_no = tep_db_prepare_input($_POST['invoice_no']);
		$students_array = tep_db_prepare_input($_POST['students']);
		$assessment_students = implode(",", $students_array);
		$assessment_body = tep_db_prepare_input($_POST['assessment_body']);
				
		switch($action){
			case 'add_payment':
				$arr_db_values = array(
					'cheque_no' => $cheque_no,
					'cheque_date' => $cheque_date,
					'amount' => $amount,
					'invoice_date' => $invoice_date,
					'bank_name' => $bank_name,
					'invoice_no' => $invoice_no,
					'assessment_body' => $assessment_body,
					'assessment_students' => $assessment_students,
					'created_on' => 'now()',
				);

				tep_db_perform(TABLE_ASSESSMENTS, $arr_db_values);
			break;
		}
		
		tep_redirect(tep_href_link(FILENAME_ASSESMENT_PAYMENTS));
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Add Assessement Payment</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmAssessment").validate();
				$('#cheque_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#invoice_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				})
			});
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
													<td class="arial18BlueN">Add New Assessement - <?php echo $info['student_full_name']; ?></td>
													<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmAssessment" id="frmAssessment" action="" method="post" enctype="multipart/form-data">
														<input type="hidden" name="action_type" id="action_type" value="add_payment">
														<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
															<tr>
																<td colspan="3">
																	<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																		<thead>
																			<tr>
																				<th>Student Name</th>
																				<th>Center</th>
																				<th>Course</th>
																				<th>Batch</th>
																			</tr>
																		</thead>
																		<tbody>
																		<?php
																			$student_array = tep_db_prepare_input($_POST['student']);
																			if(is_array($student_array) && count($student_array)){
																				$student_ids  = implode(",", $student_array);
																				$listing_query_raw = "select s.student_id, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.student_mobile, s.test_result, s.is_training_completed, s.student_status, s.is_deactivated, c.course_name, b.batch_title, cn.centre_name from " . TABLE_STUDENTS . " s, " . TABLE_COURSES . " c, " . TABLE_BATCHES . " b, " . TABLE_CENTRES . " cn where cn.centre_id = s.centre_id and b.batch_id = s.batch_id and c.course_id = s.course_id and student_id IN (". $student_ids .") ";
																				$listing_query = tep_db_query($listing_query_raw);

																				if(tep_db_num_rows($listing_query)){
																					while($listing = tep_db_fetch_array($listing_query) ){ ?>
																			<tr>
																				<td valign="top">
																					<input type="hidden" name="students[]" value="<?php echo $listing["student_id"];?>">
																					<?php echo $listing['student_full_name'] . ' ' . $listing['student_middle_name'] . ' ' . $listing['student_surname']; ?>
																				</td>
																				<td valign="top"><?php echo $listing['centre_name']; ?></td>
																				<td valign="top"><?php echo $listing['course_name']; ?></td>
																				<td valign="top"><?php echo $listing['batch_title']; ?></td>
																		
																			</tr>
																		<?php
																					}
																				}
																			}else{
																		?>
																			<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Student Found !!</td>
																			</tr>
																		<?php } ?>
																		</tbody>
																	</table>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Student Name
																					null, // Centre Name
																					null, // Course
																					null, // Batch
																				],
																				"bPaginate": false,
																				"bFilter": false,
																				"bInfo": false,
																				"iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bstudent_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																</td>
															</tr>
															<tr>
																<td>
																	<table cellpadding="0" cellspacing="0" border="0" width="100%">
																		<tr>
																			<td class="arial14LGrayBold" colspan="2">
																				<fieldset>
																					<legend>Add New Assessement</legend>
																					<table cellpadding="5" cellspacing="0" border="0" width="100%">
																						<tr>
																							<td class="arial12LGrayBold" width="20%" align="right">&nbsp;Bank Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="bank_name" id="bank_name" class="required" value="">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" width="20%" align="right">&nbsp;Cheque No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="cheque_no" id="cheque_no" class="required" value="">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" width="20%" align="right">&nbsp;Cheque Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="cheque_date" id="cheque_date" value="" class="required">
																							</td>
																						</tr>
																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="amount" id="amount" value="" class="required">
																							</td>
																						</tr>

																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Invoice No&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="invoice_no" id="invoice_no" value="" class="required">
																							</td>
																						</tr>

																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Invoice Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="invoice_date" id="invoice_date" value="" class="required">
																							</td>
																						</tr>

																						<tr>
																							<td class="arial12LGrayBold" align="right">&nbsp;Assessment Body&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																							<td class="arial12LGrayBold">
																								<input type="text" name="assessment_body" id="assessment_body" value="" class="required">
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