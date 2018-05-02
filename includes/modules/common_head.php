<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_CSS ?>style.css">

<style type="text/css" title="currentStyle">
	@import "<?php echo DIR_WS_JS; ?>data_tables/css/demo_table.css";
</style>

<?php
	$arr_exclude_pages = array(FILENAME_DEFAULT, FILENAME_INDEX_PROCESS, FILENAME_HOME);
	if(!in_array(CURRENT_PAGE, $arr_exclude_pages)){
?>
<script language="javascript" src="<?php echo DIR_WS_JS; ?>jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo DIR_WS_JS; ?>data_tables/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo DIR_WS_JS; ?>jquery.validation/jquery.validate.js"></script>
<?php } ?>