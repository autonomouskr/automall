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
		<h2>제품관리</h2>
		<hr>
	</div>
	<div class="clearfix" style="margin-top:30px"></div>
 
	<?php 

		if($cType == 'list') {
            $V = $_REQUEST;
                  
            if($V['mode']=="add" && $V['strun'] == "add_proc"){
                $V = $_POST;
                
                //$wCnt = $wpdb->get_var("select count(*) from `autopole3144`.`tbl_inven` where `goods_code`='".trim($V['goods_code'])."'  and `storage_code` = '".trim($V['storage_code'])."' and delete_yn != 'Y' ");
                
                $wCnt = $wpdb->get_var("select count(*) from `autopole3144`.`tbl_inven` where `goods_idx`='".trim($V['goods_idx'])."' and `storage_code` = '".trim($V['storage_code'])."' and goods_option_title = '".$V['goods_option_title']."' and delete_yn != 'Y' ");
                if($wCnt > 0){echo "<script type='text/javascript'>alert('이미 등록된 제품정보 입니다.');history.back();</script>";exit;}
                
                //$existGoodsCode = $wpdb->get_results("select goods_code from tbl_inven where `goods_idx`='".trim($V['goods_idx'])."' and delete_yn != 'Y'");
                $inven = $wpdb->get_results("select * From tbl_inven where goods_idx = '".$V['goods_idx']."' and goods_option_title = '".$V['goods_option_title']."' and delete_yn != 'Y' ");
                
                if(count($inven) > 0){
                    $goodsCode = $inven[0]->goods_code;
                }else{
                    $maxNumber = $wpdb->get_var("select max(goods_code) from  `autopole3144`.`tbl_inven`");
                    $value = $maxNumber + 1;
                    $goodsCode = str_pad($value, 5, "0", STR_PAD_LEFT);
                }
                
                //$goods_idx = $wpdb->get_row("select count(*) from `autopole3144`.`bbse_commerce_goods` where `goods_code`='".trim($V['goods_code'])."' and delete_yn != 'Y' ");
                //$idx = $wpdb->get_row("select count(*) from `autopole3144`.`bbse_commerce_goods` where `idx`='".trim($V['goods_idx'])."' and delete_yn != 'Y' ");
                //if(sizeof($idx) < 1){
                //    $idx_option = $wpdb->get_row("select count(*) from `autopole3144`.`bbse_commerce_goods_option` where concat(goods_idx,'-',goods_option_title)='".trim($V['goods_idx'])."'");
                //}
                
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                
                $qry1 = "INSERT INTO `autopole3144`.`tbl_inven` set ";
                
                //if(isset($V['goods_idx'])) $fields1[] = "`goods_idx`='".$V['goods_idx']."'";
                if(isset($V['goods_idx'])) $fields1[] = "`goods_idx`='".trim($V['goods_idx'])."'";
                //if(isset($V['goods_code'])) $fields1[] = "`goods_code`='".$goodsCode."'";
                if(isset($V['goods_name'])) $fields1[] = "`goods_name`='".$V['goods_name']."'";
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                //if(isset($V['input_count'])) $fields1[] = "`input_count`='".$V['input_count']."'";
                //if(isset($V['out_count'])) $fields1[] = "`out_count`='".$V['out_count']."'";
                if(isset($V['goods_option_title'])) $fields1[] = "`goods_option_title`='".$V['goods_option_title']."'";
                
                if(isset($V['current_count'])) $fields1[] = "`current_count`='".$V['current_count']."'";
                if(isset($V['notice_count'])) $fields1[] = "`notice_count`='".$V['notice_count']."'";
                if(isset($V['total_count'])) $fields1[] = "`total_count`='".$V['total_count']."'";
                
                if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                if(isset($V['delete_yn'])) $fields1[] = "`delete_yn`='".$V['delete_yn']."'";
                if(isset($V['rack_code'])) $fields1[] = "`rack_code`='".$V['rack_code']."'";
                
                $add_fields1 = implode(", ", $fields1);
                $qry1 .= $add_fields1;
                
                $wpdb->query($qry1.", reg_date='".$timeStamp."', goods_code='".$goodsCode."'");
                $goodsIdx = $wpdb->insert_id;
                
                $douzoneCodeCheck = $wpdb->get_var("select count(1) from tbl_douzone_code where goods_idx = '".trim($V['goods_idx'])."' and delete_yn != 'Y'");
                if($douzoneCodeCheck < 1){
                    //$qry2 = "INSERT INTO `autopole3144`.`tbl_douzone_code` set  delete_yn = 'N' , goods_idx ='".$goodsIdx."' , goods_code ='".$V['goods_code']."' , goods_douzone_code = '".$V['douzone_code']."' ";
                    $qry2 = "INSERT INTO `autopole3144`.`tbl_douzone_code` set  delete_yn = 'N' , goods_idx ='".trim($V['goods_idx'])."' , goods_code ='".$goodsCode."' , goods_douzone_code = '".$V['douzone_code']."', reg_date = '".$timeStamp."' ";
                    $result = $wpdb->query($qry2);
                }
                
                echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_inven&cType=list';</script>";
                exit;
                
                
            }else if($V['mode'] == "edit" && $V['strun'] == "edit_proc") {
                $V = $_POST;
                //$cnt = $wpdb->get_row("select * from tbl_inven where storage_code='".$V['storage_code']."' and goods_code = '".$V['goods_code']."' and delete_yn != 'Y'");
                //if($cnt > 0){
                //    echo "<script type='text/javascript'>alert('이미 해당 창고에 등록되어 있는 상품입니다.');location.href='admin.php?page=bbse_commerce_inven&cType=list';</script>";exit;
                //}
                
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                
                $fields1 = array();
                $qry1 = "update `autopole3144`.`tbl_inven` set ";
                
                if(isset($V['goods_name'])) $fields1[] = "`goods_name`='".$V['goods_name']."'";
                if(isset($V['storage_code'])) $fields1[] = "`storage_code`='".$V['storage_code']."'";
                if(isset($V['current_count'])) $fields1[] = "`current_count`='".$V['current_count']."'";
                if(isset($V['notice_count'])) $fields1[] = "`notice_count`='".$V['notice_count']."'";
                if(isset($V['rack_code'])) $fields1[] = "`rack_code`='".$V['rack_code']."'";
                //if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                
                $edit_fields1 = implode(", ", $fields1);
                
                $qry1 .= $edit_fields1." ,`update_date` = '".$timeStamp."' where `goods_code`='".$V['goods_code']."' and delete_yn != 'Y'";
                
                $result = $wpdb->query($qry1);
                
                if($result > 0 && $V['douzone_code'] != ""){
                    
                    $sql = "DELETE FROM tbl_douzone_code WHERE goods_code='".$V['goods_code']."' AND delete_yn != 'Y'";
                    $result = $wpdb->query($sql);
                    
                    $sql = "INSERT INTO tbl_douzone_code (goods_code, goods_douzone_code, delete_yn ) VALUES ('".$V['goods_code']."','".$V['douzone_code']."' , 'N')";
                    $result = $wpdb->query($sql);
                }
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-detail.php");
                
            }
            else if($V['strun'] == 'del_proc'){
                $timeStamp=current_time('timestamp');
                
                $timeStamp=date("Y-m-d H:i:s",$timeStamp);
                
                $fields1 = array();
                
                $V = $_POST;
                $goosCodeArr = explode(",", $V['goodsCodeArr']);
                $storageArr = explode(",", $V['storageArr']);
                for ($i=0; $i<count($V['check']); $i++){
                    $qry= "update `autopole3144`.`tbl_inven` set delete_yn='Y', update_date='".$timeStamp."' where `idx` = '".$V['check'][$i]."' and storage_code = '".$storageArr[$i]."'";
                    $wpdb->query($qry);
                    
                    //$qry2 ="update `autopole3144`.`tbl_douzone_code` set delete_yn = 'Y', update_date='".$timeStamp."' where goods_code = '".$goosCodeArr[$i]."' and delete_yn != 'Y'";
                    //$wpdb->query($qry2);
                    
                    $qry3 = "update `autopole3144`.`tbl_rfid_serial` set goods_delete='Y', update_date='".$timeStamp."' where goods_code = '".$goosCodeArr[$i]."' and storage_code = '".$storageArr[$i]."'";
                    $result = $wpdb->query($qry3);
                    
                    $qry4 = "update `autopole3144`.`tbl_inout` set delete_yn='Y', update_date='".$timeStamp."' where goods_code = '".$goosCodeArr[$i]."' and storage_code = '".$storageArr[$i]."'";
                    $result = $wpdb->query($qry4);
                }
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-list.php");
            }
            else if($V['strun'] == 'batch_proc'){
                
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
                
                wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-inven-dbinsert-csv.php");
                exit;
            }
            
            if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-detail.php");
            }elseif($_GET['mode'] == "download"){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-download.php");
            }else{
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-list.php");
            }
            
		}else{
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-inven-".$cType.".php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
