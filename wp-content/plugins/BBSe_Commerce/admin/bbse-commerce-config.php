<?php
if(!$_REQUEST['cType']) $cType='order';
else $cType=$_REQUEST['cType'];

$confingTitle=array("order"=>"주문설정","delivery"=>"배송비설정","earn"=>"적립금설정","payment"=>"결제모듈설정","ezpay"=>"카카오페이설정","bank"=>"입금계좌설정","navershop"=>"네이버지식쇼핑","popup"=>"팝업설정","mail"=>"자동메일설정","admin"=>"운영자설정");
$confingInfo=array(
	"order"=>" * 재고삭감 기준 및 장바구니 재고설정, 장바구니 보관기간 설정 등을 관리합니다.<br /><span class='emRed'> * 품절상품 입고알림 : 품절 된 상품이 판매가능 상대가 되었을때, 알림을 받기를 원하는 회원에게 SMS 및 E-mail로 상품입고를 알려주는 서비스입니다.</span><br /> * 품절상품 입고알림 신청목록(상품, 회원정보)은 'BBS e-Commerce - 품절상품입고알림목록' 메뉴를 통해 확인이 가능합니다. <br />&nbsp;&nbsp;(품절상품 입고알림(SMS 및 E-mail) 발송은 품절상품이 판매가능 상태로 변경 되는 시점에 자동 발송 됩니다.)",
	"delivery"=>"* 배송료 정책 및 지역별 배송비 설정, 배송료 기준, 택배사 설정 등을 관리합니다.",
	"earn"=>"* 적립금 사용여부, 적립비율, 적립금 사용 기준 등을 관리합니다.",
	"payment"=>"* 결제 수단 및 결제 대행사 (PG : Payment Gateway) 별 결제 정보 등을 관리합니다.",
	"ezpay"=>"* 카카오페이 결제 정보 등을 관리합니다.",
	"bank"=>"* 무통장입금에 이용 할 계좌 정보 (은행명, 계좌정보, 예금주)를 관리합니다.",
	"navershop"=>" * 네이버 지식쇼핑과의 연동을 설정합니다.<br />- 지식쇼핑 적용을 위해서는 '사용함'으로 설정하신 후, 상품등록 시 '지식쇼핑 적용'에 체크해 주셔야 정상적으로 네이버 지식쇼핑에 적용이 됩니다.<br/>- 등록 된 전체 상품을 지식쇼핑에 일괄 적용을 원하시는 경우, 지식쇼핑 적용을 '사용함'으로 선택하신 후 '전체 상품 사용함 적용' 버튼을 클릭 하시면,<br/>&nbsp;&nbsp;등록 된 모든 상품은 지식쇼핑에서 사용할 수 있는 상태로 변경됩니다. ('전체 상품 사용함 적용' 버튼은 등록 된 상품이 존재하는 경우 만 화면에 나타납니다.)<br/>* 특정 상품이 지식쇼핑에 적용 되는 것을 원하지 않는 경우, 해당 상품의 '편집(수정)' 화면에서 '지식쇼핑 적용'의 체크를 해제 해 주시면 됩니다.<br/><span class='emRed'>* 전체 EP URL (상품DB URL) : ".home_url()."/?bbseDBurl=total<br />* 요약 EP URL (상품DB URL) : ".home_url()."/?bbseDBurl=summary</span>", 
	"naverpay"=>" * 쇼핑몰의 결제방식으로 네이버페이를 사용할 수 있습니다.",
	"popup"=>" * 쇼핑몰에 사용 할 팝업을 관리 합니다. (기간별 설정 가능)",
	"mail"=>"* 회원가입, 비밀번호찾기, 주문상태 메일발송을 설정 합니다.",
	"admin"=>"* 운영자 별 접근 가능한 관리자 메뉴를 설정합니다.<br /><font color='#ED1C24'>* 상품관리 (카테고리관리/상품관리/상품등록/메인상품진열), 주문관리, 통계정산관리, 상점관리, 문의관리 (상품문의/고객상품평), 입금관리, 재고관리</font>"
);
?>

<script language="javascript">
	function go_config(tStatus){
		var goUrl="admin.php?page=bbse_commerce_config&cType="+tStatus;
		window.location.href =goUrl;
	}

	function check_number(){
		var key = event.keyCode;
		if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
			event.returnValue = false;
		}
	}

	function go_config_option(cType,tMode,tIdx){
		var goStr="admin.php?page=bbse_commerce_config&cType="+cType;

		if(tMode) goStr +="&tMode="+tMode;
		if(tIdx) goStr +="&tIdx="+tIdx;

		window.location.href =goStr;
	}

	function remove_popup(){
		tb_remove();
	}
</script>

<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>상점관리</h2>
		<hr>
	</div>
	<div class="clearfix"></div>

	<div class="titleH5" style="margin:20px 0 10px 0; "><?php echo $confingTitle[$cType]?></div>
	<div class="borderBox">
	 <?php echo $confingInfo[$cType]?>
	</div>

	<div class="tabWrap">
	  <ul class="tabList">
		<li <?php echo ($cType=='order')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=order">주문설정</a></li>
		<li <?php echo ($cType=='delivery')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=delivery">배송비설정</a></li>
		<li <?php echo ($cType=='earn')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=earn">적립금설정</a></li>
		<li <?php echo ($cType=='payment')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=payment">결제모듈설정</a></li>
		<li <?php echo ($cType=='ezpay')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=ezpay">카카오페이설정</a></li>
		<li <?php echo ($cType=='bank')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=bank">입금계좌설정</a></li>
		<li <?php echo ($cType=='navershop')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=navershop">네이버지식쇼핑설정</a></li>
		<li <?php echo ($cType=='naverpay')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=naverpay">네이버페이설정</a></li>
		<li <?php echo ($cType=='popup')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=popup">팝업설정</a></li>
		<li <?php echo ($cType=='mail')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=mail">자동메일설정</a></li>
		<li <?php echo ($cType=='admin')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_config&cType=admin">운영자설정</a></li>
	  </ul>
	  <div class="clearfix"></div>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/config/bbse-commerce-config-".$cType.".php");?>
	<div class="clearfix" style="height:20px;"></div>
</div>
