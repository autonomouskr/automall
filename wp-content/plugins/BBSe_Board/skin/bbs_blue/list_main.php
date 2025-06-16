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

			if(!empty($bData->file1) || !empty($bData->file2)){
				$addfile = "<img src='".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/images/icon_file.gif' alt='첨부파일' />";
			}else{
				$addfile = "";
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
					$total_comment = "&nbsp;<a class='comment' href='#'>[".$c_total."]</a>";
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

			// 모바일, 태블릿 접속
			if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPhone') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'Android') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false){
	?>
	<tr<?php if($bData->use_notice == 1) echo " class='noti_tr'";?>>
		<?php if($curUserPermision == 'administrator'){?>
		<td><input type="checkbox" name="check[]" value="<?php echo $bData->no?>" /></td>
		<?php }?>
		<td class="title<?php if($bData->use_notice == 1) echo " bold";?>" style="padding-left:10px;"><a href="<?php echo $curUrl.$link_add?>nType=<?php echo bbse_board_parameter_encryption($bname, 'view', $bData->no, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])?>"><span class="title_line<?php if($bData->use_notice == 1) echo " noti_title";?>"><?php echo $title?></span></a><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($secret)) echo $secret?><?php if(!empty($newicon)) echo $newicon?></td>
		<td class="date"><?php if(!empty($writetime)) echo date("Y/m/d", $writetime); else echo "-";?></td>
	</tr>
	<?php
			}else{
	?>
	<tr<?php if($bData->use_notice == 1) echo " class='noti_tr'";?>>
		<?php if($curUserPermision == 'administrator'){?>
		<td><input type="checkbox" name="check[]" value="<?php echo $bData->no?>" /></td>
		<?php }?>
		<td class="num">
			<?php
			if($bData->use_notice == 1){
				echo "<img src='".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/images/icon_notice.gif' alt='공지' />";
			}else{
				echo number_format($num);
			}
			?>
		</td>
		<td class="title<?php if($bData->use_notice == 1) echo " bold";?>" style="padding-left:10px;"><a href="<?php echo $curUrl.$link_add?>nType=<?php echo bbse_board_parameter_encryption($bname, 'view', $bData->no, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])?>"><span class="title_line<?php if($bData->use_notice == 1) echo " noti_title";?>"><?php echo $title?></span></a><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($secret)) echo $secret?><?php if(!empty($newicon)) echo $newicon?></td>
		<?php if($boardInfo->use_pds == 1){?>
		<td class="frm"><?php echo $addfile?></td>
		<?php }?>
		<?php
		if($curUserPermision == 'administrator'){
		?>
		<td><span class="writer_line author"><?php echo $bData->writer?></span></td>
		<?php
		}else{
			if(empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1){
		?>
		<td><span class="writer_line author"><?php echo $bData->writer?></span></td>
		<?php
			}
		}
		?>
		<td class="entry-date"><?php if(!empty($writetime)) echo date("Y/m/d", $writetime); else echo "-";?></td>
		<?php
		if($curUserPermision == 'administrator'){
		?>
		<td class="hit"><?php echo number_format($bData->hit)?></td>
		<?php
		}else{
			if(empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1){
		?>
		<td class="hit"><?php echo number_format($bData->hit)?></td>
		<?php
			}
		}
		?>
	</tr>
	<?php
			}
			$num--;
		}

	}else{
	?>
	<tr>
		<td colspan="<?php echo $tbl_cols?>" style="padding:50px 0;text-align:center;">등록된 게시물이 없습니다.</td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	</form>