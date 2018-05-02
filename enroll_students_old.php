<?php	
	include('includes/application_top.php');

	check_valid_type('CENTRE');

	$arrMessage = array("deleted"=>"Student has been deleted successfully", 'added'=>'Student has been added successfully',"edited"=>"Student has been updated successfully", "deleted_docs"=>"Document has been deleted successfully", "asses_edited"=>"Student Assesment Test Detail has been updated successfully", "place_edited" => "Student placement has been updated successfully!!", "ac_status_edited" => "Student Bank Account Status has been updated successfully!!", "aadhar_status_edited" => "Student Aadhar Card Status has been updated successfully!!", "update_res_allow" => "Student Non Residential Allowance has been updated successfully!!", "update_place_allow" => "Student Placement Allowance has been updated successfully!!");

	$action = $_POST['action_type'];

	function upload_documents() {
		$arr_uploaded_documents = array();
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
						$arr_uploaded_documents[$dest_filename] = array('filename' =>$dest_filename, 'document_type' => $document_type, 'document_title' => $document_title);
					}
				}
			}
		}

		return $arr_uploaded_documents;
	}

	function student_photo() {
		if($_FILES['student_photo']['name'] != ''){
			$ext = get_extension($_FILES['student_photo']['name']);
			$src = $_FILES['student_photo']['tmp_name'];

			$dest_filename = 'photo_' . time() . date("His") . $ext;
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
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$student_type = 'ENROLLED';

		if(isset($_SESSION['sess_student_info']) && is_array($_SESSION['sess_student_info'])){
			unset($_SESSION['sess_student_info']);
		}

		$student_full_name = tep_db_prepare_input($_POST['student_full_name']);
		$student_middle_name = tep_db_prepare_input($_POST['student_middle_name']);
		$student_surname = tep_db_prepare_input($_POST['student_surname']);
		$student_father_name = tep_db_prepare_input($_POST['student_father_name']);
		$father_middle_name = tep_db_prepare_input($_POST['father_middle_name']);
		$father_surname = tep_db_prepare_input($_POST['father_surname']);

		$mother_first_name = tep_db_prepare_input($_POST['mother_first_name']);
		$mother_middle_name = tep_db_prepare_input($_POST['mother_middle_name']);
		$mother_surname = tep_db_prepare_input($_POST['mother_surname']);

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
		$student_family_type = tep_db_prepare_input($_POST['student_family_type']);
		$is_bpl_card = tep_db_prepare_input($_POST['is_bpl_card']);
		$bpl_card_no = tep_db_prepare_input($_POST['bpl_card_no']);
		$bpl_score_card = tep_db_prepare_input($_POST['bpl_score_card']);
		$is_family_id = tep_db_prepare_input($_POST['is_family_id']);
		$family_id = tep_db_prepare_input($_POST['family_id']);
		$student_category = tep_db_prepare_input($_POST['student_category']);
		$is_minority_category = tep_db_prepare_input($_POST['is_minority_category']);
		$student_religion = tep_db_prepare_input($_POST['student_religion']);
		$is_physical_disability = tep_db_prepare_input($_POST['is_physical_disability']);
		$student_physical_disability = tep_db_prepare_input($_POST['student_physical_disability']);
		$is_student_aadhar_card = tep_db_prepare_input($_POST['is_student_aadhar_card']);
		$student_aadhar_card = tep_db_prepare_input($_POST['student_aadhar_card']);
		$is_student_pan_card = tep_db_prepare_input($_POST['is_student_pan_card']);
		$student_pan_card = tep_db_prepare_input($_POST['student_pan_card']);
		$student_language_known = tep_db_prepare_input($_POST['student_language_known']);
		$student_qualification = tep_db_prepare_input($_POST['student_qualification']);
		$student_other_qualification = tep_db_prepare_input($_POST['student_other_qualification']);
		$is_computer_primary_knowledge = tep_db_prepare_input($_POST['is_computer_primary_knowledge']);
		$is_play_computer_game = tep_db_prepare_input($_POST['is_play_computer_game']);
		$is_msoffice_knowledge = tep_db_prepare_input($_POST['is_msoffice_knowledge']);
		$is_internet_knowledge = tep_db_prepare_input($_POST['is_internet_knowledge']);
		$is_unemployed = tep_db_prepare_input($_POST['is_unemployed']);
		$student_occupation = tep_db_prepare_input($_POST['student_occupation']);
		$student_income = tep_db_prepare_input($_POST['student_income']);
		$student_income_source = tep_db_prepare_input($_POST['student_income_source']);
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

		$fees_amount = tep_db_prepare_input($_POST['fees_amount']);
		$fees_collected_date = tep_db_prepare_input($_POST['fees_collected_date']);
		$fees_payment_type = tep_db_prepare_input($_POST['fees_payment_type']);
		$is_fees_deposit = tep_db_prepare_input($_POST['is_fees_deposit']);
		$fees_deposit_date = tep_db_prepare_input($_POST['fees_deposit_date']);
		$fees_deposit_bank = tep_db_prepare_input($_POST['fees_deposit_bank']);
		$fees_deposit_cleared = tep_db_prepare_input($_POST['fees_deposit_cleared']);

		$is_due_balance = tep_db_prepare_input($_POST['is_due_balance']);
		$due_balance = tep_db_prepare_input($_POST['due_balance']);

		$student_remark = tep_db_prepare_input($_POST['student_remark']);

		$course_option = tep_db_prepare_input($_POST['course_option']);

		$student_dob = input_valid_date($student_dob);
		$fees_deposit_date = input_valid_date($fees_deposit_date);
		$fees_collected_date = input_valid_date($fees_collected_date);

		$student_age = (time() >= strtotime($student_dob) ? round((time()-strtotime($student_dob))/(60*60*24*365)) : 0);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'batch_id' => $batch_id,
			'student_type' => $student_type,
			'course_option' => $course_option,
			'student_full_name' => $student_full_name,
			'student_middle_name' => $student_middle_name,
			'student_father_name' => $student_father_name,
			'father_middle_name' => $father_middle_name,
			'father_surname' => $father_surname,
			'student_surname' => $student_surname,
			'mother_first_name' => $mother_first_name,
			'mother_middle_name' => $mother_middle_name,
			'mother_surname' => $mother_surname,
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
			'student_family_type' => $student_family_type,
			'is_family_id' => $is_family_id,
			'family_id' => $family_id,
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
			'is_student_pan_card' => $is_student_pan_card,
			'student_pan_card' => $student_pan_card,
			'student_language_known' => $student_language_known,
			'student_qualification' => $student_qualification,
			'student_other_qualification' => $student_other_qualification,
			'is_computer_primary_knowledge' => $is_computer_primary_knowledge,
			'is_play_computer_game' => $is_play_computer_game,
			'is_msoffice_knowledge' => $is_msoffice_knowledge,
			'is_internet_knowledge' => $is_internet_knowledge,
			'is_unemployed' => $is_unemployed,
			'student_occupation' => $student_occupation,
			'student_income' => $student_income,
			'student_income_source' => $student_income_source,
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

			'fees_amount' => $fees_amount,
			'fees_collected_date' => $fees_collected_date,
			'fees_payment_type' => $fees_payment_type,
			'is_fees_deposit' => $is_fees_deposit,
			'fees_deposit_date' => $fees_deposit_date,
			'fees_deposit_bank' => $fees_deposit_bank,
			'fees_deposit_cleared' => $fees_deposit_cleared,
			'is_due_balance' => $is_due_balance,
			'due_balance' => $due_balance,
			'student_remark' => $student_remark
		);

		if($_POST['student_photo'] != ''){
			$student_photo = $_POST['student_photo'];
		}else{
			$student_photo = student_photo();
		}

		if($student_photo != ''){
			$arr_db_values['student_photo'] = $student_photo;
		}

		if($action == 'add' || $action == 'create_student'){
			$arr_db_values['enrolled_date'] = 'now()';
		}

		switch($action){
			case 'add':

				$arr_db_values['student_created'] = 'now()';

				tep_db_perform(TABLE_STUDENTS, $arr_db_values);

				$student_id = tep_db_insert_id();
				$arr_documents = upload_documents($student_id);

				$msg = 'added';
			break;

			case 'edit':
			case 'preview_edit':

				tep_db_perform(TABLE_STUDENTS, $arr_db_values, "update", "student_id = '" . $student_id . "'");

				$arr_documents = upload_documents($student_id);

				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_STUDENTS ." where student_id = '". $student_id ."'");
				tep_db_query("delete from ". TABLE_STUDENT_DOCUMENTS ." where student_id = '". $student_id ."'");

				$msg = 'deleted';
			break;

			case 'preview':
				$arr_documents = upload_documents();
				$_POST['student_photo'] = $student_photo;

					//echo '<pre>'; print_r($arr_uploaded_files); exit;
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

		if(is_array($_POST['documents'])){
			//$arr_documents = $_POST['documents'];
		}

		if(is_array($arr_documents) && count($arr_documents) && $student_id != ''){
			foreach($arr_documents as $document_title => $documents){
				$arr_db_values = array(
					'student_id' => $student_id,
					'document' => $documents['filename'],
					'document_type' => $documents['document_type'],
					'document_title' => $documents['document_title']
				);

				tep_db_perform(TABLE_STUDENT_DOCUMENTS, $arr_db_values);
			}
		}

		if($msg != ''){
			tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo TITLE ?>: Student Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this student?")){
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

			function toggle_qualification(){
				if($('#student_qualification').val() == 'OTHERS'){
					$('.other_qualification').show();
				}else{
					$('.other_qualification').hide();
				}
			}

			function toggle_element(source_element, target_element){
				if($('#'+source_element+':checked').val() == '1'){
					$('.'+target_element).show();
				}else{
					$('.'+target_element).hide();
				}
			}

			function get_batch(default_batch){
				var course = $('#course_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course+'&ds='+default_batch,
					type: 'POST',
					async: false,
					dataType: 'json',
					success: function(response){
						$(response).each(function(key, values){
							if(default_batch == values.batch_id){
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).attr('selected', 'selected').text(values.batch_title));
							}else{
								$('#batch_id').append($("<option></option>").attr("value",values.batch_id).text(values.batch_title));
							}
						})
					}
				});
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();

				$('#student_dob').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true,
					yearRange: "-60:-10"
				});

				$('#fees_collected_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});

				$('#fees_deposit_date').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
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

		<link href="<?php echo DIR_WS_JS . 'sticky/stickytooltip.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'sticky/stickytooltip.js';?>"></script>
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
												if( $_GET['actionType'] == "add" || $_GET['actionType'] == "edit" || $_GET['actionType'] == "create_student" || $_GET['actionType'] == "edit_preview" )
												{
													if($_GET['actionType'] == "edit" || $_GET['actionType'] == "create_student" || $_GET['actionType'] == "edit_preview"){
														$int_id = $_GET['int_id'];

														$info_query_raw = "select student_id, centre_id, course_id, batch_id, student_photo, student_type, student_full_name, student_father_name, student_surname, student_middle_name, father_middle_name, father_surname, mother_first_name, mother_middle_name, mother_surname, student_address, student_village, student_district, student_taluka, student_block, student_state, student_pincode, student_mobile, student_mobile_2, student_mobile_3, student_phone_std, student_phone, student_email, student_gender, date_format(student_dob, '%d-%m-%Y') as student_dob, student_age, student_maritial, student_family_type, is_bpl_card, bpl_card_no, bpl_score_card, is_family_id, family_id, student_category, is_minority_category, student_religion, is_physical_disability, student_physical_disability, is_student_aadhar_card, student_aadhar_card, student_aadhar_card_status, is_student_pan_card, student_pan_card, student_language_known, student_qualification, student_other_qualification, is_computer_primary_knowledge, is_play_computer_game, is_msoffice_knowledge, is_internet_knowledge, is_unemployed, student_occupation, student_income, student_income_source, is_bank_account, bank_account_status, student_bank_name, student_branch, student_account_number, bank_ifsc_code, is_meet_eligibility_creteria, student_height, student_weight, student_blood_group, is_ready_migrate_job, is_ready_training, is_ready_migrate_training, fees_amount, date_format(fees_collected_date, '%d-%m-%Y') as fees_collected_date, fees_payment_type, is_fees_deposit, date_format(fees_deposit_date, '%d-%m-%Y') as fees_deposit_date, fees_deposit_bank, fees_deposit_cleared, fees_deposit_cleared, due_balance, student_remark, course_option, student_created from " . TABLE_STUDENTS . " where student_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);

														if($_GET['actionType'] != "edit_preview"){
															unset($_SESSION['sess_student_info']);
														}

														$arr_student_info = (isset($_SESSION['sess_student_info']) && count($_SESSION['sess_student_info']) ? $_SESSION['sess_student_info'] : $info);
													}

													if($_GET['actionType'] == 'add'){
														$action_type = 'add';
													}else if($_GET['actionType'] == 'create_student' || $_GET['actionType'] == 'edit_preview'){
														$action_type = 'preview';
													}else{
														$action_type = 'edit';
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Student Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType')) . '&actionType=preview'); ?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $action_type;?>">
																<input type="hidden" name="student_id" id="student_id" value="<?php echo $arr_student_info['student_id']; ?>"> 
																<input type="hidden" name="document_id" id="document_id" value=""> 
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
																									<td class="arial12LGray" width="10%">
																										<select name="course_id" id="course_id" class="required" style="width:auto;"  onchange="javascript: get_batch('');">
																											<option value="">Please choose</option>
																											<?php
																												$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id order by course_name";
																												$course_query = tep_db_query($course_query_raw);
																												
																												while($course = tep_db_fetch_array($course_query)){
																											?>
																											<option value="<?php echo $course['course_id'];?>" <?php echo($arr_student_info['course_id'] == $course['course_id'] ? 'selected="selected"' : '');?>><?php echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="5%">&nbsp;Batch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																											<option value="">Please choose</option>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Course Option&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="3">
																										<select name="course_option" id="course_option" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($arr_course_option as $kCOption=>$vCOption){ ?>
																											<option value="<?php echo $kCOption;?>" <?php echo($arr_student_info['course_option'] == $kCOption ? 'selected="selected"' : '');?>><?php echo $vCOption;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<script type="text/javascript">
																				<!--
																					get_batch('<?php echo $arr_student_info['batch_id'] ?>');
																				//-->
																				</script>
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
																									<td colspan="5">
																										<input type="text" name="student_address" id="student_address" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_address'] : $arr_student_info['student_address']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Village&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%">
																										<input type="text" name="student_village" id="student_village" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_village'] : $arr_student_info['student_village']) ?>" class="required">
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
																											<option value="<?php echo $disctrict['district_name'];?>" <?php echo($arr_student_info['student_district'] == $disctrict['district_name'] ? 'selected="selected"' : '');?>><?php echo $disctrict['district_name'];?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Taluka&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_taluka" id="student_taluka" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_taluka'] : $arr_student_info['student_taluka']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Block&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_block" id="student_block" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_block'] : $arr_student_info['student_block']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;State&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_state" id="student_state" class="required">
																											<option value="">Please choose</option>
																											<?php foreach($arr_states as $kState=>$vState){ ?>
																											<option value="<?php echo $kState;?>" <?php echo($arr_student_info['student_state'] == $kState ? 'selected="selected"' : '');?>><?php echo $vState;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Pin Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_pincode" id="student_pincode" maxlength="8" value="<?php echo  ($dupError ? $_POST['student_pincode'] : $arr_student_info['student_pincode']) ?>" class="required">
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
																											<input type="radio" name="student_gender" id="student_gender" value="<?php echo $k_gender;?>" class="required" <?php echo ($arr_student_info['student_gender'] == $k_gender ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_gender;?>&nbsp;
																										<?php } ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;DOB (DD/MM/YYYY) &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										<input type="text" name="student_dob" id="student_dob" value="<?php echo  ($dupError ? $_POST['student_dob'] : $arr_student_info['student_dob']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Age &nbsp;:</td>
																									<td>
																										<input type="text" name="student_age" id="student_age" value="<?php echo  ($dupError ? $_POST['student_age'] : ($arr_student_info['student_age'] != '' ? $arr_student_info['student_age'] : '0')) ?>" class="number" style="width:50px;">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Maritial Status&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td colspan="5">
																										<select name="student_maritial" id="student_maritial" class="required">
																											<?php
																												foreach($arr_maritial_status as $k_m_status=>$v_m_status){
																											?>
																											<option value="<?php echo $k_m_status;?>" <?php echo($arr_student_info['student_maritial'] == $k_m_status ? 'selected="selected"' : '');?>><?php echo $v_m_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Family Type&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_family_type" id="student_family_type" class="required">
																											<?php
																												foreach($arr_family_type as $k_ft_status=>$v_ft_status){
																											?>
																											<option value="<?php echo $k_ft_status;?>" <?php echo($arr_student_info['student_family_type'] == $k_ft_status ? 'selected="selected"' : '');?>><?php echo $v_ft_status;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;B.P.L. CARD &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_bpl_card" id="is_bpl_card" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_bpl_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_bpl_card', 'bpl_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold bpl_card" align="right">&nbsp;B.P.L. Card No &nbsp;<font color="#ff0000">*</font>&nbsp;:&nbsp;
																											<input type="text" name="bpl_card_no" id="bpl_card_no" value="<?php echo  ($dupError ? $_POST['bpl_card_no'] : $arr_student_info['bpl_card_no']) ?>" class="required" style="width:75px;">
																											&nbsp;B.P.L. Score Card &nbsp;<font color="#ff0000">*</font>&nbsp;:&nbsp;
																											<input type="text" name="bpl_score_card" id="bpl_score_card" value="<?php echo  ($dupError ? $_POST['bpl_score_card'] : $arr_student_info['bpl_score_card']) ?>" class="required" style="width:75px;">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_bpl_card', 'bpl_card');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Have Family ID<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_family_id" id="is_family_id" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_family_id'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_family_id', 'family_id');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold family_id" align="right">
																											Family ID&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="family_id" id="family_id" value="<?php echo  ($dupError ? $_POST['family_id'] : $arr_student_info['family_id']) ?>" class="required">
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
																											<?php
																												foreach($arr_category as $k_category=>$v_category){
																											?>
																											<option value="<?php echo $k_category;?>" <?php echo($arr_student_info['student_category'] == $k_category ? 'selected="selected"' : '');?>><?php echo $v_category;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Minority Category<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_minority_category" id="is_minority_category" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_minority_category'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Religion&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<select name="student_religion" id="student_religion" class="required">
																											<?php
																												foreach($arr_religion as $k_religion=>$v_religion){
																											?>
																											<option value="<?php echo $k_religion;?>" <?php echo($arr_student_info['student_religion'] == $k_religion ? 'selected="selected"' : '');?>><?php echo $v_religion;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Physical Unability/Disability<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="3">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_physical_disability" id="is_physical_disability" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_physical_disability'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_physical_disability', 'physical_disability');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold physical_disability" align="right">&nbsp;
																											Physical Disablity &nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_physical_disability" id="student_physical_disability" value="<?php echo  ($dupError ? $_POST['student_physical_disability'] : $arr_student_info['student_physical_disability']) ?>" class="required physical_disability">
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
																											<input type="radio" name="is_student_aadhar_card" id="is_student_aadhar_card" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_student_aadhar_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_student_aadhar_card', 'aadhar_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										<span class="arial12LGrayBold aadhar_card" align="right">
																											Aadhar Card&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_aadhar_card" id="student_aadhar_card" value="<?php echo  ($dupError ? $_POST['student_aadhar_card'] : $arr_student_info['student_aadhar_card']) ?>" class="required">
																										</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;PAN Card No<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_student_pan_card" id="is_student_pan_card" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_student_pan_card'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_student_pan_card', 'pan_card');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>&nbsp;
																										
																										<span class="arial12LGrayBold pan_card" align="right">
																											&nbsp;PAN Card&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_pan_card" id="student_pan_card" value="<?php echo  ($dupError ? $_POST['student_pan_card'] : $arr_student_info['student_pan_card']) ?>" class="required pan_card">
																										</span>
																									</td>
																								</tr>
																								<script type="text/javascript">
																								<!--
																									toggle_element('is_student_aadhar_card', 'aadhar_card');
																									toggle_element('is_student_pan_card', 'pan_card');
																								//-->
																								</script>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Student meet eligibilty creteria&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_meet_eligibility_creteria" id="is_meet_eligibility_creteria" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_meet_eligibility_creteria'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Height&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_height" id="student_height" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_height'] : $arr_student_info['student_height']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Weight&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_weight" id="student_weight" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_weight'] : $arr_student_info['student_weight']) ?>">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Blood Group&nbsp;:</td>
																									<td class="arial12LGrayBold"><input type="text" name="student_blood_group" id="student_blood_group" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_blood_group'] : $arr_student_info['student_blood_group']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for job &nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_migrate_job" id="is_ready_migrate_job" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_ready_migrate_job'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready for 10 – 12 hrs Training&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_training" id="is_ready_training" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_ready_training'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for training&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_ready_migrate_training" id="is_ready_migrate_training" value="<?php echo $k_status;?>" class="required" <?php echo ($info['is_ready_migrate_training'] == $k_status ? 'checked="checked"' : '');?> >&nbsp;<?php echo $v_status;?>&nbsp;
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="qual">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Language Known&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_language_known" id="student_language_known" maxlength="255" value="<?php echo  ($dupError ? $_POST['student_language_known'] : $arr_student_info['student_language_known']) ?>" class="required">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<select name="student_qualification" id="student_qualification" class="required" onchange="javascript: toggle_qualification();">
																											<option value="">Please choose</option>
																											<?php foreach($arr_qualification as $k_qualification=>$v_qualification){?>
																											<option value="<?php echo $k_qualification;?>" <?php echo($arr_student_info['student_qualification'] == $k_qualification ? 'selected="selected"' : '');?>><?php echo $v_qualification;?></option>
																											<?php } ?>
																										</select>
																										<span class="arial12LGrayBold other_qualification" align="right">
																											&nbsp;Other Qualification&nbsp;<font color="#ff0000">*</font>&nbsp;:
																											&nbsp;<input type="text" name="student_other_qualification" id="student_other_qualification" value="<?php echo  ($dupError ? $_POST['student_other_qualification'] : $arr_student_info['student_other_qualification']) ?>" class="required">
																										</span>
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="cl">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Primary Knowledge of Computers&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																									<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_computer_primary_knowledge" id="is_computer_primary_knowledge" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_computer_primary_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Can Play Game on Computer&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_play_computer_game" id="is_play_computer_game" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_play_computer_game'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;MS Office Knowledge&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_msoffice_knowledge" id="is_msoffice_knowledge" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_msoffice_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Internet Knowledge&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_internet_knowledge" id="is_internet_knowledge" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_internet_knowledge'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="emp">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Employed&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_unemployed" id="is_unemployed" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_unemployed'] == $k_status ? 'checked="checked"' : '');?>  onclick="toggle_element('is_unemployed', 'employe');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Occupation&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_occupation" id="student_occupation" maxlength="100" value="<?php echo  ($dupError ? $_POST['student_occupation'] : $arr_student_info['student_occupation']) ?>" class="required">
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Monthly Income&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_income" id="student_income" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_income'] : $arr_student_info['student_income']) ?>" class="required number">
																									</td>
																								</tr>
																								<tr class="employe">
																									<td class="arial12LGrayBold">&nbsp;Current Source of Income&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_income_source" id="student_income_source" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_income_source'] : $arr_student_info['student_income_source']) ?>" class="required">
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="bank">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Bank Account&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_bank_account" id="is_bank_account" value="<?php echo $k_status;?>" class="required" <?php echo ($arr_student_info['is_bank_account'] == $k_status ? 'checked="checked"' : '');?>  onclick="javascript: toggle_element('is_bank_account', 'bank_account');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" width="13%" align="right">&nbsp;Name of the Bank&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="10%">
																										<input type="text" name="student_bank_name" id="student_bank_name" maxlength="10" value="<?php echo  ($dupError ? $_POST['student_bank_name'] : $arr_student_info['student_bank_name']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Branch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td width="15%">
																										<input type="text" name="student_branch" id="student_branch" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_branch'] : $arr_student_info['student_branch']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Account Number&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="student_account_number" id="student_account_number" maxlength="50" value="<?php echo  ($dupError ? $_POST['student_account_number'] : $arr_student_info['student_account_number']) ?>" class="required number">
																									</td>
																								</tr>
																								<tr class="bank_account">
																									<td class="arial12LGrayBold" align="right">&nbsp;Bank IFSC Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="bank_ifsc_code" id="bank_ifsc_code" maxlength="20" value="<?php echo  ($dupError ? $_POST['bank_ifsc_code'] : $arr_student_info['bank_ifsc_code']) ?>" class="required">
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
																							<legend>Fees</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="fees">
																								<tr>
																									<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Fees amount&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGrayBold" width="15%">
																										<input type="text" name="fees_amount" id="fees_amount" maxlength="10" value="<?php echo  ($dupError ? $_POST['fees_amount'] : $arr_student_info['fees_amount']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Payment Type&nbsp;:</td>
																									<td width="10%">
																										<select name="fees_payment_type" id="fees_payment_type">
																											<option value="">Please choose</option>
																											<?php foreach($arr_payment_type as $k_payment_type=>$v_payment_type){?>
																											<option value="<?php echo $k_payment_type;?>" <?php echo($arr_student_info['fees_payment_type'] == $k_payment_type ? 'selected="selected"' : '');?>><?php echo $v_payment_type;?></option>
																											<?php } ?>
																										</select>
																									</td>
																									<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Fees Collected On&nbsp;:</td>
																									<td class="arial12LGray" width="15%" colspan="3">
																										<input type="text" name="fees_collected_date" id="fees_collected_date" value="<?php echo  ($dupError ? $_POST['fees_collected_date'] : $arr_student_info['fees_collected_date']) ?>">
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Fees Deposited&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_fees_deposit" id="is_fees_deposit" value="<?php echo $k_status;?>" <?php echo ($arr_student_info['is_fees_deposit'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;" onclick="javascript: toggle_element('is_fees_deposit', 'fees_deposit');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr style="display:none;" class="fees_deposit">
																									<td class="arial12LGrayBold" align="right">&nbsp;Deposit Bank&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="fees_deposit_bank" id="fees_deposit_bank" maxlength="255" value="<?php echo  ($dupError ? $_POST['fees_deposit_bank'] : $arr_student_info['fees_deposit_bank']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Deposit Date&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="fees_deposit_date" id="fees_deposit_date" maxlength="255" value="<?php echo  ($dupError ? $_POST['fees_deposit_date'] : $arr_student_info['fees_deposit_date']) ?>" class="required">
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Is Deposit Cleared&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="fees_deposit_cleared" id="fees_deposit_cleared" value="<?php echo $k_status;?>" <?php echo ($arr_student_info['fees_deposit_cleared'] == $k_status ? 'checked="checked"' : '');?>  style="width:auto;">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Any Due Balance&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																										<?php foreach($arr_status as $k_status=>$v_status){?>
																											<input type="radio" name="is_due_balance" id="is_due_balance" value="<?php echo $k_status;?>" <?php echo ($arr_student_info['is_due_balance'] == $k_status ? 'checked="checked"' : '');?>  onclick="javascript: toggle_element('is_due_balance', 'due_balance');">&nbsp;<?php echo $v_status;?>&nbsp;
																										<?php } ?>
																									</td>
																								</tr>
																								<tr class="due_balance">
																									<td class="arial12LGrayBold" align="right">&nbsp;Due Balance&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<input type="text" name="due_balance" id="due_balance" maxlength="255" value="<?php echo  ($dupError ? $_POST['due_balance'] : $arr_student_info['due_balance']) ?>" class="required number">
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<script type="text/javascript">
																				<!--
																					toggle_element('is_fees_deposit', 'fees_deposit');
																					toggle_element('is_due_balance', 'due_balance');
																				//-->
																				</script>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Student Photo / Documents</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="docs">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" valign="top">&nbsp;Student Photo&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																									<td>
																										<?php if($arr_student_info['student_photo']!=''){?>
																										<img src="<?php echo DIR_WS_UPLOAD . $arr_student_info['student_photo'];?>" width="150" style="padding:3px; border: 1px solid black;"><br><br>
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
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="remark">
																								<tr>
																									<td class="arial12LGrayBold" width="7%" valign="top">&nbsp;Remark&nbsp;:</td>
																									<td class="arial12LGrayBold">
																										<textarea name="student_remark" id="student_remark" cols="40" rows="6"><?php echo  ($dupError ? $_POST['student_remark'] : $arr_student_info['student_remark']) ?></textarea>
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
												}else if($_GET['actionType'] == "preview"){
													$strHidden = '';

													$_SESSION['sess_student_info'] = $_POST;
													
													if(is_array($_POST) && count($_POST)){
														foreach($_POST as $kPost=>$vPost){
															if($kPost == 'action_type')continue;
															$strHidden .= '<input type="hidden" name="' . $kPost . '" id="' . $kPost . '" value="' . $vPost . '">' . "\n";
														}
													}

													/*if(is_array($arr_uploaded_files) && count($arr_uploaded_files)){
														foreach($arr_uploaded_files as $kDocuments=>$vDocuments){
															$strHidden .= '<input type="hidden" name="documents[' . $kDocuments . ']" id="documents[' . $kDocuments . ']" value="' . $vDocuments . '">' . "\n";
														}
													}*/
											?>
											<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Student Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))); ?>" class="arial14LGrayBold">Student Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" action="" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="preview_edit">
																<?php echo $strHidden;?>
																<table class="tabForm" cellpadding="3" cellspacing="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" border="0" width="100%">
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Enroll Info&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#enroll_info'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%" id="enroll_info">
																								<td class="arial12LGrayBold" width="5%">&nbsp;Course&nbsp;:</td>
																								<td class="arial12LGray" width="30%">
																									<?php
																										$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id and c.course_id = '" . $_POST['course_id'] . "'";
																										$course_query = tep_db_query($course_query_raw);
																										$course = tep_db_fetch_array($course_query);

																										echo $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';
																									?>
																								</td>
																								<td class="arial12LGrayBold" align="right" width="5%">&nbsp;Batch&nbsp;:</td>
																								<td class="arial12LGray">
																									<?php
																										$batch_query_raw = " select batch_id, batch_title from ". TABLE_BATCHES ." where course_id = '" . $_POST['course_id'] . "' ";
																										$batch_query_raw .= " and batch_id = '" . $_POST['batch_id'] . "'";
																										$batch_query = tep_db_query($batch_query_raw);

																										$batch = tep_db_fetch_array($batch_query);

																										echo $batch['batch_title'];
																									?>
																								</td>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" width="50%">
																						<fieldset>
																							<legend>Candidate Full Name&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#can_info'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;First Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_full_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_middle_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_surname']; ?></td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" width="50%">
																						<fieldset>
																							<legend>Father's Name&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#can_info'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;Father's Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_father_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['father_middle_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['father_surname']; ?></td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																					<td class="arial14LGrayBold">
																						<fieldset>
																							<legend>Mother's  Name&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#m_info'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="20%">&nbsp;First Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['mother_first_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Middle Name&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['mother_middle_name']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Surname&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['mother_surname']; ?></td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Address&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#adrs'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Address&nbsp;:</td>
																									<td colspan="5" class="arial12LGray">
																										<?php echo $_POST['student_address']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Village&nbsp;:</td>
																									<td width="15%" class="arial12LGray">
																										<?php echo $_POST['student_village']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="5%">&nbsp;District&nbsp;:</td>
																									<td width="10%" class="arial12LGray">
																										<?php echo $_POST['student_district']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="8%">&nbsp;Taluka&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_taluka']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Block&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_block']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;State&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_states[$_POST['student_state']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Pin Code&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_pincode']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Contact Info&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#contact'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Mobile Number&nbsp;:</td>
																									<td width="10%" class="arial12LGray">
																										<?php echo $_POST['student_mobile']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;2<span class="sub_text">nd</span> Mobile Number&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_mobile_2']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;3<span class="sub_text">rd</span>Mobile Number&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_mobile_3']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Landline (Resi)&nbsp;:</td>
																									<td class="arial12LGray">
																										Std Code <?php echo $_POST['student_phone_std']; ?>
																										&nbsp;<?php echo $_POST['student_phone']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Email&nbsp;:</td>
																									<td colspan="3" class="arial12LGray">
																										<?php echo $_POST['student_email']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Other Information&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#other'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Gender&nbsp;:</td>
																									<td class="arial12LGray" width="15%">
																										<?php echo $arr_gender[$_POST['student_gender']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;DOB (DD/MM/YYYY) &nbsp;:</td>
																									<td class="arial12LGray" width="10%">
																										<?php echo $_POST['student_dob']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" width="10%">&nbsp;Age &nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_age']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Maritial Status&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_maritial_status[$_POST['student_maritial']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Family Type&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_family_type[$_POST['student_family_type']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;B.P.L. CARD &nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_bpl_card']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" class="bpl_card" style="display: <?php echo ($_POST['is_bpl_card'] != '1' ? 'none;' : '');?>">&nbsp;B.P.L. Card No &nbsp;:</td>
																									<td class="arial12LGray" style="display: <?php echo ($_POST['is_bpl_card'] != '1' ? 'none;' : '');?>">
																										<?php echo $_POST['bpl_card_no']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right" class="bpl_card" style="display: <?php echo ($_POST['is_bpl_card'] != '1' ? 'none;' : '');?>">&nbsp;B.P.L. Score Card&nbsp;:</td>
																									<td class="arial12LGray" style="display: <?php echo ($_POST['is_bpl_card'] != '1' ? 'none;' : '');?>">
																										<?php echo $_POST['bpl_score_card']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Having Family ID:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_family_id']]; ?>
																										&nbsp;
																										<?php echo ($_POST['is_family_id'] != '1' ? '' : $_POST['family_id']);?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Category&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_category[$_POST['student_category']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Minority Category:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_minority_category']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Religion&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_religion[$_POST['student_religion']]; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Physical Unability/Disability:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_physical_disability']]; ?>
																										&nbsp;
																										<?php echo ($_POST['is_physical_disability'] != '1' ? '' : $_POST['student_physical_disability']);?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Aadhar Card:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_student_aadhar_card']]; ?>
																										&nbsp;
																										<?php echo ($_POST['is_student_aadhar_card'] != '1' ? '' : $_POST['student_aadhar_card']);?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;PAN Card No:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_student_pan_card']]; ?>
																										&nbsp;
																										<?php echo ($_POST['is_student_pan_card'] != '1' ? '' : $_POST['student_pan_card']);?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Student meet eligibilty creteria&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_meet_eligibility_creteria']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Height&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_height']; ?></td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Weight&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_weight']; ?></td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Blood Group&nbsp;:</td>
																									<td class="arial12LGray"><?php echo $_POST['student_blood_group']; ?></td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for job &nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_ready_migrate_job']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready for 10 – 12 hrs Training&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_ready_training']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Ready to migrate for training&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_ready_migrate_training']]; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Qualification/Skills&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#qual'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" align="right" width="13%">&nbsp;Language Known&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_language_known']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Qualification&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_qualification[$_POST['student_qualification']]; ?>
																										<?php
																											echo ($_POST['student_qualification'] == 'OTHERS' ? '&nbsp;' . $_POST['student_other_qualification'] : '');
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
																							<legend>Computer Literacy&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#cl'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Primary Knowledge of Computers&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_computer_primary_knowledge']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Can Play Game on Computer&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_play_computer_game']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;MS Office Knowledge&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_msoffice_knowledge']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Internet Knowledge&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_internet_knowledge']]; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>

																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Employment Details&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#emp'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="20%">&nbsp;Employed&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['is_unemployed']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Current Occupation&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_occupation']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Current Monthly Income&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_income']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold">&nbsp;Current Source of Income&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_income_source']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Bank Info&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#bank'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Bank Account&nbsp;:</td>
																									<td class="arial12LGray" width="10%">
																										<?php echo $arr_status[$_POST['is_bank_account']]; ?>
																									</td>
																									<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Name of the Bank&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_bank_name']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Branch&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_branch']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Account Number&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['student_account_number']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Bank IFSC Code&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['bank_ifsc_code']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Fees&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#fees'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="13%" align="right">&nbsp;Fees amount&nbsp;:</td>
																									<td class="arial12LGray" width="10%">
																										<?php echo $_POST['fees_amount']; ?>
																									</td>
																									<td class="arial12LGrayBold" width="10%" align="right">&nbsp;Payment Type&nbsp;:</td>
																									<td width="1%" class="arial12LGray">
																										<?php echo ($arr_payment_type[$_POST['fees_payment_type']] != '' ? $arr_payment_type[$_POST['fees_payment_type']] : '-'); ?>
																									</td>
																									<td class="arial12LGrayBold" width="12%" align="right">&nbsp;Fees Collected On&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['fees_collected_date']; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Fees Deposited&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																										<?php echo $arr_status[$_POST['is_fees_deposit']]; ?>
																									</td>
																								</tr>
																								<tr style="display:<?php echo ($_POST['is_fees_deposit'] == '1' ? 'none;' : '' );?>" class="fees_deposit">
																									<td class="arial12LGrayBold" align="right">&nbsp;Deposit Bank&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['fees_deposit_bank']; ?>&nbsp;
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Deposit Date&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $_POST['fees_deposit_date']; ?>
																									</td>
																									<td class="arial12LGrayBold" align="right">&nbsp;Is Deposit Cleared&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo $arr_status[$_POST['fees_deposit_cleared']]; ?>
																									</td>
																								</tr>
																								<tr>
																									<td class="arial12LGrayBold" align="right">&nbsp;Any Due Balance&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																										<?php echo $arr_status[$_POST['is_due_balance']]; ?>
																									</td>
																								</tr>
																								<tr class="due_balance">
																									<td class="arial12LGrayBold" align="right">&nbsp;Due Balance&nbsp;:</td>
																									<td class="arial12LGray" colspan="5">
																										<?php echo $_POST['due_balance']; ?>
																									</td>
																								</tr>
																							</table>
																						</fieldset>
																					</td>
																				</tr>
																				<?php //if(is_array($arr_uploaded_files) && count($arr_uploaded_files)){ ?>
																				<!-- <tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Student Photo / Documents&nbsp;(&nbsp;<a href="<?php //echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#docs'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td colspan="2">
																										<table cellpadding="5" cellspacing="0" border="0" width="40%" id="student_docs">
																											<tr>
																												<td  style="border-bottom: 1px dotted #000000; border-right: 1px dotted #000000; " class="arial12LGrayBold">Document File</td>
																												<td  style="border-bottom: 1px dotted #000000;" class="arial12LGrayBold">Document Name</td>
																											</tr>
																											<?php
																												//foreach($arr_uploaded_files as $kDocuments=>$vDocuments){
																											?>
																											<tr>
																												<td style="border-bottom: 1px dotted #000000; border-right: 1px dotted #000000;">
																													<a href="<?php //echo DIR_WS_UPLOAD . $vDocuments;?>" target="_blank"><?php //echo $vDocuments;?></a>
																												</td>
																												<td style="border-bottom: 1px dotted #000000;" class="verdana12Blue"><?php //echo $kDocuments;?>&nbsp;&nbsp;</td>
																											</tr>
																											<?php
																													//}
																											?>
																										</table>
																									</td>
																								</tr>
																								<?php //} ?>
																							</table>
																						</fieldset>
																					</td>
																				</tr> -->
																				<tr>
																					<td class="arial14LGrayBold" colspan="2">
																						<fieldset>
																							<legend>Remark&nbsp;(&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg', 'actionType')) . '&actionType=edit_preview#remark'); ?>">Edit</a>&nbsp;)&nbsp;</legend>
																							<table cellpadding="5" cellspacing="5" border="0" width="100%">
																								<tr>
																									<td class="arial12LGrayBold" width="8%">&nbsp;Remark&nbsp;:</td>
																									<td class="arial12LGray">
																										<?php echo nl2br($_POST['student_remark']); ?>
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
																		<td>&nbsp;<input type="submit" value="SUBMIT" name="cmdSubmit" id="cmdSubmit" class="groovybutton">&nbsp;&nbsp;&nbsp;<input type="button" value="BACK" name="cmdBack" id="cmdBack" class="groovybutton" onclick="javascript: history.go(-1);"></td>
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
														<td class="arial18BlueN">Student Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=add"); ?>" class="arial14LGrayBold">Add Student</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = "select s.student_id, s.student_full_name, s.student_middle_name, s.student_surname, s.student_father_name, s.student_mobile, s.test_result, c.course_name, b.batch_title from " . TABLE_STUDENTS . " s, " . TABLE_COURSES . " c, " . TABLE_BATCHES . " b where b.batch_id = s.batch_id and c.course_id = s.course_id and student_type = 'ENROLLED'";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and s.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																if($_GET['actionType'] == "batch_stud"){
																	$batch_id = (int)$_GET['batch_id'];
																	$listing_query_raw .= " and s.batch_id = '" . $batch_id . "'";
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
																		<th>Course</th>
																		<th>Batch</th>
																		<th>Test Status</th>
																		<th width="10%">Status</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while($listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['student_full_name'] . ' ' . $listing['student_middle_name'] . ' ' . $listing['student_surname']; ?></td>
																			<td valign="top"><?php echo $listing['student_mobile']; ?></td>
																			<td valign="top"><?php echo $listing['course_name']; ?></td>
																			<td valign="top"><?php echo $listing['batch_title']; ?></td>
																			<td valign="top"><?php echo ($listing['test_result'] != '' ? $arr_exam_result[$listing['test_result']] : 'Not Available'); ?></td>
																			<td valign="top">
																				<a href="#" data-tooltip="sticky<?php echo $listing['student_id'];?>">Update Status</a>
																			</td>
																		</tr>
																	<?php
																			}
																	?>
																	<div id="mystickytooltip" class="stickytooltip">
																		<div style="padding:3px">
																		<?php 
																			$listing_query = tep_db_query($listing_query_raw);
																			while($listing = tep_db_fetch_array($listing_query) ){
																		?>
																			<div id="sticky<?php echo $listing['student_id'];?>" class="atip">
																				<div class="sticky_heading"><?php echo $listing['student_full_name']; ?></div>
																				<ul>
																					<li><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Edit Student</a></li>
																					<li><a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['student_id']; ?>')">Delete Student</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_STUDENT_ASSESMENT_TEST,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Assesment Test Detail</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_STUDENT_TRAINING,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Training Detail</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_PLACEMENTS,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Placement</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_HANDHOLDING,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."stud_id=".$listing['student_id']); ?>">Update Handholding</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_STUDENT_ATTENDANCE,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."stud_id=".$listing['student_id']); ?>">View Attendance</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_BANK_ACCOUNT_STATUS,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Bank Account Status</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_AADHAR_CARD_STATUS,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Aadhar Card Status</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_NON_RES_ALLOWANCE,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Non Residential Allowance</a></li>
																					<li><a href="<?php echo tep_href_link(FILENAME_PLACEMENT_ALLOWANCE,tep_get_all_get_params(array('msg','actionType','int_id','stud_id'))."actionType=edit&int_id=".$listing['student_id']); ?>">Update Placement Allowance</a></li>
																				</ul>
																			</div>
																		<?php } ?>
																		</div>
																		<div class="stickystatus"></div>
																	</div>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Student Name
																					null, // Mobile
																					null, // Course
																					null, // Branch
																					null, // Test
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
																				<td align="center" colspan="6" class="verdana11Red">No Student Found !!</td>
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