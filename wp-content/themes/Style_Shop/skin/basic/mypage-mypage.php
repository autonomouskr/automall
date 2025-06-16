	<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

	if(plugin_active_check('BBSe_Commerce')) bbse_commerce_chk_order_status(); // 취소완료, 배송완료, 구매확정 처리

	global $current_user, $theme_shortname, $orderStatus;
	wp_get_current_user();

	$currUserID=$current_user->user_login;
	$Loginflag='member';

	if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
		if($_SESSION['snsLoginData']){
			$snsLoginData=unserialize($_SESSION['snsLoginData']);

			$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
			if($snsData->idx){
				$Loginflag='social';
				$currUserID=$snsLoginData['sns_id'];
				$currUserName=$snsLoginData['sns_name'];
				$currUserClass="비회원";
				$currUserEmail=$snsLoginData['sns_email'];
				$currSnsIdx=$snsData->idx;
			}
		}
	}

	$member = $wpdb->get_row("SELECT M.*,C.class_name FROM bbse_commerce_membership AS M, bbse_commerce_membership_class AS C WHERE M.user_id='".$currUserID."' AND M.user_class=C.no");
	if($Loginflag=='member'){
		$currUserName=$member->name;
		$currUserClass=$member->class_name;
		$currUserEmail=$member->email;
		$currSnsIdx="";
	}
	
	if(is_user_logged_in()) {
	    $myInfo=bbse_get_user_information();
	}
	?>
	<div class="myp_info_box">
		<div class="title_wrap">
			<h3 class="bb_tit">나의 쇼핑</h3>
			<p class="bb_mb_info">
				<strong class="bb_name"><?php echo $currUserName;?></strong> <?php echo ($Loginflag=='member')?"회원":"";?>님의<br>
				등급은 <em><?php echo $currUserClass;?></em> 입니다
			</p>
		</div><!--//.title_wrap -->
		<div class="bb_mb_box">
			<ul class="basicInfo">
				<li class="head">전화번호</li>
				<li><?php echo ($member->phone)?$member->phone:"-";?></li>
				<li class="head">휴대전화번호</li>
				<li><?php echo ($member->hp)?$member->hp:"-";?></li>
				<li class="head">이메일</li>
				<li><?php echo $currUserEmail;?></li>
				<!-- <li class="head">적립금</li>
				<li><?php echo number_format($member->mileage);?>원</li> -->
			</ul>
		<?php if($Loginflag=='member'){?>
			<a href="<?php echo get_permalink(get_option($theme_shortname."_join_page"));?>"><button type="button" class="bb_btn shadow"><span class="sml">수정</span></button></a>
		<?php }?>
		</div><!--//.bb_mb_box -->
	</div><!--//.myp_info_box -->
	<div class="clearFloat"></div>

	<div class="article">
		<div class="req">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=order-list" class="bb_more">더보기</a>
		</div>
		<div class="fakeTable marginTop orderListTbl ">
			<ul class="header">
				<li>주문일/주문번호</li>
				<li>상품명</li>
				<li>결제금액</li>
				<li>진행상태</li>
			</ul>
	<?php 
	if($Loginflag=='member'){
		$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND user_id='".$currUserID."' ORDER BY idx DESC LIMIT %d, %d", array(0,3));
	}
	else if($Loginflag=='social' && $currSnsIdx>'0'){
		$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."' ORDER BY idx DESC LIMIT %d, %d", array(0,3));
	}

	$result = $wpdb->get_results($sql);
	$s_total=sizeof($result);
	if($s_total>'0'){
		foreach($result as $i=>$oData) {
			$gCnt  = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."'");

			if(($gCnt-1)>0) $etcCnt=" 외 ".($gCnt-1)."종";
			else $etcCnt="";

			$gData  = $wpdb->get_row("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC LIMIT 1");

			if($gData->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($gData->goods_basic_img);

			if(!$basicImg['0']){
				$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'"); 

				$imageList=explode(",",$goodsAddImg);
				if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
				else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
			}

			if($oData->order_status=='CA' || $oData->order_status=='CE' || $oData->order_status=='RA' || $oData->order_status=='RE') $statusStr="<span class=\"status_cancel\" style=\"\">".$orderStatus[$oData->order_status]."</span>";
			else $statusStr=$orderStatus[$oData->order_status];
	?>
			<ul>
				<li class="orderdInfoCell">
					<span class="orderedDate"><?php echo date("Y-m-d",$oData->order_date);?></span>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=order-detail&ordno=<?php echo $oData->order_no;?>" class="order_num"><?php echo $oData->order_no;?></a>
				</li>
				<li class="goodsInfoCell">
					<div class="goodsBaseInfo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=order-detail&ordno=<?php echo $oData->order_no;?>"><img src="<?php echo $basicImg['0'];?>" alt="<?php echo $gData->goods_name;?>" /></a>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=order-detail&ordno=<?php echo $oData->order_no;?>" class="subj"><?php echo $gData->goods_name;?><?php echo $etcCnt;?></a>
					</div>
				</li>
				<li>
					<div class="mobile-cell-title">결제금액</div>
					<div class="cell-data paidAmount"><?php echo number_format($oData->cost_total);?>원</div>
				</li>
				<li class="orderStatusInfoCell">
					<span class="orderStatus"><?php echo $statusStr;?></span>
				</li>
			</ul>
	<?php
		}
	}
	else{
	?>
			<ul>
				<li class="orderdInfoCell"></li>
				<li class="goodsInfoCell" style="text-align:center;">
					주문정보가 존재하지 않습니다.
				</li>
				<li></li>
				<li></li>
			</ul>
	<?php
	}
	?>
		</div><!-- fakeTable -->
	</div>
	<div class="clearFloat"></div>

	<div class="article">
		<h3 class="lv3_title">관심상품</h3>
		<p class="req">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=interest" class="bb_more">더보기</a>
		</p>
		<div class="bb_list_boxing basic_list">
			<ul class="" style="min-height:209px;">
	<?php
	$result = $wpdb->get_results("SELECT A.idx AS cart_idx, B.* FROM bbse_commerce_cart AS A, bbse_commerce_goods AS B WHERE A.goods_idx=B.idx AND A.cart_kind='W' AND A.user_id='".$currUserID."' ORDER BY A.idx DESC");
	$i_total=sizeof($result);
	if($i_total>'0'){
		foreach($result as $i=>$wData) {
			$soldout = goodsSoldoutCheck($wData); //품절체크
			$salePrice=0;
			$memPrice=unserialize($wData->goods_member_price);
			for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
			    if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
			        $memberPrice=$memPrice['goods_member_price'][$m];
			        $goods_vat=$memPrice['goods_vat'][$m];
			        $salePrice=$memberPrice+$goods_vat;
			    }
			}
			if($wData->goods_basic_img){
			    $basicImg = wp_get_attachment_image_src($wData->goods_basic_img,"goodsimage3");
			}
			else{
				$imageList=explode(",",$wData->goods_add_img);
				if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage3");
				else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
			}
	?>
				<li>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $wData->idx;?>" target="_blank">
						<span class="tag">
						<?php if ($wData->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
						<?php if ($wData->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
						<?php if ($soldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
						</span>
						<img src="<?php echo $basicImg['0'];?>" alt="<?php echo $wData->goods_name;?>" />
						<span class="subj"><?php echo $wData->goods_name;?></span>
						<!-- <strong class="bb_price"><?php echo number_format($wData->goods_price);?>원</strong> -->
						<strong class="bb_price"><?php echo number_format($salePrice);?>원</strong>
					</a>
				</li>
	<?php
		}
	}
	?>
			</ul>
		</div>
	</div><!--//관심상품 -->