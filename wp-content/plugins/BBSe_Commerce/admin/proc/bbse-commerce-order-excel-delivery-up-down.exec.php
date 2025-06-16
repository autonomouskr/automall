<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$tMode=$_REQUEST['tMode'];

if($tMode=='Download'){
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
	$excel['fileName'] = "bbse_delivery_list_".date('Ymd');

	# 엑셀에 No 번호 출력 여부 결정 default 는 출력

	# 정렬방식 default 는 없음
	$excel['sort'] = " ORDER BY idx DESC";

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

	$fields=Array("order_no"=>"주문번호","order_status"=>"주문상태","order_name"=>"주문자명","receive_name"=>"받으실분","order_addr"=>"배송주소","order_detail"=>"주문상품","order_date"=>"주문일자","delivery_company"=>"택배사","delivery_no"=>"송장번호");
	$fieldsWidth=Array("order_no"=>"150","order_status"=>"100","order_name"=>"100","receive_name"=>"100","order_addr"=>"300","order_detail"=>"300","order_date"=>"100","delivery_company"=>"100","delivery_no"=>"100");

	foreach ($fields as $e_key => $e_val){
		$excel['fields'][] = $e_key;
	}

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
			<Style ss:ID="s64">
			<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
			<Borders>
				<Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#000000"/>
				<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
				<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
				<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#000000"/>
			</Borders>
			<Font ss:FontName="맑은 고딕" x:CharSet="129" x:Family="Modern" ss:Color="#000000" ss:Bold="1"/>
			<Interior ss:Color="#FFC90E" ss:Pattern="Solid"/>
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
				<Column ss:Width='50'/> 
			<?php
			foreach ($excel['fields'] as $e_key => $e_val){
			?>
				<Column ss:Width='<?php echo $fieldsWidth[$e_val];?>'/> 
			<?php
			}
			?>
				<Row ss:AutoFitHeight="0" ss:Height="27.75">
					<Cell ss:StyleID="s63"><Data ss:Type="String">NO</Data></Cell>
				<?php
				foreach ($excel['fields'] as $e_key => $e_val){
					if($e_val=='delivery_company' || $e_val=='delivery_no') $tStyle="s64";
					else $tStyle="s63";
				?>
					<Cell ss:StyleID="<?php echo $tStyle;?>"><Data ss:Type="String"><?php echo $fields[$e_val]?></Data></Cell>
				<?php
				}
				?>
				</Row>
			<?php
			if($total>'0'){
				foreach($result as $i=>$data) {
			?>
				<Row ss:AutoFitHeight="0" ss:Height="24.75">
					<Cell ss:StyleID="s65"><Data ss:Type="Number"><?php echo $i +1?></Data></Cell>
				<?php 
				foreach ($excel['fields'] as $e_key => $e_val){
					$printData="";
					$ssType="String";
					if($e_val=='order_addr') $printData="(".$data->order_zip.") ".$data->receive_addr1." ".$data->receive_addr2;
					elseif($e_val=='order_status') $printData=$orderStatus[$data->order_status];
					elseif($e_val=='order_date') $printData=date("Y-m-d H:i:s",$data->order_date);
					elseif($e_val=='order_detail'){
						$dCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_order_detail WHERE order_no='".$data->order_no."'");
						$ord_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$data->order_no."' ORDER BY idx ASC LIMIT %d",Array("1")));
						foreach($ord_result as $z=>$oData) {
							if($dCnt>1) $printData .=$oData->goods_name." 외".($dCnt-1)."건";
							else $printData .=$oData->goods_name;
						}
					}
					else $printData=$data->$e_val;
				?>
					<Cell ss:StyleID="s65" style="white-space: nowrap;"><Data ss:Type="<?php echo $ssType;?>"><?php echo $printData;?></Data></Cell>
				<?php
				}
				?>
				</Row>
			<?php
				}
			}
			else{

			}
			?>

			</Table>
		</Worksheet>
	</Workbook>
<?php
}
elseif($tMode=='Upload'){
		$now_time=current_time('timestamp');

		if(!$_FILES['deliveryExcelFile']['tmp_name'] || !is_uploaded_file($_FILES['deliveryExcelFile']['tmp_name'])){
			echo "errorFileName";
			exit;
		}

		$checkArr=Array("xls","XLS","xlsx","XLSX");
		$file_arr = explode(".", $_FILES['deliveryExcelFile']['name']);
		$file_type = strtolower($file_arr[count($file_arr)-1]);
		if(!in_array($file_type,$checkArr)) {
			echo "errorFileExtend";
			exit;
		}

		$r_attach_org=$_FILES['deliveryExcelFile']['name'];

		$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/"."bbse_delivery_list_".date("YmdHis",$now_time).".".$file_type;

		if($r_attach_org && $r_attach_new){
			if(!@move_uploaded_file($_FILES['deliveryExcelFile']['tmp_name'],$r_attach_new)){ 
				echo "errorFileUpload";
				exit;
			}
		}

		$str_result = wp_remote_get(BBSE_COMMERCE_UPLOAD_BASE_URL."/bbse-commerce/"."bbse_delivery_list_".date("YmdHis",$now_time).".".$file_type);

		if($str_result[response][code]!='200'){
			echo "errorFileUpload";
			exit;
		}

		$str_body     = wp_remote_retrieve_body($str_result);
		$obj_xml    = simplexml_load_string($str_body);

		$rowCnt=sizeof($obj_xml->Worksheet->Table->Row)-1;
		$errCnt=0;
		$rtnMsg = "";

		for($z=1;$z<=$rowCnt;$z++){
			unset($psData);
			$psData['order_no']=trim($obj_xml->Worksheet->Table->Row[$z]->Cell[1]->Data);
			$psData['delivery_company']=trim($obj_xml->Worksheet->Table->Row[$z]->Cell[8]->Data);
			$psData['delivery_no']=trim($obj_xml->Worksheet->Table->Row[$z]->Cell[9]->Data);
			$oCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>'' AND order_no='".$psData['order_no']."'");
			$sCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>'' AND order_no='".$psData['order_no']."' AND order_status='DR'");

			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
			$data=unserialize($confData->config_data);
			$deliveryCompanyCnt=$data['deliveryCompanyCnt'];

			if($deliveryCompanyCnt<='0'){
				$rtnMsg .="<span style='color:#E1464C;'>상점관리-배송비설정-택배사 => [택배사 설정 오류] 상점관리의 택배사가 설정되지 않았습니다.</span><br />";
				$errCnt++;
				break;
			}
			elseif(!$psData['delivery_company']){
				$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [택배사 오류] 엑셀파일에 택배사 정보가 존재하지 않습니다.</span><br />";
				$errCnt++;
			}
			elseif(!$psData['delivery_no']){
				$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [송장번호 오류] 엑셀파일에 송장번호가 존재하지 않습니다.</span><br />";
				$errCnt++;
			}
			elseif($oCnt<='0'){
				$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [주문 번호 오류] 주문번호가 존재하지 않습니다.</span><br />";
				$errCnt++;
			}
			elseif($sCnt<='0'){

				$tmData=$wpdb->get_row("SELECT order_status FROM bbse_commerce_order WHERE idx<>'' AND order_no='".$psData['order_no']."'");


				$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [주문 상태 오류 : ".$orderStatus[$tmData->order_status]."] 주문상태가 '배송준비' 인 경우만 송장등록이 가능합니다.</span><br />";
				$errCnt++;
			}
			else{
				$deliveryUrl="";
				$deliveryFind="N";
				for($y=1;$y<=$deliveryCompanyCnt;$y++){
					if(trim($data['delivery_company_'.$y.'_name'])==$psData['delivery_company']){
						if($data['delivery_company_'.$y.'_use']=='on'){
							$deliveryUrl=trim($data['delivery_company_'.$y.'_url']);
							$deliveryFind="O";
						}
						else $deliveryFind="F";
					}
				}

				if($deliveryFind=='N'){
					$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [택배사 오류] 엑셀의 택배사 정보가 상점관리에 등록되지 않았습니다.</span><br />";
					$errCnt++;
				}
				elseif($deliveryFind=='F'){
					$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [택배사 오류] 엑셀의 택배사 정보(".$psData['delivery_company'].")가 사용안함 상태입니다.</span><br />";
					$errCnt++;
				}
				else{
					$wpdb->get_var("UPDATE bbse_commerce_order SET order_status='DI',delivery_company='".$psData['delivery_company']."',delivery_url='".$deliveryUrl."',delivery_no='".$psData['delivery_no']."',delivery_ing_date='".$now_time."' WHERE idx<>'' AND order_no='".$psData['order_no']."' AND order_status='DR'"); 
					$rCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>'' AND order_no='".$psData['order_no']."' AND order_status='DI' AND delivery_company='".$psData['delivery_company']."' AND delivery_no='".$psData['delivery_no']."' AND delivery_ing_date='".$now_time."'");
					if($sCnt>'0'){
						$rtnMsg .="주문번호 : ".$psData['order_no']." => [정상 처리 완료] '배송중' 처리를 완료하였습니다.<br />";
					}
					else{
						$rtnMsg .="<span style='color:#E1464C;'>주문번호 : ".$psData['order_no']." => [DB 저장 오류] DB 상태값 변경에 실패하였습니다.</span><br />";
						$errCnt++;
					}
				}
			}
		}

		$rtnMsg .="<strong>전체 : ".number_format($rowCnt)."건 / 정상 : ".number_format(($rowCnt-$errCnt))."건 <span style='color:#E1464C;'>/ 오류 : ".number_format($errCnt)."건</span></strong>";

		echo "success|||"."<strong>[택배사 송장 일괄 등록 결과]</strong><br />".$rtnMsg;

		@unlink($r_attach_new);
}
?>