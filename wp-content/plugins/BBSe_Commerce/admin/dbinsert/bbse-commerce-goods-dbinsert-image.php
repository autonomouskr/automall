<?php
if(!$_REQUEST['mType']) $mType='step01';
else $mType=$_REQUEST['mType'];
?>

	<script>
		//콤마찍기
		function str_comma(str) {
			str = String(str);
			return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
		}

		jQuery(document).ready(function() {
		  //사용함/사용안함
		  jQuery('span.stepUseCheck').click(function(){
			var $status    = jQuery(this).data('use');
			var $target    = jQuery(this).data('target');
			if ($status == 'yes'){// 활성이면 비활성시키고 TR 감춤
				jQuery(this).data('use','no');
				var $btn = jQuery(this).find('img').attr('src').replace("yes", "no");
				jQuery(this).find('img').attr('src', $btn);
				jQuery('.'+$target).css('display','none');
			}
			else if ($status == 'no'){ // 비활성이면 활성시키고 TR 보여줌
				jQuery(this).data('use','yes');
				var $btn = jQuery(this).find('img').attr('src').replace("no.","yes.");
				jQuery(this).find('img').attr('src', $btn);
				jQuery('.'+$target).css('display','block');
			}
		  });
		});

		function convertingStart(aIdx){
			var aIdx=jQuery("#aIdx").val();
			var targetCnt=jQuery("#targetCnt").val();
			var endProcess=true;

			for(i=0;i<targetCnt;i++){
				if(!jQuery(".targetClass_"+i).text()){
					endProcess=false;
				}
			}

			if(!endProcess){
				alert("[이미지 변환중] 잠시만 기다려 주세요.        ");
			}
			else{
				if(aIdx>0){
					document.location.href="admin.php?page=bbse_commerce_goods_dbinsert&cType=image&aIdx="+aIdx;
				}
				else{
					jQuery(".convertImgList").hide();
					jQuery("#imgConvertMsg").html("<span class='emRed'><strong>* 이미지 변환 목록이 존재하지 않습니다.</strong></span>");
					alert("이미지 변환 목록이 존재하지 않습니다.        ");
				}
			}
		}

		function imageConverting(tIdx){
			jQuery.ajax({
				type:'post',
				url:'<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goods-dbinsert.exec.php',
				data:{mType:"imgConverting", tIdx:tIdx},
				async:true,
				success:function(data){
					//alert("Response Data : "+data);
					var result=data.split("|||");
					var rtnId="idx_"+result['1'];

					if(result['0']=='success'){
						jQuery("#"+rtnId).html(result['2']);
					}
				},
				error: function() {
					var rtnId="idx_"+tIdx;
					jQuery("#"+rtnId).html("<span style='color: #ED1C24;'>[실패!] 서버 통신 오류</span>");
				}
			});
		}
	</script>

	<div class="borderBox">
		<a href="http://license.bbsetheme.com/download_zip/bbse_goods_csv_sample.zip"><button type="button" class="button-small red" style="height:25px;margin-left:50px;float:right;">대량 상품 등록 CSV 샘플 다운로드</button></a>
		*  CSV 파일의 '상품이미지(필수)' 열(Column)에 등록 된 이미지를 wp-content/uploads/goods-images 디렉토리 내에 FTP를 이용하여 업로드 해 주세요.<br />
		- 대량 상품등록(CSV)의 이미지 변환은 CSV 파일의 데이터 저장 후 작업을 진행해 주세요.<br />
		- 상품이미지의 파일 형식은 JPG/PNG 형식만을 지원하며, 가로/세로 800px 이상의 이미지를 사용해 주세요.<br/>
		* <span class='emRed'>대량 상품등록(CSV) 메뉴얼 바로가기 : </span><a href="http://www.bbsecommerce.com/commerce-plugin-manual/" target="_blank"><span style='color:#4C99BA;text-decoration:none;'>http://www.bbsecommerce.com/commerce-plugin-manual/</span></a>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<div class="imagesStep01">

	<?php
		$aIdx=($_REQUEST['aIdx']>'0')?$_REQUEST['aIdx']:"";

		if(!$aIdx){
			$query = $wpdb->get_results("SELECT * FROM bbse_commerce_csv_goods WHERE idx<>'' ORDER BY idx DESC");
			foreach($query as $row){
				$goodsCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
				if($goodsCnt<='0'){
					$wpdb->query("DELETE FROM bbse_commerce_csv_goods WHERE idx='".$row->idx."' AND goods_idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
				}
			}
		}

		if($aIdx){
			$transCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_csv_goods WHERE idx<>'' AND idx<='".$aIdx."'");
			$transData = $wpdb->get_row("SELECT A.idx, B.goods_name, A.goods_img FROM bbse_commerce_csv_goods AS A, bbse_commerce_goods AS B  WHERE A.idx<>'' AND A.idx<='".$aIdx."' AND A.goods_idx=B.idx AND B.goods_name<>'' ORDER BY A.idx DESC LIMIT 1");
			if($transCnt<=0) $aIdx="";
		}
		else{
			$transCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_csv_goods WHERE idx<>''");
			$transData = $wpdb->get_row("SELECT A.idx, B.goods_name, A.goods_img FROM bbse_commerce_csv_goods AS A, bbse_commerce_goods AS B  WHERE A.idx<>'' AND A.goods_idx=B.idx AND B.goods_name<>'' ORDER BY A.idx DESC LIMIT 1");
		}
	?>
		<input type="hidden" name="transCnt" id="transCnt" value="<?php echo $transCnt;?>" />

		<input type="hidden" name="totalCnt" id="totalCnt" value="0" /><!--전체 처리 개수-->
		<input type="hidden" name="successImg" id="successImg" value="0" /><!--변환 완료 개수-->
		<input type="hidden" name="notMatchInfo" id="notMatchInfo" value="0" /><!--상품 정보 없음 개수-->
		<input type="hidden" name="notExistImg" id="notExistImg" value="0" /><!--이미지 없음 개수-->

		<div class="titleH5" style="margin:20px 0 10px 0; ">상품등록(CSV) 이미지 변환 <span class="stepUseCheck" id="stepUseCheck-01" data-use="yes" data-target="imagesStep01-contents" style="cursor:pointer;margin-left:60px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/switch_yes.png" align="absmiddle" /></span></div>

		<div class="imagesStep01-contents">
			<div class="clearfix" style="margin-top:30px"></div>
			<div id="imgConvertMsg" class="borderBox" style="background-color:#ffffff;line-height:30px;">
			<?php if($transCnt<=0){?>
				<span class='emRed'><strong>* 이미지 변환 목록이 존재하지 않습니다.</strong></span>
			<?php }elseif($transCnt==1){?>
				<span class='emRed'><strong>* 총 1건</strong></span>의 이미지 변환 목록이 존재합니다.</strong></span><br />- <?php echo $transData->goods_name;?> (<?php echo $transData->goods_img;?>)
			<?php }else{?>
				<span class='emRed'><strong>* 총 <?php echo number_format($transCnt);?>건</strong></span>의 이미지 변환 목록이 존재합니다.<br />- <?php echo $transData->goods_name;?> (<?php echo $transData->goods_img;?>)외 <?php echo number_format($transCnt-1);?>건
			<?php }?>
			</div>

			<div id="btn_img_converting" style="margin:40px 0;text-align:center;">
				<button type="button" onClick="<?php echo ($transCnt<=0)?"alert('이미지 변환 목록이 존재하지 않습니다.      ');":"convertingStart();";?>" style="width:250px;color: #137caa;border-color: #4c99ba !important;background-color: #ffffff;font-size: 12px;font-weight: bold;font-family: '돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;border: 1px solid #b4b4b4;border-top: 3px solid #b4b4b4;height: 32px;line-height: 29px;padding: 0 20px 0;margin: 0 1px;text-decoration: none;display: inline-block;overflow: visible;zoom: 1;-webkit-box-sizing: border-box;cursor: pointer;border-radius: 3.1px;box-shadow: 0 3px 2px -1px #eee;text-shadow: -1px -1px #fff;"> 상품등록(CSV) 이미지 변환 </button>
			</div>

	<?php 
	$targetCnt=0;
	if($aIdx>'0'){
		$csvCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_csv_goods WHERE idx<='".$aIdx."' ORDER BY idx DESC");
	?>
			<div class="convertImgList" style="width:<?php echo ($csvCnt>50)?"49":"100";?>%;float:left;">
				<table style="width: 100%;border-collapse: collapse;border-top: 1px solid #4c99ba;border: 1px solid #4c99ba;">
					<colgroup><col width=""><col width=""><col width=""></colgroup>
					<tr>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">상품명</th>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">이미지 파일명</th>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">이미지 변환 결과</th>
					</tr>
					<?php
					$query = $wpdb->get_results("SELECT * FROM bbse_commerce_csv_goods WHERE idx<='".$aIdx."' ORDER BY idx DESC LIMIT 50");
					foreach($query as $row){
						$goodsName=$wpdb->get_var("SELECT goods_name FROM bbse_commerce_goods WHERE idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
						if(!$goodsName){
							$wpdb->query("DELETE FROM bbse_commerce_csv_goods WHERE idx='".$row->idx."' AND goods_idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
							continue;
						}
					?>
					<tr>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><?php echo $goodsName;?></td>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><?php echo $row->goods_img;?></td>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><span id="idx_<?php echo $row->idx;?>" class="targetClass_<?php echo $targetCnt;?>"></span></td>
					</tr>
					<?php
						echo "<script>imageConverting(".$row->idx.");</script>";
						sleep(0.1);
						$aIdx=$row->idx;
						$targetCnt++;
					}
					?>
				</table>
			</div>
		<?php
		if($csvCnt>50){
			$aIdx--;
		?>
			<div class="convertImgList" style="width:49%;float:left;margin-left:2%;">
				<table style="width: 100%;border-collapse: collapse;border-top: 1px solid #4c99ba;border: 1px solid #4c99ba;">
					<colgroup><col width=""><col width=""><col width=""></colgroup>
					<tr>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">상품명</th>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">이미지 파일명</th>
						<th style="padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #f0f0f0;">이미지 변환 결과</th>
					</tr>
					<?php
					$query = $wpdb->get_results("SELECT * FROM bbse_commerce_csv_goods WHERE idx<='".$aIdx."' ORDER BY idx DESC LIMIT 50");
					foreach($query as $row){
						$goodsName=$wpdb->get_var("SELECT goods_name FROM bbse_commerce_goods WHERE idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
						if(!$goodsName){
							$wpdb->query("DELETE FROM bbse_commerce_csv_goods WHERE idx='".$row->idx."' AND goods_idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
							continue;
						}
					?>
					<tr>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><?php echo $goodsName;?></td>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><?php echo $row->goods_img;?></td>
						<td style="text-align:center;padding: 5px;line-height: 28px;font-size: 12px;border-bottom: 1px solid #dfdfdf;background: #ffffff;"><span id="idx_<?php echo $row->idx;?>" class="targetClass_<?php echo $targetCnt;?>"></span></td>
					</tr>
					<?php
						echo "<script>imageConverting(".$row->idx.");</script>";
						sleep(0.1);
						$aIdx=$row->idx;
						$targetCnt++;
					}
					?>
				</table>
			</div>
		<?php }?>
	<?php }?>

			<?php 
			if(!$aIdx) $aIdx=(!$aIdx && $transData->idx>'0')?$transData->idx:"";
			else $aIdx--;
			?>

			<input type="hidden" name="aIdx" id="aIdx" value="<?php echo $aIdx;?>" /><!--시작 위치-->
			<input type="hidden" name="targetCnt" id="targetCnt" value="<?php echo $targetCnt;?>" /><!--시작 위치-->
		</div>
	</div>
	<div class="clearfix" style="height:20px;"></div>
