<?php $nDate=current_time('timestamp');?>
	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">
		사이트 현황
		<span style="float:right;"><?php echo date("Y.m.d",$nDate)." 기준";?></span>	
	</div>

	<div style="margin-top:20px;">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="33.4%"><col width="33.3%"><col width="33.3%"></colgroup>
				<tr>
					<th>구분</th>
					<th>오늘</th>
					<th>이번 달</th>
				</tr>
			<?php 
				$st_dDate = mktime(0, 0 , 0, date("m"), date("d"), date("Y"));
				$en_dDate = mktime(23, 59 , 59, date("m"), date("d"), date("Y"));

				$dOrderCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND order_status<>'CA' AND order_status<>'CE' AND order_status<>'RA' AND order_status<>'RE' AND order_date>='".$st_dDate."' AND order_date<='".$en_dDate."'");
				$dPayTotal=$wpdb->get_var("SELECT sum(cost_total) FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND order_status<>'CA' AND order_status<>'CE' AND order_status<>'RA' AND order_status<>'RE' AND order_date>='".$st_dDate."' AND order_date<='".$en_dDate."'");
				$dMemerCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership WHERE user_no<>'' AND reg_date>='".$st_dDate."' AND reg_date<='".$en_dDate."'");

				$st_mDate = mktime(0, 0 , 0, date("m"), 1, date("Y"));
				$end_day = date("t", $st_mDate);
				$en_mDate = mktime(23, 59 , 59, date("m"), $end_day, date("Y"));

				$mOrderCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND order_status<>'CA' AND order_status<>'CE' AND order_status<>'RA' AND order_status<>'RE' AND order_date>='".$st_mDate."' AND order_date<='".$en_mDate."'");
				$mPayTotal=$wpdb->get_var("SELECT sum(cost_total) FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR' AND order_status<>'CA' AND order_status<>'CE' AND order_status<>'RA' AND order_status<>'RE' AND order_date>='".$st_mDate."' AND order_date<='".$en_mDate."'");
				$mMemerCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership WHERE user_no<>'' AND reg_date>='".$st_mDate."' AND reg_date<='".$en_mDate."'");

				$tMemerCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership WHERE user_no<>''");
			?>
				<tr>
					<td style="text-align:center;">주문접수</td>
					<td style="text-align:center;"><?php echo number_format($dOrderCnt);?> 건</td>
					<td style="text-align:center;"><?php echo number_format($mOrderCnt);?> 건</td>
				</tr>
				<tr>
					<td style="text-align:center;">입금대기 금액</td>
					<td style="text-align:center;"><?php echo number_format($dPayTotal);?> 원</td>
					<td style="text-align:center;"><?php echo number_format($mPayTotal);?> 원</td>
				</tr>
				<tr>
					<td style="text-align:center;">회원수</td>
					<td style="text-align:center;"><?php echo number_format($dMemerCnt);?>명</td>
					<td style="text-align:center;"><?php echo number_format($mMemerCnt);?>명 / 전체회원 (<?php echo number_format($tMemerCnt);?> 명)</td>
				</tr>
			</table>
		</div>
	</div>
