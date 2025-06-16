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
		<h2>일련번호관리</h2>
		<hr>
	</div>
	<div class="clearfix" style="margin-top:30px"></div>
 
	<?php 

		if($cType == 'list') {
            $V = $_REQUEST;
                  
            if($V['mode']=="add" && $V['strun'] == "add_proc"){
                $V = $_POST;
                
                $wCnt = $wpdb->get_var("select count(*) from `autopole3144`.`tbl_rfid_serial` where `goods_code`='".trim($V['goods_code'])."' and delete_yn != 'Y' order by date desc ");
                
                if($wCnt > 0){echo "<script type='text/javascript'>alert('이미 등록된 제품정보 입니다.');history.back();</script>";exit;}
                
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                
                $qry1 = "INSERT INTO `autopole3144`.`tbl_inven` set ";
                
                if(isset($V['goods_idx'])) $fields1[] = "`goods_idx`='".$V['goods_idx']."'";
                if(isset($V['goods_code'])) $fields1[] = "`goods_code`='".$V['goods_code']."'";
                if(isset($V['goods_name'])) $fields1[] = "`goods_name`='".$V['goods_name']."'";
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                if(isset($V['input_count'])) $fields1[] = "`input_count`='".$V['input_count']."'";
                if(isset($V['out_count'])) $fields1[] = "`out_count`='".$V['out_count']."'";
                
                if(isset($V['current_count'])) $fields1[] = "`current_count`='".$V['current_count']."'";
                if(isset($V['notice_count'])) $fields1[] = "`notice_count`='".$V['notice_count']."'";
                if(isset($V['total_count'])) $fields1[] = "`total_count`='".$V['total_count']."'";
                
                if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                if(isset($V['delete_yn'])) $fields1[] = "`delete_yn`='".$V['delete_yn']."'";
                
                $add_fields1 = implode(", ", $fields1);
                $qry1 .= $add_fields1;
                
                $wpdb->query($qry1.", reg_date='".$timeStamp."'");
                
                echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_serial&cType=list';</script>";
                exit;
                
                
            }else if($V['mode'] == "edit" && $V['strun'] == "edit_proc") {
                $V = $_POST;
                $cnt = $wpdb->get_row("select * from tbl_inven where storage_code='".$V['storage_code']."' and goods_code = '".$V['goods_code']."' and delete_yn != 'Y'");
                if($cnt > 0){
                    echo "<script type='text/javascript'>alert('이미 해당 창고에 등록되어 있는 상품입니다.');location.href='admin.php?page=bbse_commerce_serial&cType=list';</script>";exit;
                }
                
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                
                $fields1 = array();
                $qry1 = "update `autopole3144`.`tbl_inven` set ";
                
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                if(isset($V['current_count'])) $fields1[] = "`current_count`='".$V['current_count']."'";
                if(isset($V['notice_count'])) $fields1[] = "`notice_count`='".$V['notice_count']."'";
                //if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                
                $edit_fields1 = implode(", ", $fields1);
                
                $qry1 .= $edit_fields1." ,`update_date` = '".$timeStamp."' where `goods_code`='".$V['goods_code']."' and idx = '".$V['invenIdx']."' and delete_yn != 'Y'";
                
                $wpdb->query($qry1);
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-detail.php");
                
            }
            else if($V['strun'] == 'del_proc'){
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                
                $V = $_POST;
                for ($i=0; $i<count($V['check']); $i++){
                    $qry= "update `autopole3144`.`tbl_inven` set delete_yn='Y', update_date='".$timeStamp."' where `idx` = '".$V['check'][$i]."'";
                    $wpdb->query($qry);
                }
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-list.php");
            }
            else if($V['strun'] == 'batch_proc'){
                
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
                
                wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-serial-dbinsert-csv.php");
                exit;
            }
            
            if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-detail.php");
            }elseif($_GET['mode'] == "download"){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-download.php");
            }else{
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-list.php");
            }
            
		}else{
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-serial-".$cType.".php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
