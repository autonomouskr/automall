		<?php
	global $goods,$current_user,$myInfo,$theme_shortname;

	$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사

	$currUserID=$current_user->user_login;
	$currUserName=$myInfo->name;

	$Loginflag='member';

	if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
		if($_SESSION['snsLoginData']){
			$snsLoginData=unserialize($_SESSION['snsLoginData']);
			$Loginflag='social';
			$currUserID=$snsLoginData['sns_id'];
			$currUserName=$snsLoginData['sns_name'];
		}
	}

	$bbseGoods=get_query_var( 'bbseGoods' );

	$reviewTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$bbseGoods."' ORDER BY idx DESC"); // 총 Q&A 수

	$page_param = array();           
	$page_param['page_row'] = '10';
	$page_param['page_block'] = '10';
	$page_param['total_count'] = $reviewTotal;
	$page_param['current_page'] = '1';
	$page_param['func'] = "review_go_page";

	//구입내역있는지 확인
	global $wpdb;
	$tf = $wpdb->get_var('
		SELECT	COUNT(bbse_commerce_order_detail.idx)
		FROM 	bbse_commerce_order_detail	JOIN bbse_commerce_order
		ON 		bbse_commerce_order_detail.order_no = bbse_commerce_order.order_no
		WHERE	bbse_commerce_order_detail.goods_idx = "'.$goods->idx.'"
				AND bbse_commerce_order.user_id = "'.$currUserID.'"
				AND order_status != "TR"
	');

	if($curUserPermision == "administrator") $reviewlinkStr="<a href=\"javascript:alert('관리자는 상품평을 작성 하실 수 없습니다.    ');\" class=\"bb_btn shadow hidden_cmtlayer\"><span class=\"sml\">상품평 작성하기</span></a>";
	elseif($currUserID && $tf > 0) $reviewlinkStr="<a href=\"javascript:review_write('insert','');\" class=\"bb_btn shadow\"><span id=\"review-write\" class=\"sml\">상품평 작성하기</span></a>";
	else $reviewlinkStr="<a href=\"javascript:alert('상품을 구입한 회원만 작성가능합니다.');\" class=\"bb_btn shadow hidden_cmtlayer\"><span class=\"sml\">상품평 작성하기</span></a>";
	?>
	
	<div class="frame_wrap">
		<div class="frame_title">
			<h1>고객상품평 <span id="review-total-count">총 <strong><?php echo $page_param['total_count'];?></strong>개가 있습니다.</span></h1>
			<div class="requierd">
				<p>
					상품을 구매하신 회원님께서는 <em>상품평을 작성해주세요.</em>
					<?php echo $reviewlinkStr;?>
				</p>
			</div>
		</div>

		<div id="review-list" class="bb_open_list">
			<dl>
			<?php echo bbse_get_goods_review_list("", $bbseGoods, $page_param['current_page'], $page_param['page_block'], $page_param['page_block']);?>
			</dl>
		</div><!--//고객 상품평-->

		<span id="review-paging">
			<?php 
			$page_class = new themePaging(); 
			$page_class->initPaging($page_param);
			echo $page_class->getPaging();
			?>
		</span>

		<div id="itemReviewF" class="cmt_write" style="display:none">
			<h2 class="cmt_title">상품평 작성하기</h2>
			<div class="tb_detail">
			<form name="reviewFrm" id="reviewFrm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="template_url" value="<?php echo bloginfo('template_url');?>" />
				<input type="hidden" name="rMode" id="rMode" value="insert" />
				<input type="hidden" name="review_login_flag" id="review_login_flag" value="<?php echo ($Loginflag=='social')?"social":"";?>" />
				<input type="hidden" name="rIdx" id="rIdx" value="" />
				<input type="hidden" name="rGoodsIdx" id="rGoodsIdx" value="<?php echo $bbseGoods;?>" />
				<input type="hidden" name="rGoodsName" id="rGoodsName" value="<?php echo $goods->goods_name;?>" />
				<input type="hidden" name="rUserName" id="rUserName" value="<?php echo $currUserName;?>" />
				<input type="hidden" name="paged" value="<?php echo $page_param['current_page'];?>" />
				<input type="hidden" name="selected_page" value="<?php echo $page_param['current_page'];?>" />
				<input type="hidden" name="review_per_page" id="review_per_page" value="<?php echo $page_param['page_row'];?>" />
				<input type="hidden" name="page_block" value="<?php echo $page_param['page_block'];?>" />
				<input type="hidden" name="function_name" value="review_go_page" />

				<table>
					<caption>상품평 작성하기</caption>
					<colgroup>
						<col style="width:100px;" />
						<col style="width:auto;" />
					</colgroup>
					<tbody>
						<tr>
							<th scope="row"><label for="r_subject">제목</label></th>
							<td>
								<input type="text" name="r_subject" id="r_subject" value="" />
							</td>
						</tr>
						<tr>
							<th scope="row">별점</th>
							<td class="bb_star_box">
								<label for="r_value_5"><input type="radio" name="r_value" id="r_value_5" value="5" /><span class="bb_cmt_star cmt5">별점 5점/5점</span></label>
								<label for="r_value_4"><input type="radio" name="r_value" id="r_value_4" value="4" /><span class="bb_cmt_star cmt4">별점 4점/5점</span></label>
								<label for="r_value_3"><input type="radio" name="r_value" id="r_value_3" value="3" /><span class="bb_cmt_star cmt3">별점 3점/5점</span></label>
								<label for="r_value_2"><input type="radio" name="r_value" id="r_value_2" value="2" /><span class="bb_cmt_star cmt2">별점 2점/5점</span></label>
								<label for="r_value_1"><input type="radio" name="r_value" id="r_value_1" value="1" /><span class="bb_cmt_star cmt1">별점 1점/5점</span></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="r_contents">내용</label></th>
							<td>
								<textarea name="r_contents" id="r_contents" cols="30" rows="10"></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cmtUploadF">사진첨부</label></th>
							<td>
								<div class="file_wrap">
									<span class="fake_upload"></span>
									<span class="file_btn">
										<label for="fileUpload" class="bb_btn shadow"><span class="mid">파일찾기</span></label>
										<input type="file" name="fileUpload" id="fileUpload" value="" />
									</span>
								</div>
								<div id='attach_name' style="display:none;margin-top:10px;"></div>
							</td>
						</tr>
						<tr id="reviewAtreeRow" style="display:<?php echo ($Loginflag=='social')?"table-row":"none";?>;">
							<td colspan="2">
								<div style="margin:10px 0;">
									<textarea name="review_private" id="review_private" cols="30" rows="10" readonly style="width:88%;font-size:11px;padding:2%;color:#949494;"><?php echo stripslashes(get_option($theme_shortname."_member_private_2"));?></textarea>
									<br />
									<input type="checkbox" name="review_agree" id="review_agree" value="Y" /> 개인정보 수집 및 이용에 동의합니다.
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			</div><!--//.tb_detail -->
			<div class="bb_btn_area">
				<button type="button" Onclick="submit_review();" class="bb_btn shadow"><strong class="mid c_point">글쓰기</strong></button>
				<button type="button" onClick="review_hide();" class="bb_btn shadow hideEditor"><strong class="mid">취소</strong></button>
			</div>
		</div><!--//상품평 작성하기 -->
	</div>
	<?php if(get_query_var('review_idx') && get_query_var('review_page')) {?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			review_go_page(<?php echo get_query_var('review_page'); ?>);
			view_review(<?php echo get_query_var('review_idx'); ?>);
		});
	</script>
	<?php }?>