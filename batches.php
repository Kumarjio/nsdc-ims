<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Batch has been deleted successfully!!!", 'added'=>'Batch has been added successfully',"edited"=>"Batch has been updated successfully", "cert_edited"=>"Certification Status has been updated successfully");
	$action = $_POST['action_type'];

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$section_id = tep_db_prepare_input($_POST['section_id']);
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$district_id = tep_db_prepare_input($_POST['district_id']);
		$batch_title = tep_db_prepare_input($_POST['batch_title']);

 		$batch_start_date = tep_db_prepare_input($_POST['batch_start_date']);
		$batch_start_date = input_valid_date($batch_start_date);

		$batch_end_date = tep_db_prepare_input($_POST['batch_end_date']);
		$batch_end_date = input_valid_date($batch_end_date);

		$handholding_end_date = tep_db_prepare_input($_POST['handholding_end_date']);
		$handholding_end_date = input_valid_date($handholding_end_date);

		$batch_size = tep_db_prepare_input($_POST['batch_size']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'section_id' => $section_id,
			'course_id' => $course_id,
			'district_id' => $district_id,
			'batch_title' => $batch_title,
			'batch_start_date' => $batch_start_date,
			'batch_end_date' => $batch_end_date,
			'handholding_end_date' => $handholding_end_date,
			'batch_size' => $batch_size
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_BATCHES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_BATCHES, $arr_db_values, "update", "batch_id = '" . $batch_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_BATCHES ." where batch_id = '". $batch_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
	<head>
		<title><?php echo TITLE ?>: Batch Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<link href="<?php echo DIR_WS_JS . 'bt_sgsy/css/bt.min.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'bt_sgsy/js/bt.min.js';?>"></script>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this batch?")){
					objForm.action_type.value = action_type;
					objForm.batch_id.value = int_id;
					objForm.submit();
				}
			}

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
						})
					}
				});
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#batch_start_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#batch_end_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#handholding_end_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});
			
			});
		//-->
		</script>

		<link href="<?php echo DIR_WS_JS . 'sticky/stickytooltip.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'sticky/stickytooltip.js';?>"></script>
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

														$info_query_raw = " select batch_id, centre_id, section_id, course_id, district_id, batch_title, date_format(batch_start_date, '%d-%m-%Y') as batch_start_date, date_format(batch_end_date, '%d-%m-%Y') as batch_end_date, date_format(handholding_end_date, '%d-%m-%Y') as handholding_end_date, batch_size, batch_status from " . TABLE_BATCHES . " where batch_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Batch Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Batch Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="batch_id" id="batch_id" value="<?php echo $info['batch_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Sector&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="section_id" id="section_id" title="Please select sector" class="required" onchange="javascript: get_courses('');">
																				<option value="">Please choose</option>
																				<?php
																					$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																					$section_query = tep_db_query($section_query_raw);
																					
																					while($section = tep_db_fetch_array($section_query)){
																				?>
																				<option value="<?php echo $section['section_id'];?>" <?php echo($info['section_id'] == $section['section_id'] ? 'selected="selected"' : '');?>><?php echo $section['section_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_id" id="course_id" title="Please select course" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<script type="text/javascript">
																	<!--
																		get_courses('<?php echo $info['course_id'] ?>');
																	//-->
																	</script>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch District&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="district_id" id="district_id" class="required">
																				<option value="">Please choose</option>
																				<?php
																					$disctrict_query_raw = " select district_id, district_name from ". TABLE_DISTRICTS ." where 1 order by district_name";
																					$disctrict_query = tep_db_query($disctrict_query_raw);
																					
																					while($disctrict = tep_db_fetch_array($disctrict_query)){
																				?>
																				<option value="<?php echo $disctrict['district_id'];?>" <?php echo($info['district_id'] == $disctrict['district_id'] ? 'selected="selected"' : '');?>><?php echo $disctrict['district_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="batch_title" id="batch_title" title="Please enter batch name" maxlength="100" value="<?php echo  ($dupError ? $_POST['batch_title'] : $info['batch_title']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch Start Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="batch_start_date" id="batch_start_date" title="Please enter batch start date" value="<?php echo  ($dupError ? $_POST['batch_start_date'] : $info['batch_start_date']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch End Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="batch_end_date" id="batch_end_date" title="Please enter batch end date" value="<?php echo  ($dupError ? $_POST['batch_end_date'] : $info['batch_end_date']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Handholding End Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="handholding_end_date" id="handholding_end_date" title="Please enter Handholding end date" value="<?php echo  ($dupError ? $_POST['handholding_end_date'] : $info['handholding_end_date']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch Size&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="batch_size" id="batch_size" title="Please enter Batch Size" value="<?php echo  ($dupError ? $_POST['batch_size'] : $info['batch_size']) ?>" class="required">
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
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Batch Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Batch</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select b.batch_id, b.batch_title, b.batch_start_date, b.batch_end_date, b.handholding_end_date, b.batch_status, b.test_allotted_date, b.test_agency, b.batch_size, c.course_name, d.district_name from " . TABLE_BATCHES . " b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id), " . TABLE_COURSES . " c where c.course_id = b.course_id ";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query_raw .= " order by b.batch_title";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="batch_id" id="batch_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Batch</th>
																		<th>Course</th>
																		<th>Start Date</th>
																		<th>End Date</th>
																		<th>Handholding Date</th>
																		<th>Batch District</th>
																		<th>Batch Status</th>
																		<th>Batch Size</th>
																		<th>Total Enrolled</th>
																		<!-- <th>Test Alloted Date</th>
																		<th>Testing Agency</th> -->
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																				$total_batch_students_query_raw = "select count(*) as total_enrolled from " . TABLE_STUDENTS . " where batch_id = '" . $listing['batch_id'] . "' and student_type = 'ENROLLED'";
																				$total_batch_students_query = tep_db_query($total_batch_students_query_raw);
																				$total_batch_students_array = tep_db_fetch_array($total_batch_students_query);
																				$total_batch_students = $total_batch_students_array['total_enrolled'];
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['batch_title']; ?></td>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo ($listing['batch_start_date'] != '0000-00-00' ? date("d M Y", strtotime($listing['batch_start_date'])) : '-'); ?></td>
																			<td valign="top"><?php echo ($listing['batch_end_date'] != '0000-00-00' ? date("d M Y", strtotime($listing['batch_end_date'])) : '-'); ?></td>
																			<td valign="top"><?php echo ($listing['handholding_end_date'] != '0000-00-00' ? date("d M Y", strtotime($listing['handholding_end_date'])) : '-'); ?></td>

																			<td valign="top"><?php echo ($listing['district_name'] != '' ? $listing['district_name'] : '-'); ?></td>
																			<td valign="top"><?php echo $arr_batch_status[$listing['batch_status']]; ?></td>

																			<td valign="top"><?php echo $listing['batch_size']; ?></td>
																			<td valign="top"><?php echo $total_batch_students; ?></td>

																			<!-- <td valign="top"><?php //echo ($listing['test_allotted_date'] != '0000-00-00' ? date("d M Y", strtotime($listing['test_allotted_date'])) : '-'); ?></td>
																			<td valign="top"><?php //echo ($listing['test_agency'] != '' ? $listing['test_agency'] : '-'); ?></td> -->
																			<td valign="top">
																				<div class="dropdown pull-left">
																					<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="javascript: void(0);">Update Status</a>
																						<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
																							<li><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['batch_id']); ?>">Edit Batch</a></li>
																							<li><a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['batch_id']; ?>')">Delete Batch</a></li>
																							<!-- <li><a href="<?php //echo tep_href_link(FILENAME_BATCH_CERTIFICATION_STATUS,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['batch_id']); ?>">Update Batch Certification Status</a></li> -->
																							<li><a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=batch_stud&batch_id=".$listing['batch_id']); ?>">View Batch Students</a></li>

																							<li><a href="<?php echo tep_href_link(FILENAME_BATCH_STATUS,tep_get_all_get_params(array('msg','actionType','int_id'))."batch_id=".$listing['batch_id']); ?>">Change Batch Status</a></li>
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
																					null, //Batch
																					null, // Course
																					null, // Start
																					null, // End
																					null, // Handholding
																					null, // Disctrict
																					null, // Status
																					null, // Size
																					null, // Total Enrolled
																					{ "bSortable": false}
																				],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bBatch_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Batch Found !!</td>
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
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>