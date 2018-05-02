<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
<?php
	if( $_SESSION['sess_adm_type'] == 'ADMIN'){
?>
	<tr>
		<td align="center" style="background-color: #FBB900; font-weight:bold;" width="5%" class="trebuchet14Black">Admin</td>
		<td>
			<table class="topNavBg" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
				<tr>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_DISTRICTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_DISTRICTS ? "link2active" : "link2" ) ?>">District</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_CITIES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_CITIES ? "link2active" : "link2" ) ?>">Cities</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_CENTERS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_CENTERS ? "link2active" : "link2" ) ?>">Centres</a></td>

					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_SECTIONS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_SECTIONS ? "link2active" : "link2" ) ?>">Course Sectors</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_COURSES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_COURSES ? "link2active" : "link2" ) ?>">Courses</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_MODULES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_MODULES ? "link2active" : "link2" ) ?>">Modules</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_SUBJECTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_SUBJECTS ? "link2active" : "link2" ) ?>">Subjects</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_USERS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_USERS ? "link2active" : "link2" ) ?>">Users</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_REPORT_OVERALL_PROJECT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_OVERALL_PROJECT ? "link2active" : "link2" ) ?>">Reports</a></td>
				</tr>
				<tr><td align="center" colspan="17"><hr/></td></tr>
				<tr>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_ADMIN_COMPANIES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_ADMIN_COMPANIES ? "link2active" : "link2" ) ?>">Companies</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_ADMIN_FACULTIES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_ADMIN_FACULTIES ? "link2active" : "link2" ) ?>">Faculties</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_PHOTOS_VIDEOS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_PHOTOS_VIDEOS ? "link2active" : "link2" ) ?>">Photos/Videos</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_DOCUMENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_DOCUMENTS ? "link2active" : "link2" ) ?>">Documents</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_DISABLE_STUDENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_DISABLE_STUDENTS ? "link2active" : "link2" ) ?>">Student Details</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_WAIVERS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_WAIVERS ? "link2active" : "link2" ) ?>">Course Waivers</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_BANK_ACCOUNTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_BANK_ACCOUNTS ? "link2active" : "link2" ) ?>">Bank Accounts</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_BOUNCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_BOUNCE ? "link2active" : "link2" ) ?>">Reconciliation</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_ASSESMENT_PAYMENTS) ?>" class="<?php echo (CURRENT_PAGE == FILENAME_ASSESMENT_PAYMENTS ? "link2active" : "link2" ) ?>">Assessment Payments</a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><img src="<?php echo DIR_WS_IMAGES ?>pixel.gif" height="10"></td>
	</tr>
<?php }else{ ?>
	<tr>
		<td align="center" style="background-color: #FBB900; font-weight:bold;" width="5%" class="trebuchet14Black">Centre</td>
		<td>
			<table class="topNavBg" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
				<tr>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_PROS_STUDENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_PROS_STUDENTS ? "link2active" : "link2" ) ?>">Prospects</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_ENROLL_STUDENTS ? "link2active" : "link2" ) ?>">Students</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_COMPANIES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_COMPANIES ? "link2active" : "link2" ) ?>">Companies</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_FACULTIES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_FACULTIES ? "link2active" : "link2" ) ?>">Faculties</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_BATCHES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_BATCHES ? "link2active" : "link2" ) ?>">Batches</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_LECTURES) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_LECTURES ? "link2active" : "link2" ) ?>">Lectures</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_STUDENT_ATTENDANCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_STUDENT_ATTENDANCE ? "link2active" : "link2" ) ?>">Mark Attendance</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>

					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_PHOTOS_VIDEOS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_PHOTOS_VIDEOS ? "link2active" : "link2" ) ?>">Photos/Videos</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>

					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_DOCUMENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_DOCUMENTS ? "link2active" : "link2" ) ?>">Documents</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>

					<td align="center" width="10%"><a href="<?php echo tep_href_link(FILENAME_REPORT_BATCH) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BATCH ? "link2active" : "link2" ) ?>">Reports</a></td>
				</tr>
			</table>
		</td>
	</tr>
<?php 
	}

if(in_array(CURRENT_PAGE, array(FILENAME_REPORT_OVERALL_PROJECT, FILENAME_REPORT_BATCH, FILENAME_REPORT_AADHAR_CARD, FILENAME_REPORT_BANK_ACCOUNT, FILENAME_REPORT_NON_RES_ALLOWANCE, FILENAME_REPORT_PLACEMENT_ALLOWANCE, FILENAME_REPORT_BATCH_CERTIFICATION, FILENAME_REPORT_BATCH_PLACEMENT, FILENAME_REPORT_HAND_HOLDING, FILENAME_REPORT_BATCH_ATTENDANCE, FILENAME_REPORT_INSTALLMENT, FILENAME_REPORT_CONSOLIDATED_PAYMENTS, FILENAME_REPORT_PAYMENT_COLLECTION, FILENAME_REPORT_REFUND, FILENAME_REPORT_BOUNCE, FILENAME_REPORT_PROSPECTS))){
?>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align="center" style="background-color: #FBB900; font-weight:bold;" width="10%" class="trebuchet14Black">Reports</td>
		<td>
			<table class="topNavBg" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
				<tr>
					<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_OVERALL_PROJECT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_OVERALL_PROJECT ? "link2active" : "link2" ) ?>">Overall Project</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<?php } ?>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BATCH) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BATCH ? "link2active" : "link2" ) ?>">Batch Reports</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_AADHAR_CARD) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_AADHAR_CARD ? "link2active" : "link2" ) ?>">Aadhar Card</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BANK_ACCOUNT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BANK_ACCOUNT ? "link2active" : "link2" ) ?>">Bank Account</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_NON_RES_ALLOWANCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_NON_RES_ALLOWANCE ? "link2active" : "link2" ) ?>">Non Res Allowance</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_PLACEMENT_ALLOWANCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_PLACEMENT_ALLOWANCE ? "link2active" : "link2" ) ?>">Placement Allowance</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BATCH_CERTIFICATION) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BATCH_CERTIFICATION ? "link2active" : "link2" ) ?>">Batch Certification</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BATCH_PLACEMENT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BATCH_PLACEMENT ? "link2active" : "link2" ) ?>">Batch Placement</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_HAND_HOLDING) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_HAND_HOLDING ? "link2active" : "link2" ) ?>">Hand Holding</a></td>
					<td align="center" width="1%"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BATCH_ATTENDANCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BATCH_ATTENDANCE ? "link2active" : "link2" ) ?>">Batch Attendance</a></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" style="background-color: #FBB900; font-weight:bold;" width="5%" class="trebuchet14Black">Payment Reports</td>
		<td>
			<table class="topNavBg" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
				<tr>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_INSTALLMENT) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_INSTALLMENT ? "link2active" : "link2" ) ?>">Due Installment Report</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_CONSOLIDATED_PAYMENTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_CONSOLIDATED_PAYMENTS ? "link2active" : "link2" ) ?>">Consolidated Finance Report</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_PAYMENT_COLLECTION) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_PAYMENT_COLLECTION ? "link2active" : "link2" ) ?>">Datewise Collection Report</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_REFUND) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_REFUND ? "link2active" : "link2" ) ?>">Refund Report</a></td>
					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_BOUNCE) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_BOUNCE ? "link2active" : "link2" ) ?>">Bounce Cheque Report</a></td>

					<td align="center"><img src="images/headreeseprater.jpg"></td>
					<td align="center"><a href="<?php echo tep_href_link(FILENAME_REPORT_PROSPECTS) ?>" class="<?php echo ( CURRENT_PAGE == FILENAME_REPORT_PROSPECTS ? "link2active" : "link2" ) ?>">Contact Log Report</a></td>
				</tr>
			</table>
		</td>
	</tr>
<?php } ?>
</table>