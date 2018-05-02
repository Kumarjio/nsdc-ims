<?php	
	include('includes/application_top.php');

	$arrAlerts = array("multiDelete" => "Company(s) selected are deleted successfully","singleDelete"=>"Selected company deleted successfully","dupCompEmail"=>"Company email id already exists",'CompAdded'=>'Company added successfully',"compEdited"=>"Company edited successfully");
	
	if(isset($_POST) && $_POST['hidSubmit'] == "1")
	{
		$strCompanyId = tep_db_input(tep_db_prepare_input($_POST['main_company_id']));
		$strCompanyName = tep_db_input(tep_db_prepare_input($_POST['txtCompanyName']));
		$strPost = tep_db_input(tep_db_prepare_input($_POST['txtPost']));
		$strContactPerson = tep_db_input(tep_db_prepare_input($_POST['txtContactPerson']));
		$strQualification = tep_db_input(tep_db_prepare_input($_POST['txtQualification']));
		$strContactNo = tep_db_input(tep_db_prepare_input($_POST['txtContactNo']));
		$strEmail = tep_db_input(tep_db_prepare_input($_POST['txtEmail']));
		$strPanNo = tep_db_input(tep_db_prepare_input($_POST['txtPanNo']));
		$strServiceTax = tep_db_input(tep_db_prepare_input($_POST['txtServiceTaxNo']));
		$strServiceName = tep_db_input(tep_db_prepare_input($_POST['txtServiceName']));
		$strAddress = tep_db_input(tep_db_prepare_input($_POST['txtarAddress']));
		
		if($_POST['hidActionMode'] == 'add_company')
		{
			$rsDupRecord = tep_db_query(" select comp_email_id from " . TABLE_MAIN_COMPANY_MST . " where comp_email_id = '" . $strEmail . "'");
			if(!tep_db_num_rows($rsDupRecord))
			{
				$qryAdd = " insert into " . TABLE_MAIN_COMPANY_MST . " ( comp_name, comp_post, comp_contact_person, comp_qualification, comp_contact_no, comp_email_id, comp_pan_no, comp_service_tax_no, comp_service_name, comp_address )  values ( '" . $strCompanyName . "','" . $strPost . "','" . $strContactPerson . "','" . $strQualification . "','" . $strContactNo . "','" . $strEmail . "','" . $strPanNo . "','" . $strServiceTax . "','" . $strServiceName . "','". $strAddress ."')";
				$rsAdd = tep_db_query($qryAdd);
				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','main_company_id','actionType')) . 'msg=CompAdded' ));
			}
			else
			{				
				$dupError = true;
				$_GET['msg'] = "dupCompEmail";
			}
		}
		else if($_POST['hidActionMode'] == 'edit_company')
		{
			$rsDupRecord = tep_db_query(" select comp_email_id from " . TABLE_MAIN_COMPANY_MST . " where comp_email_id = '" . $strEmail . "' and main_company_id != '" . $strCompanyId . "'");
			if(!tep_db_num_rows($rsDupRecord))
			{
				$qryUpdateDetails = " update " . TABLE_MAIN_COMPANY_MST . " set  comp_name = '". $strCompanyName ."',comp_post = '". $strPost ."', comp_contact_person = '". $strContactPerson ."', comp_qualification = '". $strQualification ."', comp_contact_no = '". $strContactNo ."',  	comp_email_id = '". $strEmail ."', comp_pan_no = '". $strPanNo ."',comp_service_tax_no = '". $strServiceTax ."', comp_service_name = '". $strServiceName ."', comp_address = '". $strAddress ."' where main_company_id = '". $strCompanyId ."'";

				$rsUpdateDetails = tep_db_query($qryUpdateDetails);
				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','main_company_id','actionType')) . 'msg=compEdited' ));

			}
			else
			{	
				$dupError = true;
				$_GET['msg'] = "dupCompEmail";
			}
		}		
		else if( $_POST['hidActionMode'] == 'delete_multiple' && is_array($_POST['chkListing']) ){

			$arrListing = tep_db_prepare_input($_POST['chkListing']);
			if(is_array($arrListing)){
				reset($arrListing);
				while( list($strKey, $intValue) = each($arrListing) ){
					$qryDeleteRecord = "delete from ". TABLE_MAIN_COMPANY_MST ." where main_company_id = '". $intValue ."' ";
					$rsDeleteRecord= tep_db_query($qryDeleteRecord);		
				}
				
			}
			tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','main_company_id','actionType')) . 'msg=multiDelete' ));
		}
		else if($_POST['hidActionMode'] == 'delete_single'){	
			$strCompany_id = tep_db_prepare_input($_POST['main_company_id']);
			$qryDeleteCompany = "delete from ". TABLE_MAIN_COMPANY_MST ." where main_company_id = '". $strCompany_id ."' ";
			$rsDeleteCompany = tep_db_query($qryDeleteCompany);		
			tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','main_company_id','actionType')) . 'msg=singleDelete' ));
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Main Company Management</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function deleteSelected(objForm, strActionMode , company_id)
			{
				if(strActionMode=='delete_multiple'){
					if(!chkElementSelected(objForm, 'checkbox', 'chkListing')){
						alert("Please select atleast one company");
					}
					else{
						if(confirm("Are you sure you want to delete the selected company(s)?")){
							objForm.hidActionMode.value = strActionMode;
							objForm.submit();
						}
					}
				}else if(strActionMode=='delete_single'){
					if(confirm("Are you sure you want to delete this company?")){
						objForm.hidActionMode.value = strActionMode;
						objForm.main_company_id.value = company_id;
						objForm.submit();
					}
				}				
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
						<tr>
							<td class="backgroundBgMain" valign="top">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									<tr>
										<td valign="top">
											<?php
												if( $_GET['actionType'] == "add_company" || $_GET['actionType'] == "edit_company" )
												{
													if($_GET['actionType'] == "edit_company"){
														$strCompany_id = $_GET['main_company_id'];
														$qryGetDetails = " select main_company_id, comp_name,comp_post, comp_contact_person, comp_qualification, comp_contact_no,comp_email_id, comp_pan_no,comp_service_tax_no, comp_service_name, comp_address from " . TABLE_MAIN_COMPANY_MST . " where main_company_id='" . $strCompany_id . "' ";
														$rsDetails = tep_db_query($qryGetDetails);
														$arrDetails = tep_db_fetch_array($rsDetails);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Main Company Management</td>
														<td align="right">&nbsp;</td>
													</tr>
													<?php
													if($_GET['msg']){
													?>
														<tr>
															<td colspan="2" class="verdana11Red"><?php echo $arrAlerts[$_GET['msg']] ; ?></td>
														</tr>
													<?php
													}
													?>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" onsubmit="javascript : return checkDetails(document.frmDetails,'<?php echo $_GET['actionType']; ?>');" method="post" enctype="multipart/form-data">
																<input type="hidden" name="hidSubmit" id="hidSubmit" value="1"> 
																<input type="hidden" name="hidActionMode" id="hidActionMode" value="">
																<input type="hidden" name="main_company_id" id="main_company_id" value="<?php echo $arrDetails['main_company_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<tr>
																		<td width="20%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Company Name&nbsp;:</td>
																		<td>
																			<input type="text" name="txtCompanyName" id="txtCompanyName" title="Enter company name" maxlength="30" size="25" value="<?php echo  ($dupError ? $_POST['txtCompanyName'] : $arrDetails['comp_name']) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Post&nbsp;:</td>
																		<td>
																			<input type="text" name="txtPost" id="txtPost" title="Enter post" maxlength="100" size="25" value="<?php echo ( $dupError ? $_POST['txtPost'] : $arrDetails['comp_post'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Contact Person&nbsp;:</td>
																		<td>
																			<input type="text" name="txtContactPerson" id="txtContactPerson" title="Enter contact person name" maxlength="150" size="25" value="<?php echo ( $dupError ? $_POST['txtContactPerson'] : $arrDetails['comp_contact_person'] ) ?>">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Qualification &nbsp;:</td>
																		<td>
																			<input type="text" name="txtQualification" id="txtQualification" title="Enter qualification" maxlength="50" size="25" value="<?php echo ( $dupError ? $_POST['txtQualification'] : $arrDetails['comp_qualification'] ) ?>">
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Contact No.&nbsp;:</td>
																		<td>
																			<input type="text" name="txtContactNo" id="txtContactNo" title="Enter contact no." maxlength="255" size="25" value="<?php echo ( $dupError ? $_POST['txtContactNo'] : $arrDetails['comp_contact_no'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Email Id&nbsp;:</td>
																		<td>
																			<input type="text" name="txtEmail" id="txtEmail" title="Enter email id" maxlength="30" size="25" value="<?php echo ( $dupError ? $_POST['txtEmail'] : $arrDetails['comp_email_id'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;PAN No.&nbsp;:</td>
																		<td>
																			<input type="text" name="txtPanNo" id="txtPanNo" title="Enter pan no." maxlength="30" size="25" value="<?php echo ( $dupError ? $_POST['txtPanNo'] : $arrDetails['comp_pan_no'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Service Tax No.&nbsp;:</td>
																		<td>
																			<input type="text" name="txtServiceTaxNo" id="txtServiceTaxNo" title="Enter service tax no." maxlength="30" size="25" value="<?php echo ( $dupError ? $_POST['txtServiceTaxNo'] : $arrDetails['comp_service_tax_no'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Name of Service Providing &nbsp;:</td>
																		<td>
																			<input type="text" name="txtServiceName" id="txtServiceName" title="Enter name of service provides" maxlength="30" size="25" value="<?php echo ( $dupError ? $_POST['txtServiceName'] : $arrDetails['comp_service_name'] ) ?>">&nbsp;<font color="#ff0000">*</font>
																		</td>
																	</tr>
																	<tr>
																		<td class="arial12LGrayBold" valign="top" align="right">&nbsp;Address .&nbsp;:</td>
																		<td>
																			<textarea name="txtarAddress" id="txtarAddress" maxlength="255" rows="5" cols="50"><?php echo ( $dupError ? $_POST['txtarAddress'] : $arrDetails['comp_address'] ) ?></textarea>&nbsp;<font color="#ff0000">*</font>
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
														<td colspan="2" class="arial18BlueN">Main Company Management</td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$qryListing = " select main_company_id ,comp_name, comp_contact_person, comp_email_id from ". TABLE_MAIN_COMPANY_MST ." where 1 ";
																if($searchValue != '' && $searchType != ''){
																	$qryListing .= " and " . $arrCompanySearchOption[$searchType]['column_name'] ." LIKE '%" . $searchValue . "%' ";
																}
																$qryListing .= ( $_GET['orderType'] !='' && $_GET['colName'] !='' ? " order by  " . $_GET['colName'] . " ".  $_GET['orderType'] . " " : " order by main_company_id  desc ");
																$order = ( $_GET['orderType'] == "asc" ? "desc" :"asc" );
																$objListSplit = new splitPageResults($qryListing, $defRecPerPage);
																$rsListing = tep_db_query($objListSplit->sql_query);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="hidSubmit" id="hidSubmit" value="1"> 
																<input type="hidden" name="hidActionMode" id="hidActionMode" value="">
																<input type="hidden" name="main_company_id" id="main_company_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Company Name</th>
																		<th>Contact Persone Name</th>
																		<th>Email</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if( tep_db_num_rows($rsListing) ){
																			while( $arrListing = tep_db_fetch_array($rsListing) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $arrListing['comp_name'] ?></td>
																			<td><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','main_company_id'))."actionType=edit_company&main_company_id=".$arrListing['main_company_id']); ?>"><?php echo $arrListing['comp_contact_person']; ?></a></td>
																			<td valign="top"><?php echo $arrListing['comp_email_id'] ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','main_company_id'))."actionType=edit_company&main_company_id=".$arrListing['main_company_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>editFill.jpg" border="0"></a>&nbsp;<a href="javascript: deleteSelected(document.frmListing, 'delete_single','<?php echo $arrListing['main_company_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>close.jpg" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Records Found</td>
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
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#table_filter').dataTable({
					"aoColumns": [
						null, //Name
						null, // Person
						null, // email
						{ "bSortable": false}
					],
					"aaSorting": [[1,'asc'], [2,'asc'], [3,'asc']],
					 "iDisplayLength": 300,
					"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
					"bStateSave": false,
					"bAutoWidth": false
				});
			});
		</script>
	</body>
</html>