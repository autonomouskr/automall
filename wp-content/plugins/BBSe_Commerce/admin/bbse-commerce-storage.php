<?php
if(!$_REQUEST['cType']) $cType='list';
else $cType=$_REQUEST['cType'];

$sidoEn=Array("서울"=>"SE","부산"=>"BS","대구"=>"DG","인천"=>"IC","광주"=>"GJ","대전"=>"DJ","울산"=>"US","세종"=>"SJ","경기"=>"GG","강원특별자치도"=>"GW","충북"=>"CB","충남"=>"CN","전북"=>"JB","전남"=>"JN","경북"=>"GB","경남"=>"GN","제주특별자치도"=>"JJ");
$sigunguEn=Array("종로구"=>"DJ","중구"=>"DJ","용산구"=>"DY","성동구"=>"DS","광진구"=>"DG","동대문구"=>"DD","중랑구"=>"DJ","성북구"=>"DS","강북구"=>"DK","도봉구"=>"DB","노원구"=>"DN","은평구"=>"DE","서대문구"=>"DS","마포구"=>"DM","양천구"=>"DY","강서구"=>"DK","구로구"=>"DG"
    ,"금천구"=>"DG","영등포구"=>"DY","동작구"=>"DD","관악구"=>"DG","서초구"=>"DS","강남구"=>"DK","송파구"=>"DS","강동구"=>"DK","서구"=>"DS","동구"=>"DD","영도구"=>"DY","부산진구"=>"DB","동래구"=>"DD","남구"=>"DN","북구"=>"DB","해운대구"=>"DH","사하구"=>"DS","금정구"=>"DG"
    ,"연제구"=>"DY","수영구"=>"DS","사상구"=>"DS","기장군"=>"GK","수성구"=>"DS","달성군"=>"GD","남동구"=>"DN","부평구"=>"DB","계양구"=>"DG"
    ,"강화군"=>"DJ","옹진군"=>"DJ","광산구"=>"DJ","유성구"=>"DJ","대덕구"=>"DJ","울주군"=>"DJ","조치원읍"=>"DJ","연기면"=>"DJ","연동면"=>"DJ","부강면"=>"DJ","금남면"=>"DJ","달서구"=>"DJ","미추홀구"=>"DJ","연수구"=>"DJ","수원시"=>"SS","의정부시"=>"SU","안양시"=>"SA","성남시"=>"SN"
    ,"부천시"=>"SB","광명시"=>"SG","평택시"=>"SP","동두천시"=>"SD","안산시"=>"SA","고양시"=>"SG","과천시"=>"SC","구리시"=>"SG","남양주시"=>"SN","오산시"=>"SO","시흥시"=>"SH","군포시"=>"SG","의왕시"=>"SW","하남시"=>"SH","용인시"=>"SY","파주시"=>"SP","이천시"=>"SI","안성시"=>"SA"
    ,"김포시"=>"SK","화성시"=>"SH","광주시"=>"SG","양주시"=>"SY","포천시"=>"SP","여주시"=>"SY","춘천시"=>"SC","원주시"=>"SW","강릉시"=>"SK","동해시"=>"SD","태백시"=>"ST","속초시"=>"SS","삼척시"=>"SM","청주시"=>"SC","충주시"=>"SC","제천시"=>"SJ","천안시"=>"SC","공주시"=>"SG"
    ,"보령시"=>"SB","아산시"=>"SA","서산시"=>"SS","논산시"=>"SN","계룡시"=>"SK","전주시"=>"SJ","군산시"=>"SG","익산시"=>"SI","정읍시"=>"SJ","남원시"=>"SN","김제시"=>"SK","목포시"=>"SM","여수시"=>"SY","순천시"=>"SS","나주시"=>"SN","광양시"=>"SG","포항시"=>"SP","경주시"=>"SG"
    ,"김천시"=>"SK","안동시"=>"SA","구미시"=>"SG","영천시"=>"SY","상주시"=>"SS","문경시"=>"SM","경산시"=>"SG","창원시"=>"SC","진주시"=>"SJ","통영시"=>"ST","사천시"=>"SS","김해시"=>"SK","밀양시"=>"SM","거제시"=>"SG","양산시"=>"SY","영주시"=>"SY","당진시"=>"SD","서귀포시"=>"SS"
);

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
		<h2>창고관리</h2>
		<hr>
	</div>
	<div class="clearfix" style="margin-top:30px"></div>

	<?php 

		if($cType == 'list') {
            $V = $_REQUEST;
            
            if($V['mode']=="add" && $V['strun'] == "add_proc"){
                $V = $_POST;
                
                $wCnt = $wpdb->get_var("select count(*) from `autopole3144`.`tbl_storage` where `user_id`='".trim($V['business_number'])."'");
                
                $timeStamp=current_time('timestamp');
                $reg_date = date("Y-m-d H:i:s", $timeStamp);
                
                //if($wCnt > 0 && $eCnt > 0){echo "<script type='text/javascript'>alert('이미 존재하는 창고정보 입니다.');history.back();</script>";exit;}
                
                $fields1 = array();
                
                $qry1 = "INSERT INTO `autopole3144`.`tbl_storage` set ";
                
                if(isset($V['user_id'])) $fields1[] = "`user_id`='".$V['user_id']."'";
                if(isset($V['storage_name'])) $fields1[] = "`storage_name`='".$V['storage_name']."'";
                if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                
                if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
                if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
                if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
                
                //if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
                //if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
                //if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
                //if(isset($V['hp'])) $fields1[] = "`manager_hp`='".$V['hp']."'";
                $add_fields1 = implode(", ", $fields1);
                $qry1 .= $add_fields1;
                
                $wpdb->query($qry1.", reg_date='".$reg_date."'");
                $parts = explode(" ", $V['addr1']);
                $sido = $sidoEn[$parts[0]];
                $sigugin = $sigunguEn[$parts[1]];
                
                $idx = $wpdb->insert_id;
                
                $storage_code=$sido."-".$sigugin."-".$idx;
                
                if($idx > '0'){ 
                    $result=$wpdb->query("UPDATE `autopole3144`.`tbl_storage` SET storage_code='".$storage_code."' , delete_yn = 'N' WHERE idx='".$idx."'");
                }
                
                echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_storage&cType=list';</script>";
                exit;
                
            }else if($V['mode'] == "edit" && $V['strun'] == "edit_proc") {
                $V = $_POST;
                
                $timeStamp=current_time('timestamp');
                
                $reg_date = date("Y-m-d H:i:s", $timeStamp);
                
                $fields1 = array();
                $qry1 = "update `autopole3144`.`tbl_storage` set ";
                
                if(isset($V['storage_name'])) $fields1[] = "`storage_name`='".$V['storage_name']."'";
                //if(isset($V['manager_name'])) $fields1[] = "`manager_name`='".$V['manager_name']."'";
                if(isset($V['user_id'])) $fields1[] = "`user_id`='".$V['user_id']."'";
                if(isset($V['manager_id'])) $fields1[] = "`manager_id`='".$V['manager_id']."'";
                
                if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
                if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
                if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
                
                if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
                if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
                if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
                if(isset($V['hp'])) $fields1[] = "`manager_hp`='".$V['hp']."'";
                
                $edit_fields1 = implode(", ", $fields1);
                
                $qry1 .= $edit_fields1." ,`update_date` = '".$reg_date."' where `storage_code`='".$V['storage_code']."'";
                
                $wpdb->query($qry1);
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-detail.php");
                
            }
            else if($V['strun'] == 'del_proc'){
                
                $timeStamp=current_time('timestamp');
                $timeStamp = date("Y-m-d H:i:s", $timeStamp);
                
                $fields1 = array();
                
                $V = $_POST;
                
                for ($i=0; $i<count($V['check']); $i++){
                    $qry= "update `autopole3144`.`tbl_storage` set delete_yn='Y', update_date='".$timeStamp."' where `idx` = '".$V['check'][$i]."'";
                    $wpdb->query($qry);
                    
                    $storage = $wpdb->get_var("select storage_code from tbl_storage where idx = '".$V['check'][$i]."'");
                    $qry1 = "select * from tbl_inven where storage_code = '".$storage."'";
                    $goods = $wpdb->get_results($qry1);
                    
                    //해당 창고에 등록된 재고관리 제품 목록 삭제
                    $qry2 = "update `autopole3144`.`tbl_inven` set delete_yn='Y', update_date='".$timeStamp."' where storage_code = '".$storage."'";
                    $wpdb->query($qry2);
                    
                    //더존코드 테이블 데이터 삭제
                    for($j=0; $j<count($goods); $j++){
                        $qry3 = "update `autopole3144`.`tbl_douzone_code` set delete_yn='Y', update_date='".$timeStamp."' where goods_code = '".$goods[$j]->goods_code."'";
                        $result = $wpdb->query($qry3);
                        
                        $qry4 = "update `autopole3144`.`tbl_rfid_serial` set goods_delete='Y', update_date='".$timeStamp."' where goods_code = '".$goods[$j]->goods_code."'";
                        $result = $wpdb->query($qry4);
                        
                        $qry5 = "update `autopole3144`.`tbl_inout` set delete_yn='Y', update_date='".$timeStamp."' where goods_code = '".$goods[$j]->goods_code."'";
                        $result = $wpdb->query($qry5);
                    }
                }
                
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-list.php");
            }
            else if($V['strun'] == 'batch_proc'){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-storage-dbinsert-csv.php");
                exit;
            }
            
            if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-detail.php");
            }elseif($_GET['mode'] == "download"){
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-download.php");
            }else{
                require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-list.php");
            }
            
		}else{
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/inven/bbse-commerce-storage-".$cType.".php");
		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>
