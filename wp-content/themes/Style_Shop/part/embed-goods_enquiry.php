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

	$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$bbseGoods."' AND q_type='Q' ORDER BY idx DESC"); // 총 Q&A 수

	$page_param = array();           
	$page_param['page_row'] = '10';
	$page_param['page_block'] = '10';
	$page_param['total_count'] = $qnaTotal;
	$page_param['current_page'] = '1';
	$page_param['func'] = "qna_go_page";


	if($curUserPermision == "administrator") $qnalinkStr="<a href=\"javascript:alert('관리자는 상품문의를 하실 수 없습니다.    ');\" class=\"bb_btn shadow hidden_cmtlayer\"><span class=\"sml\">상품 문의하기</span></a>";
	elseif($currUserID) $qnalinkStr="<a href=\"javascript:qna_write('insert','');\" class=\"bb_btn shadow\"><span id=\"qna-write\" class=\"sml\">상품 문의하기</span></a>";
	else $qnalinkStr="<a href=\"javascript:alert('로그인 후 이용해 주시기 바랍니다.    ');\" class=\"bb_btn shadow hidden_cmtlayer\"><span class=\"sml\">상품 문의하기</span></a>";
	?>
	<div class="frame_wrap">
		<div class="frame_title">
			<h1>상품문의 <span id="qna-total-count">총 <strong><?php echo $page_param['total_count'];?></strong>개가 있습니다.</span></h1>
			<div class="requierd">
				<p>
					<em>상품과 관련된 문의</em>를 남겨주시면 답변을 드립니다.
					<?php echo $qnalinkStr;?>
				</p>
			</div>
		</div>
		<div id="qna-list" class="bb_open_list">
			<dl>
			<?php echo bbse_get_goods_qna_list("", $bbseGoods, $page_param['current_page'], $page_param['page_block'], $page_param['page_block']);?>
			</dl>
		</div><!--//고객 상품평-->

		<span id="qna-paging">
			<?php 
			$page_class = new themePaging(); 
			$page_class->initPaging($page_param);
			echo $page_class->getPaging();
			?>
		</span>

		<div id="itemQuestionF" class="cmt_write" style="display:none">
			<h2 class="cmt_title">상품 문의하기</h2>
			<div class="tb_detail">
				<form name="qnaFrm" id="qnaFrm">
				<input type="hidden" name="template_url" value="<?php echo bloginfo('template_url');?>" />
				<input type="hidden" name="tMode" id="tMode" value="insert" />
				<input type="hidden" name="qna_login_flag" id="qna_login_flag" value="<?php echo ($Loginflag=='social')?"social":"";?>" />
				<input type="hidden" name="qIdx" id="qIdx" value="" />
				<input type="hidden" name="qGoodsIdx" id="qGoodsIdx" value="<?php echo $bbseGoods;?>" />
				<input type="hidden" name="qGoodsName" id="qGoodsName" value="<?php echo $goods->goods_name;?>" />
				<input type="hidden" name="qUserName" id="qUserName" value="<?php echo $currUserName;?>" />
				<input type="hidden" name="paged" value="<?php echo $page_param['current_page'];?>" />
				<input type="hidden" name="selected_page" value="<?php echo $page_param['current_page'];?>" />
				<input type="hidden" name="qna_per_page" id="qna_per_page" value="<?php echo $page_param['page_row'];?>" />
				<input type="hidden" name="page_block" value="<?php echo $page_param['page_block'];?>" />
				<input type="hidden" name="function_name" value="qna_go_page" />

				<table>
					<caption>상품 문의하기</caption>
					<colgroup>
						<col style="width:100px;" />
						<col style="width:auto;" />
					</colgroup>
					<tbody>
						<tr>
							<th scope="row"><label for="q_subject">제목</label></th>
							<td>
								<input type="text" name="q_subject" id="q_subject" />
								<label for="securitF"><input type="checkbox" name="q_secret" id="q_secret" />비밀글</label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="q_contents">내용</label></th>
							<td>
								<textarea name="q_contents" id="q_contents" cols="30" rows="10"></textarea>
							</td>
						</tr>
						<tr id="qnaAtreeRow" style="display:<?php echo ($Loginflag=='social')?"table-row":"none";?>;">
							<td colspan="2">
								<div style="margin:10px 0;">
									<textarea name="social_private" id="social_private" cols="30" rows="10" readonly style="width:88%;font-size:11px;padding:2%;color:#949494;"><?php echo stripslashes(get_option($theme_shortname."_member_private_2"));?></textarea>
									<br />
									<input type="checkbox" name="qna_agree" id="qna_agree" value="Y" /> 개인정보 수집 및 이용에 동의합니다.
								</div>
							</td>
						</tr>
					</tbody>
				</table>

				</form>
			</div><!--//.tb_detail -->
			<div class="bb_btn_area">
				<button type="button" class="bb_btn shadow" onClick="submit_qna();"><strong class="mid c_point">글쓰기</strong></button>
				<button type="button" class="bb_btn shadow" onClick="qna_hide();"><strong class="mid">취소</strong></button>
			</div>
		</div><!--//상품평 작성하기 -->
	</div>
