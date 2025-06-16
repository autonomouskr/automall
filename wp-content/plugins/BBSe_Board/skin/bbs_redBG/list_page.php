	<?php if($total > 0){?>
	<div class="paginate">
		<?php echo $pagelink_first?><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/btn_page_prev1.gif" alt="처음" /></a>
		<?php echo $pagelink_pre?><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/btn_page_prev.gif" alt="이전" /></a>
		<?php echo $pagelink_view?>
		<?php echo $pagelink_next?><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/btn_page_next.gif" alt="다음" /></a>
		<?php echo $pagelink_last?><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/btn_page_next1.gif" alt="맨끝" /></a>
	</div>
	<?php }?>
</div>