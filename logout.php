<?php
/* Includes the application-top.php */
include('includes/application_top.php');

if(tep_session_is_registered('sess_status')) {
	tep_session_unregister("sess_status");
	unset($_SESSION['sess_status']);
}

if(tep_session_is_registered('sess_admin_id')) {
	tep_session_unregister("sess_admin_id");
	unset($_SESSION['sess_admin_id']);
}

if(tep_session_is_registered('sess_adm_type')) {
	tep_session_unregister("sess_adm_type");
	unset($_SESSION['sess_adm_type']);
}

if(tep_session_is_registered('sess_centre_id')) {
	tep_session_unregister("sess_centre_id");
	unset($_SESSION['sess_centre_id']);
}

if(tep_session_is_registered('sess_adm_name')) {
	tep_session_unregister("sess_adm_name");
	unset($_SESSION['sess_adm_name']);
}

if(tep_session_is_registered('sess_adm_email')) {
	tep_session_unregister("sess_adm_email");
	unset($_SESSION['sess_status']);
}

/* redirect to the index.php that is ogin page. */
header("Location:". FILENAME_DEFAULT);
?>