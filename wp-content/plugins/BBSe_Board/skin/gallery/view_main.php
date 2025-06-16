<?php 
if(preg_match("/\bpage_id\b/", $curUrl)) $link_add = "&";
else $link_add = "?";

$share_link = $curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'view', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate']);
?>
<script type="text/javascript">
// 트위터 공유하기
function bbse_board_share_tw(){
	var left = (screen.width / 2) - 200;
	var top = (screen.height / 2) - 180;
	var content = "<?php if(!empty($share_title)) echo $share_title?>";
	var link = "<?php echo $share_link?>";
	var popOption = "width=400, height=360, top=" + top + ", left=" + left + ", resizable=no, scrollbars=no, status=no;";

	var wp = window.open("http://twitter.com/share?url=" + encodeURIComponent(link) + "&text=" + encodeURIComponent(content), 'share_twitter', popOption); 
	if(wp){
		wp.focus();
	}     
}

// 페이스북 공유하기
function bbse_board_share_fb(){
	var left = (screen.width / 2) - 200;
	var top = (screen.height / 2) - 180;
	var link = "<?php echo $share_link?>";
	var popOption = "width=400, height=360, top=" + top + ", left=" + left + ", resizable=no, scrollbars=no, status=no;";
	var wp = window.open("http://www.facebook.com/share.php?u=" + encodeURIComponent(link), 'share_facebook', popOption); 
	if(wp){
		wp.focus();
	}
}

<?php if( wp_is_mobile() && get_option("bbse_board_kakao_app_key") !='' ){ ?>
<?php	if (!empty($boardInfo->use_kakaotalk) && $boardInfo->use_kakaotalk == 1 ) {?>
// 카카오톡 공유하기
var bbse_board_share_kakaotalk = function($key, $label, $src, $text, $url){
	if(!$key) return false;
	Kakao.init($key);
	if($src){
		Kakao.Link.createTalkLinkButton({
			container : '.kakaotalk-link',
			label : $label,
			image : {src : $src, width : '300', height : '200'},
			webButton : {text : $text, url : $url}
		});
	}else{
		Kakao.Link.createTalkLinkButton({
			container : '.kakaotalk-link',
			label : $label,
			webButton : {text : $text, url : $url}
		});
	}
}
<?php }?>
<?php if(!empty($boardInfo->use_kakaostory) && $boardInfo->use_kakaostory == 1 ){?>
// 카카오스토리 공유하기
var bbse_board_share_kakaostory = function(){
	kakao.link("story").send({
		post : "<?php echo $share_link?>",
		appid : "<?php echo home_url()?>",
		appver : "1.0",
		appname : "<?php echo get_option('blogname')?>",
		urlinfo : JSON.stringify({title:"<?php if(!empty($share_title)) echo cut_text($share_title, 30)?>", desc:"<?php if(!empty($share_content)) echo cut_text($share_content, 50)?>"<?php if(!empty($share_imageurl)){?>, imageurl:["<?php echo $share_imageurl?>"]<?php }?>, type:"article"})
	});
}
<?php }?>
<?php }?>
</script>
<div id="bbse_board" style="width:<?php echo $table_width?>;<?php echo $table_align?>">
	<!-- 리스트 -->
	<table class="tbl_type_view" border="1" cellspacing="0" summary="글 내용">
	<caption>글 읽기</caption>
	<colgroup>
		<col width="120" /><col />
	</colgroup>
	<thead>
	<tr>
		<th scope="row">제목</th>
		<td><?php if(!empty($brdData->title)) echo stripslashes($brdData->title)?>  <span class='date'><?php if(!empty($brdData->write_date)) echo $brdData->write_date?></span></td>
	</tr>
	</thead>
	<tbody>
	<?php if(!empty($boardInfo->category_list) && !empty($brdData->category)){?>
	<tr>
		<th scope="row">카테고리</th>
		<td><?php if(!empty($brdData->category)) echo $brdData->category?></td>
	</tr>
	<?php }?>
	<?php if($curUserPermision == 'administrator'){?>
	<?php if(!empty($brdData->email)){?>
	<tr>
		<th scope="row">이메일</th>
		<td><?php echo sanitize_text_field(stripslashes($brdData->email))?></td>
	</tr>
	<?php }?>
	<?php if(!empty($brdData->phone)){?>
	<tr>
		<th scope="row">연락처</th>
		<td><?php echo sanitize_text_field(stripslashes($brdData->phone))?></td>
	</tr>
	<?php }?>
	<?php }?>
	<?php if(!empty($image_download)){?>
	<tr>
		<th scope="row">대표이미지</th>
		<td>
			<?php if(!empty($image_download)) echo $image_download?>
		</td>
	</tr>
	<?php }?>
	<?php if(!empty($file_download1) || !empty($file_download2)){?>
	<tr>
		<th scope="row">첨부파일</th>
		<td>
			<?php if(!empty($file_download1)) echo $file_download1?>
			<?php if(!empty($file_download2)) echo $file_download2?>
		</td>
	</tr>
	<?php }?>
	<?php 
	if($curUserPermision == 'administrator'){
	?>
	<tr>
		<th scope="row">작성자</th>
		<td><?php if(!empty($brdData->writer)) echo $brdData->writer?></td>
	</tr>	
	<?php 
	}else{
		if(empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1){
	?>
	<tr>
		<th scope="row">작성자</th>
		<td><?php if(!empty($brdData->writer)) echo $brdData->writer?></td>
	</tr>	
	<?php 
		}
	}
	?>
	<?php
	if($curUserPermision == 'administrator'){
	?>
	<tr>
		<th scope="row">조회</th>
		<td><?php echo number_format($brdData->hit)?></td>
	</tr>	
	<?php
	}else{
		if(empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1){
	?>
	<tr>
		<th scope="row">조회</th>
		<td><?php echo number_format($brdData->hit)?></td>
	</tr>
	<?php 
		}
	}
	?>
	<tr>
		<td class="cont" colspan="2">
			<?php if(!empty($image_file_result) && (!empty($brdData->use_content_image) && $brdData->use_content_image == 1)) echo $image_file_result?><?php if(!empty($view_file_result1)) echo $view_file_result1?><?php if(!empty($view_file_result2)) echo $view_file_result2?><?php if(!empty($content)) echo stripslashes($content)?>
			<p style="text-align:right;">
				<?php if(!empty($boardInfo->use_facebook) && $boardInfo->use_facebook == 1){?>
				<a href="javascript:;" onclick="bbse_board_share_fb();"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/icon_facebook.png" alt="facebook" title="facebook" /></a>
				<?php }?>
				<?php if(!empty($boardInfo->use_twitter) && $boardInfo->use_twitter == 1){?>
				<a href="javascript:;" onclick="bbse_board_share_tw();"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/icon_twitter.png" alt="twitter" title="twitter" /></a>
				<?php }?>

				<?php if( wp_is_mobile() && get_option("bbse_board_kakao_app_key") !='' ){ ?>
				<?php if(!empty($boardInfo->use_kakaotalk) && $boardInfo->use_kakaotalk == 1){?>
				<a class="kakaotalk-link" href="javascript:;" data-key="<?php if(!empty($kakao_app_key)) echo $kakao_app_key?>" data-url="<?php echo $share_link?>" data-label="<?php if(!empty($share_title)) echo cut_text($share_title, 50)?>" data-src="<?php if(!empty($share_imageurl)) echo $share_imageurl?>" data-text="<?php if(!empty($share_content)) echo cut_text($share_content, 50)?>"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/icon_kakaotalk.png" alt="kakaotalk" title="kakaotalk" /></a>
				<?php }?>
				<?php if(!empty($boardInfo->use_kakaostory) && $boardInfo->use_kakaostory == 1){?>
				<a href="javascript:;" onclick="bbse_board_share_kakaostory();"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/icon_kakaostory.png" alt="kakaostory" title="kakaostory" /></a>
				<?php }?>
				<?php }?>
			</p>
		</td>
	</tr>
	</tbody>
	</table>
	<!--// 리스트 -->
	<div class="bbse_board_foot">
		<?php require_once(BBSE_BOARD_PLUGIN_ABS_PATH."skin/".$boardInfo->skinname."/powered.php");?>
		<div class="btn">
			<?php echo $view_list?><span>목록</span></a>
			<?php echo $view_modify?><span>수정</span></a>
			<?php echo $view_delete?><span>삭제</span></a>
			<?php
			if(!empty($boardInfo->l_write) && $boardInfo->l_write == "administrator"){
				if($curUserPermision == 'administrator'){
			?>
			<?php echo $view_write?><strong>글쓰기</strong></a>
			<?php
				}
			}else{
			?>
			<?php echo $view_write?><strong>글쓰기</strong></a>	
			<?php
			}
			?>		
		</div>
	</div>
	<?php if( wp_is_mobile() && get_option("bbse_board_kakao_app_key") !='' ){ ?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		var $key = jQuery('.kakaotalk-link').data('key');
		var $label = jQuery('.kakaotalk-link').data('label');
		var $src = jQuery('.kakaotalk-link').data('src');
		var $text = jQuery('.kakaotalk-link').data('text');
		var $url = jQuery('.kakaotalk-link').data('url');
		bbse_board_share_kakaotalk($key, $label, $src, $text, $url);
	});
	</script>
	<?php }