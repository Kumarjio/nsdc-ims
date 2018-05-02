<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$action = $_POST['action_type'];

	$arrMessage = array("deleted"=>"Faculty has been deleted successfully!!!");
	
	if(isset($action) && tep_not_null($action))
	{
		$faculty_id = tep_db_prepare_input($_POST['faculty_id']);

		switch($action){
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
		<script type="text/javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this faculty?")){
					objForm.action_type.value = action_type;
					objForm.faculty_id.value = int_id;
					objForm.submit();
				}
			}
		//-->
		</script>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
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
													<td class="arial18BlueN">Faculties</td>
												</tr>
												<tr>
													<td colspan="2">
														<?php
															$listing_query_raw = "select f.faculty_id, f.faculty_unq_id, f.faculty_first_name, f.faculty_last_name, f.faculty_experience, f.faculty_qualification, f.faculty_photo, f.faculty_cv, c.course_name, s.subject, m.module, cntr.centre_name from " . TABLE_FACULTIES . " f, " . TABLE_COURSES . " c, " . TABLE_MODULES . " m, " . TABLE_SUBJECTS . " s, " . TABLE_CENTRES . " cntr  where m.module_id = f.module_id and c.course_id = f.course_id and s.subject_id = f.subject_id and cntr.centre_id = f.centre_id ";
															$listing_query_raw .= " order by f.faculty_first_name";

															$listing_query = tep_db_query($listing_query_raw);
														?>
														<form name="frmListing" id="frmListing" method="post">
															<input type="hidden" name="action_type" id="action_type" value="">
															<input type="hidden" name="faculty_id" id="faculty_id" value="">
															<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																<thead>
																	<th>Centre</th>
																	<th>Faculty Id</th>
																	<th>Faculty</th>
																	<th>Course</th>
																	<th>Module</th>
																	<th>Topic</th>
																	<th>Years of Experience</th>
																	<th>Qualification</th>
																	<th>Photo</th>
																	<th>CV</th>
																	<th>Action</th>
																</thead>
																<tbody>
																<?php
																	if(tep_db_num_rows($listing_query) ){
																		while( $listing = tep_db_fetch_array($listing_query) ){
																?>
																	<tr>
																		<td valign="top"><?php echo ($listing['centre_name'] != '' ? $listing['centre_name'] : '&nbsp;'); ?></td>
																		<td valign="top"><?php echo $listing['faculty_unq_id']; ?></td>
																		<td valign="top"><?php echo $listing['faculty_first_name'] . ' ' . $listing['faculty_last_name']; ?></td>
																		<td valign="top"><?php echo $listing['course_name']; ?></td>
																		<td valign="top"><?php echo $listing['module']; ?></td>
																		<td valign="top"><?php echo $listing['subject']; ?></td>

																		<td valign="top"><?php echo $listing['faculty_experience']; ?></td>
																		<td valign="top"><?php echo $listing['faculty_qualification']; ?></td>

																		<td valign="top"><a href="<?php echo DIR_WS_UPLOAD . $listing['faculty_photo']; ?>" target="_blank"><?php echo $listing['faculty_photo']; ?></a></td>

																		<td valign="top"><a href="<?php echo DIR_WS_UPLOAD . $listing['faculty_cv']; ?>" target="_blank"><?php echo $listing['faculty_cv']; ?></a></td>

																		<td valign="top"><a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['faculty_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																	</tr>
																<?php
																		}
																?>
																<script type="text/javascript" charset="utf-8">
																	$(document).ready(function() {
																		$('#table_filter').dataTable({
																			"aoColumns": [
																				null, //Centre
																				null, //Faculty Id
																				null, //Faculty
																				null, // Course
																				null, // Module
																				null, // Topic

																				null, // Experience
																				null, // Qualification

																				null, // Photo
																				null, // CV

																				{ "bSortable": false} // Action
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