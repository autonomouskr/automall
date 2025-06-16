<?php
unset($prepareParm);

$prepareParm[]=0;
$prepareParm[]=5;

$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND q_type='Q' ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total=sizeof($result);
?>
	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">
		Q&A
		<span style="float:right;"><button type="button"class="button-small blue" onClick="go_page('qna');" style="width:80px;height:25px;">전체보기</button></span>	
	</div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="7%;"><col width=""><col width="100px"><col width="100px"></colgroup>
				<tbody>
					<tr>
						<th>번호</th>
						<th>제목</th>
						<th>상태</th>
						<th>등록일자</th>
					</tr>
			<?php
			if($s_total>'0'){
				foreach($result as $i=>$data) {
					$num = $s_total - $i; //번호

					if($data->q_status=='answer'){
						$qStatus="&nbsp;&nbsp;&nbsp;<button type=\"button\" class=\"button-small-fill green default-cursor\">답변완료</button>";
						$ansData = $wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$data->goods_idx."' AND q_type='A' AND q_parent='".$data->idx."'");
					}
					else{
						$qStatus="&nbsp;&nbsp;&nbsp;<button type=\"button\" class=\"button-small-fill orange default-cursor\">답변대기</button>";
						unset($ansData);
					}

					if($data->q_secret=='on') $qSecret="&nbsp;<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_secret.png' align='absmiddle' />";
					else $qSecret="";
			?>
					<tr style="height:40px;">
						<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_qna&viewIdx=<?php echo $data->idx;?>"><?php echo $num;?></a></td>
						<td><a href="admin.php?page=bbse_commerce_qna&viewIdx=<?php echo $data->idx;?>"><?php echo $data->q_subject;?></a><?php echo $qSecret;?></td>
						<td style="text-align:center;"><?php echo $qStatus;?></td>
						<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_qna&viewIdx=<?php echo $data->idx;?>"><?php echo date("Y-m-d",$data->write_date);?></a></td>
					</tr>
			<?php
				}
			}
			else{
			?>
					<tr>
						<td style="height:40px;text-align:center;" colspan="4">등록 된 상품문의가 존재하지 않습니다.</td>
					</tr>
			<?php 
			}
			?>
				</tbody>
			</table>
		</div>
	</div>
