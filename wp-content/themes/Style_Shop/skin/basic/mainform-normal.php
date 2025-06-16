<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
<!DOCTYPE HTML>
<html lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=euc-kr">
<base target="_blank">
<title>쇼핑정보메일</title>
<style>body,td{font-size:9pt;font-family:'돋움',dotum,Verdana,Tahoma,Geneva;} p{margin-top:2px;margin-bottom:2px;}img{border:0px;} caption {font-size:0 !important;height:0 !important;font-size:0;} a,a:link,a:visited,a:active{text-decoration:none;color:#424242;}a:hover{text-decoration:underline;}</style>
</head>
<body  marginwidth="10" marginheight="5" bgcolor="#fcfcfc" style="background-color:#fcfcfc;">
<center>
<div style="width:740px;margin:0 auto;padding-bottom:10px;border-bottom:8px solid #F03612;text-align:left;">
  <!-- 쇼핑몰 로고 위치 -->
  <br><br><a href="http://bits.kr/WP/beta_shop/Bshop/main_side.html" target="_blank"><img src="http://bits.kr/WP/beta_shop/Bshop/images/bb_logo.png" height="15" alt="쇼핑몰 바로가기"  /></a>
</div>
<table style="font-family:'돋움',dotum,applegothic,sans-serif;background-color:#ffffff;border:1px solid #CFCFCF;margin:0 auto 20px;" border="0" cellspacing="0" cellpadding="0" width="740" align="center">
<tr>
  <td align="center">

    <div style="position:relative;width:650px;background:#ffffff;border-bottom:3px solid #696969;">
      <table summary="쇼핑안내메일테이블입니다." style="text-align:center;letter-spacing:-1px;font-size:11px;border-bottom:0px solid #707070" border="0" cellspacing="0" cellpadding="0" width="100%">

      <tr>
        <td style="line-height:0;font-size:0px;" height="25" valign="top"></td>
      </tr>
      <tr>
        <td style="position:relative;letter-spacing:1px;font-family:'나눔고딕',NanumGothic,'굴림',gulim,'돋움',dotum,sans-serif;font-size:28px;line-height:40px;font-weight:bold;color:#F03612;text-align:left;padding-left:20px;" height="140" valign="middle">

          <div align="left" style="float:left;text-display:table-cell;vertical-align:bottom;padding-top:15px;">
            고객님의 회원가입을 <span style="display:block;font-size:33px;color:#111111">진심으로 축하드립니다.</span>
          </div>

          <div align="right" style="float:right;display:table-cell;vertical-align:middle;color:#AAAAAA;font-family:wingdings;font-weight:normal;font-size:140px;width:140px;line-height:140px;z-index:1;">J</div>


        </td>
      </tr>
      </tbody>
      </table>
    </div>

    <table summary="정보테이블입니다." style="width:620px;text-align:left;" border="0" cellspacing="0" cellpadding="0" align="center">

    <tbody>
    <tr>
      <td style="line-height:0;font-size:0px;" height="50" valign="top"> </td>
    </tr>
    <tr>
      <th valign="top" height="50" style="background:#ffffff;">
      <strong style="color:#333333;letter-spacing:-1px;font-size:14px;">
      <em style="color:#3089ca;font-style:normal;">{성명}</em> 고객님,  저희 {사이트이름} 회원이 되신 걸 진심으로 축하드립니다.
      </strong>
      </th>
    </tr>
    <tr>
      <td style="color:rgb(85,85,85);line-height:20px;font-size:12px;" valign="top" align="left">
        <!-- 안내글 -->

        {성명}님의 가입을 환영합니다.<br />
        회원님의 보다 편리하고 안전한 쇼핑을 위해 위해 항상 노력하겠습니다.<br />감사합니다.

        <!-- //안내글 -->
      </td>
    </tr>
    <tr>
      <td style="line-height:0;font-size:0px;" height="20" valign="top"> </td>
    </tr>
    <tr>
      <td valign="top" align="center" style="background:#ffffff;">
        <!-- 내 용 -->



        <!--// 내 용 -->
      </td>
    </tr>
    <tr>
      <td style="line-height:0;font-size:0px;" height="40" valign="top"> </td>
    </tr>
    <tr>
      <td valign="top" align="center" height="60">
        <!-- 버튼 -->
        <a href="http://onsetheme.com/mypage" style="display:inline-block;background:#F03612;margin:0 3px;text-align:center;color:#fff;border:solid 1px #E02D1E;vertical-align:middle;padding:6px 17px;font-weight:bold;line-height:21px;font-family:'돋움',dotum,sans-serif;font-size:12px;">쇼핑몰 바로가기 &gt;</a>
        <!--// 버튼 -->
      </td>
    </tr>
    <tr>
      <td style="color:rgb(85,85,85);line-height:18px;font-size:12px;"  valign="top" align="left">
        <!-- 부가설명 -->
        <b></b>
        <ol style="padding-left:17px;margin-right:0;margin-top:10px;">
        </ol>
        <!--// 부가설명 -->
      </td>
    </tr>
    <tr>
      <td style="line-height:0;font-size:0px;" height="30" valign="top"> </td>
    </tr>
    </tbody>
    </table>


    <table style="text-align:center;letter-spacing:0;font-size:11px;border-top:1px solid #707070" border="0" cellspacing="0" cellpadding="0" width="88%">
    <tr>
      <td style="line-height:0;font-size:0px;" height="20" valign="top"></td>
    </tr>
    <tr>
      <td style="color:rgb(117, 118, 129);line-height:16px;letter-spacing:0;font-size:11px;" height="44" valign="top">
        <div style="padding-bottom:15px;">본 메일은 발신 전용으로 회신되지 않습니다. 궁금하신 점은 <b>고객센터(02-000-0000)</b>를 이용해주세요.</div>
        <div style="padding:10px 0;background:#f7f7f7;margin-bottom:10px;">본 메일은 정보통신망 이용촉진 및 정보보호 등에 관란 법률 시행규칙에 의거<br />회원님의 메일 수신동의 여부를 확인할 결과 수신동의를 하셨기에 발송되었습니다.<br />수신을 원하지 않으시면 회원정보 변경에서 메일수신여부를 변경하실 수 있습니다. </div>
        <strong>사이트이름</strong>
        주소 : 서울시 강동구 상일로10길 36 <span style="padding:0px 8px 0px 9px;color:rgb(124,125,130);vertical-align:bottom;">|</span>전화 : 02-000-0000 <span style="padding:0px 8px 0px 9px;color:rgb(124,125,130);vertical-align:bottom;">|</span>대표이사 : 홍길동 <br /> 사업자등록번호 : 000-00-00000 통신판매신고 제2011-서울강남-01960호
      </td>
    </tr>
    <tr>
      <td style="letter-spacing:0px;" height="55" valign="middle">
        <em style="color:rgb(149,150,158);padding-left:6px;font-family:Verdana,sans-serif;font-size:9px;font-style:normal;">Copyright ⓒ</em>
        <strong style="color:rgb(161,161,161);font-family:Tahoma,sans-serif;font-size:9px;"> Shop-domain.</strong>
        <span style="color:rgb(149,150,158);line-height:14px;padding-left:2px;font-family:Verdana,sans-serif;font-size:9px;">All Rights Reserved.</span>
      </td>
    </tr>
    </tbody>
    </table>

  </td>
</tr>
</table>
</center>
</body>
</html>
