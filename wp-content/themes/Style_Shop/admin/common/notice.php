<?php if ( $type == 'license' ) {?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<style type="text/css">
		.bbseplugin_activate{
			min-width:825px;
			border:1px solid #dfdfdf;
			padding:5px;
			margin:15px 0;
			background:#dfdfdf;
			background-image:-webkit-gradient(linear,0% 0,80% 100%,from(#dfdfdf),to(#efefef));
			background-image:-moz-linear-gradient(80% 100% 120deg,#efefef,#dfdfdf);

			-moz-border-radius:3px;border-radius:3px;
			-webkit-border-radius:3px;position:relative;overflow:hidden
		}

		.bbseplugin_activate .back_str{
			position:absolute;
			top:30px;
			right:10px;
			font-size:80px;
			color:#dfdfdf;
			font-family:Georgia, "Times New Roman", Times, serif;z-index:1
		}

		.bbseplugin_activate .back_str_container{
			cursor:pointer;
			display:inline-block;
			padding:5px;
			-moz-border-radius:2px;
			border-radius:2px;
			-webkit-border-radius:2px;
			width:266px
		}
		
		.bbseplugin_activate .back_str_description{
			position:absolute;
			top:18px;
			left:285px;
			margin-left:25px;
			color:#7f7f7f;
			font-size:14px;
			z-index:1000
		}
		
		.bbseplugin_activate .back_str_description strong{
			color:#7f7f7f;
			font-weight:normal
		}

		.bbseplugin_activate .font-size-14{
			font-size:14px;
			font-weight:700;
			width:267px;
			height:40px;
		}

		.wrap button.red {
		  color: #E1464C;
		  border-color: #E1464C !important;
		}

		.wrap .button-bbse {
		  background-color: #ffffff;
		  font-weight: bold;
		  font-family: '돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;
		  border: 1px solid #b4b4b4;
		  border-top: 3px solid #b4b4b4;
		  height: 32px;
		  line-height: 29px;
		  padding: 0 20px 0;
		  margin: 0 1px;
		  text-decoration: none;
		  display: inline-block;
		  overflow: visible;
		  zoom: 1;
		  box-sizing: border-box;
		  -webkit-box-sizing: border-box;
		  cursor: pointer;
		  border-radius: 3.1px;
		  box-shadow: 0 3px 2px -1px #eee;
		  text-shadow: -1px -1px #fff;
		}

		.notice_target_name {
			font-weight:700;
			color:#E1464C;
		}
	</style>
	<script language="javascript">
		var theme_GetLicenseInfo=function(){ // 라이센트 관리 메뉴로 이동
			var goUrl="admin.php?page=functions.php";
			window.location.href=goUrl;
		}
	</script>
	<div class="bbseplugin_activate">
		<div class="back_str">BBS e-Theme</div>
		<div class="back_str_container">
			<button type="button" class="button-bbse red font-size-14" onClick="theme_GetLicenseInfo();">라이센스키 등록</button>
		</div>
		<div class="back_str_description"><strong>라이센스키 등록</strong> - <span class="notice_target_name"><?php echo BBSE_THEME_NAME;?></span> 테마의 사용을 위해 라이센스 키를 등록해 주세요.</div>
	</div>
</div>
<?php }?>
