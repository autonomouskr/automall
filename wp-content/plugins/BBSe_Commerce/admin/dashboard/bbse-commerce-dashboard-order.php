<script language="javascript">
	function user_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("회원정보 - "+tData, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-user-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function social_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("소셜로그인 정보", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-social-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function go_page(page){
		window.location.href ="admin.php?page=bbse_commerce_"+page;
	}
</script>
<div class="wrap">
<?php
$prepareParm[]=0;
$prepareParm[]=2;

$sql  = $wpdb->prepare("SELECT idx FROM bbse_commerce_order WHERE idx<>'' ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total=sizeof($result);
?>
	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">
		최근주문내역
		<span style="float:right;"><button type="button"class="button-small blue" onClick="go_page('order');" style="width:80px;height:25px;">전체보기</button></span>	
	</div>

	<div style="margin-top:20px;">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="8%"><col width="180px"><col width=";"><col width="7%"><col width="8%"><col width="8%"><col width="9%"><col width="100px"></colgroup>
				<tr>
					<th>번호</th>
					<th>주문번호</th>
					<th>상품명</th>
					<th>배송비</th>
					<th>최종 결제금액</th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th>진행상태</th>
				</tr>
	<?php 
	if($s_total>'0'){
		foreach($result as $i=>$data) {
			$num = $s_total - $i; //번호
			$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$data->idx."'"); 

			if($oData->order_status=='CA' || $oData->order_status=='CE' || $oData->order_status=='RA'|| $oData->order_status=='RE') $btnColor="red";
			else $btnColor="black";

			if($oData->user_id){
				$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$oData->user_id."'");

				if($mData->name){
					$memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_member.png\" title=\"회원\"><br>".$mData->name."<br /><span style=\"color:#00A2E8;\">(<span onClick=\"user_view('".$mData->user_id."');\" style=\"cursor:pointer;\">".$mData->user_id."</span>)</span></div>";
				}
				else $memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_notextist_member.png\" title=\"회원 정보 없음\"></div>";
			}
			elseif($oData->sns_id && $oData->sns_idx){
				$memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_social.png\" onClick=\"social_view('".$oData->sns_idx."');\" style=\"cursor:pointer;\" title=\"소셜로그인 주문\"></div>";
			}
			else $memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_nomember.png\" title=\"비회원 주문\"></div>";
	?>
				<tr>
					<td style="text-align:center;"><?php echo $num;?></td>
					<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_order&tMode=detail&tData=<?php echo $oData->idx;?>" target="_self" title="주문 상세정보 보기"><span class="titleH5 emBlue"><?php echo $oData->order_no;?></span></a><br />
						<?php if($oData->order_device=='tablet' || $oData->order_device=='mobile'){?>
							<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-mobile.png" title="모바일 주문" />
						<?php }else{?>
							<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-desktop.png" title="데스크탑 주문" />
						<?php }?>
					</td>
					<td>
						<div class="clearfix" style="height:5px;"></div>
					<?php 
						$gResult  = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC");
						foreach($gResult as $j=>$gData) {
							unset($basicOpt);
							unset($addOpt);
							unset($basicImg);

							if($gData->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($gData->goods_basic_img);

							if(!$basicImg['0']){
								$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'"); 

								$imageList=explode(",",$goodsAddImg);
								if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
								else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
							}

							if($oData->delivery_total>'0'){ 
								$deveryAdvance=(!$deliveryData['delivery_charge_payment'] || $deliveryData['delivery_charge_payment']=='advance')?"<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_advance.png\" title=\"배송비 선불 결제\" /><br />":"<span class=\"emRed\"><br /><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_after.png\" title=\"배송비 후불 결제\" /></span><br />";
							}
							else $deveryAdvance="";
					?>
						<table class="dataNormalTbls" style="border:1px solid #DFDFDF;">
							<colgroup><col width="130px"><col width=""></colgroup>
							<tr>
								<td style="vertical-align:top;">
									<div style="width:102px;margin-left:10px;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $gData->goods_idx;?>" target="_blank"><img src="<?php echo $basicImg['0'];?>" class="list-goods-img"></a></div>
									<!-- <div style="background:#F0F0F0;border:1px solid #DFDFDF;width:101px;text-align:center;margin-left:10px;">￦ <?php echo number_format($gData->goods_price);?>원</div> -->
								</td>
								<td style="vertical-align:top;">
									<div class="clearfix" style="height:10px;"></div>
									<?php 
										echo "<div class=\"titleH5\"><a href=\"".esc_url( home_url( '/' ) )."?bbseGoods=".$gData->goods_idx."\" target=\"_blank\">".$gData->goods_name."</a></div><br />";
										$basicOpt=unserialize($gData->goods_option_basic);
										for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
											if($basicOpt['goods_option_title'][$b]=="단일상품") echo "<div style='width:100%'>&nbsp;<div style='float:right;margin-right:10px;'>".number_format($gData->goods_price)."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
											else echo "<div style='width:100%'>".$basicOpt['goods_option_title'][$b]." <span class=\"textFont-11 emBlue\">(+ ".number_format($basicOpt['goods_option_overprice'][$b])."원)</span><div style='float:right;margin-right:10px;'>".number_format($gData->goods_price+$basicOpt['goods_option_overprice'][$b])."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
										}

										$addOpt=unserialize($gData->goods_option_add);
										if(sizeof($addOpt['goods_add_title'])>'0') echo "<hr />";
										for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
											echo "<div style='width:100%'>".$addOpt['goods_add_title'][$a]." <span class=\"textFont-11 emBlue\">(".number_format($addOpt['goods_add_overprice'][$a])."원)</span><div style='float:right;margin-right:10px;'>".number_format($addOpt['goods_add_overprice'][$a])."원 * ".$addOpt['goods_add_count'][$a]."개</div></div>";
										}
									?>
								</td>
							</tr>
						</table>
						<div class="clearfix" style="height:5px;"></div>
					<?php
						}
					?>
					</td>
					<td style="text-align:center;"><?php echo $deveryAdvance;?>(+) <?php echo number_format($oData->delivery_total);?>원</span></td>
					<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_order&tMode=detail&tData=<?php echo $oData->idx;?>" target="_self" title="주문 상세정보 보기"><span class="titleH5 emBlue"><?php echo number_format($oData->cost_total);?>원</span></a><br /><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_earn_red.png" align="absmiddle" title="결제에 사용한 적립금" /> <span class="emRed">(-) <?php echo number_format($oData->use_earn);?>원<br /><?php echo $payHow[$oData->pay_how]?></td>
					<td style="text-align:center;"><?php echo date("Y.m.d H:i:s",$oData->order_date);?></td>
					<td style="text-align:center;">
						<div><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_person_send.png" align="absmiddle" />&nbsp;&nbsp;<?php echo $oData->order_name;?></div>
						<div><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_person_receive.png" align="absmiddle" />&nbsp;&nbsp;<?php echo $oData->receive_name;?></div>
						<div class="clearfix" style="height:20px;"></div>
						<div><?php echo $memberStr;?></div>
					</td>
					<td style="text-align:center;"><button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><?php echo $orderStatus[$oData->order_status];?></button></td>
				</tr>
	<?php
		}
	}
	else{
	?>
				<tr>
					<td style="height:130px;text-align:center;" colspan="10">등록 된 주문정보가 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>
		</div>
	</div>
