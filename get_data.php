<?php	
	include('includes/application_top.php');

	$action = (isset($_POST['action']) && tep_not_null($_POST['action']) ? $_POST['action'] : $_GET['action']);

	switch($action){
		case 'get_city':
			$disctrict = tep_db_input($_POST['disctrict']);
			$default_city = tep_db_input($_POST['dc']);

			$city_query_raw = " select c.city_id, c.city_name from ". TABLE_CITIES ." c where c.district_id = '" . $disctrict . "' order by c.city_name";
			$city_query = tep_db_query($city_query_raw);

			$cities = array();

			while($city = tep_db_fetch_array($city_query)){
				$cities[] = $city;
			}

			echo json_encode($cities);

		break;
		case 'get_module':
			$course = tep_db_input($_POST['course']);

			$module_query_raw = " select m.module_id, m.module from ". TABLE_MODULES ." m where m.course_id = '" . $course . "' order by m.module";
			$module_query = tep_db_query($module_query_raw);

			$modules = array();

			while($module = tep_db_fetch_array($module_query)){
				$modules[] = $module;
			}

			echo json_encode($modules);

		break;
		case 'get_subject':
			$course = tep_db_input($_POST['course']);
			$module = tep_db_input($_POST['module']);

			$subject_query_raw = " select s.subject_id, s.subject from ". TABLE_SUBJECTS ." s where s.course_id = '" . $course . "' and s.module_id = '" . $module . "' order by s.subject";
			$subject_query = tep_db_query($subject_query_raw);

			$subjects = array();

			while($subject = tep_db_fetch_array($subject_query)){
				$subjects[] = $subject;
			}

			echo json_encode($subjects);

		break;
		case 'get_batch':
			$course = tep_db_input($_POST['course']);
			$centre = tep_db_input($_POST['centre']);
			$default_subject = tep_db_input($_POST['ds']);

			$batch_query_raw = " select b.batch_id, b.batch_title, d.district_name from ". TABLE_BATCHES ." b LEFT JOIN ". TABLE_DISTRICTS . " d  ON (d.district_id = b.district_id) where b.course_id = '" . $course . "' ";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$batch_query_raw .= " and b.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}else{
				if(isset($centre) && tep_not_null($centre)){
					$batch_query_raw .= " and b.centre_id = '" . $centre . "'";
				}
			}

			$batch_query_raw .= " order by b.batch_title";

			$batch_query = tep_db_query($batch_query_raw);

			$batches = array();

			while($batch = tep_db_fetch_array($batch_query)){
				$batches[] = $batch;
			}

			echo json_encode($batches);

		break;
		case 'get_centre_batch':
			$centre_id = tep_db_input($_POST['centre']);

			$batch_query_raw = " select batch_id, batch_title from ". TABLE_BATCHES ." where centre_id = '" . $centre_id . "' ";
			$batch_query_raw .= " order by batch_title";

			$batch_query = tep_db_query($batch_query_raw);

			$batches = array();

			while($batch = tep_db_fetch_array($batch_query)){
				$batches[] = $batch;
			}

			echo json_encode($batches);
		break;
		case 'get_faculty':
			$course = tep_db_input($_POST['course']);
			$subject = tep_db_input($_POST['subject']);

			$subject_query_raw = " select faculty_id, faculty_first_name from ". TABLE_FACULTIES ." where course_id = '" . $course . "' and subject_id = '" . $subject . "' ";

			if($_SESSION['sess_adm_type'] != 'ADMIN'){
				$subject_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
			}

			$subject_query_raw .= " order by faculty_first_name";

			$subject_query = tep_db_query($subject_query_raw);

			$subjects = array();

			while($subject = tep_db_fetch_array($subject_query)){
				$subjects[] = $subject;
			}

			echo json_encode($subjects);

		break;

		case 'get_courses':
			$section = tep_db_input($_POST['section']);

			$course_query_raw = " select c.course_id, c.course_name, c.course_code, s.section_name from " . TABLE_COURSES . " c, " . TABLE_SECTIONS . " s where c.section_id = s.section_id and s.section_id = '" . $section . "' order by course_name";
			$course_query = tep_db_query($course_query_raw);

			$courses = array();

			while($course = tep_db_fetch_array($course_query)){
				$course['frm_course_name'] = $course['course_name'] . ' - ' . $course['section_name'] . ' ( ' . $course['course_code'] . ' ) ';
				$courses[] = $course;
			}

			echo json_encode($courses);

		break;

		case 'get_comp_info':
			$company_id = tep_db_input($_POST['company_id']);

			$company_info_query_raw = " select comp.company_id, comp.centre_id, comp.city_id, comp.company_name, comp.company_address, comp.company_phone, comp.company_contact_person, comp.company_contact_person_designation, comp.company_email, comp.company_phone_std, comp.company_phone, c.city_name, d.district_name, d.state from " . TABLE_COMPANIES . " comp, " . TABLE_CITIES . " c, " . TABLE_DISTRICTS . " d where comp.city_id = c.city_id and d.district_id = c.district_id and comp.company_id='" . $company_id . "' ";
			$company_info_query = tep_db_query($company_info_query_raw);

			$company_info = tep_db_fetch_array($company_info_query);

			echo '<strong>' . $company_info['company_name'] . '</strong><br>' . $company_info['company_address'] . ', ' . $company_info['city_name'] . ', ' . $company_info['district_name'] . ', ' . $arr_states[$company_info['state']] . '<br>' . $company_info['company_email'] . '<br><br><strong>Contact Person : </strong>' . $company_info['company_contact_person'] .' ( '. $company_info['company_contact_person_designation'] . ' ) <br>' . ($company_info['company_phone_std'] != '' || $company_info['company_phone_std'] != '0' ? $company_info['company_phone_std'] . ' ' : '') . $company_info['company_phone'];

		break;
		case 'do_bounce_cheque':	
			$stud_payment_id = tep_db_prepare_input(tep_db_input($_POST['sp_id']));
			$bounce_reason = tep_db_prepare_input(tep_db_input($_POST['bounce_reason']));
			$bounce_date = tep_db_prepare_input(tep_db_input($_POST['bounce_date']));
			$bounce_date = input_valid_date($bounce_date);


			$stud_payment_db_values = array(
				'stud_payment_status' => 'BOUNCE',
				'bounce_reason' => $bounce_reason,
				'bounce_date' => $bounce_date,
				'stud_payment_modified' => 'now()'
			);

			tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

			echo "<span style=\"color:red;\">Cheque has been updated.</span>";
			exit;
		break;
		case 'do_clear_cheque':	
			$stud_payment_id = tep_db_prepare_input(tep_db_input($_POST['sp_id']));
			$cheque_cleared_date = tep_db_prepare_input(tep_db_input($_POST['clearance_date']));
			$cheque_cleared_date = input_valid_date($cheque_cleared_date);


			$stud_payment_db_values = array(
				'cheque_cleared' => '1',
				'cheque_cleared_date' => $cheque_cleared_date,
				'stud_payment_modified' => 'now()'
			);

			tep_db_perform(TABLE_STUDENT_PAYMENTS, $stud_payment_db_values, "update", "stud_payment_id = '" . $stud_payment_id . "'");

			echo "<span style=\"color:red;\">Payment has been updated.</span>";
			exit;
		break;
	}
?>