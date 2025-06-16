<!-- SKIN : <?php echo $boardInfo->skinname?> -->
	<?php 
	$tbl_cols = 4;
	?>
	<form name="list_frm" method="post">
	<table class="tbl_type" style="width:100%;" border="1" cellspacing="0" summary="게시판의 글 리스트">
	<caption>게시판 리스트</caption>
	<?php 
	// 모바일, 태블릿 접속
	$tbl_cols = 2;
	if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPhone') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'Android') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false){
	?>
	<colgroup>
		<?php if($curUserPermision == 'administrator'){?><col width="30" /><?php }?>
		<col />
		<col width="85" />
	</colgroup>
	<thead>
	<tr>
		<?php 
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col"><input type="checkbox" name="list_all" id="list_all" border="0" onclick="allselect(this.form);" /></th>
		<?php 
		}
		?>
		<th scope="col">제목</th>
		<th scope="col">작성일</th>
	</tr>
	<?php 
	}else{
		// PC 접속
		$tbl_cols = 3;
	?>
	<colgroup>
		<?php if($curUserPermision == 'administrator'){?><col width="30" /><?php }?>
		<col width="50" />
		<col />
		<?php if($boardInfo->use_pds == 1){?><col width="60" /><?php }?>
		<?php 
		if($curUserPermision == 'administrator'){
		?>
		<col width="115" />
		<?php
		}else{
			if(empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1){
		?>
		<col width="115" />
		<?php 
			}
		}
		?>
		<col width="85" />
		<?php 
		if($curUserPermision == 'administrator'){
		?>
		<col width="60" />
		<?php
		}else{
			if(empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1){
		?>
		<col width="60" />
		<?php 
			}
		}
		?>
	</colgroup>
	<thead>
	<tr>
		<?php 
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col"><input type="checkbox" name="list_all" id="list_all" border="0" onclick="allselect(this.form);" /></th>
		<?php 
		}
		?>
		<th scope="col">번호</th>
		<th scope="col">제목</th>
		<?php
		if($boardInfo->use_pds == 1){
			$tbl_cols++;
		?>
		<th scope="col">첨부</th>
		<?php 
		}

		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col">작성자</th>
		<?php
		}else{
			if(empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1){
				$tbl_cols++;
		?>
		<th scope="col">작성자</th>
		<?php
			}
		}
		?>
		<th scope="col">작성일</th>
		<?php
		if($curUserPermision == 'administrator'){
			$tbl_cols++;
		?>
		<th scope="col">조회</th>
		<?php
		}else{
			if(empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1){
				$tbl_cols++;
		?>
		<th scope="col">조회</th>
		<?php 
			}
		}
		?>
	</tr>
	<?php 
	}
	?>
	</thead>