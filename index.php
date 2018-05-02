<?php
	include('includes/application_top.php');
	$arrAlerts = array('1'=>'Invalid username or password !!!');
	$error = $_GET['error'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Login</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
	</head>
	<body onload="document.frmLogin.txtUserName.focus();">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" height="50%">
			<tr>
				<td valign="top"><img src="images/proschool.png" alt="Proschool" border="0" title="Proschool"></td>
			</tr>
			<tr>
				<td align="center">
					<table cellpadding="2" cellspacing="0" border="0" width="325" align="center" class="">
						<?php
							if(isset($error))
							{
						?>
							<tr>
								<td valign="middle" class="<?php echo ($error == '1' ? 'error_msg' : 'success_msg' );?>" align="center"><?php echo $arrAlerts[$error]; ?></td>
							</tr>
							<tr>
								<td><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
							</tr>
						<?php
							}	
						?>
						<tr>
							<td valign="top" align="center">
								<form name="frmLogin" id="frmLogin" method="post" action="<?php echo tep_href_link(FILENAME_INDEX_PROCESS) ?>">
									<table class="verdana12Black" cellpadding="0" cellspacing="0" border="0" width="325" style="border-right: 1px solid #E8EAE8;border-left: 1px solid #E8EAE8;">
										<tr>
											<td colspan="2" style="color: #ffffff; background-color: #006EB5; padding: 10px 18px 10px 10px; border-bottom: 1px solid #ffffff; font-size:14px; font-weight: bold;">&nbsp;&nbsp;Login here</td>
										</tr>
										<tr>
											<td colspan="2"><img src="images/pixel.gif" height="15"></td>
										</tr>
										<tr>
											<td align="right">Username :&nbsp;&nbsp;</td>
											<td><input type="text" class="inputbox" name="txtUserName" id="txtUserName" title="Enter User name" maxlength="10" size="25"></td>
										</tr>
										<tr>
											<td colspan="2"><img src="images/pixel.gif" height="10"></td>
										</tr>
										<tr>
											<td align="right">Password :&nbsp;&nbsp;</td>
											<td><input type="password" name="txtPassword" id="txtPassword" title="Enter Your password" maxlength="50" size="25" class="inputbox"></td>
										</tr>
										<tr>
											<td colspan="2"><img src="images/pixel.gif" height="10"></td>
										</tr>
										<tr>
											<td colspan="2" align="center"><input type="submit" name="CmbLogin" id="CmbLogin" value="LOGIN" class="groovybutton" style="width: auto;">
											</td>
										</tr>
										<tr>
											<td colspan="2"><img src="images/pixel.gif" height="15"></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
					<img src="images/bottom.png" width="325">
				</td>
			</tr>
		</table>
	</body>
</html>
