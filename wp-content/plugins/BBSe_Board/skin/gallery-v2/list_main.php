		<div>
			<ul>
				<?php
				if($total > 0){
					foreach($result as $i => $bData){
						$title_width  = 100;  // 타이틀영역 가로값
						$secret_width = $new_width = $comment_width = 0;  // 비밀글, 신규게시물, 코멘트개수, 체크박스 포함시 가로값 빼기

						if($bData->re_level > 0){
							$reicon = ' &nbsp;[Re]&nbsp;';
						}else{
							$reicon = '';
						}

						if($boardInfo->use_secret == 1 && $bData->use_secret > 0){
							$secret = '<img src="'.BBSE_BOARD_PLUGIN_WEB_URL.'skin/'.$boardInfo->skinname.'/images/icon_secret.png" style="vertical-align:middle" alt="비밀글" />';
							$secret_width = 10;
						}else{
							$secret = '';
							$secret_width = 0;
						}

						$blank = '';
						for($j = 1; $j < $bData->re_level; $j++){
							$blank = $blank."&nbsp;&nbsp;&nbsp;";
						}

						if( (time(0) - strtotime($bData->write_date)) < 86400){
							$newicon = '<img class="new" alt="새글" src="'.BBSE_BOARD_PLUGIN_WEB_URL.'skin/'.$boardInfo->skinname.'/images/icon_new.png" />';
							$new_width = 10;
						}else{
							$newicon = '';
							$new_width = 0;
						}

						if($boardInfo->use_comment == '1'){
							$c_total = $wpdb->get_var("select count(*) from `".$wpdb->prefix."bbse_".$bname."_comment` where `parent`='".$bData->no."'");

							if($c_total > 0){
								$total_comment = '<span class="comm">['.$c_total.']</span>';
								$comment_width = 12;
							}else{
								$total_comment = '';
								$comment_width = 0;
							}
						}

						$title_width = $title_width - $secret_width - $new_width - $comment_width;

						$title = '';
						if(!empty($blank))        $title .= $blank;
						if(!empty($reicon))       $title .= $reicon;
						if(!empty($bData->title)) $title .= stripslashes($bData->title);

						if(!empty($bData->write_date)){
							$writetime = strtotime($bData->write_date);
						}

						if(!empty($bData->re_level) && $bData->re_level != 0) $pict = $unique."_re";
						else $pict = $bData->ref.'_';

						$image_file = NULL;
						if(!empty($bData->image_file)){
							$oFile      = explode(".", $bData->image_file);
							$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no.'_'.$pict."image.".$oFile[1];
						}

						if(!empty($bData->file1) && !$image_file){
							$oFile      = explode(".", $bData->file1);
							$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no.'_'.$pict."1.".$oFile[1];
						}

						if(!empty($bData->file2) && !$image_file){
							$oFile      = explode(".", $bData->file2);
							$image_file = BBSE_BOARD_UPLOAD_BASE_URL.'bbse-board/'.$boardInfo->board_no.'_'.$pict."2.".$oFile[1];
						}

						if(!$image_file){
							$image_file = bbse_board_first_image(stripslashes($bData->content));
						}
				?>
				<li>
					<a href="<?php echo $curUrl.$link_add?>nType=<?php echo bbse_board_parameter_encryption($bname, 'view', $bData->no, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])?>" title="<?php echo $title?>">
						<?php
						if(!empty($image_file)){
							if(!empty($bData->image_file_alt)) $image_alt = ' alt="'.$bData->image_file_alt.'"';
							else $image_alt = ' alt=""';
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
						<h2 class="entry-title"><span class="subj" style="max-width:<?php echo $title_width?>%;"><?php if($curUserPermision == 'administrator'){?><input type="checkbox" name="check[]" value="<?php echo $bData->no?>" /><?php }?><?php echo $title?></span><span class="subj_tag"><?php if(!empty($total_comment)) echo $total_comment?><?php if(!empty($secret)) echo $secret?><?php if(!empty($newicon)) echo $newicon?></span></h2>
					</a>
          <div class="entry-meta">
					  <?php if($curUserPermision == 'administrator' || ($curUserPermision != 'administrator' && (empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1))){?><span class="author"><?php echo $bData->writer?></span><?php }?><span class="entry-date updated">작성일: <?php if(!empty($writetime)) echo date("Y-m-d", $writetime); else echo "-";?> </span><?php if($curUserPermision == 'administrator' || ($curUserPermision != 'administrator' && (empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1))){?><span>조회: <?php echo number_format($bData->hit)?></span><?php }?>
          </div>
				</li>
				<?php
						$num--;
					}

				}else{
				?>
				<li style="padding:50px 0;height:60px;width:100% !important;">
					<div style="height:60px;text-align:center;margin-top:20px;">등록된 게시물이 없습니다.</div>
				</li>
				<?php
				}
				?>
			</ul>
		</div>
	</div>
	</form>