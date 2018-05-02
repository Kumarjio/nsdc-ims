<?php
	include('includes/application_top.php');
	
	if($_GET['action'] != 'logout')	{
		$admin_info_query = tep_db_query("select adm_id, adm_email, centre_id, adm_name, adm_type from ". TABLE_ADMIN_MST ." where `adm_username` = '". tep_db_input($_POST['txtUserName']) ."' and `adm_password` = '". tep_db_input($_POST['txtPassword']) ."' and adm_status = '1'");

		if(tep_db_num_rows($admin_info_query))	{
			$admin_info = tep_db_fetch_array($admin_info_query);

			tep_session_register("sess_status");
			$_SESSION['sess_status'] = 'login';
			tep_session_register("sess_admin_id");
			$_SESSION['sess_admin_id'] = $admin_info['adm_id'];
			tep_session_register("sess_centre_id");
			$_SESSION['sess_centre_id'] = $admin_info['centre_id'];
			tep_session_register("sess_adm_name");
			$_SESSION['sess_adm_name'] = $admin_info['adm_name'];
			tep_session_register("sess_adm_type");
			$_SESSION['sess_adm_type'] = $admin_info['adm_type'];
			tep_session_register("sess_adm_email");
			$_SESSION['sess_adm_email'] = $admin_info['adm_email'];

			header("Location:". FILENAME_HOME);
		}else{
			header("Location:". FILENAME_DEFAULT . "?error=1");
		}
	}else{
		if(tep_session_is_register('sess_status')) {
			tep_session_unregister("sess_status");
			unset($_SESSION['sess_status']);
		}

		if(tep_session_is_register('sess_admin_id')) {
			tep_session_unregister("sess_admin_id");
			unset($_SESSION['sess_admin_id']);
		}

		if(tep_session_is_register('sess_adm_type')) {
			tep_session_unregister("sess_adm_type");
			unset($_SESSION['sess_adm_type']);
		}

		if(tep_session_is_register('sess_centre_id')) {
			tep_session_unregister("sess_centre_id");
			unset($_SESSION['sess_centre_id']);
		}

		if(tep_session_is_register('sess_adm_name')) {
			tep_session_unregister("sess_adm_name");
			unset($_SESSION['sess_adm_name']);
		}

		if(tep_session_is_register('sess_adm_email')) {
			tep_session_unregister("sess_adm_email");
			unset($_SESSION['sess_status']);
		}

		header("Location:". FILENAME_DEFAULT);
	}
?>