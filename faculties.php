<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Faculty has been deleted successfully!!!", 'added'=>'Faculty has been added successfully',"edited"=>"Faculty  has been updated successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$faculty_id = tep_db_prepare_input($_POST['faculty_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$module_id = tep_db_prepare_input($_POST['module_id']);
		$subject_id = tep_db_prepare_input($_POST['subject_id']);
		$faculty_first_name = tep_db_prepare_input($_POST['faculty_first_name']);
		$faculty_last_name = tep_db_prepare_input($_POST['faculty_last_name']);
 		$faculty_qualification = tep_db_prepare_input($_POST['faculty_qualification']);
		$faculty_experience = tep_db_prepare_input($_POST['faculty_experience']);
		$faculty_course_level = tep_db_prepare_input($_POST['faculty_course_level']);
		$faculty_unq_id = tep_db_prepare_input($_POST['faculty_unq_id']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'module_id' => $module_id,
			'subject_id' => $subject_id,
			'faculty_first_name' => $faculty_first_name,
			'faculty_last_name' => $faculty_last_name,
			'faculty_qualification' => $faculty_qualification,
			'faculty_experience' => $faculty_experience,
			'faculty_course_level' => $faculty_course_level,
			'faculty_unq_id' => $faculty_unq_id
		);

		if($_FILES['faculty_photo']['name'] != ''){
			$ext = get_extension($_FILES['faculty_photo']['name']);
			$src = $_FILES['faculty_photo']['tmp_name'];

			$dest_filename = 'fac_img_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['faculty_photo'] = $dest_filename;
			}
		}

		if($_FILES['faculty_cv']['name'] != ''){
			$ext = get_extension($_FILES['faculty_cv']['name']);
			$src = $_FILES['faculty_cv']['tmp_name'];

			$dest_filename = 'fac_cv_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['faculty_cv'] = $dest_filename;
			}
		}

		switch($action){
			case 'add':
				tep_db_perform(TABLE_FACULTIES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_FACULTIES, $arr_db_values, "update", "faculty_id = '" . $faculty_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_FACULTIES ." where faculty_id = '". $faculty_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Faculty Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this faculty?")){
					objForm.action_type.value = action_type;
					objForm.faculty_id.value = int_id;
					objForm.submit();
				}
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

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();
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

														$info_query_raw = " select faculty_id, centre_id, course_id, module_id, subject_id, faculty_first_name, faculty_last_name, faculty_qualification, faculty_experience, faculty_photo, faculty_cv, faculty_course_level from " . TABLE_FACULTIES . " where faculty_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Faculty Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Faculty Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="faculty_id" id="faculty_id" value="<?php echo $info['faculty_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_module();">
																				<option value="">Please choose</option>
																				<?php
																					$course_query_raw = " select course_id, course_name from " . TABLE_COURSES . " where course_status = '1' order by course_name";
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
																	<script type="text/javascript">
																	<!--
																		get_module('<?php echo $info['module_id'] ?>');
																	//-->
																	</script>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Topic&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="subject_id" id="subject_id" title="Please select topic" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<script type="text/javascript">
																	<!--
																		get_subject('<?php echo $info['subject_id'] ?>');
																	//-->
																	</script>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty First Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="faculty_first_name" id="faculty_first_name" title="Please enter faculty first name" maxlength="100" value="<?php echo  ($dupError ? $_POST['faculty_first_name'] : $info['faculty_first_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Last Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="faculty_last_name" id="faculty_last_name" title="Please enter faculty last name" maxlength="100" value="<?php echo  ($dupError ? $_POST['faculty_last_name'] : $info['faculty_last_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="faculty_qualification" id="faculty_qualification" title="Please enter faculty qualification" maxlength="50" value="<?php echo  ($dupError ? $_POST['faculty_qualification'] : $info['faculty_qualification']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Course Level&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="faculty_course_level" id="faculty_course_level" class="required">
																				<option value="">Please choose</option>
																				<?php foreach($arr_course_level as $k_c_level=>$v_c_level){?>
																				<option value="<?php echo $k_c_level;?>" <?php echo($info['faculty_course_level'] == $k_c_level ? 'selected="selected"' : '');?>><?php echo $v_c_level;?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Id&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="faculty_unq_id" id="faculty_unq_id" maxlength="15" value="<?php echo  ($dupError ? $_POST['faculty_unq_id'] : $info['faculty_unq_id']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Photo&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<?php if($info['faculty_photo']!=''){?>
																				<img src="<?php echo DIR_WS_UPLOAD . $info['faculty_photo'];?>" width="150" style="padding:3px; border: 1px solid black;"><br><br>
																			<?php } ?>
																			<input type="file" name="faculty_photo" id="faculty_photo" title="Please enter faculty photo"  <?php echo ($info['faculty_photo']=='' ? 'class="required"' : ''); ?>>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty CV&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<?php if($info['faculty_cv']!=''){?>
																				<a href="<?php echo DIR_WS_UPLOAD . $info['faculty_cv'];?>" target="_blank"><?php echo $info['faculty_cv'];?></a><br><br>
																			<?php } ?>
																			<input type="file" name="faculty_cv" id="faculty_cv" title="Please enter faculty CV" <?php echo ($info['faculty_cv']=='' ? 'class="required"' : ''); ?>>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Faculty Experience&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="faculty_experience" id="faculty_experience" title="Please enter faculty experience" maxlength="3" value="<?php echo  ($dupError ? $_POST['faculty_experience'] : $info['faculty_experience']) ?>" class="number" style="width:50px;">&nbsp;Year
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
														<td class="arial18BlueN">Faculty Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Faculty</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select f.faculty_id, f.faculty_first_name, f.faculty_last_name, f.faculty_experience, f.faculty_qualification, c.course_name, s.subject, m.module from " . TABLE_FACULTIES . " f, " . TABLE_COURSES . " c, " . TABLE_MODULES . " m, " . TABLE_SUBJECTS . " s where m.module_id = f.module_id and c.course_id = f.course_id and s.subject_id = f.subject_id ";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and f.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query_raw .= " order by f.faculty_first_name";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="faculty_id" id="faculty_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Faculty</th>
																		<th>Course</th>
																		<th>Module</th>
																		<th>Topic</th>
																		<th>Years of Experience</th>
																		<th>Qualification</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['faculty_first_name'] . ' ' . $listing['faculty_last_name']; ?></td>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo $listing['module']; ?></td>
																			<td valign="top"><?php echo $listing['subject']; ?></td>

																			<td valign="top"><?php echo $listing['faculty_experience']; ?></td>
																			<td valign="top"><?php echo $listing['faculty_qualification']; ?></td>

																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['faculty_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['faculty_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Faculty
																					null, // Course
																					null, // Module
																					null, // Topic

																					null, // Experience
																					null, // Qualification
																					{ "bSortable": false}
																				],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bFaculty_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Faculty Found !!</td>
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