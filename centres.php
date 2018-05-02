<?php	
	include('includes/application_top.php');

	check_valid_type('ADMIN');

	$arrMessage = array("deleted"=>"Centre has been deleted successfully!!!", 'added'=>'Centre has been added successfully',"edited"=>"Centre  has been edited successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$centre_id = tep_db_prepare_input($_POST['centre_id']);
		$city_id = tep_db_prepare_input($_POST['city_id']);
		$district_id = tep_db_prepare_input($_POST['district_id']);
		$centre_unq_id = tep_db_prepare_input($_POST['centre_unq_id']);
		$centre_name = tep_db_prepare_input($_POST['centre_name']);
		$centre_address = tep_db_prepare_input($_POST['centre_address']);
		$centre_status = tep_db_prepare_input($_POST['centre_status']);
		$centre_status = (isset($centre_status) ? $centre_status : '0');

		$arr_db_values = array(
			'district_id' => $district_id,
			'city_id' => $city_id,
			'centre_id' => $centre_id,
			'centre_unq_id'=> $centre_unq_id,
			'centre_name' => $centre_name,
			'centre_address' => $centre_address,
			'centre_status' => $centre_status
		);

		switch($action){
			case 'add':
				tep_db_perform(TABLE_CENTRES, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_CENTRES, $arr_db_values, "update", "centre_id = '" . $centre_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_CENTRES ." where centre_id = '". $centre_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Centre Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this centre?")){
					objForm.action_type.value = action_type;
					objForm.centre_id.value = int_id;
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

														$info_query_raw = " select centre_id, district_id, city_id, centre_unq_id, centre_name, centre_address, centre_status from " . TABLE_CENTRES . " where centre_id='" . $int_id . "' ";
														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Centre Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Centre Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="centre_id" id="centre_id" value="<?php echo $info['centre_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;District&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
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
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Center Id&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="centre_unq_id" id="centre_unq_id" maxlength="15" value="<?php echo  ($dupError ? $_POST['centre_unq_id'] : $info['centre_unq_id']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Centre Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="centre_name" id="centre_name" title="Please enter centre name" maxlength="150" value="<?php echo  ($dupError ? $_POST['centre_name'] : $info['centre_name']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Centre Address&nbsp;:</td>
																		<td>
																			<input type="text" name="centre_address" id="centre_address" title="Please enter centre address" maxlength="255" value="<?php echo  ($dupError ? $_POST['centre_address'] : $info['centre_address']) ?>">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Centre Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="centre_status" id="centre_status" title="Please select status" class="required">
																				<?php
																					foreach($arr_status as $k_status=>$v_status){
																				?>
																				<option value="<?php echo $k_status;?>" <?php echo($info['centre_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Has MOU been signed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="centre_status" id="centre_status" title="Please select status" class="required">
																				<?php
																					foreach($arr_status as $k_status=>$v_status){
																				?>
																				<option value="<?php echo $k_status;?>" <?php echo($info['centre_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
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
														<td class="arial18BlueN">Centre Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Centre</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = " select cn.centre_id, cn.district_id, cn.centre_unq_id, cn.centre_name, cn.centre_address, cn.centre_status, d.district_name, d.state, c.city_name from ". TABLE_CENTRES ." cn, ". TABLE_CITIES ." c, " . TABLE_DISTRICTS . " d where d.district_id = cn.district_id and c.city_id = cn.city_id order by cn.centre_name";
																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="centre_id" id="centre_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Centre</th>
																		<th>Centre Id</th>
																		<th>State</th>
																		<th>District</th>
																		<th>City</th>
																		<th>Status</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['centre_name']; ?></td>
																			<td valign="top"><?php echo $listing['centre_unq_id']; ?></td>
																			<td valign="top"><?php echo $arr_states[$listing['state']]; ?></td>
																			<td valign="top"><?php echo ucwords(strtolower($listing['district_name'])); ?></td>
																			<td valign="top"><?php echo $listing['city_name']; ?></td>
																			<td valign="top"><?php echo $arr_status[$listing['centre_status']]; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['centre_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['centre_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Centre
																					null, //Center Id
																					null, // State
																					null, // District
																					null, // City
																					null, // Status
																					{ "bSortable": false}
																				],
																				"aaSorting": [[1,'asc'], [2,'asc'], [3,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bCentre_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Centre Found !!</td>
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