<?php	
	include('includes/application_top.php');
	check_valid_type('ADMIN');

	$action = $_POST['action_type'];

	$arrMessage = array("deleted"=>"Company has been deleted successfully!!!");
	
	if(isset($action) && tep_not_null($action))
	{
		$company_id = tep_db_prepare_input($_POST['company_id']);

		switch($action){
			case 'delete':
				tep_db_query("delete from ". TABLE_COMPANIES ." where company_id = '". $company_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Company Management</title>
		<script type="text/javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this company?")){
					objForm.action_type.value = action_type;
					objForm.company_id.value = int_id;
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
													<td class="arial18BlueN">Companies</td>
												</tr>
												<tr>
													<td colspan="2">
														<?php
															$listing_query_raw = "select c.company_id, c.centre_id, c.city_id, c.company_name, c.branch_name, c.company_address, c.company_phone, c.company_sectors, c.company_contact_person, c.company_contact_person_designation, c.has_mou_signed, c.company_email, c.company_mou, d.district_name, d.state, cntr.centre_name from " . TABLE_COMPANIES . " c left join " . TABLE_DISTRICTS . " d on d.district_id = c.district_id, " . TABLE_CENTRES . " cntr where cntr.centre_id = c.centre_id ";
															$listing_query_raw .= " order by c.company_name";

															$listing_query = tep_db_query($listing_query_raw);
														?>
														<form name="frmListing" id="frmListing" method="post">
															<input type="hidden" name="action_type" id="action_type" value="">
															<input type="hidden" name="company_id" id="company_id" value="">
															<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																<thead>
																	<th>State</th>
																	<th>District</th>
																	<th>Centre</th>
																	<th>Sector</th>
																	<th>Company</th>
																	<th>Branch</th>
																	<th>Phone</th>
																	<th>Contact Person Name</th>
																	<th>Contact Person Designation</th>
																	<th>MOU Signed</th>
																	<th>MOU</th>
																	<th>Action</th>
																</thead>
																<tbody>
																<?php
																	if(tep_db_num_rows($listing_query) ){
																		while( $listing = tep_db_fetch_array($listing_query) ){
																			$company_sectors = '';
																			if($listing['company_sectors'] != ''){
																				$section_info_query = tep_db_query("select section_id, section_name from " . TABLE_SECTIONS . " where section_id in (" . $listing['company_sectors'] . ")");
																				while($section_info = tep_db_fetch_array($section_info_query)){
																					if($company_sectors != '')$company_sectors .= ", ";
																					$company_sectors .= $section_info['section_name'];
																				}
																			}
																?>
																	<tr>
																		<td valign="top"><?php echo ($listing['state'] != '' ? ucwords(strtolower($listing['state'])) : '&nbsp;'); ?></td>
																		<td valign="top"><?php echo ($listing['district_name'] != '' ? $listing['district_name'] : '&nbsp;'); ?></td>
																		<td valign="top"><?php echo ($listing['centre_name'] != '' ? $listing['centre_name'] : '&nbsp;'); ?></td>
																		<td valign="top"><?php echo ($company_sectors != '' ? $company_sectors : '&nbsp;'); ?></td>
																		<td valign="top"><?php echo $listing['company_name']; ?></td>
																		<td valign="top"><?php echo $listing['branch_name']; ?></td>
																		<td valign="top"><?php echo $listing['company_phone']; ?></td>

																		<td valign="top"><?php echo $listing['company_contact_person']; ?></td>
																		<td valign="top"><?php echo $listing['company_contact_person_designation']; ?></td>
																		<td valign="top"><?php echo ($listing['has_mou_signed'] == '1' ? 'Yes' : 'No'); ?></td>
																		<td valign="top"><a href="<?php echo DIR_WS_UPLOAD . $listing['company_mou']; ?>" target="_blank"><?php echo $listing['company_mou']; ?></a></td>
																		<td valign="top"><a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['company_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																	</tr>
																<?php
																		}
																?>
																<script type="text/javascript" charset="utf-8">
																	$(document).ready(function() {
																		$('#table_filter').dataTable({
																			"aoColumns": [
																				null, //State
																				null, //District
																				null, //Centre
																				null, //Company
																				null, //Sector
																				null, //Branch
																				null, // Phone

																				null, // Contact Person
																				null, // Designation
																				null, // Is MOU
																				null, // MOU
																				{ "bSortable": false} // Action
																			],
																			"aaSorting": [[1,'asc'], [2,'asc']],
																			 "iDisplayLength": 300,
																			"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																			"bCompany_idSave": false,
																			"bAutoWidth": false
																		});
																	});
																</script>
																<?php
																	}else{
																?>
																	<tr>
																			<td align="center" colspan="6" class="verdana11Red">No Company Found !!</td>
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