<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Lecture has been deleted successfully!!!", 'added'=>'Lecture has been added successfully',"edited"=>"Lecture  has been updated successfully");

	$action = $_POST['action_type'];

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		$lecture_id = tep_db_prepare_input($_POST['lecture_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$module_id = tep_db_prepare_input($_POST['module_id']);
		$subject_id = tep_db_prepare_input($_POST['subject_id']);
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$faculty_id = tep_db_prepare_input($_POST['faculty_id']);
		$lecture = tep_db_prepare_input($_POST['lecture']);
 		$lecture_date = tep_db_prepare_input($_POST['lecture_date']);
		$lecture_date = input_valid_date($lecture_date);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'module_id' => $module_id,
			'subject_id' => $subject_id,
			'batch_id' => $batch_id,
			'faculty_id' => $faculty_id,
			'lecture' => $lecture,
			'lecture_date' => $lecture_date
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_LECTURES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_LECTURES, $arr_db_values, "update", "lecture_id = '" . $lecture_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_LECTURES ." where lecture_id = '". $lecture_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Lecture Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this lecture?")){
					objForm.action_type.value = action_type;
					objForm.lecture_id.value = int_id;
					objForm.submit();
				}
			}

			function get_batch(default_batch){
				var course = $('#course_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course+'&ds='+default_batch,
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

			function get_faculty(default_faculty){
				var course = $('#course_id').val();
				var subject = $('#subject_id').val();

				$('#faculty_id').empty();
				$('#faculty_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_faculty&course='+course+'&subject='+subject,
					type: 'POST',
					async: false,
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_faculty == values.faculty_id){
								$('#faculty_id').append($("<option></option>").attr("value",values.faculty_id).attr('selected', 'selected').text(values.faculty_first_name));
							}else{
								$('#faculty_id').append($("<option></option>").attr("value",values.faculty_id).text(values.faculty_first_name));
							}
						})
					}
				});
			}

			function get_module(default_module){
				var course = $('#course_id').val();

				$('#module_id').empty();
				$('#module_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_module&course='+course,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_module == values.module_id){
								$('#module_id').append($("<option></option>").attr("value",values.module_id).attr('selected', 'selected').text(values.module));
							}else{
								$('#module_id').append($("<option></option>").attr("value",values.module_id).text(values.module));
							}
						})
					}
				});
			}

			function get_subject(default_subject){
				var course = $('#course_id').val();
				var module = $('#module_id').val();

				$('#subject_id').empty();
				$('#subject_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_subject&course='+course+'&module='+module,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_subject == values.subject_id){
								$('#subject_id').append($("<option></option>").attr("value",values.subject_id).attr('selected', 'selected').text(values.subject));
							}else{
								$('#subject_id').append($("<option></option>").attr("value",values.subject_id).text(values.subject));
							}
						})
					}
				});
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#lecture_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});
			});
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
												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" )
												{
													if($_GET['actionType'] == "edit"){
														$int_id = $_GET['int_id'];

														$info_query_raw = " select lecture_id, centre_id, course_id, module_id, subject_id, batch_id, faculty_id, lecture, date_format(lecture_date, '%d-%m-%Y') as lecture_date from " . TABLE_LECTURES . " where lecture_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Lecture Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Lecture Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="lecture_id" id="lecture_id" value="<?php echo $info['lecture_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch(''); get_module('');">
																				<option value="">Please choose</option>
																				<?php
																					$course_query_raw = " select course_id, course_name from " . TABLE_COURSES . " order by course_name";
																					$course_query = tep_db_query($course_query_raw);
																					
																					while($course = tep_db_fetch_array($course_query)){
																				?>
																				<option value="<?php echo $course['course_id'];?>" <?php echo($info['course_id'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Module&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="module_id" id="module_id" title="Please select module" class="required" onchange="javascript: get_subject('');">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Topic&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="subject_id" id="subject_id" title="Please select topic" class="required" onchange="javascript: get_faculty('');">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="faculty_id" id="faculty_id" title="Please select faculty" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<script type="text/javascript">
																	<!--
																		get_module('<?php echo $info['module_id'] ?>');
																		get_subject('<?php echo $info['subject_id'] ?>');
																		get_batch('<?php echo $info['batch_id'] ?>');
																		get_faculty('<?php echo $info['faculty_id'] ?>');
																	//-->
																	</script>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Lecture&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="lecture" id="lecture" title="Please enter lecture name" maxlength="255" value="<?php echo  ($dupError ? $_POST['lecture'] : $info['lecture']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Lecture Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="lecture_date" id="lecture_date" title="Please enter lecture date" value="<?php echo  ($dupError ? $_POST['lecture_date'] : $info['lecture_date']) ?>" class="required">
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
														<td class="arial18BlueN">Lecture Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Lecture</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select l.lecture_id, l.lecture, date_format(l.lecture_date, '%d %b %Y') as lecture_date, c.course_name, b.batch_title, f.faculty_first_name, s.subject, m.module from " . TABLE_LECTURES . " l, " . TABLE_COURSES . " c, " . TABLE_MODULES . " m, " . TABLE_BATCHES . " b, " . TABLE_FACULTIES . " f, " . TABLE_SUBJECTS . " s where m.module_id = l.module_id and c.course_id = l.course_id and b.batch_id = l.batch_id and f.faculty_id = l.faculty_id and s.subject_id = l.subject_id ";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and l.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query_raw .= " order by l.lecture";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="lecture_id" id="lecture_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Lecture</th>
																		<th>Course</th>
																		<th>Module</th>
																		<th>Topic</th>
																		<th>Batch</th>
																		<th>Faculty</th>
																		<th>Date</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['lecture']; ?></td>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo $listing['module']; ?></td>
																			<td valign="top"><?php echo $listing['subject']; ?></td>
																			<td valign="top"><?php echo $listing['batch_title']; ?></td>
																			<td valign="top"><?php echo $listing['faculty_first_name']; ?></td>
																			<td valign="top"><?php echo $listing['lecture_date']; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['lecture_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['lecture_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Lecture
																					null, // Course
																					null, // Module
																					null, // Topic
																					null, // Batch
																					null, // Faculty
																					null, // Date
																					{ "bSortable": false}
																				],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bLecture_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="7" class="verdana11Red">No Lecture Found !!</td>
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