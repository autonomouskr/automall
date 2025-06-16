<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;
$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
$currUserClass = $member[0]->user_class;

$result = $wpdb->get_results("select * from bbse_commerce_coupon bcc where user_class = '".$currUserClass."'");

?>
					<h2 class="page_title">쿠폰내역</h2>	
					<div class="article">
						<ul class="bb_dot_list">
							<li>쿠폰이용: 각 할인쿠폰의 구매조건에 따라 상품구매 시 사용하실 수 있습니다.</li>
							<li>쿠폰제한: 할인쿠폰을 사용하여 주문하신 후 취소/반품하신 경우 해당 쿠폰은 재사용하실 수 없습니다.</li>
						</ul>
					</div>

					<div class="article">
						<h3 class="lv3_title">나의 보유쿠폰 및 사용현황</h3>
						<div class="tb_dp_list mobileHidden">
							<table>
								<caption>보유쿠폰 및 사용현황</caption>
								<colgroup>
									<!-- <col style="width:18%;" />-->
									<col style="width:35%;" />
									<col style="width:10%;" />
									<col style="width:25%;" />
									<col style="width:20%;" />
									<col style="width:10%;" />
								</colgroup>
								<thead>
									<tr>
										<!-- <th scope="col">발행일</th> -->
										<th scope="col">쿠폰명</th>
										<th scope="col">할인</th>
										<th scope="col">유효기간</th>
										<th scope="col">최소주문금액</th>
										<th scope="col">사용여부</th>
									</tr>
								</thead>
								<tbody>
								
								<?php 
								
								if(sizeof($result) > '0'){
								    foreach($result as $i=>$coupon){
								        $userIds = unserialize($coupon->user_ids);
								        if(in_array($currUserID,$userIds)) {
    								        $coupon_log = $wpdb->get_results("SELECT * FROM bbse_commerce_coupon_log WHERE user = '".$currUserID."' AND  coupon_id = '".$coupon->idx."'");
    								?>
    															
    									<tr>
    										<!-- <td><?php echo date("Y.m.d",$coupon->create_date); ?></td> -->
    										<td style="text-align: center;">
    											<span><?php echo $coupon->name; ?></span>
											</td>
    										<td><?php echo $coupon->discount, $coupon->discount_type; ?></td>
    										<?php if($coupon->alldate == 'on'){ ?>
    										<td>기간제한없음</td>
    										<?php }else{?>
    										<td><?php echo $coupon->sdate;?>~<?php echo $coupon->edate; ?></td>
    										<?php }?>
    										<td style="text-align: center;"><?php echo number_format($coupon->min_money) , "원";?></td>
    										<?php if(sizeof($coupon_log) >'0' ){?>
    										<td>사용</td>
    										<?php }else{?>
    										<td>미사용</td>
    										<?php }?>
    									</tr>
    								
    							   <?php }
								    }
								}else{?>
								<tr style="text-align: center;"><td colspan="4">발행된 쿠폰이 존재하지 않습니다.</td></tr>
								<?php }?>
								</tbody>
							</table>
						</div>
						
						<!-- <?php 
								
                            if(sizeof($result) > '0'){
                                foreach($result as $i=>$coupon){
                                    $userIds = unserialize($coupon->user_ids);
                                    if(in_array($currUserID,$userIds)) {
    							        $coupon_log = $wpdb->get_results("SELECT * FROM bbse_commerce_coupon_log WHERE user = '".$currUserID."' AND  coupon_id = '".$coupon->idx."'");
    							?>
    							-->
    							<!-- 
                                <div class="mobile-shown">
                                  <dl class="coupon">
                                    <dt><?php echo $coupon->name; ?></dt>
                                    <!-- <dd><span>발행일</span>2014-06-13</dd> -->
                                    <!-- <dd><span>혜택</span><?php echo $coupon->discount, $coupon->discount_type; ?></dd>
                    				<?php if($coupon->alldate == 'on'){ ?>
                    				<dd><span>유효기간</span>기간제한없음</dd>
                    				<?php }else{?>
                    				<dd><span>유효기간</span><?php echo $coupon->sdate;?>~<?php echo $coupon->edate; ?></dd>
                    				<?php }?>
                        			<?php if(sizeof($coupon_log) >'0' ){?>
                        			<dd><span>사용여부</span>사용</dd>
                        			<?php }else{?>
                        			<dd><span>사용여부</span>미사용</dd>
                        			<?php }?>
                                  </dl>
    							   <?php } 
    						          } 
								}else{?>
								<dd><span>발행된 쿠폰이 존재하지 않습니다.</span></dd>
								<?php 
								}?>
								 -->
								
						
            </div>
