<?php
unset($prepareParm);

$prepareParm[]=0;
$prepareParm[]=5;

$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_review WHERE idx<>'' ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total=sizeof($result);
?>
	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">
		고객상품평
		<span style="float:right;"><button type="button"class="button-small blue" onClick="go_page('review');" style="width:80px;height:25px;">전체보기</button></span>	
	</div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="7%;"><col width="15%;"><col width=""><col width="100px"><col width="100px"></colgroup>
				<tbody>
					<tr>
						<th>번호</th>
						<th>별점</th>
						<th>제목</th>
						<th>상태</th>
						<th>등록일자</th>
					</tr>
			<?php
			if($s_total>'0'){
				foreach($result as $i=>$data) {
					$num = ($s_total-$start_pos) - $i; //번호
			?>
					<tr style="height:40px;">
						<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_review&viewIdx=<?php echo $data->idx;?>"><?php echo $num;?></a></td>
						<td style="text-align:center;"><div style="width:83px;height:16px;text-align:left;"><span class="admin_cmt_star cmt<?php echo $data->r_value;?>">별점 <?php echo $data->r_value;?>점/5점</span></div></td>
						<td><?php echo ($data->r_attach_new)?"<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_image_exitst.png\" />":"";?> <a href="admin.php?page=bbse_commerce_review&viewIdx=<?php echo $data->idx;?>"><?php echo $data->r_subject;?></a><?php echo ($data->r_best=='Y')?"&nbsp;&nbsp;&nbsp;<button type=\"button\" class=\"button-small-fill red default-cursor\">베스트상품평</button>":"";?></td>
						<td style="text-align:center;"><?php echo ($data->r_earn_paid=='P')?"<button type=\"button\" class=\"button-small-fill green default-cursor\">승인완료</button>":"<button type=\"button\" class=\"button-small-fill orange default-cursor\">승인대기</button>";?></td>
						<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_review&viewIdx=<?php echo $data->idx;?>"><?php echo date("Y-m-d",$data->write_date);?></a></td>
					</tr>
			<?php
				}
			}
			else{
			?>
					<tr>
						<td style="height:40px;text-align:center;" colspan="5">등록 된 상품평이 존재하지 않습니다.</td>
					</tr>
			<?php 
			}
			?>
				</tbody>
			</table>
		</div>
	</div>
