	<table class="tbl_type_view" border="1" cellspacing="0" summary="글 내용">
	<caption>글 읽기</caption>
	<colgroup>
		<col width="120" /><col />
	</colgroup>
	<tbody>
	<tr>
		<th scope="row">이전글</th>
		<td><?php echo stripslashes($prevlink)?> <?php if(!empty($prev_wdate)){?><span class='date'><?php echo $prev_wdate?></span><?php }?></td>
	</tr>
	<tr>
		<th scope="row">다음글</th>
		<td><?php echo stripslashes($nextlink)?> <?php if(!empty($next_wdate)){?><span class='date'><?php echo $next_wdate?></span><?php }?></td>
	</tr>
	</tbody>
	</table>
</div>