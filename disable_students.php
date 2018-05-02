<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("status_chagned"=>"Student lock status has been changed successfully", "deact_success"=>"Student status has been changed successfully");

	$action = (isset($_GET['actionType']) ? $_GET['actionType'] : $_POST['action_type']);

	if(isset($action) && !empty($action)){
		if($action == 'change_status'){
			$student_id = (int)$_GET['int_id'];
			$student_status = $_GET['status'];

			change_student_status($student_id, $student_status);

			$msg = 'status_chagned';

		}else if($action == 'change_deactivate_status'){
			$student_id = (int)$_GET['int_id'];
			$student_status = $_GET['status'];

			change_student_deact_status($student_id, $student_status);

			$msg = 'deact_success';

		}else if($action == 'multiple_activation'){
			if(is_array($_POST['student']) && count($_POST['student'])){
				foreach($_POST['student'] as $student_id){
					change_student_status($student_id, '0');
				}
			}
                        $msg = 'status_chagned';
		}else if($action == 'delete_student'){
			$student_id = (int)$_POST['student_id'];

			if((int)$student_id > 0){
				tep_db_query("delete from " . TABLE_ATTENDANCE . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_HANDHOLDING . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_INSTALLMENTS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_PLACEMENTS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_PROS_CONTACT_LOGS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_REFUNDS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_STUDENT_DOCUMENTS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_STUDENT_PAYMENTS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_STUDENT_WAIVERS . " where student_id = '" . $student_id . "'");
				tep_db_query("delete from " . TABLE_STUDENTS . " where student_id = '" . $student_id . "'");
			}
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType', 'status', 'int_id')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo TITLE ?>: Student Management</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_JS . 'bt_sgsy/css/bt.min.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'bt_sgsy/js/bt.min.js';?>"></script>

		<script type="text/javascript">
		<!--
			function get_courses(default_course){
				var section = $('#section_id').val();

				$('#course_id').empty();
				$('#course_id').append($("<option></option>").attr("value",'').text('Please choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_courses&section='+section,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_course == values.course_id){
								$('#course_id').append($("<option></option>").attr("value",values.course_id).attr('selected', 'selected').text(values.frm_course_name));
							}else{
								$('#course_id').append($("<option></option>").attr("value",values.course_id).text(values.frm_course_name));
							}
						});

						get_batch('');
					}
				});
			}
			function get_batch(default_batch){
				var course = $('#course_id').val();
				var centre = $('#centre_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course+'&centre='+centre,
					type: 'POST',
					async: false,
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_batch == values.batch_id){
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).attr('selected', 'selected').text(values.batch_title));
							}else{
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).text(values.batch_title));
							}
						})
					}
				});
			}

			function is_student_checked(){
				var is_student_checked = false;
				$('input[name^="student["]').each(function(){
					if($(this).prop('checked') == true){
						is_student_checked = true;
						return false;
					}
				});

				return is_student_checked;
			}

			function make_selected_active(){
				if(is_student_checked() == true){
					document.frmListing.action_type.value = 'multiple_activation';
					document.frmListing.submit();
				}else{
					alert("Please choose atleast one student");
				}
			}

			function make_assessment_payment(){
				if(is_student_checked() == true){
					document.frmListing.action = '<?php echo tep_href_link(FILENAME_ADD_ASSESSMENT_PAYMENT); ?>';
					document.frmListing.submit();
				}else{
					alert("Please choose atleast one student");
				}
			}

			function check_all(){
				if($('input[name="chkSelectAll"]').prop('checked') == true){
					$('input[name^="student["]').prop('checked', true);
				}else{
					$('input[name^="student["]').prop('checked', false);
				}
			}

			function chante_student_status(url){
				if(confirm("Are you sure want to change the status?")){
					window.location = url;
				}
			}

			function delete_student(student_id){
				if(confirm("Are you sure want to delete student? It will delete all the data related with this student.")){
					document.frmListing.action_type.value = 'delete_student';
					document.frmListing.student_id.value = student_id;
					document.frmListing.submit();
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
											<table cellpadding="5" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">Student Status</td>
													<td valign="top" class="arial14LGrayBold">
														<form name="frmFilter" id="frmFilter" method="get">
														Center
														<select name="centre_id" id="centre_id" style="width:150px;">
															<option value="">All</option>
															<?php
																$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " order by centre_name";
																$centre_query = tep_db_query($centre_query_raw);
																
																while($centre = tep_db_fetch_array($centre_query)){
															?>
															<option value="<?php echo $centre['centre_id'];?>" <?php echo(isset($_GET['centre_id']) && $_GET['centre_id'] == $centre['centre_id'] ? 'selected="selected"' : '');?>><?php echo $centre['centre_name'];?></option>
															<?php } ?>
														</select>&nbsp;
														<b>Sector</b>
														<select name="section_id" id="section_id" title="Please select sector" class="required" onchange="javascript: get_courses('');" style="width:150px;">
															<option value="">Please choose</option>
															<?php
																$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																$section_query = tep_db_query($section_query_raw);
																
																while($section = tep_db_fetch_array($section_query)){
															?>
															<option value="<?php echo $section['section_id'];?>" <?php echo($info['section_id'] == $section['section_id'] ? 'selected="selected"' : '');?>><?php echo $section['section_name'];?></option>
															<?php } ?>
														</select>&nbsp;
														<b>Course</b>
														<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch('');" style="width: 120px;">
															<option value="">Please choose</option>
														</select>&nbsp;
														<b>Batch</b>
														<select name="batch_id" id="batch_id" title="Please select batch" class="required" style="width:150px;">
															<option value="">Please choose</option>
														</select>
														<input type="submit" value="Submit" name="cmdExcel" id="cmdExcel" class="groovybutton">
														</form>
													</td>
												</tr>
												<tr>
													<td colspan="3">
														<input type="button" value="Activate Selected" name="cmdActiSelected" id="cmdActiSelected" class="groovybutton" onclick="javascript: make_selected_active();">
														<input type="button" value="Add Assessment Fee" name="cmdActiSelected" id="cmdActiSelected" class="groovybutton" onclick="javascript: make_assessment_payment();">
													</td>
												</tr>
												<tr>
													<td colspan="3">
														<?php
															$listing_query_raw = "select s.student_id, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.student_mobile, s.test_result, s.is_training_completed, s.student_status, s.is_deactivated, c.course_name, b.batch_title, cn.centre_name from " . TABLE_STUDENTS . " s, " . TABLE_COURSES . " c, " . TABLE_BATCHES . " b, " . TABLE_CENTRES . " cn where cn.centre_id = s.centre_id and b.batch_id = s.batch_id and c.course_id = s.course_id and student_type = 'ENROLLED'";

															if(isset($_GET['centre_id']) && $_GET['centre_id'] != ""){
																$centre_id = (int)$_GET['centre_id'];
																$listing_query_raw .= " and s.centre_id = '" . $centre_id . "'";
															}

															if(isset($_GET['section_id']) && $_GET['section_id'] != ""){
																$section_id = (int)$_GET['section_id'];
																//$listing_query_raw .= " and s.centre_id = '" . $centre_id . "'";
															}

															if(isset($_GET['course_id']) && $_GET['course_id'] != ""){
																$course_id = (int)$_GET['course_id'];
																$listing_query_raw .= " and s.course_id = '" . $course_id . "'";
															}

															if(isset($_GET['batch_id']) && $_GET['batch_id'] != ""){
																$batch_id = (int)$_GET['batch_id'];
																$listing_query_raw .= " and s.batch_id = '" . $batch_id . "'";
															}

															$listing_query_raw .= " order by s.student_id desc";

															$listing_query = tep_db_query($listing_query_raw);
														?>
														<form name="frmListing" id="frmListing" method="post" action="">
															<input type="hidden" name="action_type" id="action_type" value="">
															<input type="hidden" name="student_id" id="student_id" value="">
															<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																<thead>
																	<th width="2%"><input type="checkbox" name="chkSelectAll" value="1" onclick="check_all();" /></th>
																	<th>Student ID</th>
																	<th>Student Name</th>
																	<th>Mobile</th>
																	<th>Center</th>
																	<th>Course</th>
																	<th>Batch</th>
																	<th>Is Locked</th>
																	<th>Current Status</th>
																	<th width="10%">Action</th>
																</thead>
																<tbody>
																<?php
																	if(tep_db_num_rows($listing_query) && isset($_GET['centre_id'])){
																		while($listing = tep_db_fetch_array($listing_query) ){
																?>
																	<tr>
																		<td valign="top">
																			<input type="checkbox" name="student[]" value="<?php echo $listing['student_id']; ?>" />
																		</td>
																		<td valign="top"><?php echo $listing['student_id']; ?></td>
																		<td valign="top"><a href="<?php echo tep_href_link(FILENAME_VIEW_STUDENT,tep_get_all_get_params(array('msg','actionType','int_id'))."int_id=".$listing['student_id']); ?>"><?php echo $listing['student_full_name'] . ' ' . $listing['student_middle_name'] . ' ' . $listing['student_surname']; ?></a></td>
																		<td valign="top"><?php echo $listing['centre_name']; ?></td>
																		<td valign="top"><?php echo $listing['student_mobile']; ?></td>
																		<td valign="top"><?php echo $listing['course_name']; ?></td>
																		<td valign="top"><?php echo $listing['batch_title']; ?></td>
																		<td valign="top"><?php echo ($listing['student_status'] != '' && $listing['student_status'] == '0' ? 'Unlocked' : 'Locked'); ?></td>
																		<td valign="top"><?php echo ($listing['is_deactivated'] != '' && $listing['is_deactivated'] == '1' ? 'Inactive' : 'Active'); ?></td>
																		<td valign="top">
																			<div class="dropdown pull-left">
																				<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript: void(0);">Update Status</a>
																				<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
																					<li class="disabled"><a style="text-decoration:none;" href="javascript: void(0);"><b><?php echo $listing['student_full_name'] . ' ' . $listing['student_middle_name'] . ' ' . $listing['student_surname']; ?></b></a></li>
																					<li class="divider"></li>
																					<li>
																						<?php if($listing['student_status'] == '0'){ ?>
																						<a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=change_status&int_id=".$listing['student_id'] . '&status=1'); ?>">Lock</a>
																						<?php }else{ ?>
																						<a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=change_status&int_id=".$listing['student_id'] . '&status=0'); ?>">Unlock</a>
																						<?php } ?>
																					</li>
																					<li>
																						<?php if($listing['is_deactivated'] == '1'){ ?>
																						<a href="javascript:void(0);" onclick="javascript: chante_student_status('<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=change_deactivate_status&int_id=".$listing['student_id'] . '&status=0'); ?>');">Activate</a>
																						<?php }else{ ?>
																						<a href="javascript:void(0);" onclick="javascript: chante_student_status('<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=change_deactivate_status&int_id=".$listing['student_id'] . '&status=1'); ?>');">Deactivate</a>
																						<?php } ?>
																					</li>
																					<li>
																						<a href="<?php echo tep_href_link(FILENAME_VIEW_STUD_PAYMENT_HISTORY,tep_get_all_get_params(array('msg','actionType','int_id'))."int_id=".$listing['student_id']); ?>">View Payments</a>
																					</li>
																					<li>
																						<a href="<?php echo tep_href_link(FILENAME_ADD_STUDENT_INSTALLMENT,tep_get_all_get_params(array('msg','actionType','int_id'))."int_id=".$listing['student_id']); ?>">Add Installment</a>
																					</li>
																					<li>
																						<a href="javascript:void(0);" onclick="javascript: delete_student('<?php echo $listing['student_id']; ?>');">Delete Student</a>
																					</li>
																				</ul>
																			</div>
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
																				null, //Student ID
																				null, //Student Name
																				null, // Centre Name
																				null, // Mobile
																				null, // Course
																				null, // Branch
																				null, // Is Locked
																				null, // Current Status
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
																		<td align="center" colspan="6" class="verdana11Red">No Student Found !!</td>
																	</tr>
																<?php } ?>
																</tbody>
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