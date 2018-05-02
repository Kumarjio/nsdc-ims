<?php
	include('includes/application_top.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Home</title>
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery.js';?>"></script>
		<link href="<?php echo DIR_WS_CSS . 'blitzer/jquery-ui-1.8.23.custom.css' ?>" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo DIR_WS_JS . 'jquery-ui-1.8.21.custom.min.js';?>"></script>

		<style type="text/css">
			.infoTable{
				/*border: solid 1px #DDDDDD;*/
				border-bottom: solid 1px #DDDDDD;
				border-left: solid 1px #DDDDDD;
			}

			.infoTable td{
				border-right: solid 1px #DDDDDD;
				border-top: solid 1px #DDDDDD;
			}

			.infoTable th{
				background-color: #FBB900;
				font-weight:bold;
				border-right: solid 1px #DDDDDD;
				border-top: solid 1px #DDDDDD;
			}
		</style>

		<script type="text/javascript">
		<!--
			$(document).ready(function(){
				$('.datepicker').datepicker({
					dateFormat: "dd-mm-yy",
					changeMonth: true,
					changeYear: true
				});
			});
		//-->
		</script>
	</head>
	<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
		<table cellpadding="0" cellspacing="0" border="0" width="98%" align="center">
			<?php
				include( DIR_WS_MODULES . 'header.php' );
			?>
			<tr>
				<td valign="top" colspan="2">
					<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">
						<tr>
							<td valign="top" colspan="2">
								<?php
									include( DIR_WS_MODULES . 'top_menu.php' );
								?>
							</td>
						</tr>
						<tr>
							<td><img src="images/pixel.gif" height="5"></td>
						</tr>
						<tr>
							<td valign="top">
								<table class="backgroundBgMain" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									<tr>
										<td colspan="3"><img src="images/pixel.gif" height="10"></td>
									</tr>
								</table>
								<table class="backgroundBgMain" cellpadding="5" cellspacing="2" border="0" width="100%" align="center">
									<tr>
										<td valign="top">
											<table cellpadding="5" cellspacing="2" border="0" width="100%" align="center">
												<tr>
													<td class="backgroundBgMain">
														<table cellpadding="5" cellspacing="0" border="0" width="100%" align="" class="infoTable">
															<thead>
																<tr>
																	<th class="arial14LGrayBold"></th>
																	<th class="arial14LGrayBold">Enrolled</th>
																	<th class="arial14LGrayBold">Trained</th>
																	<th class="arial14LGrayBold">Certified</th>
																	<th class="arial14LGrayBold">Placed</th>
																	<th class="arial14LGrayBold">NSDC Uploaded</th>
																	<th class="arial14LGrayBold">NSDC Updated</th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " where 1 ";
																	if($_SESSION['sess_adm_type'] != 'ADMIN'){
																		$centre_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																	}

																	$centre_query_raw .= " order by centre_name";

																	$centre_query = tep_db_query($centre_query_raw);
																	
																	while($centre = tep_db_fetch_array($centre_query)){
																		$total_enrolled_query = tep_db_query("select count(student_id) as total_enrolled from " . TABLE_STUDENTS . " where student_type = 'ENROLLED' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'");
																		$total_enrolled = tep_db_fetch_array($total_enrolled_query);

																		$total_trained_query = tep_db_query("select count(student_id) as total_trained from " . TABLE_STUDENTS . " where is_training_completed = '1' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'");
																		$total_trained = tep_db_fetch_array($total_trained_query);

																		$total_cert_student_query = tep_db_query("select count(student_id) as total_certified from " . TABLE_STUDENTS . " where test_result = 'PASS' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'");
																		$total_cert_student = tep_db_fetch_array($total_cert_student_query);

																		$total_placed_query = tep_db_query("select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'");
																		$total_placed = tep_db_fetch_array($total_placed_query);

																		$total_nsdc_updated_query = tep_db_query("select count(student_id) as total_updated from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage1_uploaded = '1' and is_deactivated != '1'");
																		$total_nsdc_updated = tep_db_fetch_array($total_nsdc_updated_query);

																		$total_nsdc_uploaded_query = tep_db_query("select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage2_uploaded = '1' and is_deactivated != '1'");
																		$total_nsdc_uploaded = tep_db_fetch_array($total_nsdc_uploaded_query);
																?>
																<tr>
																	<td class="arial12LGray"><?php echo $centre['centre_name']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_enrolled['total_enrolled']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_trained['total_trained']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_cert_student['total_certified']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_placed['total_placement']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_nsdc_updated['total_updated']; ?></td>
																	<td class="arial12LGray" align="center"><?php echo $total_nsdc_uploaded['total_uploaded']; ?></td>
																</tr>
																<?php } ?>
															</tbody>
														</table><br>
														<table cellpadding="5" cellspacing="0" border="0" width="100%" align="" class="infoTable">
															<thead>
																<tr>
																	<th class="arial14LGrayBold"></th>
																	<?php
																		$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." where 1 order by section_name";
																		$section_query = tep_db_query($section_query_raw);

																		if(tep_db_num_rows($section_query) ){
																			while($listing = tep_db_fetch_array($section_query) ){
																	?>
																	<th class="arial14LGrayBold"><?php echo $listing['section_name']; ?></th>
																	<?php 
																			}
																		}
																	?>
																	<th class="arial14LGrayBold">Total</th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " where 1 ";
																	if($_SESSION['sess_adm_type'] != 'ADMIN'){
																		$centre_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																	}

																	$centre_query_raw .= " order by centre_name";

																	$centre_query = tep_db_query($centre_query_raw);
																	
																	while($centre = tep_db_fetch_array($centre_query)){
																?>
																<tr>
																	<td class="arial12LGray"><?php echo $centre['centre_name']; ?></td>
																	<?php
																		$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." where 1 order by section_name";
																		$section_query = tep_db_query($section_query_raw);

																		$total_centre_enrolled = 0;
																		if(tep_db_num_rows($section_query) ){
																			while($section_array = tep_db_fetch_array($section_query) ){
																				$section_courses_query_raw = "select course_id from " . TABLE_COURSES . " where section_id = '" . $section_array['section_id'] . "' ";
																				$section_courses_query = tep_db_query($section_courses_query_raw);

																				$section_courses = array();
																				while($section_courses_array = tep_db_fetch_array($section_courses_query) ){
																					$section_courses[] = $section_courses_array['course_id'];
																				}

																				if(is_array($section_courses) && count($section_courses)){
																					$total_enrolled_query = tep_db_query("select count(student_id) as total_enrolled from " . TABLE_STUDENTS . " where student_type = 'ENROLLED' and centre_id = '" . $centre['centre_id'] . "' and course_id IN ('" . implode("','", $section_courses) . "') and is_deactivated != '1'");
																					$total_enrolled = tep_db_fetch_array($total_enrolled_query);
																				}

																				$total_student_enrolled = (isset($total_enrolled['total_enrolled']) ? $total_enrolled['total_enrolled'] : 0);

																				$total_centre_enrolled += $total_student_enrolled;
																	?>
																	<td class="arial12LGray" align="center"><?php echo $total_student_enrolled; ?></td>
																	<?php 
																			}
																		}
																	?>
																	<td class="arial12LGray" align="center"><?php echo $total_centre_enrolled; ?></td>
																</tr>
																<?php } ?>
															</tbody>
														</table>
														<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
														<br>
														<form name="frmFilter" action="" method="get">
															<table cellpadding="5" cellspacing="0" border="0" width="100%" align="">
																<tr>
																	<td class="arial12LGray">
																		Course&nbsp;
																		<select name="cid" id="cid" style="width: 150px;">
																			<?php
																				$courses_query_raw = "select course_id, section_id, course_name, course_desc, course_unq_id, course_code, course_duration, course_fee, course_installments, course_instl_duration, course_status from " . TABLE_COURSES . " where 1";
																				$courses_query = tep_db_query($courses_query_raw);

																				while($courses = tep_db_fetch_array($courses_query)){
																			?>
																			<option value="<?php echo $courses['course_id']; ?>" <?php echo (isset($_GET['cid']) && $_GET['cid'] ==  $courses['course_id'] ? 'selected="selected"' : ""); ?>><?php echo $courses['course_name']; ?></option>
																			<?php } ?>
																		</select>
																	</td>
																	<td class="arial12LGray">
																		From Date <input type="text" value="<?php echo (isset($_GET['from1']) ? date("d-m-Y", strtotime($_GET['from1'])) : date("d-m-Y")); ?>" class="datepicker" name="from1">
																	</td>
																	<td class="arial12LGray">
																		To Date <input type="text" value="<?php echo (isset($_GET['to1']) ? date("d-m-Y", strtotime($_GET['to1'])) : date("d-m-Y")); ?>" class="datepicker" name="to1">
																	</td>
																	<td class="arial12LGray">
																		From Date <input type="text" value="<?php echo (isset($_GET['from2']) ? date("d-m-Y", strtotime($_GET['from2'])) : date("d-m-Y")); ?>" class="datepicker" name="from2">
																	</td>
																	<td class="arial12LGray">
																		To Date <input type="text" value="<?php echo (isset($_GET['to2']) ? date("d-m-Y", strtotime($_GET['to2'])) : date("d-m-Y")); ?>" class="datepicker" name="to2">
																	</td>
																	<td>
																		<button type="">Go</button>
																	</td>
																</tr>
															</table>
														</form>
														<?php
															$from1 = (isset($_GET['from1']) ? date("Y-m-d", strtotime($_GET['from1'])) : date("Y-m-d"));
															$to1 = (isset($_GET['to1']) ? date("Y-m-d", strtotime($_GET['to1'])) : date("Y-m-d"));
															$from2 = (isset($_GET['from2']) ? date("Y-m-d", strtotime($_GET['from2'])) : date("Y-m-d"));
															$to2 = (isset($_GET['to2']) ? date("Y-m-d", strtotime($_GET['to2'])) : date("Y-m-d"));
														?>
														<table cellpadding="5" cellspacing="0" border="0" width="100%" align="" class="infoTable">
															<thead>
																<tr>
																	<th class="arial14LGrayBold"></th>
																	<th class="arial14LGrayBold" colspan="2">Enrolled</th>
																	<th class="arial14LGrayBold" colspan="2">Trained</th>
																	<th class="arial14LGrayBold" colspan="2">Certified</th>
																	<th class="arial14LGrayBold" colspan="2">Placed</th>
																	<th class="arial14LGrayBold" colspan="2">NSDC Uploaded</th>
																	<th class="arial14LGrayBold" colspan="2">NSDC Updated</th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " where 1 ";
																	$centre_query_raw .= " order by centre_name";

																	$centre_query = tep_db_query($centre_query_raw);
																	
																	while($centre = tep_db_fetch_array($centre_query)){
																		$total_enrolled_date1_query_raw = "select count(student_id) as total_enrolled from " . TABLE_STUDENTS . " where student_type = 'ENROLLED' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";

																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_enrolled_date1_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}

																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_enrolled_date1_query_raw .= " AND (enrolled_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}

																		$total_enrolled_date1_query = tep_db_query($total_enrolled_date1_query_raw);

																		$total_enrolled_date1 = tep_db_fetch_array($total_enrolled_date1_query);

																		$total_enrolled_date2_query_raw = "select count(student_id) as total_enrolled from " . TABLE_STUDENTS . " where student_type = 'ENROLLED' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_enrolled_date2_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_enrolled_date2_query_raw .= " AND ( enrolled_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}

																		$total_enrolled_date2_query = tep_db_query($total_enrolled_date2_query_raw);

																		$total_enrolled_date2 = tep_db_fetch_array($total_enrolled_date2_query);

																		//Trained
																		$total_trained_date1_query_raw = "select count(s.student_id) as total_trained from " . TABLE_STUDENTS . " AS s, " . TABLE_BATCHES . " AS b where b.batch_id = s.batch_id and s.is_training_completed = '1' and s.centre_id = '" . $centre['centre_id'] . "' and s.is_deactivated != '1'";

																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_trained_date1_query_raw .= " AND s.course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_trained_date1_query_raw .= " AND (b.batch_end_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}
																		$total_trained_date1_query = tep_db_query($total_trained_date1_query_raw);
																		$total_trained_date1 = tep_db_fetch_array($total_trained_date1_query);

																		$total_trained_date2_query_raw = "select count(s.student_id) as total_trained from " . TABLE_STUDENTS . " AS s, " . TABLE_BATCHES . " AS b where b.batch_id = s.batch_id and s.is_training_completed = '1' and s.centre_id = '" . $centre['centre_id'] . "' and s.is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_trained_date2_query_raw .= " AND s.course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_trained_date2_query_raw .= " AND ( b.batch_end_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}

																		$total_trained_date2_query = tep_db_query($total_trained_date2_query_raw);
																		$total_trained_date2 = tep_db_fetch_array($total_trained_date2_query);

																		//Certified
																		$total_cert_student_date1_query_raw = "select count(student_id) as total_certified from " . TABLE_STUDENTS . " where test_result = 'PASS' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_cert_student_date1_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_cert_student_date1_query_raw .= " AND (certificate_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}
																		$total_cert_student_date1_query = tep_db_query($total_cert_student_date1_query_raw);
																		$total_cert_student_date1 = tep_db_fetch_array($total_cert_student_date1_query);

																		$total_cert_student_date2_query_raw = "select count(student_id) as total_certified from " . TABLE_STUDENTS . " where test_result = 'PASS' and centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_cert_student_date2_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_cert_student_date2_query_raw .= " AND (certificate_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}
																		$total_cert_student_date2_query = tep_db_query($total_cert_student_date2_query_raw);
																		$total_cert_student_date2 = tep_db_fetch_array($total_cert_student_date2_query);

																		//Placed
																		$total_placed_date1_query_raw = "select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_placed_date1_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_placed_date1_query_raw .= " AND (job_joining_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}
																		$total_placed_date1_query = tep_db_query($total_placed_date1_query_raw);
																		$total_placed_date1 = tep_db_fetch_array($total_placed_date1_query);

																		$total_placed_date2_query_raw = "select count(p.placement_id) as total_placement from " . TABLE_PLACEMENTS . " p , " . TABLE_STUDENTS . " s where s.student_id = p.student_id and s.centre_id = '" . $centre['centre_id'] . "' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_placed_date2_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_placed_date2_query_raw .= " AND (job_joining_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}
																		$total_placed_date2_query = tep_db_query($total_placed_date2_query_raw);
																		$total_placed_date2 = tep_db_fetch_array($total_placed_date2_query);

																		$total_nsdc_updated_date1_query_raw = "select count(student_id) as total_updated from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage1_uploaded = '1' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_nsdc_updated_date1_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_nsdc_updated_date1_query_raw .= " AND (enrolled_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}
																		$total_nsdc_updated_date1_query = tep_db_query($total_nsdc_updated_date1_query_raw);
																		$total_nsdc_updated_date1 = tep_db_fetch_array($total_nsdc_updated_date1_query);

																		$total_nsdc_updated_date2_query_raw = "select count(student_id) as total_updated from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage1_uploaded = '1' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_nsdc_updated_date2_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_nsdc_updated_date2_query_raw .= " AND (enrolled_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}
																		$total_nsdc_updated_date2_query = tep_db_query($total_nsdc_updated_date2_query_raw);
																		$total_nsdc_updated_date2 = tep_db_fetch_array($total_nsdc_updated_date2_query);


																		$total_nsdc_uploaded_date1_query_raw = "select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage2_uploaded = '1' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_nsdc_uploaded_date1_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from1']) && isset($_GET['to1'])){
																			$total_nsdc_uploaded_date1_query_raw .= " AND (enrolled_date between '" . $from1 . "' AND '" . $to1 . "')";
																		}
																		$total_nsdc_uploaded_date1_query = tep_db_query($total_nsdc_uploaded_date1_query_raw);
																		$total_nsdc_uploaded_date1 = tep_db_fetch_array($total_nsdc_uploaded_date1_query);

																		$total_nsdc_uploaded_date2_query_raw = "select count(student_id) as total_uploaded from " . TABLE_STUDENTS . " where centre_id = '" . $centre['centre_id'] . "' and stage2_uploaded = '1' and is_deactivated != '1'";
																		if(isset($_GET['cid']) && (int)$_GET['cid']){
																			$total_nsdc_uploaded_date2_query_raw .= " AND course_id = '" . $_GET['cid'] . "'";
																		}
																		if(isset($_GET['from2']) && isset($_GET['to2'])){
																			$total_nsdc_uploaded_date2_query_raw .= " AND (enrolled_date between '" . $from2 . "' AND '" . $to2 . "')";
																		}
																		$total_nsdc_uploaded_date2_query = tep_db_query($total_nsdc_uploaded_date2_query_raw);
																		$total_nsdc_uploaded_date2 = tep_db_fetch_array($total_nsdc_uploaded_date2_query);
																?>
																<tr>
																	<td class="arial12LGray">
																		<?php echo $centre['centre_name']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_enrolled_date1['total_enrolled']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_enrolled_date2['total_enrolled']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_trained_date1['total_trained']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_trained_date2['total_trained']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_cert_student_date1['total_certified']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_cert_student_date2['total_certified']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_placed_date1['total_placement']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_placed_date2['total_placement']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_nsdc_updated_date1['total_updated']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_nsdc_updated_date2['total_updated']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_nsdc_uploaded_date1['total_uploaded']; ?>
																	</td>
																	<td class="arial12LGray" align="center">
																		<?php echo $total_nsdc_uploaded_date2['total_uploaded']; ?>
																	</td>
																</tr>
																<?php } ?>
															</tbody>
														</table><br>
														<?php } ?>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<!-- Main contant starts here-->
							</td>
						</tr>
						<?php include( DIR_WS_MODULES . 'footer.php' ); ?>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>