<?php	
	include('includes/application_top.php');
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo TITLE ?>: Student Management</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>
		<style type="text/css">
			.verdana11Gray{
				font-size: 12px;
				font-weight:normal;
			}
		</style>
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
												$int_id = tep_db_input($_GET['int_id']);

												$info_query_raw = "select student_id, centre_id, course_id, batch_id, student_photo, student_type, student_full_name, student_father_name, student_surname, student_middle_name, father_middle_name, father_surname, mother_first_name, mother_middle_name, mother_surname, student_address, student_village, student_district, student_taluka, student_block, student_state, student_pincode, student_mobile, student_mobile_2, student_mobile_3, student_phone_std, student_phone, student_email, student_gender, date_format(student_dob, '%d-%m-%Y') as student_dob, student_age, student_maritial, student_area, student_family_type, is_bpl_card, bpl_card_no, bpl_score_card, is_family_id, family_id, student_category, is_minority_category, student_religion, is_physical_disability, student_physical_disability, is_student_aadhar_card, student_aadhar_card, student_name_as_aadhar, student_aadhar_card_status, is_student_pan_card, student_pan_card, student_language_known, student_qualification, student_other_qualification, is_computer_primary_knowledge, is_play_computer_game, is_msoffice_knowledge, is_internet_knowledge, is_unemployed, student_occupation, student_income, student_income_source, is_bank_account, bank_account_status, student_bank_name, student_branch, student_account_number, bank_ifsc_code, is_meet_eligibility_creteria, student_height, student_weight, student_blood_group, is_ready_migrate_job, is_ready_training, is_ready_migrate_training, student_remark, course_option, student_created from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and is_deactivated != '1' ";

												if($_SESSION['sess_adm_type'] != 'ADMIN'){
													$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
												}

												$info_query = tep_db_query($info_query_raw);

												$info = tep_db_fetch_array($info_query);
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">View Student</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="javascript: history.go(-1);" class="arial14LGrayBold">Back</a></td>
													</tr>
													<tr>
														<td colspan="2">
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Enroll Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="enroll_info">
																								<tr>
																									<td class="arial12LGrayBold" width="10%">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" width="20%">
																									<?php
																										$course_info_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id and course_id = '" . $info['course_id'] . "'";

																										$course_info_query = tep_db_query($course_info_query_raw);
																										$course_info = tep_db_fetch_array($course_info_query);

																										echo $course_info['course_name'] . ' - ' . $course_info['section_name'] . ' ( ' . $course_info['course_code'] . ' ) ';		
																									?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="5%">&nbsp;Batch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php
																											 $batch_title = get_column_value("batch_title", TABLE_BATCHES, "where batch_id = '" . $info['batch_id'] . "'");
																											 echo $batch_title;
																										?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Course Option&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="3" class="verdana11Gray">
																										<?php 
																											echo $arr_course_option[$info['course_option']];
																										?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																			 
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Candidate Full Name</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;First Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo  $info['student_full_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_middle_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_surname']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold">
																						<fieldset>
																							<legend>Father's Name</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="23%">&nbsp;Father's Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_father_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['father_middle_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['father_surname']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																					<td class="arial14LGrayBold">
																						<fieldset>
																							<legend>Mother's  Name</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;First Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['mother_first_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['mother_middle_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['mother_surname']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Address</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="adrs">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Address&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="5" class="verdana11Gray">
																									<?php echo $info['student_address']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Village&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%" class="verdana11Gray">
																										<?php echo $info['student_village']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="5%">&nbsp;District&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%"class="verdana11Gray">
																									<?php
																										$disctrict_query_raw="SELECT district_name FROM districts  WHERE district_id ='16'";
																										$course=mysql_query($disctrict_query_raw);
																										$qury=mysql_fetch_array($course);
																										 
																										 echo $qury['district_name'];
																										 												
																										?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Taluka&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_taluka']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Block&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_block']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;State&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php 
																											echo $arr_states[$info['student_state']];
																										?>
																										 
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Pin Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_pincode']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Contact Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Mobile Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%" class="verdana11Gray">
																										<?php echo $info['student_mobile']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;2<span class="sub_text">nd</span> Mobile Number&nbsp;:</td>
																									<td class="verdana11Gray">
																										 <?php echo $info['student_mobile_2']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;3<span class="sub_text">rd</span> Mobile Number&nbsp;:</td>
																									<td width="10%" class="verdana11Gray">
																										<?php echo $info['student_mobile_3']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Landline (Resi)&nbsp;:</td>
																									<td  class="verdana11Gray">
																										Std Code <?php echo $info['student_phone_std']; ?>&nbsp;
																										<?php echo $info['student_phone']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Email&nbsp;:</td>
																									<td colspan="3" class="verdana11Gray">
																										<?php echo $info['student_email']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Other Information</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="other">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Gender&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" width="15%">
																									<?php
																										echo $arr_gender[$info['student_gender']];
																									?>
																									 
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;DOB (DD-MM-YYYY) &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%" class="verdana11Gray">
																										<?php echo $info['student_dob']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Age &nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo  ($info['student_age'] != '' ? $info['student_age'] : '0'); ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Maritial Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="5" class="verdana11Gray">
																									<?php echo $arr_maritial_status[$info['student_maritial']]; ?> 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Student Area&nbsp;:</td>
																									<td colspan="5" class="verdana11Gray">
																										<?php echo $arr_student_area[$info['student_area']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Family Type&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																										echo $arr_family_type[$info['student_family_type']];
																										?>
																										 
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;B.P.L. CARD &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="3">
																									
																										<?php
																											echo $arr_status[$info['is_bpl_card']];
																										
																											if($info['is_bpl_card'] == 1){
																												
																											?> 
																											<span class="arial12LGrayBold" align="right"> &nbsp;&nbsp;&nbsp;B.P.L. Card No : &nbsp;  <span class="verdana11Gray"><?php  echo $info['bpl_card_no']; ?></span>
																											&nbsp;&nbsp;&nbsp;B.P.L. Score Card :&nbsp;&nbsp;&nbsp;
																											<span class="verdana11Gray"><?php echo   $info['bpl_score_card']; ?></span >
																										</span> <?php }?>
																																																							
																										 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Have Family ID<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																										<?php
																											echo $arr_status[$info['is_family_id']];
																											
																											if($info['is_family_id'] == 1){
																										?>
																										&nbsp;&nbsp;<span class="arial12LGrayBold family_id" align="right">
																											Family ID&nbsp;&nbsp;<span class="verdana11Gray"><?php echo $info['family_id']; ?></span>
																										</span><?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Category&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																									echo $arr_category[$info['student_category']];
																									?>
																										 
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Minority Category<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="3">
																									<?php
																										echo $arr_status[$info['is_minority_category']];
																									?>
																										 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Religion&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																										echo $arr_religion[$info['student_religion']];
																										?>
																										 
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Physical Unability/Disability<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="3">
																									<?php
																										echo $arr_status[$info['is_physical_disability']];
																										if($info['is_physical_disability'] == 1){
																									?>
																										<span class="arial12LGrayBold" align="right">
																											&nbsp;
																											Physical Disablity &nbsp;:
																											&nbsp; <span class="verdana11Gray"><?php echo  $info['student_physical_disability']; ?></span>
																										</span><?php }?>

																										 
																									</td>
																								</tr>
																								 
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Aadhar Card<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																									<?php
																										echo $arr_status[$info['is_student_aadhar_card']];
																										if($info['is_student_aadhar_card'] == 1){
																									
																									?>
																									<span class="arial12LGrayBold" align="right">
																										&nbsp;&nbsp; Aadhar Card&nbsp;&nbsp;:<span class="verdana11Gray"><?php echo   $info['student_aadhar_card']; ?></span>
																									</span>
																									<span class="arial12LGrayBold" align="right">
																										&nbsp;&nbsp; Name as per Aadhar Card&nbsp;&nbsp;:<span class="verdana11Gray"><?php echo $info['student_name_as_aadhar']; ?></span>
																									</span>
																									<?php }?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;PAN Card No<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																										<?php
																											echo $arr_status[$info['is_student_pan_card']];
																										
																											if($info['is_student_pan_card'] == 1){																								
																											?>
																											<span class="arial12LGrayBold" align="right">
																											&nbsp;PAN Card&nbsp;&nbsp;:
																											&nbsp;
																											<span class="verdana11Gray"><?php echo    $info['student_pan_card']; ?></span>
																										</span><?php } ?>
																									</td>
																								</tr>
																								 																																						<tr>
																									<td class="arial12LGrayBold">&nbsp;Student meet eligibilty creteria&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																									<?php
																										echo $arr_status[$info['is_meet_eligibility_creteria']];
																									?>
																									 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Height&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php echo  $info['student_height']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Weight&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php echo $info['student_weight']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Blood Group&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php echo  $info['student_blood_group']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for job &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																									<?php
																										echo $arr_status[$info['is_ready_migrate_job']];
																									?>
																									 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready for 10 - 12 hrs Training&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																									<?php
																										echo $arr_status[$info['is_ready_training']];
																									?>
																									 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for training&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray" colspan="5">
																									<?php
																										echo $arr_status[$info['is_ready_migrate_training']];
																									?>
																									 
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Qualification/Skills</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="qual">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Language Known&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo   $info['student_language_known']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																											echo $arr_qualification[$info['student_qualification']]	
																									?>
																										 
																										<span class="arial12LGrayBold other_qualification" align="right">
																											&nbsp;Other Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;
																											<?php   $info['student_other_qualification']; ?>
																										</span>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				 
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Computer Literacy</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="cl">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Primary Knowledge of Computers&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																										echo $arr_status[$info['is_computer_primary_knowledge']];
																									?>
																									 
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Can Play Game on Computer&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																										echo $arr_status[$info['is_play_computer_game']];
																									?>

																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;MS Office Knowledge&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php
																										echo $arr_status[$info['is_msoffice_knowledge']];
																									?>

																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Internet Knowledge&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php
																										echo $arr_status[$info['is_internet_knowledge']];
																									?>
																										 
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>

																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Employment Details</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="emp">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Employed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																									<?php
																										echo $arr_status[$info['is_unemployed']];
																									?>
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Occupation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo   $info['student_occupation']; ?>
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Monthly Income&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo  $info['student_income']; ?>
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Source of Income&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo $info['student_income_source'] ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Bank Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="bank">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Bank Account&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php
																											echo $arr_status[$info['is_bank_account']];		
																										?>
																									</td>
																								</tr>
																								<?php if($info['is_bank_account']){ ?> 
																								<tr>
																									<td class="arial12LGrayBold" width="13%" align="right">&nbsp;Name of the Bank&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%" class="verdana11Gray">
																										<?php echo $info['student_branch']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Account Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo  $info['student_account_number']; ?>
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" align="right">&nbsp;Bank IFSC Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="verdana11Gray">
																										<?php echo   $info['bank_ifsc_code']; ?>
																									</td>
																								</tr>
																								<?php } ?>
																							</table>
																						</fieldset>
																					</td>
																				</tr>	
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Student Photo / Documents</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="docs">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" valign="top">&nbsp;Student Photo&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<?php if($info['student_photo']!=''){?>
																										<img src="<?php echo DIR_WS_UPLOAD . $info['student_photo'];?>" width="150" style="padding:3px; border: 1px solid black;"><br><br>
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td colspan="2" style="border: none;">
																										<table cellpadding="5" cellspacing="3" border="0" width="70%" id="student_docs">
																											<tr>
																												<td class="arial12LGrayBold">Document File</td>
																												<td class="arial12LGrayBold">Document Name</td>
																												<td class="arial12LGrayBold">Document Type</td>
																											</tr>
																											<?php
																												$student_documents_query_raw = "select student_document_id, document, document_title, document_type from " . TABLE_STUDENT_DOCUMENTS . " where student_id = '" . $int_id . "'";
																												$student_documents_query = tep_db_query($student_documents_query_raw);

																												if(tep_db_num_rows($student_documents_query)){
																													while($student_documents = tep_db_fetch_array($student_documents_query)){
																											?>
																											<tr>
																												<td>
																													<a href="<?php echo DIR_WS_UPLOAD . $student_documents['document'];?>" target="_blank"><?php echo $student_documents['document'];?></a>
																												</td>
																												<td class="verdana12Blue"><?php echo $student_documents['document_title'];?></td>
																												<td class="verdana12Blue"><?php echo $student_documents['document_type'];?></td>
																											</tr>
																											<?php
																													}
																												}
																											?>
																										</table>
																									</td>
																								</tr>
																								<tr>
																									 
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Remark</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="remark">
																								<tr>
																									<td class="arial12LGrayBold" width="7%" valign="top">&nbsp;Remark&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										 <?php echo $info['student_remark']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
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