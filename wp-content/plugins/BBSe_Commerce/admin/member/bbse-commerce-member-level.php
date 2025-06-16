<script>
	jQuery(function() {
		jQuery("#member-level-detail").hide();
		jQuery(".member-level-add").bind('click', function() {
			jQuery("#member-level-detail").fadeIn('fast');
		});
	});
	function levelProc(mode,no) {
		if(mode=="add") {
			if(jQuery("#class_name").val()=="") {
				alert("등급명을 입력해주세요.");
				jQuery("#class_name").focus();
				return;
			}
		}else if(mode=="mod") {
			if(jQuery("#class_name"+no).val()=="") {
				alert("등급명을 입력해주세요.");
				jQuery("#class_name"+no).focus();
				return;
			}else{jQuery("#procNo").val(no);}
		}else{
			if(!confirm(jQuery("#class_name"+no).val()+" 등급을 삭제하시겠습니까?")) {
				return;
			}else{jQuery("#procNo").val(no);}
		}
		jQuery("#tMode").val(mode);
		jQuery("#levelFrm").attr("action","<?=BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-member-level.exec.php");
		jQuery("#levelFrm").submit();
	}
</script>
<div>
	<?php
	if($_GET['tMode']=="add"){$msgTitle = "추가";}
	else if($_GET['tMode']=="mod"){$msgTitle = "수정";}
	else if($_GET['tMode']=="del"){$msgTitle = "삭제";}
	else{$msgTitle = "";}
	if($msgTitle){echo '<div id="message" class="updated fade"><p><strong>등급이 정상적으로 '.$msgTitle.'되었습니다.</strong></p></div>';}
	?>

	<div class="borderBox">
		* 회원 등급을 관리할수 있습니다. <a href="javascript:;" class="add-new-h2 member-level-add">등급추가</a>
	</div>

	<div style="width:100%;">
		<form id="levelFrm" name="levelFrm" method="post">
		<input type="hidden" id="tMode" name="tMode" value="">
		<input type="hidden" id="procNo" name="procNo" value="">
		<div style="float:left;width:80%;">

			<table class="dataTbls normal-line-height collapse" style="width:95%;">
			<colgroup><col width="50px"><col width=""><col width="150px"><col width="300px"><col width="150px"><col width="130px"></colgroup>
			<tr>
				<th>번호</th>
				<th>회원등급</th>
				<th>특별회원가</th>
				<th>자동등업조건</th>
				<th>구매할인율</th>
				<th>관리</th>
			</tr>
			<?php
				$result = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no DESC");
				$total = count($result);
				foreach($result as $i => $data){
			?>
			<tr valign="top">
				<td align="center"><?php echo number_format($total-$i)?></td>
				<td align="center">
					<?php if($data->no!="1" && $data->no!="2" && $data->no!="3"){?>
					<input type="text" id="class_name<?=$data->no?>" name="class_name<?=$data->no?>" value="<?php echo stripslashes($data->class_name)?>" style="width:90%;" />
					<?php }else{echo stripslashes($data->class_name);?>
					<input type="hidden" id="class_name<?=$data->no?>" name="class_name<?=$data->no?>" value="<?php echo stripslashes($data->class_name)?>" />
					<?php }?>
				</td>
				<td align="center">
					<?php if($data->no!="1" && $data->no!="2"){?>
					<label><input type='radio' id='use_sale<?=$data->no?>' name='use_sale<?=$data->no?>' value='Y' style="border:0px;"<?php if(!empty($data->use_sale) && $data->use_sale == "Y") echo " checked"?>>사용&nbsp;&nbsp;</label>
					<label><input type='radio' id='use_sale<?=$data->no?>' name='use_sale<?=$data->no?>' value='N' style="border:0px;"<?php if(!empty($data->use_sale) && $data->use_sale == "N") echo " checked"?>>사용안함</label>
					<?php }else{echo "-";}?>
				</td>
				<td align="center">
					<?php if($data->no!="1" && $data->no!="2"){?>
					<label>총구매금액</label>
					<input style="display: inline-block;width: 70px;" type="text" name="mem_auto_total<?=$data->no?>" id="mem_auto_total" value="<?php echo $data->auto_total; ?>" />
					<label>총구매횟수</label>
					<input style="display: inline-block;width: 70px;" type="text" name="mem_auto_cnt<?=$data->no?>" id="mem_auto_cnt" value="<?php echo $data->auto_cnt; ?>" />
					<?php } ?>
				</td>
				<td align="center">
					<?php if($data->no!="1" && $data->no!="2"){?>
					<label>할인율</label>
					<input style="display: inline-block;width: 70px;" type="text" name="mem_discount<?=$data->no?>" id="mem_discount" value="<?php echo $data->discount; ?>" />
					<?php } ?>
				</td>
				<td align="center">
					<?php if($data->no!="1" && $data->no!="2"){?>
					<button type="button" class="button-small blue" style="height:25px;" onclick="levelProc('mod',<?=$data->no?>);">수정</button>
					<?php if($data->no!="3"){?><button type="button" class="button-small red" style="height:25px;" onclick="levelProc('del',<?=$data->no?>);">삭제</button><?php }?>
					<?php }else{echo "-";}?>
				</td>
			</tr>
			<?php 
				}
			?>
			<tr><td colspan="6"></td></tr>
			</table>
			
		</div>

		<div id="member-level-detail" style="float:left;width:20%;">
			<table class="dataTbls normal-line-height collapse">
			<colgroup>
				<col width=""><col width=""><col width=""><col width="">
			</colgroup>
			<tr>
				<th width="80" style="padding-left:10px;"><span>등급명</span></th>
				<th><input type="text" id="class_name" name="class_name" value="" style="width:90%;"/></th>
			</tr>
			</table>
			<div style="padding:10px;text-align:center;">
				<button type="button" class="button-small blue" style="width:80px;height:30px;" onclick="levelProc('add');">추가</button>
				<button type="button" class="button-small gray" style="width:80px;height:30px;" onclick="jQuery('#member-level-detail').fadeOut('fast');">취소</button>
			</div>

		</div>
		<div class="des" style="clear: both;
    float: left;
    margin: 10px 0;
    border: 1px solid #dedede;
    background: #fff;
    padding: 15px 15px;
    width: 80%;
    box-sizing: border-box;">
			<p>
				ㆍ자동등업은 고객의 구매확정 주문의 총 구매금액이나 총 구매횟수를 초과하는 시점에 실시됩니다.
				<br>
				ㆍ둘중 하나의 조건만 이용하고 싶을 땐 이용하지 않을 조건의 입력칸에 빈값이나 0을 입력해주세요.
				<br>
				ㆍ구매할인율을 사용하고 싶을 땐 빈값이나 0을 입력해주세요.   
			</p>
		</div>
		</form>
	</div>



</div>