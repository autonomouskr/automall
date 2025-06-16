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
	
	function download_excel(){
	
	}
	
</script>

<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>입출고내역</h2>
		<hr>
	</div>
	<div class="clearfix"></div>

<!-- 	<div class="tabWrap"> -->
<!-- 	  <ul class="tabList"> -->
<!--		<li <?php echo ($cType=='list')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_invenInOut&cType=list">입출고관리</a></li>
<!--		<li <?php echo ($cType=='result-list')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_invenInOut&cType=result-list">처리내역</a></li>
<!-- 	  </ul> -->
<!-- 	  <div class="clearfix"></div> -->
<!-- 	</div> -->

	<div class="clearfix" style="margin-top:30px"></div>

	<?php 
		if($cType == 'list') {
            $V = $_REQUEST;
            
            if($V['mode']=="add" && $V['strun'] == "add_proc"){
                $V = $_POST;
                
                $wCnt = $wpdb->get_var("select count(*) from `bbse_commerce_inven` where `business_number`='".trim($V['business_number'])."'");
                $eCnt = $wpdb->get_var("select count(*) from `bbse_commerce_inven` where `business_sub_number`='".trim($V['business_sub_number'])."'");
                
                $timeStamp=current_time('timestamp');
                
                if($wCnt > 0 && $eCnt > 0){echo "<script type='text/javascript'>alert('이미 존재하는 창고정보 입니다.');history.back();</script>";exit;}
                
                $fields1 = array();
                
                $qry1 = "INSERT INTO `bbse_commerce_inven` set ";
                
                if(isset($V['business_number'])) $fields1[] = "`business_number`='".$V['business_number']."'";
                if(isset($V['business_sub_number'])) $fields1[] = "`business_sub_number`='".$V['business_sub_number']."'";
                if(isset($V['storage_name'])) $fields1[] = "`storage_name`='".$V['storage_name']."'";
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                if(isset($V['manager_name'])) $fields1[] = "`manager_name`='".$V['manager_name']."'";
                
                if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
                if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
                if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
                
                if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
                if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
                if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
                if(isset($V['manager_hp'])) $fields1[] = "`manager_hp`='".$V['hp']."'";
                $add_fields1 = implode(", ", $fields1);
                $qry1 .= $add_fields1;
                $idx = $wpdb->insert_id;
                
                $storage_code=$timeStamp."-".$idx;
                
                
                if($idx > '0'){
                    $result=$wpdb->query("UPDATE bbse_commerce_inven SET storage_code='".$storage_code."' , delete_yn = 'N' WHERE idx='".$idx."'");
                }
                
                echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_invenInOut&cType=list';</script>";
                exit;
                
            }else if($V['mode'] == "edit" && $V['strun'] == "edit_proc") {
                $V = $_POST;
                
                $timeStamp=current_time('timestamp');
                
                $fields1 = array();
                $qry1 = "update `bbse_commerce_inven` set ";
                
                if(isset($V['storage_name'])) $fields1[] = "`storage_name`='".$V['storage_name']."'";
                if(isset($V['manager_name'])) $fields1[] = "`zipcode`='".$V['manager_name']."'";
                
                if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
                if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
                if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
                
                if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
                if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
                if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
                if(isset($V['manager_hp'])) $fields1[] = "`manager_hp`='".$V['manager_hp']."'";
                if(isset($V['update_id'])) $fields1[] = "`update_id`='".$V['update_id']."'";
                
                $edit_fields1 = implode(", ", $fields1);
                
                $qry1 .= $edit_fields1." where `business_number`='".$V['business_number']."' and `business_sub_number`=".$V['business_sub_number']."'";
                
                $wpdb->query($qry1.", update_date='".$timeStamp."'");
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-invenInOut-detail.php");
                
            }
            else if($V['strun'] == 'del_proc'){
                $timeStamp=current_time('timestamp');
                $fields1 = array();
                
                $V = $_POST;
                $wpdb->query("update `bbse_commerce_inven` set delete_yn='Y', update_date=".$timeStamp." where `idx`='".$V['idx']."'");
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-list.php");
            }
            
            if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-detail.php");
            }elseif($_GET['mode'] == "download"){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-download.php");
            }else{
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-list.php");
            }
            
		}else if($cType == 'result-list'){
		    require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-".$cType.".php");
		}else{
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-invenInOut-".$cType.".php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
