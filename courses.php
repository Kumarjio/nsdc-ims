<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("deleted"=>"Course has been deleted successfully!!!", 'added'=>'Course has been added successfully',"edited"=>"Course  has been edited successfully");

	$action = $_POST['action_type'];

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$section_id = tep_db_prepare_input($_POST['section_id']);
		$course_name = tep_db_prepare_input($_POST['course_name']);
		$course_code = tep_db_prepare_input($_POST['course_code']);
		$course_unq_id = tep_db_prepare_input($_POST['course_unq_id']);
		$course_desc = $_POST['course_desc'];
		$course_duration = tep_db_prepare_input($_POST['course_duration']);
		$base_fee = tep_db_prepare_input($_POST['base_fee']);
		$course_fee = tep_db_prepare_input($_POST['course_fee']);
		$tax_amount = tep_db_prepare_input($_POST['tax_amount']);
		$course_installments = tep_db_prepare_input($_POST['course_installments']);
		$course_instl_duration = tep_db_prepare_input($_POST['course_instl_duration']);
		$course_status = tep_db_prepare_input($_POST['course_status']);
		$course_status = (isset($course_status) ? $course_status : '0');

		$arr_db_values = array(
			'section_id' => $section_id,
			'course_name' => $course_name,
			'course_unq_id' => $course_unq_id,
			'course_code' => $course_code,
			'course_desc' => $course_desc,
			'course_duration' => $course_duration,
			'course_fee' => $course_fee,
			'base_fee' => $base_fee,
			'tax_amount' => $tax_amount,
			'course_installments' => $course_installments,
			'course_instl_duration' => $course_instl_duration,
			'course_status' => $course_status
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_COURSES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_COURSES, $arr_db_values, "update", "course_id = '" . $course_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_COURSES ." where course_id = '". $course_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Course Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>


		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this course?")){
					objForm.action_type.value = action_type;
					objForm.course_id.value = int_id;
					objForm.submit();
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();
			});

			function calc_total_fee(){
				var base_fee = $('input[name="base_fee"]').val();
				var tax_amount = $('input[name="tax_amount"]').val();

				base_fee = eval(base_fee);
				tax_amount = eval(tax_amount);

				base_fee = (!isNaN(base_fee) ? base_fee : 0 );
				tax_amount = (!isNaN(tax_amount) ? tax_amount : 0 );

				var course_fee = base_fee + tax_amount;

				$('input[name="course_fee"]').val(course_fee);
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
											<?php
												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" )
												{
													if($_GET['actionType'] == "edit"){
														$int_id = $_GET['int_id'];

														$info_query_raw = " select course_id, section_id, course_name, course_desc, course_unq_id, course_code, course_duration, course_fee, tax_amount, base_fee, course_installments, course_instl_duration, course_status from " . TABLE_COURSES . " where course_id='" . $int_id . "' ";
														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Course Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Course Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="course_id" id="course_id" value="<?php echo $info['course_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Sector&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="section_id" id="section_id" title="Please select sector" class="required">
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
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="course_name" id="course_name" title="Please enter course name" maxlength="150" value="<?php echo  ($dupError ? $_POST['course_name'] : $info['course_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Id&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="course_unq_id" id="course_unq_id" maxlength="15" value="<?php echo  ($dupError ? $_POST['course_unq_id'] : $info['course_unq_id']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="course_code" id="course_code" title="Please enter course code" maxlength="50" value="<?php echo  ($dupError ? $_POST['course_code'] : $info['course_code']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Description&nbsp;:</td>
																		<td>
																			<?php
																				$CKEditor = new CKEditor('ckeditor/') ;
																				$CKEditor->editor("course_desc", stripslashes($info['course_desc']));
																			?>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Duration&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="course_duration" id="course_duration" title="Please enter course duration" maxlength="50" value="<?php echo  ($dupError ? $_POST['course_duration'] : $info['course_duration']) ?>" class="required number">&nbsp;Hour(s)
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Base Fees&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="base_fee" id="base_fee" title="Please enter base fees" maxlength="50" onblur="calc_total_fee();" value="<?php echo  ($dupError ? $_POST['base_fee'] : $info['base_fee']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Tax Amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="tax_amount" id="tax_amount" title="Please enter tax amount" maxlength="50" onblur="calc_total_fee();" value="<?php echo  ($dupError ? $_POST['tax_amount'] : $info['tax_amount']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Total Fees&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="course_fee" id="course_fee" title="Please enter course fees" maxlength="50" value="<?php echo  ($dupError ? $_POST['course_fee'] : $info['course_fee']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Installment&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="course_installments" id="course_installments" title="Please enter course installment" maxlength="50" value="<?php echo  ($dupError ? $_POST['course_installments'] : $info['course_installments']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Installment Duration&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<input type="text" name="course_instl_duration" id="course_instl_duration" title="Please enter course installment duartion" value="<?php echo  ($dupError ? $_POST['course_instl_duration'] : $info['course_instl_duration']) ?>" size="8" class="required number"> Days
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Course Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_status" id="course_status" title="Please select status" class="required">
																				<?php
																					foreach($arr_status as $k_status=>$v_status){
																				?>
																				<option value="<?php echo $k_status;?>" <?php echo($info['course_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																				<?php } ?>
																			</select>
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
														<td class="arial18BlueN">Course Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Course</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = " select c.course_id, c.section_id, c.course_name, c.course_unq_id, c.course_code, c.course_fee, c.course_status, c.course_duration, s.section_name from ". TABLE_COURSES ." c, ". TABLE_SECTIONS ." s where c.section_id = s.section_id order by c.course_name";
																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="course_id" id="course_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Course</th>
																		<th>Course Id</th>
																		<th>Sector</th>
																		<th>Code</th>
																		<th>Duration</th>
																		<th>Fee</th>
																		<th>Status</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo $listing['course_unq_id']; ?></td>
																			<td valign="top"><?php echo $listing['section_name']; ?></td>
																			<td valign="top"><?php echo $listing['course_code']; ?></td>
																			<td valign="top"><?php echo $listing['course_duration']; ?>&nbsp;Hour(s)</td>
																			<td valign="top"><?php echo display_currency($listing['course_fee']);		?></td>
																			<td valign="top"><?php echo $arr_status[$listing['course_status']]; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['course_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['course_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Course
																					null, //Course Id
																					null, // Sector
																					null, // Code
																					null, // Duration
																					null, // Status
																					null, // Fee
																					{ "bSortable": false}
																				],
																				"aaSorting": [[1,'asc'], [2,'asc'], [3,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bCourse_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Course Found !!</td>
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