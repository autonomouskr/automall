<?php
/*
 [테마 수정 시 주의사항]
 1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
 업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
 2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
 */

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$s_userId=$_REQUEST['s_userId'];

$costByMonth = $wpdb->get_results("select year(FROM_UNIXTIME(order_date)) year	,month(FROM_UNIXTIME(order_date)) month    , month(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_month    , year(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_year    ,sum(cost_total)    cost_total 
                                        , sum(coupon_discount) coupon_discount
                                        , idx
                                        , sum(accounts_receivable) accounts_receivable
                                     from bbse_commerce_order bco
                                    where user_id = '".$s_userId."'
                                      and order_status <> 'TR'
                                      and order_status in ('OE','DE') 
                                      and payMode = '01' 
                                      and pay_status = 'PW'
                                    group by
                                    	year(FROM_UNIXTIME(order_date)),month(FROM_UNIXTIME(order_date)) ")
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script language="javascript">
	function saveAR(){
	
		tMode = "AR";
		var idxs_len = jQuery("input[name=idx\\[\\]]").length;
		var ars_len = jQuery("input[name=accounts_receivable\\[\\]]").length;
		
		var idxs = [];
		for(var i=0; i<idxs_len; i++){                          
			idxs.push(jQuery("input[name=idx\\[\\]]").eq(i).val());
		}
		
		var ars = [];
		for(var i=0; i<ars_len; i++){                          
			ars.push(jQuery("input[name=accounts_receivable\\[\\]]").eq(i).val());
		}
			
	        
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-order.exec.php',
			//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-order.exec.php',
			data: {tMode:tMode,idxs:idxs,ars:ars}, 
			success: function(data){
				var result = data; 
				if(result=='success'){
					alert('미수금액이 정상적으로 저장되었습니다.');
					location.reload();
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   ");
			}
		});	
	}
	</script>

</head>
<body>
	<div class="wrap">
    	<div class="clearfix" style="height:10px;"></div>
    	<div style="float:right;margin-right:5px;">
    		<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="saveAR();" style="width:100px;"> 저장하기 </button>
    	</div>
    	<div class="clearfix" style="height:10px;"></div>
		<div id="popup-account-list">
			<div class="clearfix" style="height:30px;"></div>

			<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="20%"><col width="20%"><col width="30%"><col width="30%"></colgroup>
				<tr>
					<th style="text-align:center;">주문월</th>
					<th style="text-align:center;">정산월</th>
					<th style="text-align:center;">청구금액</th>
					<th style="text-align:center;">입금금액</th>
				</tr>
			<?php
			if(sizeof($costByMonth)>'0'){
			    foreach($costByMonth as $i=>$data) {
			?>
				<tr>
					<td style="display: none;"> <input type="hidden" name="idx[]" value="<?php echo $data->idx;?>" /></td>				
					<td style="text-align:center;"><?php echo $data->year,"/",$data->month;?></td>
					<td style="text-align:center;"><?php echo $data->pay_year,"/",$data->pay_month;?></td>
					<td style="text-align:center;"><?php echo number_format($data->cost_total);?>원</td>
					<td style="text-align:center;"><input type="text" name="accounts_receivable[]" style="text-align: right;" value='<?php echo number_format($data->accounts_receivable);?>'></td>
				</tr>
			<?php
				}
			}
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">주문내역이 존재하지 않습니다.</td>
				</tr>
			<?php
			}
			?>
			</table>
			<div class="clearfix"></div>
		</div>
	</div>
</body>
</html>
