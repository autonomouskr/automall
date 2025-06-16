<html>
	<head>
		<meta charset="UTF-8">
	<style type="text/css">
<!--
#e_body .top_content div .text_box .addr_s {
	font-size: 11px;
}
-->
    </style>
	</head>
<body>
		<?php
			//require_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
			//require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
			//require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
			
		    //local dev
    		require_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	       	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
    		require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
			global $wpdb,$current_user;
			
			$esti_config = unserialize($wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'")->config_data);
			
			wp_get_current_user();
			$currUserID=$current_user->user_login;
			$Loginflag='memer';
			$result = $wpdb->get_results("
				SELECT 	C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic,
						C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* 
				FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G 
				WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.user_id='".$currUserID."' ORDER BY C.idx DESC");
			$myInfo=$bbseMember = $wpdb->get_row("SELECT A.*,B.class_name,B.use_sale FROM bbse_commerce_membership AS A, bbse_commerce_membership_class AS B WHERE A.user_id='".$currUserID."' AND A.user_class=B.no");
			//print_r($result);
			$total_array = array();
			$goods_c = '';
			$g_idx = '';
			if($currUserID == ''){
				$goods_c = '';
			}else{
				foreach($result as $cart) {
					$add_array = explode('|',$cart->goods_cat_list);
					$total_array = array_merge($total_array,$add_array);
					if($g_idx == ''){
						$g_idx = $cart->cart_idx;
					}else{
						$g_idx .= ','.$cart->cart_idx;
					}
				}
			}
			if(count($total_array) > 0){
				$total_array = array_unique($total_array);
				$total_array = array_values(array_filter(array_map('trim',$total_array)));
				$where = '';
				foreach($total_array AS &$idx){
					if($where == ''){
						$where = "idx = '".$idx."'";
					}else{
						$where .= " OR idx = '".$idx."'";
					}
				}
				if($where != ''){
					$result2 = $wpdb->get_results("SELECT c_name FROM bbse_commerce_category WHERE (".$where.") AND depth_2 > 0 AND depth_3 = '0'");
					foreach($result2 AS $c_name){
						if($goods_c == ''){
							$goods_c = $c_name->c_name;
						}else{
							$goods_c .= ','.$c_name->c_name;
						}
					}
				}
			}
			
			
			$today = date('Y').'년 '.date('n').'월 '.date('d').'일';
			
			$mAgent = array("iPhone","iPod","Android","Blackberry", 
		    "Opera Mini", "Windows ce", "Nokia", "sony" );
			$chkMobile = false;
			for($i=0; $i<sizeof($mAgent); $i++){
			    if(stripos( $_SERVER['HTTP_USER_AGENT'], $mAgent[$i] )){
			        $chkMobile = true;
			        break;
			    }
			}
		?>
			<script  src="http://code.jquery.com/jquery-latest.min.js"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.debug.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/html2canvas.js"></script>
			<script>
				$(document).ready(function($) {
					var agt = navigator.userAgent.toLowerCase();
					if((navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agt.indexOf("msie") != -1) ){ 
						// 익스플로러 일 경우 
						$('.top_t').css('width','687px');
						$('.content_t').css('width','687px');
						$('.total_t').css('width','687px');
				}
			   $('.save_pdf').click(function(){
		 			html2canvas(document.getElementById('e_body'),{
		   			onrendered: function(canvas){
		   				var imgData = canvas.toDataURL('image/png');
		   				console.log(imgData);
		   				var doc = new jsPDF('p','mm',"a4");
		   				doc.addImage(imgData,'PNG',34,10,140,95);
		   				//doc.addImage(imgData,'PNG',10,10,190,95);
		   				doc.save('estimate.pdf');
		   				//save_ajax();
		   			}
		   		});
			   		
			   }); 
			   $('.print_c').click(function(){
			   		$('#edit_form').hide();
			   		$('.b_body').css('display','none');
			   		$('body').css('overflow','unset');
						window.print();
						$('.b_body').css('display','block');
						$('body').css('overflow','scroll');
						//save_ajax();
			   });
				});
				function save_ajax(){
					$.ajax({
						url:'<?php echo BBSE_THEME_WEB_URL; ?>/estimate_proc.php',
						type: 'post',
						data:$('#edit_form form').serialize(),
						success : function(result){
							if(result == 'success'){
								
							}else{
								//alert(result)
							}
						}
					})
				}
			</script>
		<style>
			html{ overflow: hidden; }
			body{ overflow: scroll; height: 800px; }
			.e_body{ border: 1px solid #000; padding: 5px; width: 688px; }
			.top_content,.bottom_content{ border: 1px solid #000; display: inline-block; width: 685px; }
			.top_content > div{ width: 49%; float: left; }
			.top_content > div:first-child{ border-right: 1px solid #000; }
			.top_content > div:last-child{ font-size: 40px; text-align: center; font-weight: bold; padding: 12px 0; }
			.top_content > div > .img_box { width: 55px; margin: 24px 5px 0; display: inline-block; vertical-align: top; }
			.top_content > div > .img_box > img{ width: 100%; }
			.top_content > div > .text_box { width: 210px; display: inline-block; line-height: 27px; } 
			.top_content > div > .text_box > span{ display: block; text-align: center; }
			.top_content > div > .text_box > .title_s{ color: red; font-size: 20px; font-weight: 900; }
			.top_content > div > .text_box > .addr_s{ font-size: 10px; }
			.top_content > div > .text_box > .ph_s{ font-size: 12px; }
			
			.top_t , .content_t, .total_t{ border-top: 1px solid #000; border-left: 1px solid #000; width: 686px; border-spacing: 0px; margin: 2px 0 0 0; padding: 0; display: block; }
			
			.top_t > tbody > tr > td, .top_t > tbody > tr > th { padding: 2px 0; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: center; font-size: 12px; }
			.top_t > tbody > tr > th{ font-size: 11px; }
			
			.se_body{ border: 1px solid #000; text-align: center; margin-top: 2px; width: 685px; }
			.se_body > p{ margin: 5px 0; font-size: 13px; }
			.e_line{ border: 2px solid #000; margin-top: 2px; width: 683px; }
			.e_line2{ border: 1px solid #000; margin-top: 2px; width: 685px; height: 2px; }
			
			.content_t > thead > tr > th, .content_t > tbody > tr > td{ padding: 2px 0; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: center; }
			.content_t > thead > tr > th{ font-size: 14px; }
			.content_t > tbody > tr > td{ font-size: 13px; }
			.total_t > tbody > tr > td,.total_t > tbody > tr > th{ padding: 2px 0; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: center; }
			.total_t > tbody > tr > th{ font-size: 18px; }
			.total_t > tbody > tr > td > span,.total_t > tbody > tr > th > span{ float:left; margin-left: 5px; }
			
			.bottom_content{ margin-top: 2px; }
			.bottom_content > div{ width: 46%; float: left; border-left: 1px solid #000; padding: 2px 0;}
			.bottom_content > div:first-child{ border-left: 0px; width: 53%; }
			.bottom_content > .left_c{ font-size: 11px; }
			.bottom_content > .right_c{ font-size: 11px; position: relative;}
			.bottom_content > div > p{ margin: 10px 0 0 0 }
			.bottom_content > .right_c > p{ text-align: center; }
			.bottom_content > .right_c > p:first-child{ margin: 0; font-size: 15px; font-weight: 900; }
			.bottom_content > .right_c > img{ bottom: 4px; right: 2px; position: absolute; width: 56px; }
			.b_body{ text-align: center; width: 686px; margin: 10px 0; }
			.b_body > button{ cursor: pointer; }
		</style>
		<div class="e_body" id="e_body">
			<div class="top_content">
				<div>
					<div class="img_box">
						<?php
						if(!empty($esti_config['esti_logo'])){
							echo '<img src="'.$esti_config['esti_logo'].'" />';
						}
						?>
					</div>
					<div class="text_box">
						<span class="title_s"><?php echo (!empty($esti_config['esti_company']) ? $esti_config['esti_company']:''); ?></span>
						<span class="addr_s"><?php echo (!empty($esti_config['esti_addr']) ? $esti_config['esti_addr']:''); ?></span>
						<span class="ph_s"><?php echo (!empty($esti_config['esti_tel']) ? 'TEL.'.$esti_config['esti_tel']:''); ?>     <?php echo (!empty($esti_config['esti_fax']) ? 'FAX.'.$esti_config['esti_fax']:''); ?></span>
					</div>
				</div>
				<div>견  적  서</div>
			</div>
			<table class="top_t">
				<colgroup>
					<col width="56px"/>
					<col width="281px"/>
					<col width="56px"/>
					<col width="293px"/>
				</colgroup>
				<?php
					$name = (empty($_POST['name']) ? $myInfo->name:$_POST['name']);
					$cham = (empty($_POST['cham']) ? '':$_POST['cham']);
					$hp = (empty($_POST['hp']) ? $myInfo->hp:$_POST['hp']);
					$email = (empty($_POST['email']) ? $myInfo->email:$_POST['email']);
					$phone = (empty($_POST['phone']) ? $myInfo->phone:$_POST['phone']);
				?>
				<tbody>
					<tr>
						<th height="30">수&nbsp;&nbsp;신</th>
					  <td height="30"><?=$name?></td>
					  <th height="30">내&nbsp;&nbsp;&nbsp;&nbsp;용</th>
						<td height="30"><?=$goods_c?></td>
					</tr>
					<tr>
						<th height="30">참&nbsp;&nbsp;조</th>
					  <td height="30"><?=$cham?></td>
					  <th height="30">견&nbsp;적&nbsp;일</th>
						<td height="30"><?=$today?></td>
					</tr>
					<tr>
						<th height="30">전&nbsp;&nbsp;화</th>
					  <td height="30"><?=$hp?></td>
					  <th height="30">담&nbsp;당&nbsp;자</th>
						<td height="30"><?php echo (!empty($esti_config['esti_manager']) ? $esti_config['esti_manager']:''); ?></td>
					</tr>
					<tr>
						<th height="30">메일/팩스</th>
					  <td height="30"><?=$email?>/<?=$phone?></td>
					  <th height="30">c.p/e-mail</th>
						<td height="30"><?php echo (!empty($esti_config['esti_manager_tel']) ? $esti_config['esti_manager_tel']:''); ?>/<?php echo (!empty($esti_config['esti_manager_email']) ? $esti_config['esti_manager_email']:''); ?> </td>
					</tr>
				</tbody>
			</table>
			<div class="se_body">
		    <p>▶ 의뢰하신 제품에 대하여 아래와 같이 견적을 드립니다.</p>
				<p>ㅡ   아     래    ㅡ</p>
			</div>
			<div class="e_line"></div>
			<table class="content_t">
				<colgroup>
					<col width="29px">
					<col width="260px">
					<col width="48px">
					<col width="56px">
					<col width="100px">
					<col width="152px">
					<col width="41px">
				</colgroup>
				<thead>
					<tr>
						<th height="30">No.</th>
					  <th height="30">제&nbsp;품&nbsp;명&nbsp;(&nbsp;메&nbsp;뉴&nbsp;명)</th>
					  <th height="30">규격</th>
					  <th height="30">수량</th>
					  <th height="30">단가</th>
					  <th height="30">공급가</th>
						<th height="30">비고</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total_price = 0;
					$total_amount = 0;
					$i=1;
					//print_r($result);
					//a:2:{s:18:"goods_option_title";a:1:{i:0;s:12:"단일상품";}s:18:"goods_option_count";a:1:{i:0;s:1:"1";}}
					//a:2:{s:18:"goods_option_title";a:1:{i:0;s:12:"단일상품";}s:18:"goods_option_count";a:1:{i:0;s:1:"1";}}
					//a:2:{s:15:"goods_add_title";a:1:{i:0;s:20:"용기 미제공 시";}s:15:"goods_add_count";a:1:{i:0;s:1:"1";}}
					//a:2:{s:15:"goods_add_title";N;s:15:"goods_add_count";N;}
					foreach($result as $cart) {
						//Array ( [goods_add_option_count] => 1 [goods_add_1_use] => on [goods_add_1_choice] => selection [goods_add_1_title] => 용기(그릇)제공여부 [goods_add_1_item_count] => 2 [goods_add_1_item] => Array ( [0] => 용기 제공 시 [1] => 용기 미제공 시 ) [goods_add_1_item_overprice] => Array ( [0] => 0 [1] => 11000 ) [goods_add_1_item_unique_code] => Array ( [0] => [1] => ) [goods_add_1_item_display] => Array ( [0] => view [1] => view ) [goods_add_1_item_soldout] => Array ( [0] => [1] => ) )
						$t_option = unserialize($cart->cart_option_add);
						$amount_1 = explode("goods_option_count",$cart->cart_option_basic);
						$amount_2 = explode(":",$amount_1[1]);
						$last_num = $amount_2[count($amount_2)-1];
						$last_num = preg_replace("/[^0-9]*/s", "",$last_num); 
						
						$goods_total = $last_num * $cart->goods_price;
						$total_price += $goods_total;
						$total_amount += $last_num;
					?>
					<tr>
						<td height="30"><?=$i?></td>
					  <td height="30" style="text-align: left;"><?=$cart->goods_name?></td>
					  <td height="30"></td>
					  <td height="30"><?=number_format($last_num,0)?></td>
					  <td height="30" style="text-align: right; padding-right: 5px;"><?=number_format($cart->goods_price,0)?></td>
					  <td height="30" style="text-align: right; padding-right: 5px;"><?=number_format($goods_total)?></td>
						<td height="30"></td>
					</tr>
					<?php $i++;
						if(!empty($t_option['goods_add_title'][0])){
							$cnt = $t_option['goods_add_count'][0];
							$option_prices = unserialize($cart->goods_option_add);
							$pk = 0;
							foreach ($option_prices['goods_add_1_item'] as $key => $value) {
								if($t_option['goods_add_title'][0] == $value){
									$pk = $key;
									break;
								}
							}
							$price = $option_prices['goods_add_1_item_overprice'][$pk];
							$goods_total = $cnt * $price;
							$total_price += $goods_total;
					?>
						<tr>
							<td height="30">+</td>
						  <td height="30" style="text-align: left;"><?=$t_option['goods_add_title'][0] ?></td>
						  <td height="30"></td>
						  <td height="30"><?=number_format($cnt,0)?></td>
						  <td height="30" style="text-align: right; padding-right: 5px;"><?=number_format($price,0)?></td>
						  <td height="30" style="text-align: right; padding-right: 5px;"><?=number_format($goods_total)?></td>
							<td height="30"></td>
						</tr>
					<?php
						}
					}?>
				</tbody>
			</table>
			<table class="total_t">
			<colgroup>
					<col width="337px">
					<col width="56px">
					<col width="252px">
					<col width="41px">
				</colgroup>
				<tbody>
					<tr>
						<td height="30">소&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;계</td>
					  <td height="30"><?=number_format($total_amount,0)?></td>
					  <td height="30" style="text-align: right; padding: 0 5px 0 0;"><span>￦</span><?=number_format($total_price,0)?></td>
						<td height="30"></td>
					</tr>
					<tr>
						<td height="30">부&nbsp;가&nbsp;세&nbsp;[VAT]</td>
					  <td height="30"></td>
					  <td height="30">포함</td>
						<td height="30"></td>
					</tr>
					<tr>
						<th height="30">합&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;계</th>
					  <th height="30"></th>
					  <th height="30" style="text-align: right; padding: 0 5px 0 0;"><span>￦</span><?=number_format($total_price,0)?></th>
						<th height="30"></th>
					</tr>
				</tbody>
			</table>
		  <div class="e_line"></div>
			<div class="bottom_content">
				<div class="left_c">
					<특&nbsp;기&nbsp;사&nbsp;항>
<br>
					<br>
					<br>
					<p>견적유효기간 : <?php echo (!empty($esti_config['esti_period']) ? $esti_config['esti_period']:''); ?></p>
					<p>계약조건 : <?php echo (!empty($esti_config['esti_condi']) ? $esti_config['esti_condi']:''); ?></p>
					<p>입금계좌 : <?php echo (!empty($esti_config['esti_account']) ? $esti_config['esti_account']:''); ?></p>
				</div>
				<div class="right_c">
					<p class="c_name"><?php echo (!empty($esti_config['esti_company']) ? $esti_config['esti_company']:''); ?></p>
					<p><?php echo (!empty($esti_config['esti_num']) ? $esti_config['esti_num']:''); ?></p>
					<p><?php echo (!empty($esti_config['esti_addr']) ? $esti_config['esti_addr']:''); ?></p>
					<p><?php echo (!empty($esti_config['esti_service']) ? $esti_config['esti_service']:''); ?></p>
					<p>대&nbsp;표&nbsp;이&nbsp;사&nbsp;&nbsp;&nbsp;&nbsp;<?php echo (!empty($esti_config['esti_ceo']) ? $esti_config['esti_ceo']:''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(인)</p>
					<img src="<?php echo (!empty($esti_config['esti_file']) ? $esti_config['esti_file']:''); ?>" />
				</div>
			</div>
		</div>
		<div class="b_body">
			<?php if(!$chkMobile){?>
			<button type="button" class="bb_btn cus_solid w150 print_c" style="border: 1px solid #383838; padding: 10px 5px; background-color: #383838; color: #fff; font-weight: 600;">견적서 인쇄</button>
			<?php }?>
			<button type="button" class="bb_btn cus_solid w150 save_pdf" style="border: 1px solid #383838; padding: 10px 5px; background-color: #383838; color: #fff; font-weight: 600;">견적서 PDF 저장</button>
		</div>
		<div id="edit_form">
			<h4>수신정보 수정</h4>
			<form method="post">
				<input type="text" placeholder="수신" name="name" value="<?php echo $name; ?>" />
				<input type="text" placeholder="참조" name="cham" value="<?php echo $cham; ?>" />
				<input type="text" placeholder="전화" name="hp" value="<?php echo $hp; ?>" />
				<input type="text" placeholder="메일" name="email" value="<?php echo $email; ?>" />
				<input type="text" placeholder="팩스" name="phone" value="<?php echo $phone; ?>" />
				
				<input type="submit" class="button" value="수정하기" />
			</form>
		</div>
		<style>
			#edit_form{
				width: 98%;
				margin: 0 auto;
			}
			#edit_form h4{
				background: #000;
			    color: #fff;
			    padding: 5px 0;
			    letter-spacing: -1px;
			    font-size: 14px;
			    margin: 0 0 5px;
			    text-align: center;
			    box-sizing: border-box;
			}
			#edit_form input[type="text"]{
				height: 30px;
			    line-height: 30px;
			    border: 1px solid #dedede;
			    border-radius: 4px;
			    padding: 0 10px;
			    margin: 0 3px 5px;
			    display: inline-block;
			}
			#edit_form .button{
				border: 1px solid #383838;
			    padding: 3px 8px;
			    background-color: #383838;
			    color: #fff;
			    font-weight: 600;
			    letter-spacing: -1px;
			    display: inline-block;
			    vertical-align: middle;
			    border-radius: 4px;
			}
		</style>
		<?php ?>
	</body>
</html>