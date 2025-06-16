<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname, $current_user;

$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사

$currUserID=$current_user->user_login;
$Loginflag='member';
$currSnsIdx="";

if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsLoginData=unserialize($_SESSION['snsLoginData']);

		$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
		if($snsData->idx){
			$Loginflag='social';
			$currUserID=$snsLoginData['sns_id'];
			$currSnsIdx=$snsData->idx;
		}
	}
}
if($Loginflag=='member'){
	$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND q_type='Q' AND user_id='".$currUserID."' ORDER BY idx DESC"); // 총 Q&A 수
}
else if($Loginflag=='social' && $currSnsIdx>'0'){
	$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND q_type='Q' AND user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."' ORDER BY idx DESC"); // 총 Q&A 수
}

$page_param = array();           
$page_param['page_row'] = '10';
$page_param['page_block'] = '10';
$page_param['total_count'] = $qnaTotal;
$page_param['current_page'] = '1';
$page_param['func'] = "qna_go_page";

?>
<style type="text/css" scoped>
.man2manTitle {margin:30px 0 0 0;}
.man2manList {margin:0 0 0 0;width:100%;border-top:2px solid #666666}
  .man2manList dl {font-size:11px;color:#999}
    .man2manList dl dt {float:left;width:100%;line-height:35px;color:#676767;border-bottom:1px solid #eee;cursor:pointer;}
      .man2manList dl dt .subject {float:left;width:58%;height:35px;overflow:hidden}
      .man2manList dl dt .subject:before {content:'Q';margin-right:10px;padding:0 10px;font-weight:bold;font-size:12px;color:#f71426;border-right: 1px solid #BDBDBD}
      .man2manList dl dt .status {float:left;width:20%;text-align:right}
      .man2manList dl dt .date {float:right;padding-right:2%;width:18%;text-align:right;}

    .man2manList dl dd {display:none;;float:left;padding:20px 0 ;width:100%;border-bottom:1px solid #eee;background:#FBFBFB}
      .man2manList dl dd div.btns {float:right;padding-right:2%;width:18%;text-align:right}
      .man2manList dl dd div.enquiry {float:left;padding-left:3%;width:77%;}
      .man2manList dl dd div.answer {float:left;margin:20px 0 0 0;padding:0 0 0 2%;width:78%;}
      .man2manList dl dd div.answer:before {float:left;content:'└ A';margin-right:10px;padding:0 10px 0 0;font-weight:bold;font-size:12px;border-right: 1px solid #BDBDBD}
        .man2manList dl dd div.answer p {float:left;width:90%;}
</style>
<h2 class="page_title">1:1 문의 내역</h2>
<div class="frame_wrap">
	<div class="frame_title man2manTitle">
		<h1>상품문의 <span id="qna-total-count">총 <strong><?php echo $page_param['total_count'];?></strong>개가 있습니다.</span></h1>
	</div>
</div>
<div id="qna-list" class="man2manList">
	<dl>
		<?php echo bbse_get_goods_qna_mypage_list("", $page_param['current_page'], $page_param['page_block'], $page_param['page_block']); ?>
	</dl>

	<div style="clear:both;"></div>

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
			<input type="hidden" name="template_url" id="template_url" value="<?php echo bloginfo('template_url');?>">
			<input type="hidden" name="tMode" id="tMode" value="modify">
			<input type="hidden" name="qIdx" id="qIdx" value="">
			<input type="hidden" name="qGoodsIdx" id="qGoodsIdx" value="">
			<input type="hidden" name="paged" id="paged" value="<?php echo $page_param['current_page'];?>">
			<input type="hidden" name="selected_page" id="selected_page" value="<?php echo $page_param['current_page'];?>">
			<input type="hidden" name="qna_per_page" id="qna_per_page" value="<?php echo $page_param['page_row'];?>">
			<input type="hidden" name="page_block" id="page_block" value="<?php echo $page_param['page_block'];?>">
			<input type="hidden" name="function_name" id="function_name" value="mypage_qna_go_page">

			<table>
				<caption>상품 문의하기</caption>
				<colgroup>
					<col style="width:100px;" />
					<col style="width:auto;" />
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="cmtSubjectF">제목</label></th>
						<td>
							<input type="text" name="q_subject" id="q_subject" />
							<label for="securitF"><input type="checkbox" name="q_secret" id="q_secret" />비밀글</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cmtContentF">내용</label></th>
						<td>
							<textarea name="q_contents" id="q_contents" cols="30" rows="10"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div><!--//.tb_detail -->
		<div class="bb_btn_area">
			<button type="button" class="bb_btn shadow" onClick="mypage_submit_qna();"><strong class="mid c_point">글쓰기</strong></button>
			<button type="button" class="bb_btn shadow" onClick="mypage_qna_hide();"><strong class="mid">취소</strong></button>
		</div>
	</div><!--//상품평 작성하기 -->


</div>