<?php
require('../../../../wp-load.php');
global $wpdb;
get_header();
$id = get_current_user_id();
$user_id = get_userdata($id)->user_login;
$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$user_id."'");
$currUserClass = $member[0]->user_class;

$total = $_GET['total'];
?>
<style>
#header,.mb_top,#allcategory,.pc_wrap,#bbs_top_banner,#footer,#pageHead,
#pageFoot,.goOnTop{
	display: none;
}
#paper_coupon_wrap{
	border: 1px solid #dedede;
    padding: 10px 15px;
    margin: 0 20px;
    text-align: center;
} 
#paper_coupon_wrap h2{
	margin: 0 0 5px;
    font-size: 15px;
    letter-spacing: -1px;
    color: #333;9
    font-weight: bold;
}
#paper_coupon_wrap input[type="text"]{
	width: 50%;
}
#coupon_apply_wrap{
	    margin: 0;
}
#coupon_apply_wrap h1{
	top: auto;
    display: block;
    position: static;
    left: auto;
    margin: 0 0 10px;
    background: #424242;
    color: #fff;
    font-size: 16px;
    letter-spacing: -1px;
    font-weight: bold;
    padding: 5px 10px;
}
#coupon_apply_wrap table{
	width: 100%;
    font-size: 13px;
    border: none;
    border-collapse: collapse;
}
#coupon_apply_wrap th{
	text-align: center;
    font-weight: bold;
    color: #000;
    border-bottom: 1px solid #333;
    padding: 5px 0;
}
#coupon_apply_wrap td{
    padding: 5px;
	text-align: center;
	border-bottom: 1px solid #ededed;
}
#coupon_apply_wrap .btn_wrap{
	text-align: center;
	margin: 20px 0;
}
#coupon_apply_wrap .btn_wrap button,
#paper_coupon_wrap input[type="submit"]{
	border: 1px solid #dedede;
	padding: 5px 10px;
}
#coupon_apply_wrap .btn_wrap button.submit,
#paper_coupon_wrap input[type="submit"]{
	background: #424242;
    border-color: #424242;
    color: #fff;
}
</style>
<?php
//종이쿠폰 등록
if($_POST){
	//여부확인
	$paper_code = $wpdb->get_row('
		SELECT	*
		FROM	bbse_commerce_paper_coupon
		WHERE	code = "'.$_POST['paper_coupon'].'"
	');
	if(empty($paper_code)){
		echo '
			<script>
				alert("존재하지 않은 쿠폰코드입니다.");
				location.href = document.referrer;
			</script>
		';
	}
	elseif(!empty($paper_code->user)){
		echo '
			<script>
				alert("이미 등록된 쿠폰코드입니다.");
				location.href = document.referrer;
			</script>
		';
	}
	//등록
	$wpdb->query('
		UPDATE 	bbse_commerce_paper_coupon
		SET		user="'.$user_id.'"
		WHERE	code = "'.$_POST['paper_coupon'].'"
	');
	echo '
		<script>
			alert("등록되었습니다.");
			location.href = document.referrer;
		</script>
	';
}
?>

<div id="coupon_apply_wrap">
		<?php
			$items = explode(',', $_GET['item']);
			
			$date = new DateTime();
			$date_result = $date->format('Y-m-d');
			
			$item_sql = '1 ';
			foreach ($items as $key => $value) {
				$item_sql .= " AND product LIKE '%\"".$value."\"%'";
			}
      		//사용가능 쿠폰
      		$sql = '
				SELECT	*
				FROM	bbse_commerce_coupon
				WHERE	product_type = "all"
                  AND user_class = "'.$currUserClass.'"
                  AND edate >= date_format("'.$date_result.'" , "%Y%m%d")
					OR
						(product_type="noall" 
							AND ('.$item_sql.'))
			';
      		$coupons = $wpdb->get_results($sql);
      		
      		$avaliablecoupon = [];
      		foreach ($coupons as $key => $value) {
      		    $coupon = unserialize($value->user_ids);
      		    if(in_array($user_id, $coupon)){
      		        array_push($avaliablecoupon,$value->idx);
      		    }
      		}
      		
      		$escaped = array_map(function($item) {
      		    return "'" . addslashes($item) . "'";
      		}, $avaliablecoupon);
      		
            $in = implode(',', $escaped);
      		
      		$sqlUse = '
				SELECT	*
				FROM	bbse_commerce_coupon
				WHERE	product_type = "all"
                  AND user_class = "'.$currUserClass.'"
                  and idx in ('.$in.')
                  AND edate >= date_format("'.$date_result.'" , "%Y%m%d")
					OR
						(product_type="noall"
							AND ('.$item_sql.'))
			';
      		
      		$coupons_2 = $wpdb->get_results($sqlUse);
      		
			$use_coupons = $wpdb->get_results('
				SELECT	coupon_id
				FROM	bbse_commerce_coupon_log
				WHERE	user = "'.$user_id.'"
			');
			$use_coupons_arr = array();
			foreach ($use_coupons as $key => $value) {
				$use_coupons_arr[]= $value->coupon_id;
			}
			$coupon_cnt = 0;
			foreach ($coupons as $key => $value) {
				if(in_array($value->idx, $use_coupons_arr)) continue;
				$coupon_cnt++;
			}
      		
			//종이쿠폰
			$sql = '
				SELECT	*
				FROM	bbse_commerce_paper_coupon 
				WHERE	user = "'.$user_id.'" AND status IS NULL"
			';
      		$paper_coupons = $wpdb->get_results($sql);
		?>
		<h1>할인쿠폰 적용하기</h1>
		<table>
			<thead>
				<tr>
					<th>쿠폰명</th>
					<th>할인내용</th>
					<th>최소주문금액</th>
					<th>적용하기</th>	
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($coupons_2 as $key => $value) {
						if(in_array($value->idx, $use_coupons_arr)) continue;
						echo '
							<tr>
								<td>'.$value->name.'</td>
								<td>'.$value->discount.$value->discount_type.' 할인</td>
								<td style="color:blue;">'.number_format($value->min_money). ' 원</td>
								<td>'
                ?>
    
                <?php  if($value->min_money >= $total) { 
                            echo '<div><input type="radio" class="coupon_sel" name="apply_sel'.$value->idx.'" value="" disabled /> 즉시할인
                            <input type="hidden" name="c_id" value="'.$value->idx.'" />
                            <input type="hidden" name="c_discount" value="'.$value->discount.'" />
                            <input type="hidden" name="c_discount_type" value="'.$value->discount_type.'" />
                            </div>
                            <div>
                            <input type="radio" class="coupon_sel" name="apply_sel'.$value->idx.'" value="off" disabled checked/> 적용안함
                            </div>'
                            ;
                        
                        }
                        else{
                            echo '<div><input type="radio" class="coupon_sel" name="apply_sel'.$value->idx.'" value="on"  /> 즉시할인
                            <input type="hidden" name="c_id" value="'.$value->idx.'" />
                            <input type="hidden" name="c_discount" value="'.$value->discount.'" />
                            <input type="hidden" name="c_discount_type" value="'.$value->discount_type.'" />
                            </div>
                            <div>
                            <input type="radio" class="coupon_sel" name="apply_sel'.$value->idx.'" value="off" checked /> 적용안함
                            </div>';
                }
                ?>
				<?php 
							'</td>
						</tr>
					';
					}
					foreach ($paper_coupons as $key => $value) {
						echo '
							<tr>
								<td>종이쿠폰</td>
								<td>'.$value->code.'</td>
								<td>'.$value->discount.'원 할인</td>
								<td>
									<div>
										<input type="radio" class="pcoupon_sel" name="apply_selp'.$value->idx.'" value="on" /> 즉시할인
										<input type="hidden" name="c_id" value="'.$value->idx.'" />
										<input type="hidden" name="c_discount" value="'.$value->discount.'" />
									</div>
									<div>
										<input type="radio" class="pcoupon_sel"name="apply_selp'.$value->idx.'" value="off"  checked /> 적용안함
									</div
								</td>
							</tr>';
					}
					if($coupon_cnt < 1 && count($paper_coupons) < 1){
						echo '
							<tr>
								<td colspan="4">사용가능한 쿠폰이 존재하지 않습니다.</td>
							</tr>
						';
					}
				?>
			</tbody>
		</table>
		<div class="btn_wrap">
			<button class="bbtn submit" id="apply_coupon">쿠폰적용</button>
			<input type="hidden" name="total" value="" />
			<input type="hidden" name="coupon" value="" />
			<input type="hidden" name="pcoupon" value="" />
			<input type="hidden" name="order_total" id="order_total" value="<?php echo $total; ?>" />
			<button class="bbtn cancel">취소</button>
		</div>
<!-- 		<div id="paper_coupon_wrap">
			<h2>종이쿠폰 등록하기</h2>
			<form method="post">
				<input type="text" name="paper_coupon" />
				<input type="submit" value="등록하기" />
			</form>
		</div> -->
		</div>
		<script>
			jQuery(document).ready(function($) {
			$('.mb_top_top').css('display','none');
				$('.cancel').click(function(){
					self.close();
				});
				$('.pcoupon_sel').change(function(){
					var pcnt = 0;
					var this_sel = $(this);
					$('.pcoupon_sel:checked').each(function(){
						if($(this).val() == 'on' && ++pcnt > 1){
							alert("종이쿠폰은 1개만 사용가능합니다.");
							this_sel.attr("checked", false);
							this_sel.parent().siblings('div').find('.pcoupon_sel').prop("checked", true);
							return false;
						}
					});
				});
				$('#apply_coupon').click(function(){
				
					var total = 0;
					var c_id = [];
					var pc_id = [];
					
					let len = $('input[name="apply_sel<?php echo $value->idx;?>"]:checked').length;
					
					if(len < 1){
						alert("적용 가능한 쿠폰이 없습니다.");
						return;
					}
					
					$('.coupon_sel:checked').each(function(){
					
						if($(this).val() == 'on'){
							c_id.push($(this).siblings('input[name="c_id"]').val());
							var discount = parseInt($(this).siblings('input[name="c_discount"]').val());
							if($(this).siblings('input[name="c_discount_type"]').val() == '%'){
								var order_total = parseInt($('#order_total').val());
								discount = order_total * (discount / 100);
							}
							total += Math.round(discount);
						}
					});
					$('.pcoupon_sel:checked').each(function(){
						if($(this).val() == 'on'){
							pc_id.push($(this).siblings('input[name="c_id"]').val());
							var discount = parseInt($(this).siblings('input[name="c_discount"]').val());
							total += Math.round(discount);
						}
					});
					$(opener.document).find('input[name="coupon_total"]').val(total);
					$(opener.document).find('input[name="coupon"]').val(c_id);
					$(opener.document).find('input[name="pcoupon"]').val(pc_id);
					$(opener.document).find('input[name="coupon_discount"]').val(total);
					$(opener.document).find('#coupon_discount_1').text(numberWithCommas(total));
					$(opener.document).find('#payview_coupon_price').text(numberWithCommas(total));
					var deli = parseInt($(opener.document).find('input[name="coupon_delivery_price"]').val());
					var user_discount = parseInt($(opener.document).find('input[name="user_discount"]').val());
					$(opener.document).find('#payview_total_price').text(numberWithCommas(parseInt($('#order_total').val()) - total + deli - user_discount));
					$(opener.document).find('#payview_total_goods_earn_price').text(numberWithCommas(parseInt($('#order_total').val()) - total - user_discount));
					self.close();
				});
			});
			function numberWithCommas(x) {
			    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
		</script>
<?php
get_footer();
?>