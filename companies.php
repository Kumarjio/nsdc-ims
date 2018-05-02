<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Company has been deleted successfully!!!", 'added'=>'Company has been added successfully',"edited"=>"Company  has been updated successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$company_id = tep_db_prepare_input($_POST['company_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$company_name = tep_db_prepare_input($_POST['company_name']);
		$branch_name = tep_db_prepare_input($_POST['branch_name']);

		$district_id = tep_db_prepare_input($_POST['district_id']);
		$city_id = tep_db_prepare_input($_POST['city_id']);

		$company_address = tep_db_prepare_input($_POST['company_address']);
		$company_pincode = tep_db_prepare_input($_POST['company_pincode']);
 		$company_phone = tep_db_prepare_input($_POST['company_phone']);
		$company_phone_std = tep_db_prepare_input($_POST['company_phone_std']);
		$company_contact_person = tep_db_prepare_input($_POST['company_contact_person']);
		$company_contact_person_designation = tep_db_prepare_input($_POST['company_contact_person_designation']);
		$company_email = tep_db_prepare_input($_POST['company_email']);
		$has_mou_signed = tep_db_prepare_input($_POST['has_mou_signed']);

		$company_sectors = implode(",", $_POST['company_sectors']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'company_name' => $company_name,
			'branch_name' => $branch_name,
			'district_id' => $district_id,
			'city_id' => $city_id,
			'company_sectors' => $company_sectors,
			'company_address' => $company_address,
			'company_pincode' => $company_pincode,
			'company_phone_std' => $company_phone_std,
			'company_phone' => $company_phone,
			'company_contact_person' => $company_contact_person,
			'company_contact_person_designation' => $company_contact_person_designation,
			'has_mou_signed' => $has_mou_signed,
			'company_email' => $company_email
		);

		if($_FILES['company_mou']['name'] != ''){

			$ext = get_extension($_FILES['company_mou']['name']);
			$src = $_FILES['company_mou']['tmp_name'];

			$dest_filename = 'mou_'. time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['company_mou'] = $dest_filename;
			}
		}

		switch($action){
			case 'add':
				tep_db_perform(TABLE_COMPANIES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_COMPANIES, $arr_db_values, "update", "company_id = '" . $company_id . "'");
				$msg = 'edited';
			break;

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
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this company?")){
					objForm.action_type.value = action_type;
					objForm.company_id.value = int_id;
					objForm.submit();
				}
			}

			function get_city(default_city){
				var disctrict = $('#district_id').val();

				$('#city_id').empty();
				$('#city_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_city&disctrict='+disctrict+'&dc='+default_city,
					type: 'POST',
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_city == values.city_id){
								$('#city_id').append($("<option></option>").attr("value",values.city_id).attr('selected', 'selected').text(values.city_name));
							}else{
								$('#city_id').append($("<option></option>").attr("value",values.city_id).text(values.city_name));
							}
						})
					}
				});
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
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

														$info_query_raw = " select company_id, district_id, centre_id, city_id, company_name, company_sectors, branch_name, company_address, company_phone_std, company_phone, company_contact_person, company_contact_person_designation, company_email, company_pincode, has_mou_signed, company_mou from " . TABLE_COMPANIES . " where company_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Company Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Company Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="company_id" id="company_id" value="<?php echo $info['company_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="20%" class="arial12LGrayBold" valign="top" align="right">&nbsp;District&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="district_id" id="district_id" title="Please select district" class="required" onchange="javascript: get_city('');">
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
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;City&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="city_id" id="city_id" title="Please select city" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<script type="text/javascript">
																	<!--
																		get_city('<?php echo $info['city_id'] ?>');
																	//-->
																	</script>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_name" id="company_name" title="Please enter company name" maxlength="255" value="<?php echo  ($dupError ? $_POST['company_name'] : $info['company_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Sector&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray" >
																			<?php
																				$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																				$section_query = tep_db_query($section_query_raw);

																				$sectors_array = array();
																				$sectors_array = explode(",", $info['company_sectors']);
																				
																				while($section = tep_db_fetch_array($section_query)){
																			?>
																			<input type="checkbox" name="company_sectors[]" value="<?php echo $section['section_id'];?>" <?php echo((is_array($sectors_array) && in_array($section['section_id'], $sectors_array)) ? 'checked="checked"' : '');?>><?php echo $section['section_name'];?>
																			<?php } ?>
																			<!-- <input type="checkbox" name="company_sectors[]" value="99999" <?php //echo( (is_array($sectors_array) && in_array(99999, $sectors_array)) ? 'checked="checked"' : '');?>>Others -->
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Branch Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="branch_name" id="branch_name" title="Please enter branch name" maxlength="255" value="<?php echo  ($dupError ? $_POST['branch_name'] : $info['branch_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Address&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<textarea name="company_address" id="company_address" title="Please enter company address" class="required" cols="40" rows="8"><?php echo $info['company_address'];?></textarea>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Pincode&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_pincode" id="company_pincode" title="Please enter company pincode" maxlength="6" value="<?php echo  ($dupError ? $_POST['company_pincode'] : $info['company_pincode']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Phone&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_phone_std" id="company_phone_std" title="Please enter phone std" maxlength="5" value="<?php echo  ($dupError ? $_POST['company_phone_std'] : $info['company_phone_std']) ?>" style="width:50px;" class="number">&nbsp;-&nbsp;<input type="text" name="company_phone" id="company_phone" title="Please enter company phone" maxlength="50" value="<?php echo  ($dupError ? $_POST['company_phone'] : $info['company_phone']) ?>" class="required number">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Contact Person&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_contact_person" id="company_contact_person" title="Please enter contact person name" maxlength="100" value="<?php echo  ($dupError ? $_POST['company_contact_person'] : $info['company_contact_person']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Contact Person Designation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_contact_person_designation" id="company_contact_person_designation" title="Please enter contact person designation" maxlength="100" value="<?php echo  ($dupError ? $_POST['company_contact_person_designation'] : $info['company_contact_person_designation']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Email&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="company_email" id="company_email" title="Please enter company email" maxlength="150" value="<?php echo  ($dupError ? $_POST['company_email'] : $info['company_email']) ?>" class="required email">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Is MOU signed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td class="arial12LGray">
																			<?php foreach($arr_status as $k_status=>$v_status){?>
																				<input type="radio" name="has_mou_signed" id="has_mou_signed" value="<?php echo $k_status;?>" <?php echo ($info['has_mou_signed'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('has_mou_signed', 'company_mou');">&nbsp;<?php echo $v_status;?>&nbsp;
																			<?php } ?>
																		</td>
																	</tr>
																	<tr class="company_mou">
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Copy of MOU&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<?php if(isset($info['company_mou']) && $info['company_mou'] != ''){ ?>
																				<a href="<?php echo DIR_WS_UPLOAD . $info['company_mou']; ?>" target="_blank"><?php echo $info['company_mou']; ?></a><br/><br/>
																			<?php } ?>
																			<input type="file" name="company_mou" id="company_mou" style="border:none;">
																		</td>
																	</tr>
																	<script type="text/javascript">
																	<!--
																		toggle_element('has_mou_signed', 'company_mou');
																	//-->
																	</script>
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
														<td class="arial18BlueN">Company Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=add"); ?>" class="arial14LGrayBold">Add Company</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select c.company_id, c.centre_id, c.city_id, c.company_name, c.branch_name, c.company_address, c.company_phone, c.company_sectors, c.company_contact_person, c.company_contact_person_designation, c.has_mou_signed, c.company_email, d.district_name, d.state from " . TABLE_COMPANIES . " c left join " . TABLE_DISTRICTS . " d on d.district_id = c.district_id where 1 ";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and c.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

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
																		<th>Company</th>
																		<th>Sector</th>
																		<th>Branch</th>
																		<th>Phone</th>
																		<th>Contact Person Name</th>
																		<th>Contact Person Designation</th>
																		<th>MOU Signed</th>
																		<th width="10%">Action</th>
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
																			<td valign="top"><?php echo ($listing['company_name'] != '' ? $listing['company_name'] : '&nbsp;'); ?></td>
																			<td valign="top"><?php echo ($company_sectors != '' ? $company_sectors : '&nbsp;'); ?></td>
																			<td valign="top"><?php echo $listing['branch_name']; ?></td>
																			<td valign="top"><?php echo $listing['company_phone']; ?></td>

																			<td valign="top"><?php echo $listing['company_contact_person']; ?></td>
																			<td valign="top"><?php echo $listing['company_contact_person_designation']; ?></td>
																			<td valign="top"><?php echo ($listing['has_mou_signed'] == '1' ? 'Yes' : 'No'); ?></td>

																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['company_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['company_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
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
																					null, //Company
																					null, //Sector
																					null, //Branch
																					null, // Phone

																					null, // Contact Person
																					null, // Designation
																					null, // MOU
																					{ "bSortable": false}
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