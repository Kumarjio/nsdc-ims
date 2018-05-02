<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Prospect Contact has been deleted successfully", 'added'=>'Prospect Contact has been added successfully',"edited"=>"Prospect Contact has been updated successfully");
	$action = $_POST['action_type'];

	if(isset($action) && tep_not_null($action)){
		$student_id = tep_db_prepare_input($_POST['student_id']);

		$contact_log_id =  tep_db_prepare_input($_POST['contact_log_id']);

		$contact_log_date =  tep_db_prepare_input($_POST['contact_log_date']);
		$contact_log_date = date('Y-m-d', strtotime($contact_log_date));

		$contact_log_next_date =  tep_db_prepare_input($_POST['contact_log_next_date']);
		$contact_log_next_date = date('Y-m-d', strtotime($contact_log_next_date));

		$contact_log_status =  tep_db_prepare_input($_POST['contact_log_status']);
		$contact_log_remark =  tep_db_prepare_input($_POST['contact_log_remark']);

		$arr_db_values = array(
			'contact_log_date' => $contact_log_date,
			'contact_log_next_date' => $contact_log_next_date,
			'contact_log_status' => $contact_log_status,
			'contact_log_remark' => $contact_log_remark
		);

		switch($action){
			case 'add':
				$arr_db_values['student_id'] = $student_id;
				tep_db_perform(TABLE_PROS_CONTACT_LOGS, $arr_db_values);

				$msg = 'added';
			break;
			case 'edit':
				tep_db_perform(TABLE_PROS_CONTACT_LOGS, $arr_db_values, "update", "contact_log_id = '" . $contact_log_id . "'");
				$msg = 'edited';
			break;
			case 'delete':
				//tep_db_query("delete from ". TABLE_PROS_CONTACT_LOGS ." where contact_log_status = '". $contact_log_status ."'");
				//$msg = 'deleted';
			break;
		}
		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}

	$current_student_id = tep_db_input($_GET['stud_id']);

	$student_query_raw = "select s.student_id, s.student_full_name, s.student_father_name, s.student_mobile, s.student_status from " . TABLE_STUDENTS . " s where is_deactivated != '1'";
	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$student_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}
	$student_query_raw .= " and s.student_id  = '" . $current_student_id . "'";

	$student_query = tep_db_query($student_query_raw);

	if(!tep_db_num_rows($student_query)){
		tep_redirect(tep_href_link(FILENAME_PROS_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType', 'stud_id'))));
	}

	$student = tep_db_fetch_array($student_query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo TITLE ?>: Prospect Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery.labelify.js';?>"></script>

		<script language="javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#contact_log_date').datepicker({
					dateFormat: "dd-mm-yy",
				});
			});
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#contact_log_next_date').datepicker({
					dateFormat: "dd-mm-yy",
				});
			});
		-->
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

												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" )
												{
													if($_GET['actionType'] == "edit"){
														$int_id = $_GET['int_id'];

														$info_query_raw = "select student_id, contact_log_id, contact_log_remark, date_format(contact_log_date, '%d-%m-%Y') as contact_log_date, date_format(contact_log_next_date, '%d-%m-%Y') as contact_log_next_date,  contact_log_status  from " . TABLE_PROS_CONTACT_LOGS. " where contact_log_id = '" . $int_id . "' ";

														$info_query = tep_db_query($info_query_raw);
														$info = tep_db_fetch_array($info_query);
													}
											?>
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN"><?php echo $student['student_full_name'] . ' ' . $student['student_father_name'];?> - Contact Logs</td>
													<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Contact Logs</a></td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
															<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
															<input type="hidden" name="student_id" id="student_id" value="<?php echo ($_GET['stud_id'] != '' ? $_GET['stud_id'] : $_GET['stud_id']); ?>"> 
															<input type="hidden" name="contact_log_id" id="contact_log_id" value="<?php echo $_GET['int_id']; ?>">
															<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																<tr>
																	<td>
																		<table cellpadding="0" cellspacing="0" border="0" width="100%">
																			<tr>
																				<td class="arial14LGrayBold" colspan="2">
																					<fieldset>
																						<legend>Contact Log Info</legend>
																						<table cellpadding="5" cellspacing="5" border="0" width="100%" id="pros_info">
																							<tr>
																								<td class="arial12LGrayBold" align="left" width="13%">&nbsp;Log Date &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td>
																									<input type="text" name="contact_log_date" id="contact_log_date" value="<?php echo  ($dupError ? $_POST['contact_log_date'] : $info['contact_log_date']) ?>" class="required">
																								</td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" align="left" width="13%">&nbsp;Next Contact Date &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td>
																									<input type="text" name="contact_log_next_date" id="contact_log_next_date" value="<?php echo  ($dupError ? $_POST['contact_log_next_date'] : $info['contact_log_next_date']) ?>" class="required">
																								</td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" align="left">&nbsp;Remark&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGrayBold">
																									<textarea name="contact_log_remark" id="contact_log_remark" cols="40" rows="6"><?php echo  ($dupError ? $info['contact_log_remark'] : $info['contact_log_remark']) ?></textarea>
																								</td>
																							</tr>
																							<tr>
																								<td class="arial12LGrayBold" align="left">&nbsp;Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																								<td class="arial12LGray">
																									<select name="contact_log_status" id="ontact_log_status" class="required" style="width:200px;">
																										<option value="">Please choose</option>
																										<?php foreach($arr_status_pros as $status_key => $status_value){ ?>
																										<option value="
																										<?php echo $status_key;?>" <?php echo($info['contact_log_status'] == $status_key? 'selected="selected"' : '');?>><?php echo $status_value;?></option>
																									<?php } ?>
																									</select>
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
																	<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton"></td>
																	<td >&nbsp;</td>
																<tr>
															</table>
														</form>
													</td>
												</tr>
											</table>	
											<?php 
												}else{ 
													$order = "asc";
													$searchValue = tep_db_input($_GET['txtSearchValue']);
													$searchType = tep_db_input($_GET['cmbSearch']);
													$get_student_id = tep_db_input($_GET['stud_id']);
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN"><?php echo $student['student_full_name'] . ' ' . $student['student_father_name'];?> - Contact Logs</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Contact Log</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw ="select student_id, contact_log_id, contact_log_remark, contact_log_date,  contact_log_next_date, contact_log_status from " . TABLE_PROS_CONTACT_LOGS ." WHERE student_id = '" . $get_student_id . "'ORDER BY contact_log_id DESC";
																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="student_id" id="student_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Remark</th>
																		<th>Date</th>
																		<th>Next Contact Date</th>
																		<th>Status</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																				
																	?>
																		<tr>
																			<td valign="top">
																				<?php echo $listing['contact_log_remark']; ?>
																			</td>
																			<td valign="top">
																				<?php echo $listing['contact_log_date']; ?>
																			</td>
																			<td valign="top">
																				<?php echo $listing['contact_log_next_date']; ?>
																			</td>
																			<td valign="top">
																				<?php echo $listing['contact_log_status']; ?>
																			</td>
																			<td valign="top">
																			<a href="<?php echo	tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&	int_id=".$listing['contact_log_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>
																			</td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Remark
																					null, //Date
																					null,//Next  Contact Date
																					null, //Status
																					{ "bSortable": false}
																				],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bstudent_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Log Found !!</td>
																		</tr>
																	<?php } ?>
																	</tbody>
																</table>
															</form>
														</td>
													</tr>
												</table>	
											<?php } ?>
										</td>
									</tr>
									<?php include( DIR_WS_MODULES . 'footer.php' ); ?>
								</table>
							</td>
						</tr>
						</tr>
					</table>
				</td>
			</tr>
		</tr>
		</table>
	</body>
</html>
