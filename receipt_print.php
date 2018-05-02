<?php
	include('includes/application_top.php');

	$receipt_id = tep_db_input(tep_db_prepare_input($_GET['rid']));
	$receipt_info_query_raw = "select r.*, sp.*, s.*, i.installment_no from " . TABLE_RECEIPTS .  " AS r, " . TABLE_STUDENT_PAYMENTS . " AS sp left join " . TABLE_INSTALLMENTS . " i on (i.installment_id = sp.installment_id), " . TABLE_STUDENTS . " s WHERE sp.stud_payment_id = r.stud_payment_id AND s.student_id = sp.student_id AND r.receipt_id = '" . $receipt_id . "'";
	
	if($_SESSION['sess_adm_type'] != 'ADMIN'){
		$receipt_info_query_raw .= " and r.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
	}

	$receipt_info_query = tep_db_query($receipt_info_query_raw);
	$receipt_info = tep_db_fetch_array($receipt_info_query);

	$total_tax  = 18;
	$tax_amount = calculate_tax($receipt_info['receipt_amount'], $total_tax);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>IMS Proschool Receipt</title>
		<style>
			body{
				font-family:Arial, Helvetica, sans-serif;
				font-size:13px;
				line-height:18px;
				margin:0px;
				padding:0px;
			}
		</style>
	</head>
	<body>
		<table width="600" align="center" cellpadding="5" cellspacing="0" style="border: 1px solid #ccc;">
			<tr>
				<th style="text-align:left; width:220px; border-bottom: solid 1px #2662a0">
					<img src="images/proschool.png" width="130" alt="Proschool" border="0" title="Proschool">
				</th>
				<th style="text-align:left; font-family:Arial, Helvetica, sans-serif; color:#2662a0; font-size:25px; height:55px;  border-bottom: solid 1px #2662a0">Receipt</th>
			</tr>
			<tr>
				<td style="padding:10px; vertical-align:top;">
					<strong>Receipt To:</strong><br />
					<?php
						echo $receipt_info['student_full_name'].' '.$receipt_info['father_surname'].'<br>';
						echo $receipt_info['student_address'].'<br>';
						echo $receipt_info['student_village'] . (!empty($receipt_info['student_taluka']) ? ', ' . $receipt_info['student_taluka'] : '') . (!empty($receipt_info['student_district']) ? ', ' . $receipt_info['student_district'] : '') .'<br>';
						echo $receipt_info['student_state'] . (!empty($receipt_info['student_pincode']) ? ' - ' . $receipt_info['student_pincode'] : '');
					?>
				</td>
				<td style="padding:10px; text-align:right;">
					<strong>Receipt By:</strong><br />
					IMS PROSCHOOL PVT LTD<br>
					Office No 704, 7th Floor,<br>
					G Square Business Park,<br>
					Plot No 25 & 26,<br>
					Sector 30, Opposite Sanpada Railway Station,<br>
					Navi Mumbai 400 703<br>
					GSTIN (Gujarat) - 24AAACI7332J1ZO
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr style="margin-bottom: 1px;"/></td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%">
						<tr>
							<td align="left" style="padding:10px; vertical-align:top;"><strong>Receipt Date:</strong> <?php echo date("M d, Y", strtotime($receipt_info['receipt_created'])); ?></td>
							<td align="center" style="padding:10px; vertical-align:top;">&nbsp;</td>
							<td align="right" style="padding:10px; vertical-align:top;"><strong>Receipt No:</strong> <?php echo $receipt_info['receipt_number']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%" style="border: 1px solid #ccc; margin:0px 10px 10px 10px; width: 580px;" cellpadding="0" cellspacing="0">
						<tr>
							<td width="75%" align="center" style="border-right:1px solid #ccc; border-bottom:1px solid #ccc; vertical-align:middle;"><strong>Particular</strong></td>
							<td width="25%" align="center" style="border-bottom:1px solid #ccc; vertical-align:middle;"><strong>Amount</strong></td>
						</tr>
						<tr style="height:130px; vertical-align:top;">
							<td style="padding:3px 5px;">
								<?php 
									echo $disp_stud_payment_type_array[$receipt_info['stud_payment_type']];
									if($receipt_info['installment_no'] != ''){
										echo ' - Installment : ' . $receipt_info['installment_no'];
									}
								?>
							</td>
							<td style="padding:3px 5px; text-align:right;">
								<?php //echo display_price($receipt_info['receipt_amount']);?>
								<?php echo (display_price($receipt_info['receipt_amount']- $tax_amount));?>
							</td>
						<tr>
							<td style="padding:3px 5px; border-top:1px solid #ccc;"><strong>GST ( <?php echo $total_tax; ?>% ) </strong></td>
							<td style="padding:3px 5px; text-align:right; border-top:1px solid #ccc;">
							<?php echo (display_price($tax_amount));?></td>
						</tr>	
						<tr>
							<td style="padding:3px 5px; border-top:1px solid #ccc;"><strong>Total Receipt Amount in</strong></td>
							<td style="padding:3px 5px; text-align:right; border-top:1px solid #ccc;">
							<?php echo display_price($receipt_info['receipt_amount']);?></td>
						</tr>
						<?php
							if((int)$receipt_info['receipt_amount'] > 0){
						?>
						<tr>
							<td colspan="2" style="padding:3px 5px; border-top:1px solid #ccc;"><strong>Amount in Words :</strong> <?php echo convert_number($receipt_info['receipt_amount']); ?> only</td>
						</tr>
						<?php
							}
						?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding:10px 0px;">Thank You</td>
			</tr>
		</table>
		<script type="text/javascript">
		<!--
			window.print();
		//-->
		</script>
	</body>
</html>