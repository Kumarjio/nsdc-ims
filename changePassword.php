<?php
	include('includes/application_top.php');

	$arrAlerts = array("passwordChanged" => "Password changed successfully","invalidPassword"=>"Invalid old passwpord");

	if( isset($_POST) && $_POST['hidActionMode'] != '' )
	{
		$strOldPassword = tep_db_input(tep_db_prepare_input($_POST['txtOldPassword']));
		$strNewPassword = tep_db_input(tep_db_prepare_input($_POST['txtNewPassword']));

		$qryCheckPassword = " select adm_id from " . TABLE_ADMIN_MST. " where adm_password = '" . $strOldPassword . "' ";
		$rsCheckPassword = tep_db_query($qryCheckPassword );
		if(tep_db_num_rows($rsCheckPassword))
		{
			$arrCheckPassword = tep_db_fetch_array($rsCheckPassword);
			$qryChangePassword = " update " . TABLE_ADMIN_MST. " set adm_password = '" . $strNewPassword . "' where adm_id = '" . $arrCheckPassword['adm_id'] . "' ";
			$rsChangePassword = tep_db_query($qryChangePassword);
			tep_redirect(tep_href_link(CURRENT_PAGE, 'msg=passwordChanged' ));
		}
		else
		{
			tep_redirect(tep_href_link(CURRENT_PAGE, 'msg=invalidPassword' ));
		}		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo TITLE ?>: Change Password</title>

		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmChangePassword").validate();
			});
		//-->
		</script>
	</head>
	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
		<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			<tr>
				<td>
					<?php
						include( DIR_WS_MODULES . 'header.php' );
					?>
				</td>
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
								<td valign="middle" class="<?php echo ($_GET['msg'] == 'invalidPassword' ? 'error_msg' : 'success_msg' );?>" align="center"><?php echo $arrAlerts[$_GET['msg']]?></td>
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
										<!-- Main Content  starts here-->
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
												<tr>
													<td class="arial18BlueN">Change Password</td>
													<td align="right">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="2">
														<form name="frmChangePassword" id="frmChangePassword" onsubmit="javascript : return submitForm(document.frmChangePassword);" method="post">
														<input type="hidden" name="hidActionMode" id="hidActionMode" value="changePassword">
															<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																<tr>
																	<td width="20%" class="arial12LGrayBold" align="right">&nbsp;Old Password&nbsp;:</td>
																	<td valign="top" class="arial12LGray"><input type="password" name="txtOldPassword" id="txtOldPassword" size="50" maxlength="50" class="required"></td>
																</tr>
																<tr>
																	<td width="20%" class="arial12LGrayBold" align="right">&nbsp;New Password&nbsp;:</td>
																	<td valign="top" class="arial12LGray"><input type="password" name="txtNewPassword" id="txtNewPassword" size="50" maxlength="50" class="required"></td>
																</tr>
																<tr>
																	<td width="20%" class="arial12LGrayBold" align="right">&nbsp;Confirm Password&nbsp;:</td>
																	<td valign="top" class="arial12LGray"><input type="password" name="txtConfirmPassword" id="txtConfirmPassword" size="50" maxlength="50" class="required"></td>
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
										<!-- Main Content ends here-->
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