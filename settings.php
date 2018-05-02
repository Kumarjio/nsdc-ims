<?php
	include('includes/application_top.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Settings</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

	</head>
	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
			<tr>
				<td valign="top">
					<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
						<tr>
							<td valign="top" colspan="2">
								<?php
									include( DIR_WS_MODULES . 'top_menu.php' );
								?>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<?php
									include( DIR_WS_MODULES . 'top_info.php' );
								?>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="backgroundHeaderTB"><img src="images/pixel.gif" height="5"></td>
						</tr>
						<tr>
							<td colspan="2" >
								<?php
									include( DIR_WS_MODULES . 'header.php' );
								?>
							</td>
						</tr>
						<tr>
							<td class="backgroundHeaderTB"><img src="images/pixel.gif" height="5"></td>
						</tr>
						<tr>
							<td colspan="2" valign="top">
								<?php
									include( DIR_WS_MODULES . 'main_menu.php' );
								?>
							</td>
						</tr>
						<tr>
							<td class="backgroundBgMain" valign="top">
								<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									<tr>
										<td valign="top" width="20%">
									<!-- left pannel starts here-->
											<table cellpadding="0" cellspacing="5" border="0" width="100%" align="center" >
												<tr>
													<td><img src="images/pixel.gif" height="5"></td>
												</tr>
												<tr>
													<td class="verdana12BlackB text"><a href="#" class="link2">Settings</a></td>
												</tr>
												<tr>
													<td><img src="images/pixel.gif"></td>
												</tr>
												<tr>
													<td class="arial12LGray brdrBottomLBlue">&nbsp;&nbsp;<img src="images/arrow1.gif">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_CHANGE_PASSWORD) ?>" class="link2">Change Password</a></td>
												</tr>
												<tr>
													<td><img src="images/pixel.gif"></td>
												</tr>
											</table>
										<!-- left pannel ends here-->
										</td>
										<td valign="top" class="brdr">
										<!-- Main Content  starts here-->
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td colspan="2" background="images/blueShade.jpg"><img src="images/pixel.gif"></td>
													</tr>
													<tr>
														<td class="arialblackB">Settings</td>
														<td align="right"><a href="#"><img src="images/edit.jpg" border="0"></a>&nbsp;<a href="#"><img src="images/refresh.jpg" border="0"></a></td>
													</tr>
													<tr>
														<td colspan="2">
															<table cellpadding="5" cellspacing="0" width="100%" align="center" border="0" class="brdrBlue backgroundBgMain">
																<tr>
																	<td colspan="6" align="right" class="brdrBottomGray verdana11Gray"><img src="images/previous.jpg" align="absmiddle">&nbsp;<img src="images/back.jpg" align="absmiddle">&nbsp; (1-5 of 5) <img src="images/next.jpg" align="absmiddle">&nbsp;<img src="images/forward.jpg" align="absmiddle"></td>
																</tr>
																<tr>
																	<td class="backgroundLightTitleHead brdrBottomGray verdana11DarkGray" width="10%">Choose</td>
																	<td class="backgroundLightTitleHead brdrBottomGray verdana11DarkGray" width="50%">Subject</td>
																	<td class="backgroundLightTitleHead brdrBottomGray verdana11DarkGray" width="15%">Duration</td>
																	<td class="backgroundLightTitleHead brdrBottomGray verdana11DarkGray" width="15%">Start Date</td>
																	<td width="10%" class="backgroundLightTitleHead brdrBottomGray verdana11DarkGray">Action</td>
																</tr>
																<tr>
																	<td class="brdrBottomLightBlue"><input type="checkbox" name="chkMembers" id="chkMembers"></td>
																	<td class="brdrBottomLightBlue verdana12Blue"><a href="#" class="verdana12Blue">Dummy listing</a></td>
																	<td class="brdrBottomLightBlue verdana11Gray"> 0h30m</td>
																	<td class="brdrBottomLightBlue verdana11Gray">2008-04-28 16:00</td>
																	<td class="brdrBottomLightBlue"><a href="#"><img src="images/editFill.jpg" border="0"></a>&nbsp;<a href="#"><img src="images/save.jpg" border="0"></a></td>
																</tr>
																<tr>
																	<td class="brdrBottomLightBlue"><input type="checkbox" name="chkMembers" id="chkMembers"></td>
																	<td class="brdrBottomLightBlue verdana12Blue"><a href="#" class="verdana12Blue">Dummy listing 1</a></td>
																	<td class="brdrBottomLightBlue verdana11Gray"> 0h30m</td>
																	<td class="brdrBottomLightBlue verdana11Gray">2008-07-27 17:15</td>
																	<td class="brdrBottomLightBlue"><a href="#"><img src="images/editFill.jpg" border="0"></a>&nbsp;<a href="#"><img src="images/save.jpg" border="0"></a></td>
																</tr>
																<tr>
																	<td class="brdrBottomLightBlue"><input type="checkbox" name="chkMembers" id="chkMembers"></td>
																	<td class="brdrBottomLightBlue verdana12Blue"><a href="#" class="verdana12Blue">Dummy listing 2</a></td>
																	<td class="brdrBottomLightBlue verdana11Gray"> 0h30m</td>
																	<td class="brdrBottomLightBlue verdana11Gray">2008-11-08 05:45</td>
																	<td class="brdrBottomLightBlue"><a href="#"><img src="images/editFill.jpg" border="0"></a>&nbsp;<a href="#"><img src="images/save.jpg" border="0"></a></td>
																</tr>
															</table>
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

