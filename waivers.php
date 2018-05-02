<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("deleted"=>"Waiver has been deleted successfully!!!", 'added'=>'Waiver has been added successfully',"edited"=>"Waiver  has been edited successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$waiver_id = tep_db_prepare_input($_POST['waiver_id']);
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$waiver_title = tep_db_prepare_input($_POST['waiver_title']);
		$waiver_desc = tep_db_prepare_input($_POST['waiver_desc']);
		$waiver_amount = tep_db_prepare_input($_POST['waiver_amount']);
		$waiver_expiry = tep_db_prepare_input($_POST['waiver_expiry']);
		$waiver_expiry = input_valid_date($waiver_expiry);

		$arr_db_values = array(
			'course_id' => $course_id,
			'waiver_title' => $waiver_title,
			'waiver_desc' => $waiver_desc,
			'waiver_amount' => $waiver_amount,
			'waiver_expiry' => $waiver_expiry
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_COURSES_WAIVERS, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_COURSES_WAIVERS, $arr_db_values, "update", "waiver_id = '" . $waiver_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_COURSES_WAIVERS ." where waiver_id = '". $waiver_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Waiver Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this waiver?")){
					objForm.action_type.value = action_type;
					objForm.waiver_id.value = int_id;
					objForm.submit();
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#waiver_expiry').datepicker({
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

														$info_query_raw = " select waiver_id, course_id, waiver_title, waiver_desc, waiver_amount, date_format(waiver_expiry, '%d-%m-%Y') as waiver_expiry from " . TABLE_COURSES_WAIVERS . " where waiver_id = '" . $int_id . "' ";
														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Waiver Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Waiver Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="waiver_id" id="waiver_id" value="<?php echo $info['waiver_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_id" id="course_id" title="Please select course" class="required">
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
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Waiver Title&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="waiver_title" id="waiver_title" title="Please enter waiver title" maxlength="255" value="<?php echo  ($dupError ? $_POST['waiver_title'] : $info['waiver_title']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Waiver Description&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<textarea name="waiver_desc" id="waiver_desc" title="Please enter waiver description" cols="30" rows="10" maxlength="255"><?php echo  ($dupError ? $_POST['waiver_desc'] : $info['waiver_desc']) ?></textarea>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Waiver Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="waiver_amount" id="waiver_amount" title="Please enter waiver amount" value="<?php echo  ($dupError ? $_POST['waiver_amount'] : $info['waiver_amount']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Waiver Expiry&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="waiver_expiry" id="waiver_expiry" title="Please enter waiver expiry" value="<?php echo  ($dupError ? $_POST['waiver_expiry'] : $info['waiver_expiry']) ?>" class="required">
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
														<td class="arial18BlueN">Waiver Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Waiver</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = " select w.waiver_id, w.waiver_title, w.waiver_amount, date_format(w.waiver_expiry, '%d %b %Y') as waiver_expiry, c.course_name from ". TABLE_COURSES_WAIVERS ." w, ". TABLE_COURSES ." c where c.course_id = w.course_id order by w.waiver_title";
																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="waiver_id" id="waiver_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Waiver</th>
																		<th>Course</th>
																		<th>Amount</th>
																		<th>Expiry</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['waiver_title']; ?></td>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo $listing['waiver_amount']; ?></td>
																			<td valign="top"><?php echo $listing['waiver_expiry']; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['waiver_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['waiver_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Waiver title
																					null, // Course
																					null, // Amount
																					null, // Expiry
																					{ "bSortable": false}
																				],
																				"aaSorting": [[1,'asc'], [2,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bSubject_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Waiver Found !!</td>
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