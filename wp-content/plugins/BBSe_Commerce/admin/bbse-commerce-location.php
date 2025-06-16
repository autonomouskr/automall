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
		<h2>로케이션관리</h2>
		<hr>
	</div>
	<div class="clearfix" style="margin-top:30px"></div>
 
	<?php 

		if($cType == 'list') {
            $V = $_REQUEST;
                  
            if($V['mode']=="add" && $V['strun'] == "add_proc"){
                $V = $_POST;
                
                $wCnt = $wpdb->get_var("select count(*) from `autopole3144`.`tbl_locations` where `location_x`='".trim($V['location_x'])."' and `location_y`='".trim($V['location_y'])."' and `rack_code`='".trim($V['rack_code'])."' and `storage_code` = '".trim($V['storage_code'])."' and delete_yn != 'Y' ");
                if($wCnt > 0){echo "<script type='text/javascript'>alert('이미 등록된 로케이션 위치 입니다.');history.back();</script>";exit;}
                
                $timeStamp=current_time('timestamp');
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $qry1 = "INSERT INTO `autopole3144`.`tbl_locations` set ";
                
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                if(isset($V['location_x'])) $fields1[] = "`location_x`='".$V['location_x']."'";
                if(isset($V['location_y'])) $fields1[] = "`location_y`='".$V['location_y']."'";
                if(isset($V['rack_code'])) $fields1[] = "`rack_code`='".$V['rack_code']."'";
                if(isset($V['delete_yn'])) $fields1[] = "`delete_yn`='N'";
                
                $add_fields1 = implode(", ", $fields1);
                $qry1 .= $add_fields1;
                
                $reuslt = $wpdb->query($qry1.", reg_date='".$timeStamp."'");
                
                echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_location&cType=list';</script>";
                exit;
                
            }else if($V['mode'] == "edit" && $V['strun'] == "edit_proc") {
                $V = $_POST;
                
                $timeStamp=current_time('timestamp');
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                $qry1 = "update `autopole3144`.`tbl_inven` set ";
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                
                $edit_fields1 = implode(", ", $fields1);
                
                $qry1 .= $edit_fields1." ,`update_date` = '".$timeStamp."' where `id`='".$V['id']."' and delete_yn != 'Y'";
                $result = $wpdb->query($qry1);
                
                if($result > 0){
                    echo "<script type='text/javascript'>location.href='admin.php?page=bbse-commerce-location-detail.php';</script>";
                    exit;
                }
                
                //require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-detail.php");
                
            }
            else if($V['strun'] == 'del_proc'){
                $timeStamp=current_time('timestamp');
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                $V = $_POST;
                
                for ($i=0; $i<count($V['check']); $i++){
                    $qry= "update `autopole3144`.`tbl_locations` set delete_yn='Y', update_date='".$timeStamp."' where `idx` = '".$V['check'][$i]."'";
                    $wpdb->query($qry);
                }
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-list.php");
            }
            else if($V['strun'] == 'batch_proc'){
                
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
                
                wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-location-dbinsert-csv.php");
                exit;
            }
            
            if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-detail.php");
            }elseif($_GET['mode'] == "download"){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-download.php");
            }else{
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-list.php");
            }
            
		}else{
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-location-".$cType.".php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
