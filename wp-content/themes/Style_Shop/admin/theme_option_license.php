<?php
	global $varLcKey;
	if(get_option($varLcKey)){
		if(bbse_theme_license_check()==true){
			$licenseMsg="* 정상적인 라이센스키 입니다.";
			$btnStr="라이센스키 재발급";
		}
		else{
			$licenseMsg="* 올바르지 않은 라이센스 키입니다.";
			$btnStr="라이센스키 재발급";
		}
	}
	else{
		$licenseMsg="* 라이센스 키가 존재하지 않습니다.";
		$btnStr="라이센스키 발급";
	}
?>
<style type="text/css">
	/*강조*/
	.emRed {color:#ED1C24;}
	.emBlue {color:#00A2E8;}
	.emBlack {color:#444444;}

	.emLine {text-decoration:underline;}
	.emOblique {font-style:oblique;}
	.emBold {font-weight:bold}

	/*텍스트 정렬*/
	.TextAlignLeft {text-align:left}
	.TextAlignCenter {text-align:center}
	.TextAlignJustfy {text-align:justify}
	.TextAlignRight {text-align:right}

	/*보더박스*/
	.borderBox {margin:0 0 30px;padding:20px;border:1px solid #4c99ba;border-radius: 2px;background:#f7f7f7;}

	/*제목*/
	.titleH1 {margin:0;padding:0;font-size:2em;line-height:2.2em;font-weight:bold}
	.titleH2 {margin:0;padding:0;font-size:1.8em;line-height:2em;font-weight:bold}
	.titleH3 {margin:0;padding:0;font-size:1.6em;line-height:1.7em;font-weight:bold}
	.titleH4 {margin:0;padding:0;font-size:1.4em;line-height:1.6em;font-weight:bold}
	.titleH5 {margin:0;padding:0;font-size:1.2em;line-height:1.4em;font-weight:bold}
	.titleH6 {margin:0;padding:0;font-size:1em;line-height:1.2em;font-weight:bold}

	/*탭*/
	.tabWrap {margin:30px 0 0  0;width:100%;border-bottom:1px solid #4c99ba}
	  .tabWrap .tabList {position:relative;z-index:1;float:left;margin:0;padding:0;width:100%;}
	  .tabWrap:after {clear:both;content:" ";}
		.tabWrap .tabList li {margin:0 0 -1px -5px;padding:0;float:left;width:130px;list-style:none;text-align:center;}
		.tabWrap .tabList li:first-child {margin:0 0 -1px 5px;}

		.tabWrap .tabList li a,
		.tabWrap .tabList li span {display:block;position:relative;z-index:1;margin:5px 0 0 0;padding:5px 10px;height:25px;line-height:25px;
								  color:#999999;font-size:12px;text-decoration:none;text-shadow:1px 1px 0 #fff;
								  border:1px solid #cccccc;border-top:3px solid #cccccc;border-bottom:1px solid #4c99ba;border-radius:2px 2px 0 0 ;
								  background : -webkit-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -moz-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -o-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -ms-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  cursor:pointer;
								  }
		.tabWrap .tabList li:hover a,
		.tabWrap .tabList li.active a,
		.tabWrap .tabList li:hover span,
		.tabWrap .tabList li.active span {display:block;z-index:3;margin:0px;height:30px;line-height:30px;color:#137caa;text-decoration:none;
										  border:1px solid #4c99ba;border-top:3px solid #4c99ba;border-radius: 2px 15px 0 0;border-bottom:1px solid #F1F1F1;}

		.tabWrap .tabList li:hover a,
		.tabWrap .tabList li:hover span {z-index:5}


	.reverseTabWrap {margin:0 0 30px 0;width:100%;border-top:1px solid #4c99ba}
	  .reverseTabWrap .tabList {position:relative;z-index:1;float:left;margin:0;padding:0;width:100%;}
		.reverseTabWrap .tabList li {margin:-1px 0 0 -5px;padding:0;float:left;width:auto;list-style:none;}
		.reverseTabWrap .tabList li:first-child {margin:-1px 0 0 5px;}

		.reverseTabWrap .tabList li a,
		.reverseTabWrap .tabList li span {display:block;position:relative;z-index:1;margin:0 0 5px 0;padding:5px 20px 0;height:25px;line-height:25px;
								  color:#999999;font-size:12px;text-decoration:none;text-shadow:-1px -1px 0 #fff;
								  border:1px solid #cccccc;border-bottom:3px solid #cccccc;border-top:1px solid #4c99ba;border-radius:0 0 2px 2px;
								  background : -webkit-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -moz-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -o-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -ms-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  background : -linear-gradient(top, rgb(255, 255, 255) 00%, rgb(241, 241, 241) 100%);
								  cursor:pointer;
								  }
		.reverseTabWrap .tabList li:hover a,
		.reverseTabWrap .tabList li.active a,
		.reverseTabWrap .tabList li:hover span,
		.reverseTabWrap .tabList li.active span {display:block;z-index:3;margin:0px;height:30px;line-height:30px;color:#137caa;text-decoration:underline;
												border:1px solid #4c99ba;border-bottom:3px solid #4c99ba;border-radius:0 0 15px 2px;border-top:1px solid #fff;}

		.reverseTabWrap .tabList li:hover a,
		.reverseTabWrap .tabList li:hover span {z-index:5}

	/*TABLE*/
	.dataTbls {width:100%;border-collapse:collapse;border-top:2px solid #4c99ba;}

	.dataTbls th,
	.dataTbls td {padding:5px;line-height:28px;font-size:12px;border-bottom: 1px solid #dfdfdf;background-color: #fff;}

	.dataTbls th.noneLine,
	.dataTbls td.noneLine {border-bottom: 0px;}

	.dataTbls th.dotLine,
	.dataTbls td.dotLine {border-bottom: 1px dotted #dfdfdf;}

	.dataTbls.normal-line-height td {line-height:22px;}

	.dataTbls th {background:#f0f0f0}

	.dataTbls tr:hover th,
	.dataTbls tr:hover td {background:#f0f0f0}

	.overWhite tr:hover td {background:#ffffff;}
	.noTopline {border-top:0px;}

	.dataTbls td.txtCenter {text-align:center;}

	.dataTbls input[type=text] {font-size:12px;padding:0 5px;height:22px;line-height:20px;vertical-align:middle;border:1px solid #ddd;box-shadow:1px 1px 1px #eee inset}
	.dataTbls select {padding:4px 5px;font-size:12px;padding:0 5px;height:22px;line-height:20px;vertical-align:middle;border:1px solid #ddd;box-shadow:1px 1px 1px #eee inset}
	.dataTbls input[type=checkbox],
	.dataTbls input[type=radio]  {margin:0;padding:0;height:19x;line-height:19px;vertical-align:text-bottom}
	.dataTbls textarea {padding:0 5px;min-height:60px;line-height:20px;vertical-align:middle;border:1px solid #ddd;box-shadow:1px 1px 1px #eee inset}

	/*v2.1.5*/
	.dataTbls .closebuttonRow .bgB9B9B9 {background:#B9b9b9;}
	.closebuttonRow .dataTbls td.btnlist {background:#B9b9b9;height:100px;min-width:35px;text-align:center;vertical-align:top;line-height:25px;padding:5px 0;}
	.closebuttonRow .dataTbls td.btnlist img {cursor:pointer;}
	/*v2.1.5*/

	.dataTbls .bgColorDDD {background:#dfdfdf}

	.dataTbls.separate {border-collapse :separate;border:1px solid #aaa;background:#f6f6f6}
	.dataTbls.separate th,
	.dataTbls.separate td {padding:5px 10px;line-height:30px;font-size:12px;border:none;background-color:transparent;}
	.dataTbls.separate td {border: 1px solid #dfdfdf;border-radius:2px;background-color: #fff;}

	.dataTbls .tbClear {font-size:10px;line-height:15px;border-bottom:0px;padding:0;}

	.dataTbls .list-goods-img {margin-top:10px;width:100px;height:100px;border:1px solid #efefef;}
	.dataTbls .list-goods-img2 {margin-top:10px;width:50px;height:50px;border:1px solid #efefef;}

	/* Button */
	.newBtns {display:inline-block;margin:0;padding:5px 15px 2px;height:20px;line-height:20px;
			  color:#137caa;font-size:12px;text-decoration:none;text-shadow:-1px -1px 0 #fff;
			  border:1px solid #4c99ba;border-top:3px solid #4c99ba;border-radius: 2px;
			  background : -webkit-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(238, 238, 238) 100%, rgb(241, 241, 241) 99%);
			  background : -moz-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(238, 238, 238) 100%, rgb(241, 241, 241) 99%);
			  background : -o-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(238, 238, 238) 100%, rgb(241, 241, 241) 99%);
			  background : -ms-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(238, 238, 238) 100%, rgb(241, 241, 241) 99%);
			  background : -linear-gradient(top, rgb(255, 255, 255) 00%, rgb(238, 238, 238) 100%, rgb(241, 241, 241) 99%);
			  cursor:pointer;
			  }

	input.newBtns,
	button.newBtns {margin:0;height:31px;line-height:20px;}

	.clearfix:after {  content:" "; display:block; clear:both; height:0;visibility:hidden; font-size:0; }

	/* btn */
	.wrap .button-bbse {background-color:#ffffff; font-size:12px; font-weight:bold; font-family:'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;
		border:1px solid #b4b4b4; border-top:3px solid #b4b4b4; height:32px; line-height:29px; padding:0 20px 0; margin:0 1px; text-decoration:none; display:inline-block; overflow:visible; zoom:1;
		box-sizing:border-box; -webkit-box-sizing:border-box; cursor:pointer; border-radius:3.1px; box-shadow:0 3px 2px -1px #eee; text-shadow:-1px -1px #fff
	}

	.wrap .button-small {background-color:#ffffff;font-size:12px; font-weight:bold; font-family:'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;
		border:1px solid #b4b4b4; border-top:3px solid #b4b4b4; height:20px; line-height:18px; padding:0 10px 0; margin:0 1px; text-decoration:none; display:inline-block; overflow:visible; zoom:1;
		box-sizing:border-box; -webkit-box-sizing:border-box; cursor:pointer; border-radius:3.1px; box-shadow:0 3px 2px -1px #eee; text-shadow:-1px -1px #fff
	}

	.wrap button.blue {color:#137caa; border-color:#4c99ba !important;}
	.wrap button.blue:hover {color:#157199; border-color:#157199 !important;}

	.wrap button.red {color:#E1464C; border-color:#E1464C !important;}
	.wrap button.red:hover {color:#BA060D; border-color:#BA060D !important;}

	.wrap button.green {color:#1E9741; border-color:#1E9741 !important;}
	.wrap button.green:hover {color:#12732F; border-color:#12732F !important;}


	/* btn fill-color */
	.wrap .button-fill {font-size:12px; font-weight:bold; font-family:'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;
		height:32px; line-height:29px; padding:0 20px 0; margin-top:0 1px; text-decoration:none; display:inline-block; overflow:visible; zoom:1;
		box-sizing:border-box; -webkit-box-sizing:border-box; cursor:pointer; border-radius:3.1px; box-shadow:0 3px 2px -1px #eee;
	}

	.wrap .button-small-fill {font-size:12px; font-family:'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;
		height:18px; line-height:12px; padding:0 5px 0; margin:0 1px; text-decoration:none; display:inline-block; overflow:visible; zoom:1;
		box-sizing:border-box; -webkit-box-sizing:border-box; cursor:pointer; border-radius:3.1px;
	}

	.wrap button.button-fill.red {color:#FFFFFF;background-color:#E1464C; border-color:#E1464C !important;}
	.wrap button.button-small-fill.red{font-weight:normal;color:#FFFFFF;background-color:#E1464C; border-color:#E1464C !important;}

	.wrap button.button-fill.blue {color:#FFFFFF; background-color:#137caa; border-color:#4c99ba !important;}
	 .wrap button.button-small-fill.blue {font-weight:normal;color:#FFFFFF; background-color:#137caa; border-color:#4c99ba !important;}

	.wrap button.button-fill.green {color:#FFFFFF;background-color:#2D8848; border-color:#22B14C !important;}
	.wrap button.button-small-fill.green {font-weight:normal;color:#FFFFFF; background-color:#2D8848; border-color:#22B14C !important;}

	.wrap button.button-fill.orange {color:#FFFFFF;background-color:#E58849; border-color:#FF7F27 !important;}
	.wrap button.button-small-fill.orange {font-weight:normal;color:#FFFFFF; background-color:#E58849; border-color:#FF7F27 !important;}

	.wrap button.button-fill.gray {color:#FFFFFF;background-color:#7F7F7F; border-color:#AAA9A9 !important;}
	.wrap button.button-small-fill.gray {font-weight:normal;color:#FFFFFF; background-color:#7F7F7F; border-color:#AAA9A9 !important;}

	.wrap button.button-fill.black {color:#FFFFFF;background-color:#262626; border-color:#363636 !important;}
	.wrap button.button-small-fill.black {font-weight:normal;color:#FFFFFF; background-color:#262626; border-color:#363636 !important;}

	.wrap .title-sub-desc {list-style: none;margin:0;padding: 0;font-size: 13px;float: left;color: #666;}
	.wrap .title-sub-desc li {display: inline-block;padding: 0;margin-right:10px;white-space: nowrap;list-style: none;}

	.wrap .title-sub-desc li a {text-decoration: none;}
	.wrap .title-sub-desc li.current a {color:#000;}

	.wrap .title-sub-desc li:after {margin-left:10px;content:"|";color:#707070;}
	.wrap .title-sub-desc li:last-child::after {content:"";}

	.wrap .title-sub-desc.none-content li:after {margin-left:0;content:""}

	.wrap .dataTbls a {text-decoration:none;}

	.wrap .default-cursor {cursor:default;}

	.wrap .admin-pagination {clear:both;margin:0 auto;padding:20px 0;position:relative;font-size:11px;line-height:13px;}
	.wrap .admin-pagination span, .pagination a {display:block;float:left;margin: 2px 2px 2px 0;padding:6px 9px 5px 9px;text-decoration:none;width:auto;color:#fff;background: #6d6d6d;}

	.wrap .admin-pagination a {display: block;float: left;margin: 2px 2px 2px 0;padding: 6px 9px 5px 9px;text-decoration: none;width: auto;color: #fff;background: #6d6d6d;}
	.wrap .admin-pagination a:hover{color:#fff;background: #3279BB;}
	.wrap .admin-pagination .current{padding:6px 9px 5px 9px;background: #3279BB;color:#fff;}

	.hide{display:none;}
	.pulldown-icon {float:right;cursor:pointer;margin-top:7px;}
</style>

<script language="javascript">
	function option_view(tName,tVersion,tAbs,bName,bHome,bLanguage,bAdminEmail,bServerIp,iUrl){
		/*
		var tbWidth = window.innerWidth * .35;
		var tbHeight = window.innerHeight * .65;
		*/
		var tbTitle=jQuery("#tb_Title").val();
		var tbWidth = 700;
		var tbHeight = 650;
		tb_show(tbTitle+" - "+tName, "http://license.bbsetheme.com/Popup/bbse-license.php?tName="+tName+"&#38;tVersion="+tVersion+"&#38;tAbs="+tAbs+"&#38;bName="+bName+"&#38;bHome="+bHome+"&#38;bLanguage="+bLanguage+"&#38;bAdminEmail="+bAdminEmail+"&#38;bServerIp="+bServerIp+"&#38;iUrl="+iUrl+"&#38;width="+tbWidth+"&#38;height="+tbHeight+"&#38;TB_iframe=true");
		return false;
	}

	function theme_license_key_insert(licenseKey){
		jQuery("#<?php echo $varLcKey;?>").val(licenseKey);
		tb_remove();
	}

	function theme_toggle_contents(tObj){
		var imgUrl="";
		var tRow=jQuery("."+tObj+"Row");
		var tIcon=jQuery("#"+tObj+"PulldownIcon");

	    	tRow.toggleClass("hide");

		if(tRow.is(':visible')) imgUrl=tIcon.prop("src").replace("arrow_down_16.png","arrow_up_16.png");
		else imgUrl=tIcon.prop("src").replace("arrow_up_16.png","arrow_down_16.png");

		tIcon.prop("src",imgUrl);
	}

	function bbse_theme_save_submit(oType){
		if(confirm('라이센스 키를 저장하시겠습니까?   ')){
			jQuery("#action").val('save');
			jQuery("#option_type").val(oType);
			jQuery("#optionForm").submit();
		}
	}
</script>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>
			<?php echo BBSE_THEME_NAME;?> 라이센스 키 관리
			<span style="float:right;"><a href="<?php echo (BBSE_POPUP_LANGUAGE=='ko-KR')?"http://manual.bbsetheme.com/license-key/":"http://manual.bbsetheme.com/license-key_en/";?>" target="_blank" title="매뉴얼"><button type="button" class="button-bbse blue" style="width:120px;float:right;"> 매뉴얼 </button></a></span>
		</h2>
		<hr>
	</div>
	<div class="column-full">
		<div class="borderBox">
			<table width="100%">
				<colgroup><col width="60%"><col width="40%"></colgroup>
				<tr>
					<td><?php echo $licenseMsg;?></td>
					<td>
						<span style="margin-left:50px;"><button type="button" class="button-bbse red" onClick="option_view('<?php echo BBSE_THEME_NAME;?>','<?php echo BBSE_THEME_VERSION;?>','<?php echo BBSE_THEME_ABS_PATH;?>','<?php echo BBSE_BLOG_NAME;?>','<?php echo BBSE_BLOG_HOME;?>','<?php echo BBSE_BLOG_LANGUAGE;?>','<?php echo BBSE_BLOG_ADMIN_EMAIL;?>','<?php echo BBSE_BLOG_SERVER_IP;?>','<?php echo BBSE_THEME_WEB_URL;?>/' );" style="width:250px;"><?php echo $btnStr;?></button></span>
					</td>
				</tr>
			</table>
		</div>

		<form id='optionForm' method='post'>
		<input type='hidden' name='option_type' id='option_type' value='license' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='tb_Title' id='tb_Title' value='라이센스 키 발급' />

		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th colspan="2" style="text-align:center;font-size:14px;"><?php echo BBSE_THEME_NAME;?> 라이센스 키 관리<img src="<?php echo BBSE_THEME_WEB_URL?>/images/arrow_up_16.png" id="basicPulldownIcon" class="pulldown-icon" onClick="theme_toggle_contents('basic');" /></th>
			</tr>
			<tr class="basicRow">
				<th>라이센스 키</th>
				<td><input type="text" style="width:300px;" name="<?php echo $varLcKey;?>" id="<?php echo $varLcKey;?>" value="<?php echo get_option($varLcKey);?>" /></td>
			</tr>
		</table>

		<div style="margin:40px 0;text-align:center;">
			<button type="button" class="button-bbse blue" onClick="bbse_theme_save_submit('license');" style="width:20%;"> 이 라이센스 키 사용 </button>
		</div>
	</div>

	</form>
</div>

