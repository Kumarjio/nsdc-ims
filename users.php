<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("deleted"=>"User has been deleted successfully!!!", 'added'=>'User has been added successfully',"edited"=>"User has been updated successfully");

	$action = $_POST['action_type'];

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		$adm_id = tep_db_prepare_input($_POST['adm_id']);
		$centre_id = tep_db_prepare_input($_POST['centre_id']);
		$adm_username = tep_db_prepare_input($_POST['adm_username']);
		$adm_password = tep_db_prepare_input($_POST['adm_password']);
		$adm_name = tep_db_prepare_input($_POST['adm_name']);
 		$adm_email = tep_db_prepare_input($_POST['adm_email']);
		$adm_mobile = tep_db_prepare_input($_POST['adm_mobile']);
		$adm_status = tep_db_prepare_input($_POST['adm_status']);
		$adm_type = 'CENTRE';

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'adm_username' => $adm_username,
			'adm_password' => $adm_password,
			'adm_name' => $adm_name,
			'adm_email' => $adm_email,
			'adm_mobile' => $adm_mobile,
			'adm_status' => $adm_status,
			'adm_type' => $adm_type
		);

		switch($action){
			case 'add':
				$arr_db_values['created_date'] = 'now()';

				tep_db_perform(TABLE_ADMIN_MST, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_ADMIN_MST, $arr_db_values, "update", "adm_id = '" . $adm_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_ADMIN_MST ." where adm_id = '". $adm_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: User Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this user?")){
					objForm.action_type.value = action_type;
					objForm.adm_id.value = int_id;
					objForm.submit();
				}
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

														$info_query_raw = " select u.adm_id, u.centre_id, u.adm_username, u.adm_password, u.adm_name, u.adm_email, u.adm_mobile, u.adm_status, u.adm_type, u.created_date from " . TABLE_ADMIN_MST . " u where u.adm_id='" . $int_id . "' ";
														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">User Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">User Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="adm_id" id="adm_id" value="<?php echo $info['adm_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Centre&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="centre_id" id="centre_id" title="Please choose centre" class="required">
																				<option value="">Please choose</option>
																				<?php
																					$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " order by centre_name";
																					$centre_query = tep_db_query($centre_query_raw);
																					
																					while($centre = tep_db_fetch_array($centre_query)){
																				?>
																				<option value="<?php echo $centre['centre_id'];?>" <?php echo($info['centre_id'] == $centre['centre_id'] ? 'selected="selected"' : '');?>><?php echo $centre['centre_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Login Username&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="adm_username" id="adm_username" title="Please enter login username" maxlength="50" value="<?php echo  ($dupError ? $_POST['adm_username'] : $info['adm_username']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Login Password&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="adm_password" id="adm_password" title="Please enter login password" maxlength="50" value="<?php echo  ($dupError ? $_POST['adm_password'] : $info['adm_password']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;User Full Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="adm_name" id="adm_name" title="Please enter user full name" maxlength="100" value="<?php echo  ($dupError ? $_POST['adm_name'] : $info['adm_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;User Email&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="adm_email" id="adm_email" title="Please enter user email" maxlength="100" value="<?php echo  ($dupError ? $_POST['adm_email'] : $info['adm_email']) ?>" class="required email">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;User Mobile No.&nbsp;:</td>
																		<td>
																			<input type="text" name="adm_mobile" id="adm_mobile" title="Please enter user mobile no." maxlength="50" value="<?php echo  ($dupError ? $_POST['adm_mobile'] : $info['adm_mobile']) ?>">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;User Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="adm_status" id="adm_status" title="Please choose user status" class="required">
																				<?php
																					foreach($arr_status as $k_status=>$v_status){
																				?>
																				<option value="<?php echo $k_status;?>" <?php echo($info['adm_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
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
														<td class="arial18BlueN">User Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add User</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select u.adm_id, u.centre_id, u.adm_username, u.adm_password, u.adm_name, u.adm_email, u.adm_mobile, u.adm_status, u.adm_type, u.created_date, c.centre_name from " . TABLE_ADMIN_MST . " u, " . TABLE_CENTRES . " c where 1 and c.centre_id = u.centre_id ";
																$listing_query_raw .= " order by adm_name";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="adm_id" id="adm_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Name</th>
																		<th>Email</th>
																		<th>Centre</th>
																		<th>Status</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['adm_name']; ?></td>
																			<td valign="top"><?php echo $listing['adm_email']; ?></td>
																			<td valign="top"><?php echo $listing['centre_name']; ?></td>
																			<td valign="top"><?php echo $arr_status[$listing['adm_status']]; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['adm_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['adm_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Name
																					null, // Email
																					null, // Centre
																					null, // Status
																					{ "bSortable": false}
																				],
																				"aaSorting": [[1,'asc'], [2,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bUser_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No User Found !!</td>
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