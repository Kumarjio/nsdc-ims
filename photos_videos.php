<?php	
	include('includes/application_top.php');

	$arrMessage = array("deleted"=>"Photo/Video has been deleted successfully!!!", 'added'=>'Photo/Video has been added successfully',"edited"=>"Photo/Video has been edited successfully");

	$action = $_POST['action_type'];
	
	if(isset($action) && tep_not_null($action))
	{
		$media_id = tep_db_prepare_input($_POST['media_id']);

		$centre_id = tep_db_prepare_input($_POST['centre_id']);
		$section_id = tep_db_prepare_input($_POST['section_id']);
		$course_id = tep_db_prepare_input($_POST['course_id']);
		$batch_id = tep_db_prepare_input($_POST['batch_id']);
		$media_category = tep_db_prepare_input($_POST['media_category']);
		$media_title = tep_db_prepare_input($_POST['media_title']);
		$media_type = tep_db_prepare_input($_POST['media_type']);
		$media_file_desc = $_POST['media_file_desc'];
		$media_embed_code = $_POST['media_embed_code'];
		$media_sort_order = tep_db_prepare_input($_POST['media_sort_order']);

		$arr_db_values = array(
			'centre_id' => $centre_id,
			'course_id' => $course_id,
			'section_id' => $section_id,
			'batch_id' => $batch_id,
			'media_category' => $media_category,
			'media_title' => $media_title,
			'media_type' => $media_type,
			'media_file_desc' => $media_file_desc,
			'media_embed_code' => $media_embed_code,
			'media_sort_order' => $media_sort_order,
			'media_added' => 'now()'
		);

		if(isset($_FILES['media_file_name']['name']) && $_FILES['media_file_name']['name'] != ''){
			$ext = get_extension($_FILES['media_file_name']['name']);
			$src = $_FILES['media_file_name']['tmp_name'];

			$dest_filename = 'gallery_photo_' . time() . date("His") . $ext;
			$dest = DIR_FS_UPLOAD . $dest_filename;

			if(file_exists($dest))
			{
				@unlink($dest);
			}

			if(move_uploaded_file($src, $dest))	
			{
				$arr_db_values['media_file_name'] = $dest_filename;
			}
		}

		switch($action){
			case 'add':
				tep_db_perform(TABLE_MEDIA, $arr_db_values);
				$msg = 'added';
			break;

			case 'edit':
				tep_db_perform(TABLE_MEDIA, $arr_db_values, "update", "media_id = '" . $media_id . "'");
				$msg = 'edited';
			break;

			case 'delete':
				tep_db_query("delete from ". TABLE_MEDIA ." where media_id = '". $media_id ."'");
				$msg = 'deleted';
			break;
		}

		tep_redirect(tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','int_id','actionType')) . 'msg=' . $msg));
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title><?php echo TITLE ?>: Photos / Videos Management</title>
		
		<?php include(DIR_WS_MODULES . 'common_head.php'); ?>

		<script language="javascript">
		<!--
			function delete_selected(objForm, action_type, int_id){
				if(confirm("Are you want to delete this photo/video?")){
					objForm.action_type.value = action_type;
					objForm.media_id.value = int_id;
					objForm.submit();
				}
			}

			function get_courses(default_course){
				var section = $('#section_id').val();

				$('#course_id').empty();
				$('#course_id').append($("<option></option>").attr("value",'').text('Please choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_courses&section='+section,
					type: 'POST',
					dataType: 'json',
					async: false,
					success: function(response){
						$(response).each(function(key, values){
							if(default_course == values.course_id){
								$('#course_id').append($("<option></option>").attr("value",values.course_id).attr('selected', 'selected').text(values.frm_course_name));
							}else{
								$('#course_id').append($("<option></option>").attr("value",values.course_id).text(values.frm_course_name));
							}
						});

						get_batch('');
					}
				});
			}

			function get_batch(default_batch){
				var course = $('#course_id').val();
				var centre = $('#centre_id').val();

				$('#batch_id').empty();
				$('#batch_id').append($("<option></option>").attr("value",'').text('Please Choose'));

				$.ajax({
					url: 'get_data.php',
					data: 'action=get_batch&course='+course+'&centre='+centre,
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

			function toggle_type(){
				var media_type = $('select[name="media_type"] option:selected').val();

				$('.blk_photo').hide();
				$('.blk_video').hide();

				if(media_type == 'IMAGE'){
					$('.blk_photo').show();
				}else if(media_type == 'VIDEO'){
					$('.blk_video').show();
				}
			}

			$(document).ready(function(){
				$.validator.messages.required = "";
				$("#frmDetails").validate();
			});
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

														$info_query_raw = " select * from " . TABLE_MEDIA . " where media_id='" . $int_id . "' ";

														if($_SESSION['sess_adm_type'] != 'ADMIN'){
															$info_query_raw .= " and centre_id = '" . $_SESSION['sess_centre_id'] . "'";
														}

														$info_query = tep_db_query($info_query_raw);

														$info = tep_db_fetch_array($info_query);
													}
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Photos / Videos Management</td>
														<td align="right"><img src="images/left.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))); ?>" class="arial14LGrayBold">Photos/Videos Listing</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<form name="frmDetails" id="frmDetails" method="post" enctype="multipart/form-data">
																<input type="hidden" name="action_type" id="action_type" value="<?php echo $_GET['actionType'];?>">
																<input type="hidden" name="media_id" id="media_id" value="<?php echo $info['media_id']; ?>"> 
																<table class="tabForm" cellpadding="5" cellspacing="5" border="0" width="100%" align="center">
																	<?php if($_SESSION['sess_adm_type'] == 'ADMIN'){?>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Center&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="centre_id" id="centre_id" class="required">
																				<option value="">Please choose</option>
																				<?php
																					$centre_query_raw = " select centre_id, centre_name from " . TABLE_CENTRES . " order by centre_name";
																					$centre_query = tep_db_query($centre_query_raw);
																					
																					while($centre = tep_db_fetch_array($centre_query)){
																				?>
																				<option value="<?php echo $centre['centre_id'];?>" <?php echo($info['centre_id'] == $centre['centre_id'] ? 'selected="selected"' : '');?>><?php echo $centre['centre_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<?php }else { ?>
																	<input type="hidden" name="centre_id" id="centre_id" value="<?php echo $_SESSION['sess_centre_id'];?>">
																	<?php } ?>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Sector&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="section_id" id="section_id" title="Please select sector" class="required" onchange="javascript: get_courses('');">
																				<option value="">Please choose</option>
																				<?php
																					$section_query_raw = " select section_id, section_name from ". TABLE_SECTIONS ." order by section_name";
																					$section_query = tep_db_query($section_query_raw);
																					
																					while($section = tep_db_fetch_array($section_query)){
																				?>
																				<option value="<?php echo $section['section_id'];?>" <?php echo($info['section_id'] == $section['section_id'] ? 'selected="selected"' : '');?>><?php echo $section['section_name'];?></option>
																				<?php } ?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Course&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="course_id" id="course_id" title="Please select course" class="required" onchange="javascript: get_batch('');" style="width: 120px;">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Batch&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="batch_id" id="batch_id" title="Please select batch" class="required">
																				<option value="">Please choose</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Photo/Video Title&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<input type="text" name="media_title" id="media_title" title="Enter photo/video title" size="25" value="<?php echo  ($dupError ? $_POST['media_title'] : $info['media_title']) ?>" class="required">
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Category&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="media_category" id="media_category" class="required">
																				<option value="">Please choose</option>
																				<?php
																					foreach($media_category_array as $kMediaCat => $vMediaCat ){
																				?>
																				<option value="<?php echo $vMediaCat; ?>" <?php echo($info['media_category'] == $vMediaCat ? 'selected="selected"' : '');?>><?php echo $vMediaCat; ?></option>
																				<?php
																					}
																				?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Type&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<select name="media_type" id="media_type" class="required" onchange="javascript: toggle_type();">
																				<option value="">Please choose</option>
																				<?php
																					foreach($media_type_array as $kMediaType => $vMediaType ){
																				?>
																				<option value="<?php echo $kMediaType; ?>" <?php echo($info['media_type'] == $kMediaType ? 'selected="selected"' : '');?>><?php echo $vMediaType; ?></option>
																				<?php
																					}
																				?>
																			</select>
																		</td>
																	</tr>
																	<tr class="blk_photo" style="display: none;">
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Photo&nbsp;:</td>
																		<td>
																			<?php
																				if($info['media_file_name'] !=''){
																			?>
																			<img src="<?php echo DIR_WS_UPLOAD . $info['media_file_name'];?>" width="200" /><br/><br/>
																			<?php
																				}
																			?>
																			<input type="file" name="media_file_name" id="media_file_name" title="Choose photo file">
																		</td>
																	</tr>
																	<tr class="blk_video" style="display: none;">
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Video Embed Code&nbsp;<font color="#ff0000">*</font>&nbsp;:</td>
																		<td>
																			<textarea name="media_embed_code"><?php echo(isset($info['media_embed_code']) ? $info['media_embed_code'] : '');?></textarea>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Photo / Video Description&nbsp;:</td>
																		<td>
																			<textarea name="media_file_desc"><?php echo(isset($info['media_file_desc']) ? $info['media_file_desc'] : '');?></textarea>
																		</td>
																	</tr>
																	<tr>
																		<td width="15%" class="arial12LGrayBold" valign="top" align="right">&nbsp;Sort Order&nbsp;:</td>
																		<td>
																			<input type="text" name="media_sort_order" id="media_sort_order" title="Enter sort order" size="25" value="<?php echo  ($info['media_sort_order'] != '' && $info['media_sort_order'] != '9999' ? $info['media_sort_order'] : '') ?>">
																		</td>
																	</tr>
																</table>
																<script type="text/javascript">
																<!--
																	toggle_type();
																	get_courses('<?php echo $info['course_id']; ?>');
																	 get_batch('<?php echo $info['batch_id']; ?>');
																//-->
																</script>
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
											?>
												<table cellpadding="2" cellspacing="0" border="0" width="100%" align="" class="tab">
													<tr>
														<td class="arial18BlueN">Photos/Videos Management</td>
														<td align="right"><img src="images/add.png" align="absmiddle" width="25">&nbsp;&nbsp;<a href="<?php echo tep_href_link(CURRENT_PAGE, tep_get_all_get_params(array('msg','actionType','intID'))."actionType=add"); ?>" class="arial14LGrayBold">Add Photo/Video</a></td>
													</tr>
													<tr>
														<td colspan="2">
															<?php
																$listing_query_raw = " select m.media_id, m.media_type, m.media_title, m.media_category, cn.centre_name, d.district_name, sec.section_name, b.batch_title from ". TABLE_MEDIA ." m, ". TABLE_CENTRES ." cn, ". TABLE_DISTRICTS ." d, " . TABLE_SECTIONS . " sec, " . TABLE_BATCHES . " b where b.batch_id = m.batch_id and sec.section_id = m.section_id and d.district_id = cn.district_id and cn.centre_id = m.centre_id ";

																if($_SESSION['sess_adm_type'] != 'ADMIN'){
																	$listing_query_raw .= " and m.centre_id = '" . $_SESSION['sess_centre_id'] . "'";
																}

																$listing_query = tep_db_query($listing_query_raw);
															?>
															<form name="frmListing" id="frmListing" method="post">
																<input type="hidden" name="action_type" id="action_type" value="">
																<input type="hidden" name="media_id" id="media_id" value="">
																<table cellpadding="5" cellspacing="0" width="99%" align="center" border="0" id="table_filter" class="display">
																	<thead>
																		<th>District</th>
																		<th>Centre</th>
																		<th>Sector</th>
																		<th>Batch</th>
																		<th>Category</th>
																		<th>Title</th>
																		<th>Type</th>
																		<th width="10%">Action</th>
																	</thead>
																	<tbody>
																	<?php
																		if(tep_db_num_rows($listing_query) ){
																			while( $listing = tep_db_fetch_array($listing_query) ){
																	?>
																		<tr>
																			<td valign="top"><?php echo $listing['district_name']; ?></td>
																			<td valign="top"><?php echo $listing['centre_name']; ?></td>
																			<td valign="top"><?php echo $listing['section_name']; ?></td>
																			<td valign="top"><?php echo $listing['batch_title']; ?></td>
																			<td valign="top"><?php echo $listing['media_category']; ?></td>
																			<td valign="top"><?php echo $listing['media_title']; ?></td>
																			<td valign="top"><?php echo $media_type_array[$listing['media_type']]; ?></td>
																			<td valign="top"><a href="<?php echo tep_href_link(CURRENT_PAGE,tep_get_all_get_params(array('msg','actionType','int_id'))."actionType=edit&int_id=".$listing['media_id']); ?>"><img src="<?php echo DIR_WS_IMAGES ?>edit.png" border="0" width="20" title="Edit"></a>&nbsp;&nbsp;&nbsp;<a href="javascript: delete_selected(document.frmListing, 'delete','<?php echo $listing['media_id']; ?>')"><img src="<?php echo DIR_WS_IMAGES ?>delete.png" width="20" border="0" alt="Delete" title="Delete"></a></td>
																		</tr>
																	<?php
																			}
																	?>
																	<script type="text/javascript" charset="utf-8">
																		$(document).ready(function() {
																			$('#table_filter').dataTable({
																				"aoColumns": [
																					null, //Disctrict
																					null, //Centre
																					null, //Section
																					null, //Batch
																					null, //Category
																					null, //Title
																					null, //Type
																					{ "bSortable": false}
																				],
																				"aaSorting": [[1,'asc'], [2,'asc'], [3,'asc']],
																				 "iDisplayLength": 300,
																				"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
																				"bStateSave": false,
																				"bAutoWidth": false
																			});
																		});
																	</script>
																	<?php
																		}else{
																	?>
																		<tr>
																				<td align="center" colspan="6" class="verdana11Red">No Photo/Video Found !!</td>
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