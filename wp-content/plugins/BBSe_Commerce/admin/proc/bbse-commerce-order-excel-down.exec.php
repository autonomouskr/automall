<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_type=$_REQUEST['s_type'];
$s_keyword=$_REQUEST['s_keyword'];
$s_list=$_REQUEST['s_list'];

$sOption="";

if($s_list){
	if($s_list!='all') $sOption .=" AND order_status='".$s_list."'";
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND order_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND order_date<='".$s_period_2_time."'";
}

if($s_keyword){
	if($s_type){
		$sOption .=" AND ".$s_type." LIKE %s";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
	else{
		$sOption .=" AND (order_no LIKE %s OR order_name LIKE %s OR receive_name LIKE %s OR input_name LIKE %s OR user_id LIKE %s)";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

unset($excel);

# 엑셀파일명 및 시트명
$excel['fileName'] = !$_REQUEST['fileName'] ? "bbse_order_list_".date('Ymd'): $_REQUEST['fileName'];

# 엑셀에 No 번호 출력 여부 결정 default 는 출력
$excel['isNo'] = true;

# 정렬방식 default 는 없음
$excel['sort'] = "";

if(!$_REQUEST['orderCondition1'] && !$_REQUEST['orderCondition2'] && !$_REQUEST['orderCondition3']){
	$excel['sort'] = " ORDER BY idx DESC";
}else{
	# 첫번째 정렬조건이 있는경우
	if($_REQUEST['orderCondition1']){
		$excel['sort'] .= $excel['sort'] ? ", ".$_REQUEST['orderCondition1']." ".$_REQUEST['order_orderCondition1']: " ORDER BY ".$_REQUEST['orderCondition1']." ".$_REQUEST['order_orderCondition1'];
	}
	# 두번째 정렬조건이 있는경우
	if($_REQUEST['orderCondition2'])
	{
		$excel['sort'] .= $excel['sort'] ? ", ".$_REQUEST['orderCondition2']." ".$_REQUEST['order_orderCondition2']: " ORDER BY ".$_REQUEST['orderCondition2']." ".$_REQUEST['order_orderCondition2'];
	}
	# 세번째 정렬조건이 있는경우
	if($_REQUEST['orderCondition3'])
	{
		$excel['sort'] .= $excel['sort'] ? ", ".$_REQUEST['orderCondition3']." ".$_REQUEST['order_orderCondition3']: " ORDER BY ".$_REQUEST['orderCondition3']." ".$_REQUEST['order_orderCondition3'];
	}
}

# No 출력 여부 결정
$excel['isNo'] = $_REQUEST['willContainNO']=="Y" ? true: false;
# 출력 필드 데이터
$excel['fields'] = $_REQUEST['selFieldName'];


$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE idx<>''".$sOption.$excel['sort'], $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>''".$sOption, $prepareParm);
$total = $wpdb->get_var($s_total_sql);    // 총 상품수

# 엑셀 출력 해더 설정
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Content-Type:  application/vnd.ms-excel; charset=UTF-8\r\n");
header("Content-Disposition: attachment; filename=".rawurlencode($excel['fileName']).".xls\r\n\r\n");
header("Content-Description: PHP4 Generated Data");
header("Content-Transfer-Encoding: binary\r\n");
header("Pragma: no-cache");
header("Expires: 0");

$fields=Array(
	"order_no"		=>"주문번호",
	"order_status"	=>"주문상태",
	"pay_how"		=>"결제방법",
	"use_earn"		=>"적립금사용",
	"cost_total"	=>"결제금액",
	"order_name"	=>"주문자명",
	"order_phone"	=>"주문자 연락처",
	"order_hp"		=>"주문자 핸드폰",
	"receive_name"	=>"받으실분",
	"order_addr"	=>"배송주소",
	"receive_phone"	=>"연락처",
	"receive_hp"	=>"핸드폰",
	"order_comment"	=>"남기실말씀",
	"order_detail"	=>"주문상품",
	"order_date"	=>"주문일자",
	"delivery_no"	=>"송장번호"
);
$fieldsWidth=Array(
	"order_no"		=>"150",
	"order_status"	=>"100",
	"pay_how"		=>"100",
	"use_earn"		=>"70",
	"cost_total"	=>"100",
	"order_name"	=>"100",
	"order_phone"	=>"100",
	"order_hp"		=>"100",
	"receive_name"	=>"100",
	"order_addr"	=>"300",
	"receive_phone"	=>"100",
	"receive_hp"	=>"100",
	"order_comment"	=>"300",
	"order_detail"	=>"300",
	"order_date"	=>"100",
	"delivery_no"	=>"150"
);

echo"<?xml version='1.0' encoding='UTF-8'?>";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">

	<Styles>
		<Style ss:ID="Default" ss:Name="Normal">
		<Alignment ss:Vertical="Center"/>
		<Borders/>
		<Font ss:FontName="맑은 고딕" x:CharSet="129" x:Family="Modern" ss:Size="11" ss:Color="#000000"/>
		<Interior/>
		<NumberFormat/>
		<Protection/>
		</Style>
		<Style ss:ID="s63">
		<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#000000"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
		</Borders>
		<Font ss:FontName="맑은 고딕" x:CharSet="129" x:Family="Modern" ss:Color="#000000" ss:Bold="1"/>
		<Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
		</Style>
		<Style ss:ID="s65">
		<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
		<Borders>
			<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
			<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
			<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
			<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
		</Borders>
		<Font ss:FontName="맑은 고딕" x:CharSet="129" x:Family="Modern" ss:Size="9" ss:Color="#000000"/>
		<Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
		<NumberFormat/>
		</Style>
	</Styles>

	<Worksheet ss:Name="<?php echo $excel['fileName']?>">
		<Table>
		<?php if($excel['isNo']) { ?>
			<Column ss:Width='50'/> 
		<?php } ?>

		<?php
		foreach ($excel['fields'] as $e_key => $e_val){
		?>
			<Column ss:Width='<?php echo $fieldsWidth[$e_val];?>'/> 
		<?php
			if($e_val=='order_detail'){
		?>
			<Column ss:Width='70' />
		<?php
			}
		}
		?>
			<Row ss:AutoFitHeight="0" ss:Height="27.75">
			<?php if($excel['isNo']) { ?>
				<Cell ss:StyleID="s63"><Data ss:Type="String">NO</Data></Cell>
			<?php } ?>
			<?php
			foreach ($excel['fields'] as $e_key => $e_val){
			?>
				<Cell ss:StyleID="s63"><Data ss:Type="String"><?php echo $fields[$e_val]?></Data></Cell>
			<?php
				if($e_val=='order_detail'){
			?>
				<Cell ss:StyleID="s63"><Data ss:Type="String">수량</Data></Cell>
			<?php
				}
			}
			?>
			</Row>
		<?php
		if($total>'0'){
			foreach($result as $i=>$data) {
		?>
			<Row ss:AutoFitHeight="0" ss:Height="24.75">
			<?php 
			if($excel['isNo']) { 
			?>
				<Cell ss:StyleID="s65"><Data ss:Type="Number"><?php echo $i +1?></Data></Cell>
			<?php 
			}

			foreach ($excel['fields'] as $e_key => $e_val){
				$printData="";
				$ssType="String";
				if($e_val=='order_addr') $printData="(".$data->receive_zip.") ".$data->receive_addr1." ".$data->receive_addr2;
				elseif($e_val=='order_status') $printData=$orderStatus[$data->order_status];
				elseif($e_val=='pay_how') $printData=$payHow[$data->pay_how];
				elseif($e_val=='use_earn'){
					$printData=number_format($data->use_earn);
					$ssType="Number";
				}
				elseif($e_val=='cost_total'){
					$printData=number_format($data->cost_total);
					$ssType="Number";
				}
				elseif($e_val=='order_date') $printData=date("Y-m-d H:i:s",$data->order_date);
				elseif($e_val=='order_detail'){
					$products = array();
					$dCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_order_detail WHERE order_no='".$data->order_no."'");
					$ord_result = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$data->order_no."' ORDER BY idx ASC");
					$t_p_name = array();
					$t_p_cnt = array();
					foreach ($ord_result as $key => $value) {
						$t = array();
						$title = unserialize($value->goods_option_basic);
						
						$ptitle = $wpdb->get_var('
							SELECT	goods_name
							FROM	bbse_commerce_goods
							WHERE 	idx = "'.$value->goods_idx.'" 
						');
						foreach ($title['goods_option_title'] as $k => $v) {
							if($v == '단일상품') continue;
							$t []= $v.' * '.$title['goods_option_count'][$k].'개';	
						}
						foreach ($title['goods_option_count'] as $k => $v) {
							$t_c = $v;	
						}
						$t_p_name[]= $ptitle.(count($t) > 0 ? ' ('.implode(',', $t).')':'');
						$t_p_cnt []= $t_c;
					}
					$products = $t_p_name;
					//$t_p_name = implode(", ", $t_p_name);
					$printData = $t_p_name[0];
				}
				elseif($e_val=='delivery_no') $printData=($data->delivery_no && $data->delivery_company)?$data->delivery_no."(".$data->delivery_company.")":"";
				//elseif($e_val=='order_phone') $printData = $data->receive_phone;
				//elseif($e_val=='order_hp') $printData = $data->receive_hp;
				else $printData=$data->$e_val;

				if(!$printData) $printData="-";
			?>
				<Cell ss:StyleID="s65" style="white-space: nowrap;"><Data ss:Type="<?php echo $ssType;?>"><?php echo $printData;?></Data></Cell>
			<?php
				if($e_val=='order_detail'){
			?>
					<Cell ss:StyleID="s65" style="white-space: nowrap;">
						<Data ss:Type="Number"><?php echo $t_p_cnt [0]; ?></Data>
					</Cell>
			<?php
			
				}
			}
			?>
			</Row>
		<?php
				//상품 행 추가
				$index = 0;
				foreach ($products as $key => $value) {
					if($index++ == 0) continue;
					echo '
							<Row ss:AutoFitHeight="0" ss:Height="24.75">
								<Cell ss:StyleID="s65"><Data ss:Type="Number"></Data></Cell>
					';
					foreach ($excel['fields'] as $e_key => $e_val){
						if($e_val=='order_detail'){
							echo'
								<Cell ss:StyleID="s65" style="white-space: nowrap;">
									<Data ss:Type="String">'.$value.'</Data>
								</Cell>
								<Cell ss:StyleID="s65" style="white-space: nowrap;">
									<Data ss:Type="Number">'.$t_p_cnt[$key].'</Data>
								</Cell>
							';
						}
						else{
							echo'
								<Cell ss:StyleID="s65" style="white-space: nowrap;"><Data ss:Type="String">-</Data></Cell>
							';	
						}
					}
					echo '
							</Row>
					';
				}
			
			}
		}
		else{

		}
		?>

		</Table>
	</Worksheet>
</Workbook>