<tr>
	<td colspan="2"><img src="images/pixel.gif" height="10"></td>
</tr>
<tr>
	<td valign="top"><img src="images/proschool.png" alt="Proschool" border="0" title="Proschool"></td>
	<td valign="top" align="right">
		<table cellpadding="5" cellspacing="0" border="0" align="right">
			<tr>
				<td class="verdana11GrayB">&nbsp;&nbsp;Welcome <?php echo $_SESSION['sess_adm_name'];?>!</td>
				<td align="center" width="5%"><img src="images/headreeseprater.jpg"></td>
				<td align="center" width="20%"><a href="<?php echo tep_href_link(FILENAME_HOME) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_HOME ? "link2active" : "link2" ) ?>">Dashboard</a></td>
				<td align="center" width="5%"><img src="images/headreeseprater.jpg"></td>
				<td align="center" width="20%"><a href="<?php echo tep_href_link(FILENAME_DEPOSITS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_DEPOSITS ? "link2active" : "link2" ) ?>">Deposit</a></td>
				<td align="center" width="5%"><img src="images/headreeseprater.jpg"></td>
				<td width="25%"><a href="<?php echo tep_href_link(FILENAME_CHANGE_PASSWORD) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_CHANGE_PASSWORD ? "link2active" : "link2" ) ?>">Change Password</a></td>
				<td align="center" width="5%"><img src="images/headreeseprater.jpg"></td>
				<td><a href="<?php echo tep_href_link(FILENAME_LOGOUT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_LOGOUT ? "link2active" : "link2" ) ?>">Logout</a></td>
			</tr>
			<tr>
				<td class="verdana11Blue" align="right" colspan="7"><?php echo date("j'S F, Y h:i A"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2"><img src="images/pixel.gif" height="50"></td>
</tr>