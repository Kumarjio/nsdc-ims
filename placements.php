<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$placement_id = tep_db_prepare_input($_POST['placement_id']);
		$student_id = tep_db_prepare_input($_POST['student_id']);
		$centre_id = $_SESSION['sess_centre_id'];
		$placement_type = tep_db_prepare_input($_POST['placement_type']);
		$company_id = tep_db_prepare_input($_POST['company_id']);
		$job_status = tep_db_prepare_input($_POST['job_status']);
		$job_joining_date = tep_db_prepare_input($_POST['job_joining_date']);
		$job_designation = tep_db_prepare_input($_POST['job_designation']);

		$gross_salary = tep_db_prepare_input($_POST['gross_salary']);
		$in_hand_salary = tep_db_prepare_input($_POST['in_hand_salary']);
		$job_other_benifits = tep_db_prepare_input($_POST['job_other_benifits']);
		$post_palacement_allowance = tep_db_prepare_input($_POST['post_palacement_allowance']);

		$offer_letter_collected = tep_db_prepare_input($_POST['offer_letter_collected']);
		$salary_slip_collected = tep_db_prepare_input($_POST['salary_slip_collected']);

		$job_joining_date = input_valid_date($job_joining_date);

		$arr_db_values = array(
			'student_id' => $student_id,
			'company_id' => $company_id,
			'centre_id' => $centre_id,
			'placement_type' => $placement_type,
			'job_status' => $job_status,
			'job_joining_date' => $job_joining_date,
			'job_designation' => $job_designation,
			'gross_salary' => $gross_salary,
			'in_hand_salary' => $in_hand_salary,
			'job_other_benifits' => $job_other_benifits,
			'post_palacement_allowance' => $post_palacement_allowance,
			'offer_letter_collected' => $offer_letter_collected,
			'salary_slip_collected' => $salary_slip_collected
		);

		if($_FILES['offer_letter']['name'] != ''){
			$ext = get_extension($_FILES['offer_letter']['name']);
			$src = $_FILES['offer_letter']['tmp_name'];

			$dest_filename = 'offer_letter_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['offer_letter'] = $dest_filename;
			}
		}

		if($_FILES['salary_slip']['name'] != ''){
			$ext = get_extension($_FILES['salary_slip']['name']);
			$src = $_FILES['salary_slip']['tmp_name'];

			$dest_filename = 'salary_slip_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['salary_slip'] = $dest_filename;
			}
		}

		switch($action){
			case 'edit':
				$placement_query = tep_db_query("select placement_id from " . TABLE_PLACEMENTS . " where student_id = '" . $student_id . "' and centre_id = '" . $centre_id . "'");

				if(tep_db_num_rows($placement_query)){
					tep_db_perform(TABLE_PLACEMENTS, $arr_db_values, "update", "placement_id = '" . $placement_id . "'");
				}else{
					tep_db_perform(TABLE_PLACEMENTS, $arr_db_values);
				}

				change_student_status($student_id, '1');

				$msg = 'place_edited';
			break;
			case 'delete':
				tep_db_query("delete from " . TABLE_PLACEMENTS . " where placement_id = '" . $placement_id . "' limit 1");
				tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
			break;
		}

		if($post_palacement_allowance == '1'){
			tep_redirect(tep_href_link(FILENAME_PLACEMENT_ALLOWANCE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'actionType=edit&int_id=' . $student_id . '&msg=' . $msg));
		}else{
			tep_redirect(tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
		}
	}

	$student_status = '0';

	if($_GET['actionType'] == "edit")
	{
		$int_id = $_GET['int_id'];

		$info_query_raw = "select placement_id, student_id, company_id, centre_id, placement_type, job_status, date_format(job_joining_date, '%d-%m-%Y') as job_joining_date, job_designation, gross_salary, in_hand_salary, job_other_benifits, post_palacement_allowance, offer_letter_collected, offer_letter, salary_slip_collected, salary_slip from " . TABLE_PLACEMENTS . " where student_id = '" . $int_id . "' ";

		if($_SESSION['sess_adm_type'] != 'ADMIN'){
			$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
		}

		$info_query = tep_db_query($info_query_raw);

		$info = tep_db_fetch_array($info_query);

		$student_status = get_column_value("student_status", TABLE_STUDENTS, " where student_id = '" . $int_id . "'");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Placement Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function get_company_info(){
				var company_id = $('#company_id').val();
				var params = 'action=get_comp_info&company_id='+company_id;

				$('#comp_info_tr').hide();
				$('#comp_info_td').empty();

				if(company_id != ''){
					$.ajax({
						url: 'get_data.php',
						data: params,
						type: 'POST',
						success: function(response){
							$('#comp_info_tr').show();
							$('#comp_info_td').html(response);
						}
					});
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#job_joining_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				<?php if($student_status == '1'){ ?>
				$('#frmDetails input, #frmDetails select, #frmDetails textarea, #frmDetails button').attr('disabled', true);
				<?php } ?>
			});

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function delete_placement(objForm){
				if(confirm("Are you want to delete this placement?")){
					objForm.action_type.value = 'delete';
					objForm.submit();
				}
			}
			function toggle_placement_type(){
				var placement_type = $('select[name="placement_type"] option:selected').val();

				if(placement_type == 'ON_JOB' || placement_type == 'UP_SKILLED'){
					$('select[name="company_id"]').addClass("required");
					$('select[name="job_status"]').addClass("required");
					$('input[name="job_designation"]').addClass("required");
					$('input[name="gross_salary"]').addClass("required");
					$('input[name="in_hand_salary"]').addClass("required");
				}else{
					$('select[name="company_id"]').removeClass("required");
					$('select[name="job_status"]').removeClass("required");
					$('input[name="job_designation"]').removeClass("required");
					$('input[name="gross_salary"]').removeClass("required");
					$('input[name="in_hand_salary"]').removeClass("required");
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
												if($_GET['actionType'] == "edit")
												{

													$action_type = 'edit';
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Placement Management</td>
														<td align="right"><img src="images/edit.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_PLACEMENT_ALLOWANCE,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."actionType=edit&int_id=".$int_id); ?>" class="arial14LGrayBold">Placement Allowance</a>&nbsp;&nbsp;<img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType'))); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="placement_id" id="placement_id" value="<?php echo $info['placement_id']; ?>"> 
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $int_id; ?>"> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Placement</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Placement Type &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="placement_type" id="placement_type" class="required" onchange="javascript: toggle_placement_type();">
																											<option value="">Please choose</option>
																											<?php foreach($placement_type_array as $k_placement=>$v_placement){?>
																											<option value="<?php echo $k_placement;?>" <?php echo($info['placement_type'] == $k_placement? 'selected="selected"' : '');?>><?php echo $v_placement;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="15%">&nbsp;Company&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php
																											$companies_query_raw = "select company_id, company_name from ". TABLE_COMPANIES ." where 1 ";
																											if($_SESSION['sess_adm_type'] != 'ADMIN'){
																												$companies_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																											}
																											$companies_query_raw .= " order by company_name";

																											$companies_query = tep_db_query($companies_query_raw);
																										?>
																										<select name="company_id" id="company_id" class="required" onchange="javascript: get_company_info();">
																											<option value="">Please choose</option>
																											<?php
																												while($companies = tep_db_fetch_array($companies_query)){
																											?>
																											<option value="<?php echo $companies['company_id'];?>" <?php echo($info['company_id'] == $companies['company_id'] ? 'selected="selected"' : '');?>><?php echo $companies['company_name'];?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr id="comp_info_tr">
																									<td>&nbsp;</td>
																									<td id="comp_info_td" class="arial12LGray" align="left">&nbsp;</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									get_company_info();
																								//-->
																								</script>
																								
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Placement Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="job_status" id="job_status" class="required">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_placement_status as $k_status=>$v_status){
																											?>
																											<option value="<?php echo $k_status;?>" <?php echo($info['job_status'] == $k_status ? 'selected="selected"' : '');?>><?php echo $v_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Date of Joining Job&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="job_joining_date" id="job_joining_date" value="<?php echo  ($info['job_joining_date'] != '' ? $info['job_joining_date'] : date("d-m-Y")) ?>" readonly>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Designation of Candidate&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="job_designation" id="job_designation" maxlength="50" value="<?php echo  ($info['job_designation'] != '' ? $info['job_designation'] : '') ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Gross Salary&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="gross_salary" id="gross_salary" maxlength="13" value="<?php echo  ($info['gross_salary'] != '' ? $info['gross_salary'] : '') ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;In Hand Salary&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<input type="text" name="in_hand_salary" id="in_hand_salary" maxlength="13" value="<?php echo  ($info['in_hand_salary'] != '' ? $info['in_hand_salary'] : '') ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Any Other Benefits&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="job_other_benifits" id="job_other_benifits" rows="5" cols="40"><?php echo  ($info['job_other_benifits'] != '' ? $info['job_other_benifits'] : '') ?></textarea>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Letter of Offer/Declaration Collected&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="offer_letter_collected" id="offer_letter_collected" value="<?php echo $k_status;?>" class="required" <?php echo ($info['offer_letter_collected'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: toggle_element('offer_letter_collected', 'offer_letter');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																										<span class="arial12LGrayBold offer_letter" align="right">&nbsp;<br><br>Letter of Offer/Declaration &nbsp;:&nbsp;
																											<?php if($info['offer_letter'] != ''){ ?>
																											<a href="<?php echo DIR_WS_UPLOAD . $info['offer_letter'];?>" target="_blank"><?php echo $info['offer_letter'];?></a><br><br>
																											<?php } ?>
																											<input type="file" name="offer_letter" id="offer_letter">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('offer_letter_collected', 'offer_letter');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Salary Slip Collected&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="salary_slip_collected" id="salary_slip_collected" value="<?php echo $k_status;?>" class="required" <?php echo ($info['salary_slip_collected'] == $k_status ? 'checked="checked"' : '');?> onclick="javascript: toggle_element('salary_slip_collected', 'salary_slip');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																										<span class="arial12LGrayBold salary_slip" align="right">&nbsp;<br><br>Salary Slip&nbsp;:&nbsp;
																											<?php if($info['salary_slip'] != ''){ ?>
																											<a href="<?php echo DIR_WS_UPLOAD . $info['salary_slip'];?>" target="_blank"><?php echo $info['salary_slip'];?></a><br><br>
																											<?php } ?>
																											<input type="file" name="salary_slip" id="salary_slip">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('salary_slip_collected', 'salary_slip');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" valign="top">&nbsp;Post Placement Allowance&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="post_palacement_allowance" id="post_palacement_allowance" value="<?php echo $k_status;?>" class="required" <?php echo ($info['post_palacement_allowance'] == $k_status ? 'checked="checked"' : '');?>>&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
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
																<?php if($student_status == '0'){ ?>
																<table cellpadding="5" cellspacing="4" border="0" width="100%" align="center">
																	<tr>
																		<td>&nbsp;<input type="submit" value="UPADTE" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="reset" value="RESET" name="cmdReg" id="cmdReg" class="groovybutton">
																		<?php if($info['placement_id'] != ''){ ?>
																		&nbsp;&nbsp;&nbsp;<input type="button" value="DELETE" name="cmdDel" id="cmdDel" class="groovybutton" onclick="javascript: delete_placement(document.frmDetails);">
																		<?php } ?>
																		</td>
																		<td >&nbsp;</td>
																	<tr>
																</table>
																<?php } ?>
															</form>
														</td>
													</tr>
												</table>	
											<?php 
												}
											?>
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