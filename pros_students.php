<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Prospect has been deleted successfully", 'added'=>'Prospect has been added successfully',"edited"=>"Prospect has been updated successfully", "deleted_docs"=>"Document has been deleted successfully");
	$action = $_POST['action_type'];

	function upload_documents($student_id) {
		if(is_array($_POST['document_title']) && count($_POST['document_title'])){
			foreach($_POST['document_title'] as $key_docs => $document_title){
				if($_FILES['document']['name'][$key_docs] != ''){

					$ext = get_extension($_FILES['document']['name'][$key_docs]);
					$src = $_FILES['document']['tmp_name'][$key_docs];

					$dest_filename = 'docs_' . $key_docs . time() . date("His") . $ext;
					$dest = DIR_FS_UPLOAD . $dest_filename;

					$document_type = $_POST['document_type'][$key_docs];

					if(file_exists($dest))
					{
						@unlink($dest);
					}

					if(move_uploaded_file($src, $dest))	
					{
						$arr_db_values = array(
							'student_id' => $student_id,
							'document' => $dest_filename,
							'document_title' => $document_title,
							'document_type' => $document_type
						);

						tep_db_perform(TABLE_STUDENT_DOCUMENTS, $arr_db_values);
					}
				}
			}
		}
	}

	function student_photo() {
		if($_FILES['student_photo']['name'] != ''){
			$ext = get_extension($_FILES['student_photo']['name']);
			$src = $_FILES['student_photo']['tmp_name'];

			$dest_filename = 'photo_' . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				return $dest_filename;
			}
		}
	}

	include_once("ckeditor/ckeditor.php");
	
	if(isset($action) && tep_not_null($action))
	{
		
		$student_id = tep_db_prepare_input($_POST['student_id']);
		
		$centre_id = $_SESSION['sess_centre_id'];
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$student_type = 'PROSPECT';

		$enq_type =  tep_db_prepare_input($_POST['enq_type']);
		
		$enq_date =  tep_db_prepare_input($_POST['enq_date']);
		$enq_date = date('Y-m-d', strtotime($enq_date));


		$student_full_name = tep_db_prepare_input($_POST['student_full_name']);
		$student_middle_name = tep_db_prepare_input($_POST['student_middle_name']);
		$student_surname = tep_db_prepare_input($_POST['student_surname']);
		$student_father_name = tep_db_prepare_input($_POST['student_father_name']);
		$father_middle_name = tep_db_prepare_input($_POST['father_middle_name']);
		$father_surname = tep_db_prepare_input($_POST['father_surname']);
		$student_father_occupation = tep_db_prepare_input($_POST['student_father_occupation']);
		$student_father_mincome = tep_db_prepare_input($_POST['student_father_mincome']);
		$student_father_yincome = tep_db_prepare_input($_POST['student_father_yincome']);
		$mother_first_name = tep_db_prepare_input($_POST['mother_first_name']);
		$mother_middle_name = tep_db_prepare_input($_POST['mother_middle_name']);
		$mother_surname = tep_db_prepare_input($_POST['mother_surname']);
		$student_mother_occupation = tep_db_prepare_input($_POST['student_mother_occupation']);
		$student_mother_mincome = tep_db_prepare_input($_POST['student_mother_mincome']);
		$student_mother_yincome = tep_db_prepare_input($_POST['student_mother_yincome']);

		$student_address = tep_db_prepare_input($_POST['student_address']);
		$student_village = tep_db_prepare_input($_POST['student_village']);
		$student_district = tep_db_prepare_input($_POST['student_district']);
		$student_taluka = tep_db_prepare_input($_POST['student_taluka']);
		$student_block = tep_db_prepare_input($_POST['student_block']);
		$student_state = tep_db_prepare_input($_POST['student_state']);
		$student_pincode = tep_db_prepare_input($_POST['student_pincode']);
		$student_mobile = tep_db_prepare_input($_POST['student_mobile']);
		$student_mobile_2 = tep_db_prepare_input($_POST['student_mobile_2']);
		$student_mobile_3 = tep_db_prepare_input($_POST['student_mobile_3']);
		$student_phone_std = tep_db_prepare_input($_POST['student_phone_std']);
		$student_phone = tep_db_prepare_input($_POST['student_phone']);
		$student_email = tep_db_prepare_input($_POST['student_email']);
		$student_gender = tep_db_prepare_input($_POST['student_gender']);
		$student_dob = tep_db_prepare_input($_POST['student_dob']);
		$student_maritial = tep_db_prepare_input($_POST['student_maritial']);
		$student_spouse_first_name = tep_db_prepare_input($_POST['student_spouse_first_name']);
		$student_spouse_middle_name = tep_db_prepare_input($_POST['student_spouse_middle_name']);
		$student_spouse_last_name = tep_db_prepare_input($_POST['student_spouse_last_name']);
		$student_spouse_occupation = tep_db_prepare_input($_POST['student_spouse_occupation']);
		$student_spouse_mincome = tep_db_prepare_input($_POST['student_spouse_mincome']);
		$student_spouse_yincome = tep_db_prepare_input($_POST['student_spouse_yincome']);
		$student_area = tep_db_prepare_input($_POST['student_area']);
		$student_family_type = tep_db_prepare_input($_POST['student_family_type']);
		$is_bpl_card = tep_db_prepare_input($_POST['is_bpl_card']);
		$bpl_card_no = tep_db_prepare_input($_POST['bpl_card_no']);
		$bpl_score_card = tep_db_prepare_input($_POST['bpl_score_card']);
		$is_family_id = tep_db_prepare_input($_POST['is_family_id']);
		$family_id = tep_db_prepare_input($_POST['family_id']);
		$student_total_family = tep_db_prepare_input($_POST['student_total_family']);
		$student_total_family_yincome = tep_db_prepare_input($_POST['student_total_family_yincome']);
		$student_family_source_income = tep_db_prepare_input($_POST['student_family_source_income']);
		$student_category = tep_db_prepare_input($_POST['student_category']);
		$is_minority_category = tep_db_prepare_input($_POST['is_minority_category']);
		$student_religion = tep_db_prepare_input($_POST['student_religion']);
		$is_physical_disability = tep_db_prepare_input($_POST['is_physical_disability']);
		$student_physical_disability = tep_db_prepare_input($_POST['student_physical_disability']);
		$is_student_aadhar_card = tep_db_prepare_input($_POST['is_student_aadhar_card']);
		$student_aadhar_card = tep_db_prepare_input($_POST['student_aadhar_card']);
		$student_name_as_aadhar = tep_db_prepare_input($_POST['student_name_as_aadhar']);
		$is_student_pan_card = tep_db_prepare_input($_POST['is_student_pan_card']);
		$student_pan_card = tep_db_prepare_input($_POST['student_pan_card']);
		$is_student_other_proof = tep_db_prepare_input($_POST['is_student_other_proof']);
		$student_other_proof_name = tep_db_prepare_input($_POST['student_other_proof_name']);
		$student_other_proof_number = tep_db_prepare_input($_POST['student_other_proof_number']);
		$student_language_known = tep_db_prepare_input($_POST['student_language_known']);
		$student_qualification = tep_db_prepare_input($_POST['student_qualification']);
		$student_other_qualification = tep_db_prepare_input($_POST['student_other_qualification']);
		$student_stream = tep_db_prepare_input($_POST['student_stream']);
		$student_inst_name = tep_db_prepare_input($_POST['student_inst_name']);
		$student_passing_year = tep_db_prepare_input($_POST['student_passing_year']);
		$student_marks = tep_db_prepare_input($_POST['student_marks']);
		$student_other_skill = tep_db_prepare_input($_POST['student_other_skill']);
		$is_computer_primary_knowledge = tep_db_prepare_input($_POST['is_computer_primary_knowledge']);
		$is_play_computer_game = tep_db_prepare_input($_POST['is_play_computer_game']);
		$is_msoffice_knowledge = tep_db_prepare_input($_POST['is_msoffice_knowledge']);
		$is_internet_knowledge = tep_db_prepare_input($_POST['is_internet_knowledge']);
		$is_unemployed = tep_db_prepare_input($_POST['is_unemployed']);
		$student_occupation = tep_db_prepare_input($_POST['student_occupation']);
		$student_income = tep_db_prepare_input($_POST['student_income']);
		$student_income_source = tep_db_prepare_input($_POST['student_income_source']);
		$student_current_emp = tep_db_prepare_input($_POST['student_current_emp']);
		$student_designation = tep_db_prepare_input($_POST['student_designation']);
		$student_total_exp = tep_db_prepare_input($_POST['student_total_exp']);
		$is_bank_account = tep_db_prepare_input($_POST['is_bank_account']);
		$student_bank_name = tep_db_prepare_input($_POST['student_bank_name']);
		$student_branch = tep_db_prepare_input($_POST['student_branch']);
		$student_account_number = tep_db_prepare_input($_POST['student_account_number']);
		$bank_ifsc_code = tep_db_prepare_input($_POST['bank_ifsc_code']);
		$is_meet_eligibility_creteria = tep_db_prepare_input($_POST['is_meet_eligibility_creteria']);

		$student_height = tep_db_prepare_input($_POST['student_height']);
		$student_weight = tep_db_prepare_input($_POST['student_weight']);
		$student_blood_group = tep_db_prepare_input($_POST['student_blood_group']);

		$is_ready_migrate_job = tep_db_prepare_input($_POST['is_ready_migrate_job']);
		$is_ready_training = tep_db_prepare_input($_POST['is_ready_training']);
		$is_ready_migrate_training = tep_db_prepare_input($_POST['is_ready_migrate_training']);

		$student_remark = tep_db_prepare_input($_POST['student_remark']);

		$student_dob = input_valid_date($student_dob);
		$student_age = (time() >= strtotime($student_dob) ? round((time()-strtotime($student_dob))/(60*60*24*365)) : 0);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'student_type' => $student_type,

			'enq_type' => $enq_type,
			'enq_date' => $enq_date,

			'student_full_name' => $student_full_name,
			'student_middle_name' => $student_middle_name,
			'student_father_name' => $student_father_name,
			'father_middle_name' => $father_middle_name,
			'father_surname' => $father_surname,
			'student_father_occupation' => $student_father_occupation,
			'student_father_mincome' => $student_father_mincome,
			'student_father_yincome' => $student_father_yincome,
			'student_surname' => $student_surname,
			'mother_first_name' => $mother_first_name,
			'mother_middle_name' => $mother_middle_name,
			'mother_surname' => $mother_surname,
			'student_mother_occupation' => $student_mother_occupation,
			'student_mother_mincome' => $student_mother_mincome,
			'student_mother_yincome' => $student_mother_yincome,
			'student_address' => $student_address,
			'student_village' => $student_village,
			'student_district' => $student_district,
			'student_taluka' => $student_taluka,
			'student_block' => $student_block,
			'student_state' => $student_state,
			'student_pincode' => $student_pincode,
			'student_mobile' => $student_mobile,
			'student_mobile_2' => $student_mobile_2,
			'student_mobile_3' => $student_mobile_3,
			'student_phone_std' => $student_phone_std,
			'student_phone' => $student_phone,
			'student_email' => $student_email,
			'student_gender' => $student_gender,
			'student_dob' => $student_dob,
			'student_age' => $student_age,
			'student_maritial' => $student_maritial,
			'student_spouse_first_name' => $student_spouse_first_name,
			'student_spouse_middle_name' => $student_spouse_middle_name,
			'student_spouse_last_name' => $student_spouse_last_name,
			'student_spouse_occupation' => $student_spouse_occupation,
			'student_spouse_mincome' => $student_spouse_mincome,
			'student_spouse_yincome' => $student_spouse_yincome,
			'student_area' => $student_area,
			'student_family_type' => $student_family_type,
			'is_family_id' => $is_family_id,
			'family_id' => $family_id,
			'student_total_family' => $student_total_family,
			'student_total_family_yincome' => $student_total_family_yincome,
			'student_family_source_income' => $student_family_source_income,
			'is_bpl_card' => $is_bpl_card,
			'bpl_card_no' => $bpl_card_no,
			'bpl_score_card' => $bpl_score_card,
			'student_category' => $student_category,
			'is_minority_category' => $is_minority_category,
			'student_religion' => $student_religion,
			'is_physical_disability' => $is_physical_disability,
			'student_physical_disability' => $student_physical_disability,
			'is_student_aadhar_card' => $is_student_aadhar_card,
			'student_aadhar_card' => $student_aadhar_card,
			'student_name_as_aadhar' => $student_name_as_aadhar,
			'is_student_pan_card' => $is_student_pan_card,
			'student_pan_card' => $student_pan_card,
			'is_student_other_proof' => $is_student_other_proof,
			'student_other_proof_name' => $student_other_proof_name,
			'student_other_proof_number' => $student_other_proof_number,
			'student_language_known' => $student_language_known,
			'student_qualification' => $student_qualification,
			'student_other_qualification' => $student_other_qualification,
			'student_stream' => $student_stream,
			'student_inst_name' => $student_inst_name,
			'student_passing_year' => $student_passing_year,
			'student_marks' => $student_marks,
			'student_other_skill' => $student_other_skill,
			'is_computer_primary_knowledge' => $is_computer_primary_knowledge,
			'is_play_computer_game' => $is_play_computer_game,
			'is_msoffice_knowledge' => $is_msoffice_knowledge,
			'is_internet_knowledge' => $is_internet_knowledge,
			'is_unemployed' => $is_unemployed,
			'student_occupation' => $student_occupation,
			'student_income' => $student_income,
			'student_income_source' => $student_income_source,
			'student_current_emp' => $student_current_emp,
			'student_designation' => $student_designation,
			'student_total_exp' => $student_total_exp,
			'is_bank_account' => $is_bank_account,
			'student_bank_name' => $student_bank_name,
			'student_branch' => $student_branch,
			'student_account_number' => $student_account_number,
			'bank_ifsc_code' => $bank_ifsc_code,
			'is_meet_eligibility_creteria' => $is_meet_eligibility_creteria,

			'student_height' => $student_height,
			'student_weight' => $student_weight,
			'student_blood_group' => $student_blood_group,

			'is_ready_migrate_job' => $is_ready_migrate_job,
			'is_ready_training' => $is_ready_training,
			'is_ready_migrate_training' => $is_ready_migrate_training,

			'student_remark' => $student_remark
		);
			//	print_r($arr_db_values);exit;

		$student_photo = student_photo();

		if($student_photo != ''){
			$arr_db_values['student_photo'] = $student_photo;
		}

		switch($action){
			case 'add':
				
				$arr_db_values['student_created'] = 'now()';

				tep_db_perform(TABLE_STUDENTS, $arr_db_values);

				$student_id = tep_db_insert_id();
				upload_documents($student_id);

				$msg = 'added';
			break;

			case 'edit':
				$query = tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");
				
				upload_documents($student_id);
				

				$msg = 'edited';

			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_STUDENTS ." where student_id = '". $student_id ."'");
				tep_db_query("delete from ". TABLE_STUDENT_DOCUMENTS ." where student_id = '". $student_id ."'");

				$msg = 'deleted';
			break;

			case 'delete_document':
				$document_id = tep_db_input($_POST['document_id']);

				$document_query = tep_db_query("select document from ". TABLE_STUDENT_DOCUMENTS ." where student_document_id = '". $document_id ."' and student_id = '". $student_id ."'");
				$document = tep_db_fetch_array($document_query);

				if(file_exists(DIR_FS_UPLOAD . $document['document'])){
					@unlink(DIR_FS_UPLOAD . $document['document']);
				}

				tep_db_query("delete from ". TABLE_STUDENT_DOCUMENTS ." where student_document_id = '". $document_id ."' and student_id = '". $student_id ."'");
				$msg = 'deleted_docs';

				tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit&msg=' . $msg));
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo TITLE ?>: Prospect Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery.labelify.js';?>"></script>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this prospect?")){
					objForm.action_type.value = action_type;
					objForm.student_id.value = int_id;
					objForm.submit();
				}
			}

			function add_document_row(){
				var html = '<tr><td style="border-bottom: 1px dotted #000000; border-right: 1px dotted #000000;"><input type="file" name="document[]" id="document[]"></td><td style="border-bottom: 1px dotted #000000;"><input type="text" name="document_title[]" id="document_title[]" maxlength="150" value=""></td><td><select name="document_type[]" id="document_type[]"><?php foreach($arr_document_type as $document_type){?><option value="<?php echo $document_type;?>"><?php echo $document_type;?></option><?php } ?></select></td><td>&nbsp;</td></tr>';

				$('#student_docs').append(html);
			}

			function delete_document(objForm, document_id){
				if(confirm("Are you want to delete this document?")){
					objForm.action_type.value = 'delete_document';
					objForm.document_id.value = document_id;
					objForm.submit();
				}
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function toggle_qualification(){
				if($('#student_qualification').val() == 'OTHERS'){
					$('.other_qualification').show();
				}else{
					$('.other_qualification').hide();
				}
			}
			function toggle_marital(){
				if($('#student_maritial').val() == 'MARRIED'){
					$('.student_spouse').show();
				}else{
					$('.student_spouse').hide();
				}
			}

			function updateNameAsAadhar(){
				if($('input[name="chkSameAsNameAN"]:checked').val() == '1'){
					var student_fname = $('input[name="student_full_name"]').val();
					var student_middle_name = $('input[name="student_middle_name"]').val();
					var student_surname = $('input[name="student_surname"]').val();

					$('input[name="student_name_as_aadhar"]').val( student_fname + ' ' + student_middle_name + ' ' + student_surname);
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#enq_date').datepicker({
					dateFormat: "dd-mm-yy",
				});

				//$('#enq_date').bind('change');
			});


			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#student_dob').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true,
					yearRange: "-60:-10"
				});

				$('#student_dob').bind('change', calculate_age);
			});
			
			function calculate_age(){
				var dob = $('#student_dob').val();
				var arrDOB = new Array();
				arrDOB = dob.split('-');
				dob = arrDOB[2] + '-' + arrDOB[1] + '-' + arrDOB[0];
				dob = new Date(dob);
				var today = new Date();
				var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
				age = (age > 0 ? age : 0);
				$('#student_age').val(age);
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
												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" )
												{
													if($_GET['actionType'] == "edit"){
														$int_id = $_GET['int_id'];

														$info_query_raw = "select student_id, centre_id, course_id, student_photo, student_type, enq_type, enq_date, student_full_name, student_father_name, student_surname, student_middle_name, father_middle_name, father_surname, student_father_occupation, 	student_father_mincome, student_father_yincome, mother_first_name, mother_middle_name, mother_surname, student_mother_occupation, student_mother_mincome, student_mother_yincome, student_address, student_village, student_district, student_taluka, student_block, student_state, student_pincode, student_mobile, student_mobile_2, student_mobile_3, student_phone_std, student_phone, student_email, student_gender, date_format(student_dob, '%d-%m-%Y') as student_dob, student_age, student_maritial, student_spouse_first_name,student_spouse_middle_name,student_spouse_last_name,	student_spouse_occupation,student_spouse_mincome,student_spouse_yincome, student_area, student_family_type, is_bpl_card, bpl_card_no, bpl_score_card, is_family_id, family_id, student_total_family, student_total_family_yincome, student_family_source_income, student_category, is_minority_category, student_religion, is_physical_disability, student_physical_disability, is_student_aadhar_card, student_aadhar_card, student_name_as_aadhar, is_student_pan_card, student_pan_card, is_student_other_proof,student_other_proof_name,student_other_proof_number, student_language_known, student_qualification, student_other_qualification, student_stream,student_inst_name,student_passing_year,student_marks,student_other_skill, is_computer_primary_knowledge, is_play_computer_game, is_msoffice_knowledge, is_internet_knowledge, is_unemployed, student_occupation, student_income, student_income_source, student_current_emp,student_designation,student_total_exp, is_bank_account, student_bank_name, student_branch, student_account_number, bank_ifsc_code, is_meet_eligibility_creteria, student_height, student_weight, student_blood_group, is_ready_migrate_job, is_ready_training, is_ready_migrate_training, student_remark, student_created from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' and student_type = 'PROSPECT' and is_deactivated != '1'";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
														//print_r($info); exit;
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Prospect Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Prospect Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $info['student_id']; ?>"> 
																<input type="hidden" name="document_id" id="document_id" value=""> 
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<!-- <tr>
																		<td class="arial12LGrayBold" valign="top">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:
																			<select name="course_id" id="course_id" class="required" style="width:auto;">
																				<option value="">Please choose</option>
																				<?php
																					$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id order by course_name";
																					$course_query = tep_db_query($course_query_raw);
																					
																					while($course = tep_db_fetch_array($course_query)){
																				?>
																				<option value="<?php echo $course['course_id'];?>" <?php echo($info['course_id'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';?></option>
																				<?php } ?>
																			</select><br><br>
																		</td>
																	</tr> -->
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Pros Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="pros_info">
																								<tr>
																									<td class="arial12LGrayBold"  width="10%">&nbsp;Type of Enquiry&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray" maxlength="255" >
																										<select name="enq_type" id="enq_type" class="required" style="width:200px;"  onchange="javascript: get_batch('');">
																											<option value="">Please choose</option>
																											<?php foreach($enq_type_array as $enq_key => $enq_value){ ?>
																											<option value="
																											<?php echo $enq_key;?>" <?php echo($info['enq_type'] == $enq_key? 'selected="selected"' : '');?>><?php echo $enq_value;?></option>
																										<?php } ?>											
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Enquiry For&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<select name="course_id" id="course_id" class="required" style="width:200px;"  onchange="javascript: get_batch('');">
																											<option value="">Please choose</option>
																											<?php
																												$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id order by course_name";
																												$course_query = tep_db_query($course_query_raw);
																												
																												while($course = tep_db_fetch_array($course_query)){
																											?>
																											
																											<option value="<?php echo $course['course_id'];?>" <?php echo($info['course_id'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="left">&nbsp; Date of Enquiry&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="enq_date" id="enq_date" value="<?php echo ($dupError ? $_POST['enq_date'] : (isset($info['enq_date']) ? $info['enq_date'] : date("d-m-Y"))) ?>" class="required">
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
																									<td>
																										<input type="text" name="student_full_name" id="student_full_name" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_full_name'] : $info['student_full_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_middle_name" id="student_middle_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_middle_name'] : $info['student_middle_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_surname" id="student_surname" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_surname'] : $info['student_surname']) ?>" class="required">
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
																									<td>
																										<input type="text" name="student_father_name" id="student_father_name" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_father_name'] : $info['student_father_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="father_middle_name" id="father_middle_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['father_middle_name'] : $info['father_middle_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="father_surname" id="father_surname" maxlength="50" value="<?php echo  ($dupError ? $_POST['father_surname'] : $info['father_surname']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;Father Occupation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_father_occupation" id="student_father_occupation" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($occupation_array as $k_occupation=>$v_occupation){?>
																											<option value="<?php echo $k_occupation;?>" <?php echo($info['student_father_occupation'] == $k_occupation? 'selected="selected"' : '');?>><?php echo $v_occupation;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Monthly Income&nbsp;:</td>
																									<td>
																										<input type="text" name="student_father_mincome" id="student_father_mincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_father_mincome'] : ($info['student_father_mincome'] != '' ? $info['student_father_mincome'] : '0')) ?>" class="required" style="width:100px;">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Yearly Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_father_yincome" id="student_father_yincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_father_yincome'] : ($info['student_father_yincome'] != '' ? $info['student_father_yincome'] : '0')) ?>" class="required" style="width:100px;">
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
																									<td>
																										<input type="text" name="mother_first_name" id="mother_first_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['mother_first_name'] : $info['mother_first_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="mother_middle_name" id="mother_middle_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['mother_middle_name'] : $info['mother_middle_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="mother_surname" id="mother_surname" maxlength="50" value="<?php echo  ($dupError ? $_POST['mother_surname'] : $info['mother_surname']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;Mother Occupation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_mother_occupation" id="student_mother_occupation" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($occupation_array as $k_occupation=>$v_occupation){?>
																											<option value="<?php echo $k_occupation;?>" <?php echo($info['student_mother_occupation'] == $k_occupation? 'selected="selected"' : '');?>><?php echo $v_occupation;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Monthly Income&nbsp;:</td>
																									<td>
																										<input type="text" name="student_mother_mincome" id="student_mother_mincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_mother_mincome'] : ($info['student_mother_mincome'] != '' ? $info['student_mother_mincome'] : '0')) ?>" class="number" style="width:100px;">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Yearly Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_mother_yincome" id="student_mother_yincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_mother_yincome'] : ($info['student_mother_yincome'] != '' ? $info['student_mother_yincome'] : '0')) ?>" class="number" style="width:100px;">
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Address&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="5">
																										<input type="text" name="student_address" id="student_address" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_address'] : $info['student_address']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Village&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%">
																										<input type="text" name="student_village" id="student_village" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_village'] : $info['student_village']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="5%">&nbsp;District&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										<select name="student_district" id="student_district" class="required">
																											<option value="">Please choose</option>
																											<?php
																												$disctrict_query_raw = " select district_id, district_name from ". TABLE_DISTRICTS ." where 1 order by district_name";
																												$disctrict_query = tep_db_query($disctrict_query_raw);
																												
																												while($disctrict = tep_db_fetch_array($disctrict_query)){
																											?>
																											<option value="<?php echo $disctrict['district_name'];?>" <?php echo($info['student_district'] == $disctrict['district_name'] ? 'selected="selected"' : '');?>><?php echo $disctrict['district_name'];?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Taluka&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_taluka" id="student_taluka" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_taluka'] : $info['student_taluka']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Block&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_block" id="student_block" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_block'] : $info['student_block']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;State&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_state" id="student_state" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($arr_states as $kState=>$vState){ ?>
																											<option value="<?php echo $kState;?>" <?php echo($info['student_state'] == $kState ? 'selected="selected"' : '');?>><?php echo $vState;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Pin Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_pincode" id="student_pincode" maxlength="8" value="<?php echo  ($dupError ? $_POST['student_pincode'] : $info['student_pincode']) ?>" class="required">
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
																									<td width="15%">
																										<input type="text" name="student_mobile" id="student_mobile" maxlength="10" minlength="10" value="<?php echo  ($dupError ? $_POST['student_mobile'] : $info['student_mobile']) ?>" class="required number">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;2<span class="sub_text">nd</span> Mobile Number&nbsp;:</td>
																									<td>
																										<input type="text" name="student_mobile_2" id="student_mobile_2" maxlength="10" minlength="10" value="<?php echo  ($dupError ? $_POST['student_mobile_2'] : $info['student_mobile_2']) ?>" class="number">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;3<span class="sub_text">rd</span> Mobile Number&nbsp;:</td>
																									<td width="10%">
																										<input type="text" name="student_mobile_3" id="student_mobile_3" maxlength="10" minlength="10" value="<?php echo  ($dupError ? $_POST['student_mobile_3'] : $info['student_mobile_3']) ?>" class="number">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Landline (Resi)&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										Std Code <input type="text" name="student_phone_std" id="student_phone_std" maxlength="6" value="<?php echo  ($dupError ? $_POST['student_phone_std'] : $info['student_phone_std']) ?>" style="width:50px;">&nbsp;
																										<input type="text" name="student_phone" id="student_phone" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_phone'] : $info['student_phone']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Email&nbsp;:</td>
																									<td colspan="3">
																										<input type="text" name="student_email" id="student_email" maxlength="150" value="<?php echo  ($dupError ? $_POST['student_email'] : $info['student_email']) ?>" class="email">
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
																									<td class="arial12LGrayBold" width="15%">
																										<?php foreach($arr_gender as $k_gender=>$v_gender){?>
																											<input type="radio" name="student_gender" id="student_gender" value="<?php echo $k_gender;?>" class="required" <?php echo ($info['student_gender'] == $k_gender ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_gender;?>&nbsp;
																										<?php } ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;DOB (DD-MM-YYYY) &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										<input type="text" name="student_dob" id="student_dob" value="<?php echo  ($dupError ? $_POST['student_dob'] : $info['student_dob']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Age &nbsp;:</td>
																									<td>
																										<input type="text" name="student_age" id="student_age" value="<?php echo  ($dupError ? $_POST['student_age'] : ($info['student_age'] != '' ? $info['student_age'] : '0')) ?>" class="number" style="width:50px;">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Maritial Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="5">
																										<select name="student_maritial" id="student_maritial" onchange="javascript: toggle_marital();" class="required">
																											<?php
																												foreach($arr_maritial_status as $k_m_status=>$v_m_status){
																											?>
																											<option value="<?php echo $k_m_status;?>" <?php echo($info['student_maritial'] == $k_m_status ? 'selected="selected"' : '');?>><?php echo $v_m_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr class ="student_spouse" style="display:none;">
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Spouse Name &nbsp;:</td>
																									<td>
																										<input type="text" name="student_spouse_first_name" id="student_spouse_first_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_spouse_first_name'] : $info['student_spouse_first_name']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Spouse Middle Name&nbsp;:</td>
																									<td>
																										<input type="text" name="student_spouse_middle_name" id="student_spouse_middle_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_spouse_middle_name'] : $info['student_spouse_middle_name']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Spouse Surname&nbsp;:</td>
																									<td>
																										<input type="text" name="student_spouse_last_name" id="student_spouse_last_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_spouse_last_name'] : $info['student_spouse_last_name']) ?>" class="required">
																									</td>
																								</tr>
																								<tr class ="student_spouse" style="display:none;">
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Occupation&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_spouse_occupation" id="student_spouse_occupation" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($occupation_array as $k_occupation=>$v_occupation){?>
																											<option value="<?php echo $k_occupation;?>" <?php echo($info['student_spouse_occupation'] == $k_occupation? 'selected="selected"' : '');?>><?php echo $v_occupation;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Monthly Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_spouse_mincome" id="student_spouse_mincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_spouse_mincome'] : ($info['student_spouse_mincome'] != '' ? $info['student_spouse_mincome'] : '0')) ?>" class="required" style="width:50px;">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Yearly Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_spouse_yincome" id="student_spouse_yincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_spouse_yincome'] : ($info['student_spouse_yincome'] != '' ? $info['student_spouse_yincome'] : '0')) ?>" class="required" style="width:50px;">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_marital();
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Student Area&nbsp;:</td>
																									<td colspan="5">
																										<select name="student_area" id="student_area">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_student_area as $k_area=>$v_area){
																											?>
																											<option value="<?php echo $k_area;?>" <?php echo($arr_student_info['student_area'] == $k_area ? 'selected="selected"' : '');?>><?php echo $v_area;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Family Type&nbsp;&nbsp;:</td>
																									<td>
																										<select name="student_family_type" id="student_family_type">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_family_type as $k_ft_status=>$v_ft_status){
																											?>
																											<option value="<?php echo $k_ft_status;?>" <?php echo($info['student_family_type'] == $k_ft_status ? 'selected="selected"' : '');?>><?php echo $v_ft_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;B.P.L. CARD &nbsp;&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_bpl_card" id="is_bpl_card" value="<?php echo $k_status;?>" <?php echo ($info['is_bpl_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_bpl_card', 'bpl_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold bpl_card" align="right">&nbsp;B.P.L. Card No &nbsp;<font color="#ff0000">*</font>&nbsp;:&nbsp;
																											<input type="text" name="bpl_card_no" id="bpl_card_no" value="<?php echo  ($dupError ? $_POST['bpl_card_no'] : $info['bpl_card_no']) ?>" class="required" style="width:75px;">
																											&nbsp;B.P.L. Score Card &nbsp;<font color="#ff0000">*</font>&nbsp;:&nbsp;
																											<input type="text" name="bpl_score_card" id="bpl_score_card" value="<?php echo  ($dupError ? $_POST['bpl_score_card'] : $info['bpl_score_card']) ?>" class="required" style="width:75px;">
																										</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Total Family Members &nbsp;:</td>
																									<td>
																										<input type="text" name="student_total_family" id="student_total_family" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_total_family'] : ($info['student_total_family'] != '' ? $info['student_total_family'] : '0')) ?>" class="number" style="width:50px;">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Total Yearly Family Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_total_family_yincome" id="student_total_family_yincome" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_total_family_yincome'] : ($info['student_total_family_yincome'] != '' ? $info['student_total_family_yincome'] : '0')) ?>" style="width:100px;;">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Source of Income &nbsp;:</td>
																									<td>
																										<input type="text" name="student_family_source_income" id="student_family_source_income" maxlength="100" value="<?php echo  ($dupError ? $_POST['student_family_source_income'] : ($info['student_family_source_income'] != '' ? $info['student_family_source_income'] : '')) ?>" style="width:100px;">
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_bpl_card', 'bpl_card');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Have Family ID&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_family_id" id="is_family_id" value="<?php echo $k_status;?>" <?php echo ($info['is_family_id'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_family_id', 'family_id');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold family_id" align="right">
																											Family ID&nbsp;:
																											&nbsp;<input type="text" name="family_id" id="family_id" value="<?php echo  ($dupError ? $_POST['family_id'] : $info['family_id']) ?>">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_family_id', 'family_id');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Category&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_category" id="student_category" class="required">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_category as $k_category=>$v_category){
																											?>
																											<option value="<?php echo $k_category;?>" <?php echo($info['student_category'] == $k_category ? 'selected="selected"' : '');?>><?php echo $v_category;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Minority Category<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_minority_category" id="is_minority_category" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_minority_category'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Religion&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_religion" id="student_religion" class="required">
																											<option value="">Please choose</option>
																											<?php
																												foreach($arr_religion as $k_religion=>$v_religion){
																											?>
																											<option value="<?php echo $k_religion;?>" <?php echo($info['student_religion'] == $k_religion ? 'selected="selected"' : '');?>><?php echo $v_religion;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Physical Unability/Disability<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_physical_disability" id="is_physical_disability" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_physical_disability'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_physical_disability', 'physical_disability');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold physical_disability" align="right">&nbsp;
																											Physical Disablity &nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_physical_disability" id="student_physical_disability" value="<?php echo  ($dupError ? $_POST['student_physical_disability'] : $info['student_physical_disability']) ?>" class="required physical_disability">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_physical_disability', 'physical_disability');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Aadhar Card<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_student_aadhar_card" id="is_student_aadhar_card" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_student_aadhar_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_student_aadhar_card', 'aadhar_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold aadhar_card" align="right">
																											Aadhar Card&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_aadhar_card" id="student_aadhar_card" value="<?php echo  ($dupError ? $_POST['student_aadhar_card'] : $info['student_aadhar_card']) ?>" class="required">
																										</span>
																										<span class="arial12LGrayBold aadhar_card" align="right">
																											Name as per Aadhar Card&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_name_as_aadhar" id="student_name_as_aadhar" value="<?php echo  ($dupError ? $_POST['student_name_as_aadhar'] : $info['student_name_as_aadhar']) ?>" class="required">&nbsp;<label for="chkSameAsNameAN"><input type="checkbox" name="chkSameAsNameAN" id="chkSameAsNameAN" value="1" onclick="updateNameAsAadhar();">Same as student name</label>
																										</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;PAN Card No<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_student_pan_card" id="is_student_pan_card" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_student_pan_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_student_pan_card', 'pan_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										
																										<span class="arial12LGrayBold pan_card" align="right">
																											&nbsp;PAN Card&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_pan_card" id="student_pan_card" value="<?php echo  ($dupError ? $_POST['student_pan_card'] : $info['student_pan_card']) ?>" class="required pan_card">
																										</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Any Other Govt ID Proof<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_student_other_proof" id="is_student_other_proof" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_student_other_proof'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_student_other_proof', 'other_proof');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										
																										<span class="arial12LGrayBold other_proof" align="right">
																											&nbsp;ID Proof Name&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_other_proof_name" id="student_other_proof_name" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_other_proof_name'] : $info['student_other_proof_name']) ?>" class="required other_proof">
																										</span>
																										<span class="arial12LGrayBold other_proof" align="right">
																											&nbsp;ID Proof No&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_other_proof_number" id="student_other_proof_number" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_other_proof_number'] : $info['student_other_proof_number']) ?>" class="required">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_student_aadhar_card', 'aadhar_card');
																									toggle_element('is_student_pan_card', 'pan_card');
																									toggle_element('is_student_other_proof', 'other_proof');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Student meet eligibilty creteria&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_meet_eligibility_creteria" id="is_meet_eligibility_creteria" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_meet_eligibility_creteria'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Height&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_height" id="student_height" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_height'] : $info['student_height']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Weight&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_weight" id="student_weight" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_weight'] : $info['student_weight']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Blood Group&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_blood_group" id="student_blood_group" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_blood_group'] : $info['student_blood_group']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for job &nbsp;&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_migrate_job" id="is_ready_migrate_job" value="<?php echo $k_status;?>" <?php echo ($info['is_ready_migrate_job'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready for 10 - 12 hrs Training&nbsp;&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_training" id="is_ready_training" value="<?php echo $k_status;?>" <?php echo ($info['is_ready_training'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for training&nbsp;&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_migrate_training" id="is_ready_migrate_training" value="<?php echo $k_status;?>" <?php echo ($info['is_ready_migrate_training'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Language Known&nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_language_known" id="student_language_known" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_language_known'] : $info['student_language_known']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_qualification" id="student_qualification" class="required" onchange="javascript: toggle_qualification();">
																											<option value="">Please choose</option>
																											<?php foreach($arr_qualification as $k_qualification=>$v_qualification){?>
																											<option value="<?php echo $k_qualification;?>" <?php echo($info['student_qualification'] == $k_qualification ? 'selected="selected"' : '');?>><?php echo $v_qualification;?></option>
																											<?php } ?>
																										</select>
																										<span class="arial12LGrayBold other_qualification" align="right">
																											&nbsp;Other Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_other_qualification" id="student_other_qualification" value="<?php echo  ($dupError ? $_POST['student_other_qualification'] : $info['student_other_qualification']) ?>" class="required">
																										</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Stream &nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_stream" id="student_stream" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_stream'] : $info['student_stream']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Institute Name &nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_inst_name" id="student_inst_name" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_inst_name'] : $info['student_inst_name']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Year of Passing &nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_passing_year" id="student_passing_year" value="<?php echo  ($dupError ? $_POST['student_passing_year'] : $info['student_passing_year']) ?>">
																									</td>
																								</tr>
																								<tr>
																									
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Marks Obtained &nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_marks" id="student_marks" value="<?php echo  ($dupError ? $_POST['student_marks'] : $info['student_marks']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Other Skills &nbsp;&nbsp;:</td>
																									<td>
																										<input type="text" name="student_other_skill" id="student_other_skill" value="<?php echo  ($dupError ? $_POST['student_other_skill'] : $info['student_other_skill']) ?>">
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<script type="text/javascript">
																				<!--
																					toggle_qualification();
																				//-->
																				</script>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Computer Literacy</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Primary Knowledge of Computers&nbsp;&nbsp;:</td>
																									<td class="arial12LGrayBold">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_computer_primary_knowledge" id="is_computer_primary_knowledge" value="<?php echo $k_status;?>" <?php echo ($info['is_computer_primary_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Can Play Game on Computer&nbsp;&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_play_computer_game" id="is_play_computer_game" value="<?php echo $k_status;?>" <?php echo ($info['is_play_computer_game'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;MS Office Knowledge&nbsp;&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_msoffice_knowledge" id="is_msoffice_knowledge" value="<?php echo $k_status;?>" <?php echo ($info['is_msoffice_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Internet Knowledge&nbsp;&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_internet_knowledge" id="is_internet_knowledge" value="<?php echo $k_status;?>" <?php echo ($info['is_internet_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Employed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_unemployed" id="is_unemployed" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_unemployed'] == $k_status ? 'checked="checked"' : '');?>  onclick="toggle_element('is_unemployed', 'employe');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Occupation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_occupation" id="student_occupation" class="required" onchange="javascript: toggle_qualification();">
																											<option value="">Please choose</option>
																											<?php foreach($employee_detail_array as $k_employee_detail=>$v_employee_detail){?>
																											<option value="<?php echo $k_employee_detail;?>" <?php echo($info['student_occupation'] == $k_employee_detail ? 'selected="selected"' : '');?>><?php echo $v_employee_detail;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold">&nbsp;Current Employer/Company Name&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_current_emp" id="student_current_emp" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_current_emp'] : $info['student_current_emp']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold">&nbsp;Designation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_designation" id="student_designation" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_designation'] : $info['student_designation']) ?>" class="required">
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Monthly Income&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_income" id="student_income" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_income'] : $info['student_income']) ?>" class="required number">
																									</td>
																									<td class="arial12LGrayBold">&nbsp;Total Work Experience&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_total_exp" id="student_total_exp" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_total_exp'] : $info['student_total_exp']) ?>" class="required">
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Industry/Sector of Employment&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_income_source" id="student_income_source" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_income_source'] : $info['student_income_source']) ?>" class="required">
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<script type="text/javascript">
																				<!--
																					toggle_element('is_unemployed', 'employe');
																				//-->
																				</script>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Bank Info</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="13%" align="right">&nbsp;Bank Account&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_bank_account" id="is_bank_account" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_bank_account'] == $k_status ? 'checked="checked"' : '');?>  onclick="javascript: toggle_element('is_bank_account', 'bank_account');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" align="right">&nbsp;Name of the Bank&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_bank_name" id="student_bank_name" maxlength="150" value="<?php echo  ($dupError ? $_POST['student_bank_name'] : $info['student_bank_name']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_branch" id="student_branch" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_branch'] : $info['student_branch']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Account Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_account_number" id="student_account_number" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_account_number'] : $info['student_account_number']) ?>" class="required">
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" align="right">&nbsp;Bank IFSC Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="bank_ifsc_code" id="bank_ifsc_code" maxlength="20" value="<?php echo  ($dupError ? $_POST['bank_ifsc_code'] : $info['bank_ifsc_code']) ?>" class="required">
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<script type="text/javascript">
																				<!--
																					toggle_element('is_bank_account', 'bank_account');
																				//-->
																				</script>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Student Photo / Documents</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" valign="top">&nbsp;Student Photo&nbsp;&nbsp;:</td>
																									<td>
																										<?php 
																											$img_mandatory = true;
																											if($info['student_photo']!=''){
																												$img_mandatory = false;
																										?>
																										<img src="<?php echo DIR_WS_UPLOAD . $info['student_photo'];?>" width="150" style="padding:3px; border: 1px solid black;"><br><br>
																										<?php } ?>
																										<input type="file" name="student_photo" id="student_photo">
																									</td>
																								</tr>
																								<tr>
																									<td colspan="2" style="border: none;">
																										<table cellpadding="5" cellspacing="3" border="0" width="70%" id="student_docs">
																											<tr>
																												<td class="arial12LGrayBold">Document File</td>
																												<td class="arial12LGrayBold">Document Name</td>
																												<td class="arial12LGrayBold">Document Type</td>
																												<td>&nbsp;</td>
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
																												<td class="verdana12Blue">&nbsp;&nbsp;[&nbsp;<a href="javascript:;" onclick="javascript:delete_document(document.frmDetails, '<?php echo $student_documents['student_document_id'];?>');"><img src="images/delete.jpg" align="absmiddle" title="Delete" alt="Delete"></a>&nbsp;]</td>
																											</tr>
																											<?php
																													}
																												}
																											?>
																											<tr>
																												<td style="border-bottom: 1px dotted #000000; border-right: 1px dotted #000000;"><input type="file" name="document[]" id="document[]"></td>
																												<td style="border-bottom: 1px dotted #000000;">
																													<input type="text" name="document_title[]" id="document_title[]" maxlength="150" value="<?php echo  ($dupError ? $_POST['document_title'] : $info['document_title']) ?>">
																												</td>
																												<td>
																													<select name="document_type[]" id="document_type[]">
																														<?php foreach($arr_document_type as $document_type){?>
																														<option value="<?php echo $document_type;?>"><?php echo $document_type;?></option>
																														<?php } ?>
																													</select>
																												</td>
																												<td>&nbsp;</td>
																											</tr>
																										</table>
																									</td>
																								</tr>
																								<tr>
																									<td colspan="2"><a href="javascript:;" onclick="javascript: add_document_row();">Add More</a></td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Remark</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="7%" valign="top">&nbsp;Remark&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="student_remark" id="student_remark" cols="40" rows="6"><?php echo  ($dupError ? $_POST['student_remark'] : $info['student_remark']) ?></textarea>
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
														<td class="arial18BlueN">Prospect Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Prospect</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select s.student_id, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.student_mobile, enq_date, course_id from " . TABLE_STUDENTS . " s where student_type = 'PROSPECT'";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query_raw .= " order by s.student_id desc";

																$listing_query = tep_db_query($listing_query_raw);
																

																
																
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="student_id" id="student_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>Student Name</th>
																		<th>Mobile</th>
																		<th>Enquiry for</th>
																		<th>Status</th>
																		<th>Last Follow up date</th>
																		<th>Next Contact Date</th>
																		<th>Date of Enquiry</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while($listing = tep_db_fetch_array($listing_query) ){
																				$contact_listing_query_raw ="select student_id, contact_log_id, contact_log_remark, contact_log_date, contact_log_next_date, contact_log_status from " . TABLE_PROS_CONTACT_LOGS ." WHERE student_id = '" .$listing['student_id']. "'Order By contact_log_id DESC Limit 1";
																				$contact_listing_query = tep_db_query($contact_listing_query_raw);

																				$student_contact_info = array();
																				if(tep_db_num_rows($contact_listing_query)){
																					$student_contact_info = tep_db_fetch_array($contact_listing_query);
																				}

																				$course_query_raw = "SELECT course_name from courses where course_id = '" . $listing['course_id'] . "'";
																				$course_query = tep_db_query($course_query_raw);

																				$course_info = array();
																				if(tep_db_num_rows($course_query)){
																					$course_info = tep_db_fetch_array($course_query);
																				}
																				
																	?>
																		<tr>
																			<td valign="top">
																				<?php echo $listing['student_full_name'] . ' ' . $listing['student_middle_name'] . ' ' . $listing['student_surname']; ?>
																			</td>
																			<td valign="top">
																				<?php echo $listing['student_mobile']; ?>
																			</td>
																			<td valign="top">
																				<?php echo (isset($course_info['course_name']) ? $course_info['course_name'] : '-'); ?>
																			</td>
																			<td valign="top">
																				<?php echo (isset($student_contact_info['contact_log_status']) ? $student_contact_info['contact_log_status'] : '-'); ?>
																			</td>
																			<td valign="top">
																				<?php echo (isset($student_contact_info['contact_log_date']) ? display_valid_date($student_contact_info['contact_log_date']) : '-'); ?>
																			</td>
																			<td valign="top">
																				<?php echo (isset($student_contact_info['contact_log_next_date']) ? display_valid_date($student_contact_info['contact_log_next_date']) : '-'); ?>
																			</td>
																			<td valign="top">
																				<?php echo display_valid_date($listing['enq_date']); ?>
																			<td valign="top">
																				<a href="<?php echo  tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&	int_id=".$listing['student_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>
																				&nbsp;&nbsp;&nbsp;
																				<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['student_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a>&nbsp;&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_ENROLL_STUDENTS,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=create_student&int_id=".$listing['student_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>user_enroll.png" border="0" width="20" title="Add as Student"></a>&nbsp;&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_PROS_CONTACT,tep_get_all_get_params(array('msg','int_id'))."stud_id=".$listing['student_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>add_option_2.png" border="0" width="20" title="Student Contact Logs"></a>
																			</td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Student Name
																					null, // Mobile
																					null, //Enquiry For
																					null, //Status
																					null, //Last follow date
																					null,//Next Contact Date
																					null, //DOE
																					{ "bSortable": false}
																				],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bstudent_idSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Prospect Found !!</td>
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
						</tr>
					</table>
				</td>
			</tr>
		</tr>
		</table>
	</body>
</html>