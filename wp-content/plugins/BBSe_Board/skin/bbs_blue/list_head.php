<!-- SKIN : <?php echo $boardInfo->skinname?> -->
	<form name="list_frm" method="post">
	<table class="tbl_type" style="width:100%;" border="1" cellspacing="0" summary="게시판의 글 리스트">
	<caption>게시판 리스트</caption>
	<?php 
	// 모바일, 태블릿 접속
	$tbl_cols = 2;
	if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPhone') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'Android') !== false || strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false){
	?>
	<colgroup>
		<?php if($curUserPermision == 'administrator'){?><col style="width:30px" /><?php }?>
		<col />
		<col style="width:85px" />
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
		<?php if($curUserPermision == 'administrator'){?><col style="width:30px" /><?php }?>
		<col style="width:50px" />
		<col />
		<?php if($boardInfo->use_pds == 1){?><col style="width:60px" /><?php }?>
		<?php
		if($curUserPermision == 'administrator'){
		?>
		<col style="width:115px" />
		<?php
		}else{
			if(empty($boardInfo->hidden_writer) || $boardInfo->hidden_writer != 1){
		?>
		<col style="width:115px" />
		<?php 
			}
		}
		?>
		<col style="width:85px" />
		<?php 
		if($curUserPermision == 'administrator'){
		?>
		<col style="width:60px" />
		<?php
		}else{
			if(empty($boardInfo->hidden_hit) || $boardInfo->hidden_hit != 1){
		?>
		<col style="width:60px" />
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