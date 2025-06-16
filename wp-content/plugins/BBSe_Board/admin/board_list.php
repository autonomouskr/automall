<?php
$per_page = (empty($_REQUEST['per_page']))?10:$_REQUEST['per_page'];
$paged = (empty($_REQUEST['paged']))?1:intval($_REQUEST['paged']);
$start_pos = ($paged - 1) * $per_page;
$total = $BBSeBoard->get_board_count();
$total_pages = ceil($total / $per_page);
$add_args = array("page"=>$_GET['page'], "per_page"=>$per_page);

$result = $BBSeBoard->get_board_list($start_pos, $per_page);
?>
<script language="javascript">
function checkExtend(frm, objName){
	var frm = eval('document.' + frm);
	var checkOk = 0;

	for(var i = 0; i < frm.elements.length; i++){
		if(frm.elements[i].name == objName && frm.elements[i].checked == true){
			checkOk += 1;
		}
	}
	return checkOk;
}

function delete_board(tNo){
	var frm = document.boardList;

	if(tNo > 0){
		if(confirm('해당 게시판을 삭제하시겠습니까?')){
			frm.delNo.value = tNo;
			frm.action = 'admin.php?page=bbse_board';
			frm.submit();
		}
	}
}

function delete_batch_board(sNo){
	var chkNum = 0;
	var frm = document.boardList;

	if(eval("frm.tBatch" + sNo).value == -1){
		alert('일괄 작업을 선택해주세요.');
		eval("frm.tBatch" + sNo).focus();
		return false;
	}
	chkNum = checkExtend('boardList', 'check[]');
	if(chkNum <= 0){
		alert('삭제하실 게시판을 선택해주세요.');
		return false;
	}
	if(confirm('선택하신 게시판을 삭제하시겠습니까?')){
		frm.action = 'admin.php?page=bbse_board';
		frm.submit();
	}
}
</script>
<div class="wrap">
	<div id="bbse_box">
		<div class="inner">
			<div class="guide_top">
				<span class="tl"></span><span class="tr"></span><span class="manual_btn"><a href="http://manual.bbsetheme.com/bbse-board" target="_blank"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/btn_manual.png" /></a></span>
				<a href="#"><span class="logo">BBS</span><span class="logo_board">e-Board</span><span class="logo_version"><?php echo BBSE_BOARD_VER?></span></a>
			</div>
			<div id="content">
				<!-- 내용 start -->
				<div class="tit">게시판 리스트<a href="admin.php?page=bbse_board_config" class="add-new-h2">게시판 추가하기</a></div>
				<div class="cate_all">
					<a href="admin.php?page=bbse_board">모두 <span>(<?php echo $total?>)</span></a>
				</div>
				<form name="boardList" method="post">
				<input type="hidden" name="delNo" id="delNo" value="">
				<!--
				<input type="hidden" id="paged" name="paged" value="<?php //echo $paged?>">
				<input type="hidden" id="add_args" name="add_args" value="<?php //echo base64_encode(serialize($add_args))?>">
				-->
				<div class="search_all">
					<div class="search_list" style="margin-bottom:5px;">
						<select name="tBatch1">
							<option value="-1">일괄 작업</option>
							<option value="remove">게시판 삭제</option>
						</select>
						<input type="button" name="btn_batch" id="doaction" class="button-secondary" value="적용" onclick="delete_batch_board(1);">
					</div>
				</div>
				<table class="table_mem" cellspacing="0">
				<colgroup>
					<col width="50px" style="padding-left:10px"><col width=""><col width=""><col width=""><col width=""><col width=""><col width="">
				</colgroup>
				<thead>
				<tr>
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
					<th scope="col" width="150" class="manage-column sortable asc"><a><span>게시판명</span></a></th>
					<th scope="col" class="manage-column">게시판 코드(Shortcode)</th>
					<th scope="col" width="110" class="manage-column">스킨</th>
					<th scope="col" width="120" class="manage-column">읽기권한</th>
					<th scope="col" width="120" class="manage-column">쓰기권한</th>
					<th scope="col" width="140" class="manage-column">생성일자</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
					<th scope="col" width="150" class="manage-column sortable asc"><a><span>게시판명</span></a></th>
					<th scope="col" class="manage-column">게시판 코드(Shortcode)</th>
					<th scope="col" width="110" class="manage-column">스킨</th>
					<th scope="col" width="120" class="manage-column">읽기권한</th>
					<th scope="col" width="120" class="manage-column">쓰기권한</th>
					<th scope="col" width="140" class="manage-column">생성일자</th>
				</tr>
				</tfoot>
				<tbody id="the-list">
				<?php
				if($total <= 0){
				?>
				<tr valign="middle">
					<td colspan="7" align="center"><a href="<?php echo BBSE_BOARD_SETUP_URL?>">게시판을 생성해 주세요.</a></td>
				</tr>
				<?php
				}else{

					$bbsemember_roles = array(
						'all'           => '비회원',
						'subscriber'    => '구독자',
						'contributor'   => '기여자',
						'author'        => '글쓴이',
						'editor'        => '편집자',
						'administrator' => '관리자',
						'private'       => '지정사용자'
					);

					foreach($result as $i => $data){
						if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
						else $alternate_class = "";
				?>
				<tr <?php echo $alternate_class?> valign="middle">
					<td scope="row" class="check-column"><input type="checkbox" name="check[]" value="<?php echo $data->board_no?>"></td>
					<td>
						<a class="row-title" href="admin.php?page=bbse_board_config&board_no=<?php echo $data->board_no?>" title="수정"><?php echo $data->boardname?></a>
						<div class="row-actions"><span class='edit'><a href="admin.php?page=bbse_board_config&board_no=<?php echo $data->board_no?>" title="이 게시판 편집하기">편집</a> | </span><span class='trash'><a class='submitdelete' title='이 게시판을 삭제' href="javascript:delete_board('<?php echo $data->board_no?>');">삭제</a></span></div>
					</td>
					<td><input type="text" value="[bbse_board bname=<?php echo $data->boardname?>]" style="width:250px;background-color:#ffffff;" readonly></td>
					<td><?php echo $data->skinname?></td>
					<td><?php echo $bbsemember_roles[$data->l_read]?$bbsemember_roles[$data->l_read]:'전체'?></td>
					<td><?php echo $bbsemember_roles[$data->l_write]?$bbsemember_roles[$data->l_write]:'전체'?></td>
					<td><abbr title="<?php echo date("Y-m-d H:i:s", $data->reg_date)?>"><?php echo date("Y-m-d H:i:s", $data->reg_date)?></abbr></td>
				</tr>
				<?php
					}
				}
				?>
				</tbody>
				</table>
				<div class="search_all">
					<div class="search_list mb40">
						<select name="tBatch2">
							<option value="-1">일괄 작업</option>
							<option value="remove">게시판 삭제</option>
						</select>
						<input type="button" name="btn_batch" id="doaction" class="button-secondary" value="적용" onclick="delete_batch_board(2);">
					</div>
				</div>
				</form>
				<?php echo bbse_board_create_paging($paged, $total_pages, $add_args)?>
				<!-- 내용 end -->
			</div>
			<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
		</div>
	</div>
	<?php global $noticeBoxComment; echo $noticeBoxComment;?>
</div>