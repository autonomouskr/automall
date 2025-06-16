<?php
if(!$_REQUEST['cType']) $cType='list';
else $cType=$_REQUEST['cType'];
?>

<script language="javascript">
	function go_config(tStatus){
		var goUrl="admin.php?page=bbse_commerce_member&cType="+tStatus;
		window.location.href =goUrl;
	}

	function check_number(){
		var key = event.keyCode;
		if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
			event.returnValue = false;
		}
	}

	function go_config_option(cType,tMode,tIdx){
		var goStr="admin.php?page=bbse_commerce_member&cType="+cType;

		if(tMode) goStr +="&tMode="+tMode;
		if(tIdx) goStr +="&tIdx="+tIdx;

		window.location.href =goStr;
	}

	function remove_popup(){
		tb_remove();
	}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>


<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>더존코드관리</h2>
		<hr>
	</div>
	<div class="clearfix" style="margin-top:30px"></div>
 
	<?php 

		if($cType == 'list') {
            $V = $_REQUEST;
            require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-douzone-list.php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
