<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb;

$V = $_POST;

$configData=Array();

if($V['cType']=='order'){ 
	if(!$V['count_cutback'] || !$V['cart_empty_cycle']){
		echo "fail";
		exit;
	}
	
	$configData['pass_num_use']=$V['pass_num_use'];
	$configData['soldout_notice_use']=$V['soldout_notice_use'];
	$configData['soldout_notice_sms']=($configData['soldout_notice_use']=='on')?$V['soldout_notice_sms']:"";
	$configData['soldout_notice_email']=($configData['soldout_notice_use']=='on')?$V['soldout_notice_email']:"";
	$configData['count_cutback']=$V['count_cutback'];
	$configData['cart_empty_cycle']=$V['cart_empty_cycle'];
	//$configData['order_cancel_day']=$V['order_cancel_day'];
	$configData['delivery_end_day']=$V['delivery_end_day'];
	//$configData['order_end_day']=$V['order_end_day'];
	$configData['total_pay_unit']=$V['total_pay_unit'];
	$configData['total_pay_round']=$V['total_pay_round'];
	$configData['order_phone_use']=($V['order_phone_use'])?$V['order_phone_use']:"U";
	
	//견적서 정보 추가
	$configData['esti_use']		=$V['esti_use'];
	$configData['esti_company']	=$V['esti_company'];
	$configData['esti_ceo']		=$V['esti_ceo'];
	$configData['esti_tel']		=$V['esti_tel'];
	$configData['esti_fax']		=$V['esti_fax'];
	
	$configData['esti_addr']	=$V['esti_addr'];
	
	$configData['esti_manager']	=$V['esti_manager'];
	$configData['esti_manager_tel']		=$V['esti_manager_tel'];
	$configData['esti_manager_email']	=$V['esti_manager_email'];
	$configData['esti_num']		=$V['esti_num'];
	
	$configData['esti_service']=$V['esti_service'];
	
	$configData['esti_period']=$V['esti_period'];
	$configData['esti_condi']=$V['esti_condi'];
	$configData['esti_account']=$V['esti_account'];
	
	$configData['esti_logo']=$V['esti_logo_url'];
	$configData['esti_file']=$V['esti_file_url'];

	//견적서 인감도장 파일 업로드
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	$upload_overrides 	= array( 'test_form' => false );
	if(!empty($_FILES['esti_file']['name'])){
		$uploadedfile 		= $_FILES['esti_file'];
	    $movefile 			= wp_handle_upload( $uploadedfile, $upload_overrides );
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			$configData['esti_file']= $movefile['url'];
		}
		else{
			$configData['esti_file']= $movefile['error'];	
		}
	}
	//견적서 로고파일 업로드
    if(!empty($_FILES['esti_logo']['name'] )){
		$uploadedfile 		= $_FILES['esti_logo'];
	    $movefile 			= wp_handle_upload( $uploadedfile, $upload_overrides );
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			$configData['esti_logo']= $movefile['url'];
		}
		else{
			$configData['esti_logo']= $movefile['error'];	
		}
	}
	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='delivery'){ 
	if(!$V['localCnt'] || !$V['delivery_charge_type']){
		echo "fail";
		exit;
	}

	$configData['localCnt']=$V['localCnt'];
	$configData['deliveryCompanyCnt']=$V['deliveryCompanyCnt'];
	$configData['delivery_charge_type']=$V['delivery_charge_type'];
	$configData['delivery_charge_payment']=$V['delivery_charge_payment'];
	$configData['delivery_charge']=$V['delivery_charge'];
	$configData['condition_free_use']=$V['condition_free_use'];
	$configData['delivery_feee_terms']=$V['delivery_feee_terms'];
	$configData['total_pay']=$V['total_pay'];

	for($i=1;$i<=$V['localCnt'];$i++){
		$configData['local_charge_'.$i.'_use']=$V['local_charge_'.$i.'_use'];
		$configData['local_charge_pay_'.$i]=$V['local_charge_pay_'.$i];
		$configData['local_charge_list_'.$i.'_idx']=$V['local_charge_list_'.$i.'_idx'];
		$configData['local_charge_list_'.$i.'_name']=$V['local_charge_list_'.$i.'_name'];
	}

	for($j=1;$j<=$V['deliveryCompanyCnt'];$j++){
		$configData['delivery_company_'.$j.'_use']=$V['delivery_company_'.$j.'_use'];
		$configData['delivery_company_'.$j.'_select']=$V['delivery_company_'.$j.'_select'];
		$configData['delivery_company_'.$j.'_name']=$V['delivery_company_'.$j.'_name'];
		$configData['delivery_company_'.$j.'_url']=$V['delivery_company_'.$j.'_url'];
	}

	$configEditor=$V['mall_delivery_info'];

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_editor,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$configEditor."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."',config_editor='".$configEditor."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='earn'){
	if(!$V['earn_member_use'] || !$V['earn_birth_use'] || !$V['earn_review_use'] || !$V['earn_pay_use']){
		echo "fail";
		exit;
	}

	$configData['earn_member_use']=$V['earn_member_use'];
	$configData['earn_member_point']=$V['earn_member_point'];
	$configData['earn_birth_use']=$V['earn_birth_use'];
	$configData['earn_birth_point']=$V['earn_birth_point'];
	$configData['earn_review_use']=$V['earn_review_use'];
	$configData['earn_review_point']=$V['earn_review_point'];
	$configData['earn_pay_use']=$V['earn_pay_use'];
	$configData['earn_hold_point']=$V['earn_hold_point'];
	$configData['earn_order_pay']=$V['earn_order_pay'];
	$configData['earn_min_point']=$V['earn_min_point'];
	$configData['earn_max_percent']=$V['earn_max_percent'];
	$configData['earn_use_unit']=$V['earn_use_unit'];
	$configData['earn_reset_use']=$V['earn_reset_use'];

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='payment'){
	if(!$V['payment_card'] && !$V['payment_bank'] && !$V['payment_trans'] && !$V['payment_vbank']){
		echo "fail";
		exit;
	}

	$configData['payment_card']=$V['payment_card'];
	$configData['payment_bank']=$V['payment_bank'];
	$configData['payment_trans']=$V['payment_trans'];
	$configData['payment_vbank']=$V['payment_vbank'];
	$configData['payment_agent']=$V['payment_agent'];             // ����
	$configData['payment_id']=$V['payment_id'];                        // ���� (�������̵�)
	
	$configData['payment_mert_type']=$V['payment_mert_type']; // LGU+�� (�׽ý�/�Ǽ���)
	$configData['payment_mert_key']=$V['payment_mert_key'];    // LGU+�� (����Ű)

	$configData['payment_key_path']=$V['payment_key_path'];    // INICIS�� (Ű���� ���)
	$configData['payment_key_pw']=$V['payment_key_pw'];         // INICIS�� (Ű���� ��й�ȣ)
	$configData['payment_sign_key']=$V['payment_sign_key'];         // INICIS�� (��ǥ�� ��Ŀ� SignKey)

	$configData['payment_allthegate_nonActiveX_use']=$V['payment_allthegate_nonActiveX_use'];    // �ô�����Ʈ(ActiveX:Y, ��ǥ��:N)
	$configData['payment_inicis_nonActiveX_use']=$V['payment_inicis_nonActiveX_use'];    // INICIS(ActiveX:Y, ��ǥ��:N)

	$configData['payment_inicis_escorw_use']=$V['payment_inicis_escorw_use'];         // INICIS�� ����ũ�� ��뿩��)
	$configData['payment_inicis_escorw_id']=$V['payment_inicis_escorw_id'];    // LGU+�� (����ũ�� ����Ű)

	$configData['payment_inicis_escrow_nonActiveX_use']=$V['payment_inicis_escrow_nonActiveX_use'];    // INICIS ����ũ�� (ActiveX:Y, ��ǥ��:N)
	$configData['payment_escrow_sign_key']=$V['payment_escrow_sign_key'];                  // INICIS ����ũ�� (��ǥ�� ��Ŀ� SignKey)
		$configData['payment_inicis_escorw_key_path']=$V['payment_inicis_escorw_key_path'];    // INICIS�� (����ũ�� Ű���� ���)
	$configData['payment_inicis_escorw_key_pw']=$V['payment_inicis_escorw_key_pw'];         // INICIS�� (����ũ�� Ű���� ��й�ȣ)
	$configData['payment_inicis_escorw_trans']=$V['payment_inicis_escorw_trans'];         // INICIS�� (����ũ�� �ǽð�������ü)
	$configData['payment_inicis_escorw_vbank']=$V['payment_inicis_escorw_vbank'];         // INICIS�� (����ũ�� �������)

	$configData['payment_escrow_use']=$V['payment_escrow_use'];

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}

}
elseif($V['cType']=='bank'){
	if(!$V['tMode'] || ($V['tMode']!='remove' && !$V['bank_info_use']) || ($V['tMode']!='add' && !$V['tIdx'])){
		echo "fail";
		exit;
	}

	if($V['tMode']=='remove'){
		$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		if($cnt<='0'){
			echo "notExist";
			exit;
		}

		$wpdb->query("DELETE FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		$rCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		
		if($rCnt<='0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
	else{
		$configData['bank_info_use']=$V['bank_info_use'];
		$configData['bank_name']=$V['bank_name'];
		$configData['bank_account_number']=$V['bank_account_number'];
		$configData['bank_owner_name']=$V['bank_owner_name'];

		$configSerial=serialize($configData);
		$reg_date=current_time('timestamp');

		if($V['tMode']=='add'){
			$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
			$idx = $wpdb->insert_id;
		}
		elseif($V['tMode']=='modify'){
			if($V['tIdx']<='0'){
				echo "notExist";
				exit;
			}

			$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE idx='".$V['tIdx']."'");
			$idx=$V['tIdx'];
		}

		if($idx>'0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
}
elseif($V['cType']=='navershop'){
	if(!$V['naver_shop_use']){
		echo "fail";
		exit;
	}

	$configData['naver_shop_use']=$V['naver_shop_use'];
	$configData['naver_shop_ep_version']=$V['naver_shop_ep_version'];

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='naverpay'){
	//간편결제 저장
	update_option('bbse_npay_client_id',trim($V['npay_client_id']));
	update_option('bbse_npay_client_secret',trim($V['npay_client_secret']));
	
	if($V['naver_pay_type']=='real' && (!$V['naver_pay_id'] || !$V['naver_pay_auth_key'] || !$V['naver_pay_button_key'])){
		echo "fail";
		exit;
	}

	$configData['naver_pay_use']=$V['naver_pay_use'];
	$configData['naver_pay_use2']=$V['naver_pay_use2'];
	$configData['guest_cart_use']=$V['guest_cart_use'];
	$configData['member_naver_pay_use']=$V['member_naver_pay_use'];
	$configData['naver_pay_type']=$V['naver_pay_type'];
	$configData['naver_pay_id']=trim($V['naver_pay_id']);
	$configData['naver_pay_auth_key']=trim($V['naver_pay_auth_key']);
	$configData['naver_pay_button_key']=trim($V['naver_pay_button_key']);
	$configData['naver_pay_common_key']=trim($V['naver_pay_common_key']);

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='applynavershop'){
	$wpdb->query("UPDATE bbse_commerce_goods SET goods_naver_shop='on'");
	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE goods_naver_shop<>'on'");

	if($cnt<='0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='applynaverpay'){
	$wpdb->query("UPDATE bbse_commerce_goods SET goods_naver_pay='on'");
	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE goods_naver_pay<>'on'");

	if($cnt<='0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='popup'){
	if(!$V['tMode'] || ($V['tMode']!='remove' && !$V['popup_use']) || ($V['tMode']!='add' && !$V['tIdx'])){
		echo "fail";
		exit;
	}

	if($V['tMode']=='remove'){
		$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		if($cnt<='0'){
			echo "notExist";
			exit;
		}

		$wpdb->query("DELETE FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		$rCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		
		if($rCnt<='0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
	else{
		$configData['popup_use']=$V['popup_use'];
		$configData['popup_title']=$V['popup_title'];

		$startDate=explode("-",$V['s_period_1']);
		$configData['s_period_1']=mktime('00','00','00',$startDate['1'],$startDate['2'],$startDate['0']);

		$endDate=explode("-",$V['s_period_2']);
		$configData['s_period_2']=mktime('23','59','59',$endDate['1'],$endDate['2'],$endDate['0']);

		$configData['popup_width']=$V['popup_width'];
		$configData['popup_height']=$V['popup_height'];
		$configData['popup_top']=$V['popup_top'];
		$configData['popup_left']=$V['popup_left'];
		$configData['popup_scrollbar']=$V['popup_scrollbar'];
		$configData['popup_window']=$V['popup_window'];

		$configEditor=$V['popup_contents'];

		$configSerial=serialize($configData);
		$reg_date=current_time('timestamp');

		if($V['tMode']=='add'){
			$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_editor,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$configEditor."','".$reg_date."')");
			$idx = $wpdb->insert_id;
		}
		elseif($V['tMode']=='modify'){
			if($V['tIdx']<='0'){
				echo "notExist";
				exit;
			}

			$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."',config_editor='".$configEditor."' WHERE idx='".$V['tIdx']."'");
			$idx=$V['tIdx'];
		}

		if($idx>'0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
}
elseif($V['cType']=='mail'){
	if(!$V['member_mail_use'] || !$V['findpw_mail_use'] || !$V['order_mail_use'] || !$V['input_mail_use'] || !$V['shipment_mail_use']){
		echo "fail";
		exit;
	}

	$configData['member_mail_use']=$V['member_mail_use'];
	$configData['findpw_mail_use']=$V['findpw_mail_use'];
	$configData['order_mail_use']=$V['order_mail_use'];
	$configData['input_mail_use']=$V['input_mail_use'];
	$configData['shipment_mail_use']=$V['shipment_mail_use'];
	$configData['cancel_mail_use']=$V['cancel_mail_use'];
	$configData['refund_mail_use']=$V['refund_mail_use'];

	$configData['blog_olny_mail_join']=$V['blog_olny_mail_join'];
	$configData['blog_olny_mail_idpw']=$V['blog_olny_mail_idpw'];

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['cType']=='admin'){
	if(!$V['tMode'] || ($V['tMode']!='remove' && (!$V['admin_info_use'] || !$V['admin_id'] || !$V['admin_name'])) || ($V['tMode']!='add' && !$V['tIdx'])){
		echo "fail";
		exit;
	}

	if($V['tMode']=='remove'){
		$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		if($cnt<='0'){
			echo "notExist";
			exit;
		}

		$wpdb->query("DELETE FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		$rCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE idx='".$V['tIdx']."' AND config_type='".$V['cType']."'");
		
		if($rCnt<='0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
	else{
		$configData['admin_info_use']=$V['admin_info_use'];
		$configData['admin_id']=$V['admin_id'];
		$configData['admin_name']=$V['admin_name'];

		$configData['admin_menu_member']=$V['admin_menu_member'];
		$configData['admin_menu_goods']=$V['admin_menu_goods'];
		$configData['admin_menu_order']=$V['admin_menu_order'];
		$configData['admin_menu_statistics']=$V['admin_menu_statistics'];
		$configData['admin_menu_config']=$V['admin_menu_config'];
		$configData['admin_menu_qna']=$V['admin_menu_qna'];
		$configData['admin_menu_payment']=$V['admin_menu_payment'];
		$configData['admin_menu_inven']=$V['admin_menu_inven'];
		
		$configData['bbse_commerce_invenInOut']=$V['bbse_commerce_invenInOut'];
		$configData['bbse_commerce_inven']=$V['bbse_commerce_inven'];
		$configData['bbse_commerce_storage']=$V['bbse_commerce_storage'];
		$configData['bbse_commerce_douzone']=$V['bbse_commerce_douzone'];
		$configData['bbse_commerce_serial']=$V['bbse_commerce_serial'];
		$configData['bbse_commerce_location']=$V['bbse_commerce_location'];
		

		$configSerial=serialize($configData);
		$reg_date=current_time('timestamp');

		if($V['tMode']=='add'){
			$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
			$idx = $wpdb->insert_id;
		}
		elseif($V['tMode']=='modify'){
			if($V['tIdx']<='0'){
				echo "notExist";
				exit;
			}

			$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE idx='".$V['tIdx']."'");
			$idx=$V['tIdx'];
		}

		if($idx>'0'){
			echo "success";
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
}
elseif($V['cType']=='ezpay'){
	$configData['paynow']['paynow_use_yn']=$V['paynow_use_yn'];
	$configData['paynow']['paynow_mert_type']=$V['paynow_mert_type'];
	$configData['paynow']['paynow_mert_id']=trim($V['paynow_mert_id']);
	$configData['paynow']['paynow_mert_key']=trim($V['paynow_mert_key']);
	$configData['paynow']['paynow_escrow_yn']=$V['paynow_escrow_yn'];

	$configData['kakaopay']['kakaopay_use_yn']=$V['kakaopay_use_yn'];
	$configData['kakaopay']['kakaopay_mert_id']=trim($V['kakaopay_mert_id']);
	$configData['kakaopay']['kakaopay_cancel_pw']=trim($V['kakaopay_cancel_pw']);
	$configData['kakaopay']['kakaopay_auth_enckey']=trim($V['kakaopay_auth_enckey']);
	$configData['kakaopay']['kakaopay_auth_hashkey']=trim($V['kakaopay_auth_hashkey']);
	$configData['kakaopay']['kakaopay_mert_key']=trim($V['kakaopay_mert_key']);
	$configData['kakaopay']['kakaopay_escrow_yn']=$V['kakaopay_escrow_yn'];

	$configData['payco']['payco_use_yn']=$V['payco_use_yn'];
	$configData['payco']['payco_easy_buy']=trim($V['payco_easy_buy']);
	$configData['payco']['payco_easy_pay']=trim($V['payco_easy_pay']);
	$configData['payco']['payco_mert_id']=trim($V['payco_mert_id']);
	$configData['payco']['payco_mert_code']=trim($V['payco_mert_code']);
	$configData['payco']['payco_escrow_yn']=$V['payco_escrow_yn'];

	$configData['kpay']['kpay_use_yn']=$V['kpay_use_yn'];
	$configData['kpay']['kpay_mert_id']=trim($V['kpay_mert_id']);
	$configData['kpay']['kpay_key_path']=trim($V['kpay_key_path']);
	$configData['kpay']['kpay_key_pw']=trim($V['kpay_key_pw']);
	$configData['kpay']['kpay_escrow_yn']=$V['kpay_escrow_yn'];
	$configData['kpay']['kpay_escrow_mert_id']=trim($V['kpay_escrow_mert_id']);
	$configData['kpay']['kpay_escrow_key_path']=trim($V['kpay_escrow_key_path']);
	$configData['kpay']['kpay_escrow_key_pw']=trim($V['kpay_escrow_key_pw']);

	$configSerial=serialize($configData);
	$reg_date=current_time('timestamp');

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($cnt<='0'){
		$wpdb->query("INSERT INTO bbse_commerce_config (config_type,config_data,config_reg_date) VALUES ('".$V['cType']."','".$configSerial."','".$reg_date."')");
	}
	else{
		$wpdb->query("UPDATE bbse_commerce_config SET config_data='".$configSerial."' WHERE config_type='".$V['cType']."'");
	}

	$idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_config WHERE config_type='".$V['cType']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
else{
	echo "nonData";
	exit;
}
?>