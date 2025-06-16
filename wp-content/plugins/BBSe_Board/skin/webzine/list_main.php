	<tbody>
	<?php
	if($total > 0){
		foreach($result as $i => $bData){
			if($bData->re_level > 0){
				$reicon = " &nbsp;[Re]&nbsp;";
			}else{
				$reicon = "";
			}

			if($boardInfo->use_secret == 1 && $bData->use_secret > 0){
				$secret = "&nbsp;<img src='".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/images/icon_secret.png' style='vertical-align:middle' alt='비밀글' />";
			}else{
				$secret = "";
			}

			$blank = "";
			for($j = 1; $j < $bData->re_level; $j++){
				$blank = $blank."&nbsp;&nbsp;&nbsp;";
			}

			if( (time(0) - strtotime($bData->write_date)) < 86400){
				$newicon = "&nbsp;<img class='new' alt='새글' src='".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/images/icon_new.png' />";
			}else{
				$newicon = "";
			}

			if($boardInfo->use_comment == '1'){
				$c_total = $wpdb->get_var("select count(*) from `".$wpdb->prefix."bbse_".$bname."_comment` where `parent`='".$bData->no."'");

				if($c_total > 0){
					$total_comment = "&nbsp;<span class=\"comm\">[".$c_total."]</span>";
				}else{
					$total_comment = "";
				}
			}

			$title = "";
			if(!empty($blank)) $title .= $blank;
			if(!empty($reicon)) $title .= $reicon;
			if(!empty($bData->title)) $title .= stripslashes($bData->title);

			if(!empty($bData->write_date)){
				$writetime = strtotime($bData->write_date);
			}
			global $unique;
			if(!empty($bData->re_level) && $bData->re_level != 0) $pict = $unique."_re";
			else $pict = $bData->ref."_";

			$image_file = NULL;
			if(!empty($bData->image_file)){
				$oFile      = explode(".", $bData->image_file);
				$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no."_".$pict."image.".$oFile[1];
			}

			if(!empty($bData->file1) && !$image_file){
				$oFile      = explode(".", $bData->file1);
				$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no."_".$pict."1.".$oFile[1];
			}

			if(!empty($bData->file2) && !$image_file){
				$oFile      = explode(".", $bData->file2);
				$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no."_".$pict."2.".$oFile[1];
			}

			if(!$image_file){
				$image_file = bbse_board_first_image(stripslashes($bData->content));
			}
	?>
	<tr>
		<td class="img1">
			<?php
			if(!empty($image_file)){
				if(!empty($bData->image_file_alt)) $image_alt = ' alt="'.$bData->image_file_alt.'"';
				else $image_alt = ' alt="'.__('Main image', 'bbse_board').'"';
				$thumbBg  = 'style="background-image:url('.$image_file.');"';
				//$thumbTag = '<img src="'.$image_file.'" '.$image_alt.' >';
				$thumbTag = '';
			}else{
				$thumbBg  = '';
				$thumbTag = '<div class="g_no_image">no image</div>';
			}
			$thumb_ratio = $boardInfo->thumb_ratio ? $boardInfo->thumb_ratio : 3;
			?>
			<div class="thumb1" <?php echo $thumbBg?>><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL.'skin/'.$boardInfo->skinname?>/images/tr<?php echo $thumb_ratio?>.png" class="ratio_template"><?php echo $thumbTag?></div>
		</td>
		<td class="con">
			<a href="<?php echo $curUrl.$link_add?>nType=<?php echo bbse_board_parameter_encryption($bname, 'view', $bData->no, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])?>">
				<p class="title">
					<span style="font-weight:bold;color:#333;float:left;max-width:80%;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;word-wrap:break-word;"><?php echo stripslashes(cut_text($title, 100))?></span><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($secret)) echo $secret?><?php if(!empty($newicon)) echo $newicon?>
					<?php if($curUserPermision == 'administrator'){?><span style="position:relative;float:right;width:50px;text-align:right;"><input type="checkbox" name="check[]" value="<?php echo $bData->no?>"></span><?php }?>
				</p>
				<p class="conbox"><?php echo stripslashes(strip_tags($bData->content))?></p>
			</a>
			<p class="name">
				<?php if($curUserPermision == 'administrator' || ($curUserPermision != 'administrator' && (empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1))){?><?php echo $bData->writer?> <?php }?>
				<span class="date">작성일: <?php if(!empty($writetime)) echo date("Y-m-d", $writetime); else echo "-";?> <?php if($curUserPermision == 'administrator' || ($curUserPermision != 'administrator' && (empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1))){?>조회:<?php echo number_format($bData->hit)?><?php }?></span>
			</p>
		</td>
	</tr>
	<?php
			$num--;
		}

	}else{
	?>
	<tr>
		<td colspan="<?php echo $tbl_cols?>" align="center" style="padding:50px 0;text-align:center;">등록된 게시물이 없습니다.</td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	</form>