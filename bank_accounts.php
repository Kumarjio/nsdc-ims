<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("deleted"=>"Bank Account has been deleted successfully!!!", 'added'=>'Bank Account has been added successfully',"edited"=>"Bank Account has been updated successfully");

	$action = $_POST['action_type'];

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		$bank_account_id = tep_db_prepare_input($_POST['bank_account_id']);
		$centre_id = tep_db_prepare_input($_POST['centre_id']);
		$bank_name = tep_db_prepare_input($_POST['bank_name']);
		$branch_name = tep_db_prepare_input($_POST['branch_name']);
		$bank_account_no = tep_db_prepare_input($_POST['bank_account_no']);
 		$bank_address = tep_db_prepare_input($_POST['bank_address']);
		$bank_ifsc_code = tep_db_prepare_input($_POST['bank_ifsc_code']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'bank_name' => $bank_name,
			'branch_name' => $branch_name,
			'bank_account_no' => $bank_account_no,
			'bank_address' => $bank_address,
			'bank_ifsc_code' => $bank_ifsc_code
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_BANK_ACCOUNTS, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_BANK_ACCOUNTS, $arr_db_values, "update", "bank_account_id = '" . $bank_account_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_BANK_ACCOUNTS ." where bank_account_id = '". $bank_account_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Bank Account Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this account?")){
					objForm.action_type.value = action_type;
					objForm.bank_account_id.value = int_id;
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

														$info_query_raw = " select bank_account_id, centre_id, bank_name, branch_name, bank_account_no, bank_address, bank_ifsc_code from " . TABLE_BANK_ACCOUNTS . " where bank_account_id = '" . $int_id . "' ";
														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Bank Account Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Bank Account Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="bank_account_id" id="bank_account_id" value="<?php echo $info['bank_account_id']; ?>"> 
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
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Bank Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="bank_name" id="bank_name" title="Please enter bank name" maxlength="50" value="<?php echo  ($dupError ? $_POST['bank_name'] : $info['bank_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Branch Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="branch_name" id="branch_name" title="Please enter branch name" maxlength="50" value="<?php echo  ($dupError ? $_POST['branch_name'] : $info['branch_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Account No.&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="bank_account_no" id="bank_account_no" title="Please enter branch name" maxlength="30" value="<?php echo  ($dupError ? $_POST['bank_account_no'] : $info['bank_account_no']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Bank Address&nbsp;:</td>
																		<td>
																			<input type="text" name="bank_address" id="bank_address" title="Please enter branch address" maxlength="255" value="<?php echo  ($dupError ? $_POST['bank_address'] : $info['bank_address']) ?>">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;IFSC Code&nbsp;:</td>
																		<td>
																			<input type="text" name="bank_ifsc_code" id="bank_ifsc_code" title="Please enter IFSC code." maxlength="10" value="<?php echo  ($dupError ? $_POST['bank_ifsc_code'] : $info['bank_ifsc_code']) ?>">
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
														<td class="arial18BlueN">Bank Account Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Account</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select ba.bank_account_id, ba.bank_name, ba.branch_name, ba.bank_account_no, c.centre_name from " . TABLE_BANK_ACCOUNTS . " ba, " . TABLE_CENTRES . " c where 1 and c.centre_id = ba.centre_id ";
																$listing_query_raw .= " order by c.centre_name";

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="bank_account_id" id="bank_account_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Centre</th>
																		<th>Account Number</th>
																		<th>Bank</th>
																		<th>Branch</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['centre_name']; ?></td>
																			<td valign="top"><?php echo $listing['bank_account_no']; ?></td>
																			<td valign="top"><?php echo $listing['bank_name']; ?></td>
																			<td valign="top"><?php echo $listing['branch_name']; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['bank_account_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['bank_account_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Centre
																					null, // Account No
																					null, // Bank
																					null, // Branch
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
																				<td align="center" colspan="6" class="verdana11Red">No Acccount Found !!</td>
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