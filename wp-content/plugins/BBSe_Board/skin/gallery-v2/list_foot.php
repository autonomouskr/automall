	<div class="bbse_board_foot">
		<?php require_once(BBSE_BOARD_PLUGIN_ABS_PATH."skin/".$boardInfo->skinname."/powered.php");?>
		<div class="btn">
			<?php if($curUserPermision == 'administrator'){?>
			<?php echo $list_move?><span>이동</span></a>
			<?php echo $list_copy?><span>복사</span></a>
			<?php echo $list_delete?><span>삭제</span></a>
			<?php }?>
			<?php
			if(!empty($boardInfo->l_write) && $boardInfo->l_write == "administrator"){
				if($curUserPermision == 'administrator'){
			?>
			<?php echo $list_write?><strong>글쓰기</strong></a>
			<?php
				}
			}else{
			?>
			<?php echo $list_write?><strong>글쓰기</strong></a>
			<?php
			}
			?>
		</div>
	</div>