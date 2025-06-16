<?php
class BBSeBoardBackup{
	public function __construct(){
		set_time_limit(0);
	}

	/**
	 * BBS e-Board 테이블 목록
	 */
	public function get_tables(){
		global $wpdb;

		$get_tables = array();

		// 기본 테이블
		$tbls_res = $wpdb->get_results("show tables from ".DB_NAME, ARRAY_N);
		foreach($tbls_res as $tbl){
			//if(substr($tbl[0], 0, 5) == "bbse_"){
				if($tbl[0] == $wpdb->prefix."bbse_board") $get_tables[] = $tbl[0];
				else if($tbl[0] == $wpdb->prefix."bbse_board_config") $get_tables[] = $tbl[0];
				else if($tbl[0] == $wpdb->prefix."bbse_board_syndi_delete_content_log") $get_tables[] = $tbl[0];
			//}
		}

		// board 테이블
		$board_res = $wpdb->get_results("show tables where Tables_in_".DB_NAME." LIKE '".$wpdb->prefix."bbse_%_board' and Tables_in_".DB_NAME."<>'".$wpdb->prefix."bbse_board'", ARRAY_N);
		foreach($board_res as $board_tbl){
			$get_tables[] = $board_tbl[0];
		}

		// comment 테이블
		$comm_res = $wpdb->get_results("show tables where Tables_in_".DB_NAME." LIKE '".$wpdb->prefix."bbse_%_comment'", ARRAY_N);
		foreach($comm_res as $comm_tbl){
			$get_tables[] = $comm_tbl[0];
		}
		return $get_tables;
	}

	/**
	 * xml 데이터 생성
	 */
	public function get_xml($tbls){
		global $wpdb;


		$get_xml = "";
		$get_xml .= "\t<".$tbls.">\n";

		// 필드정보 가져오기
		$fields_res = $wpdb->get_results("DESCRIBE `".$tbls."`", ARRAY_A);
		foreach($fields_res as $fields_rows){
			$get_xml .= "\t\t<fields>\n";

			foreach($fields_rows as $key => $value){
				$get_xml .= "\t\t\t<".$key.">";
				$get_xml .= "<![CDATA[".stripslashes($value)."]]>";
				$get_xml .= "</".$key.">\n";
			}

			$get_xml .= "\t\t</fields>\n";
		}

		// 데이터 가져오기
		$res = $wpdb->get_results("SELECT * FROM `".$tbls."`", ARRAY_A);
		foreach($res as $rows){
			$get_xml .= "\t\t<data>\n";

			foreach($rows as $key => $value){
				$get_xml .= "\t\t\t<".$key.">";
				$get_xml .= "<![CDATA[".stripslashes($value)."]]>";
				$get_xml .= "</".$key.">\n";
			}

			$get_xml .= "\t\t</data>\n";
		}
		$get_xml .= "\t</".$tbls.">\n";

		return $get_xml;
	}

	/**
	 * 파일 다운로드
	 */
	public function xml_download($data, $filename=""){
		if(empty($filename)) $filename = "BBSe-Board-".date("YmdHis", current_time('timestamp')).".xml";
		header("Content-Type: application/xml");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
		header("Pragma: no-cache");
		Header("Expires: 0");

		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<bbse>\n";
		echo $data;
		echo "</bbse>";

		exit;
	}

	/*
	 * 데이터 입력
	 */
	public function xml_import($file){
		global $wpdb;
		include "XML2Array.php";
		$xml = file_get_contents($file);
		$array = XML2Array::createArray($xml);

		// 테이블 개수만큼 반복
		foreach($array['bbse'] as $tbls => $rows){
			if(substr($tbls, 0, 5) == "bbse_"){
				$drop_tbls = $tbls;
				$create_tbls = $wpdb->prefix.$tbls;
			}else{
				$drop_tbls = $tbls;
				$create_tbls = $tbls;
			}

			//if(substr($tbls, 0, 5) == "bbse_"){  // bbse_ 로 시작하는 테이블로 제한
				// 테이블 삭제
				$wpdb->query("DROP TABLE IF EXISTS `".$drop_tbls."`");

				if(is_array($rows['fields'])){
					$keys = array_keys($rows['fields']);
					if(reset($keys) == "0") $fields = $rows['fields'];
					else $fields = $rows;
				}else{
					$fields = $rows;
				}
				$fields_count = count($fields);

				if(!empty($fields)){
					// 테이블 생성
					$create_tbl = "CREATE TABLE IF NOT EXISTS `".$create_tbls."` (";

					$pri_cnt = 0;
					$pri_fields = "";

					// 필드개수만큼 반복 (fileds 데이터타입 sql 구문 생성)
					foreach($fields as $key => $row){
						$keys = array_keys($row);  // 필드정보 ([0] => Field, [1] => Type, [2] => Null, [3] => Key, [4] => Default, [5] => Extra)
						$row_count = count($row);

						$val_arr = array();

						$pri_fields_arr = array();

						// 필드정보개수만큼 반복
						for($i = 0; $i < $row_count; $i++){
							$val_arr[] = $row[$keys[$i]]['@cdata'];
							$tmp = "";
							if($keys[$i] == "Field"){
								if(!empty($val_arr[$i])) $tmp .= "`".$val_arr[$i]."`";
							}else if($keys[$i] == "Type"){
								if(!empty($val_arr[$i])) $tmp .= " ".$val_arr[$i];
							}else if($keys[$i] == "Null"){
								if(!empty($val_arr[$i])){
									if($val_arr[$i] == "NO") $tmp .= " not null";
									else if($val_arr[$i] == "YES") $tmp .= " default null";
								}
							}else if($keys[$i] == "Key"){
								if(!empty($val_arr[$i])){
									if($val_arr[$i] == "PRI"){
										if($pri_cnt == 0) $pri_fields .= "`".$val_arr[0]."`";
										else $pri_fields .= ",`".$val_arr[0]."`";
										$pri_cnt++;
									}
								}
							}else if($keys[$i] == "Default"){
								if(!empty($val_arr[$i])) $tmp .= " default '".$val_arr[$i]."'";
							}else if($keys[$i] == "Extra"){
								if(!empty($val_arr[$i])) $tmp .= " ".$val_arr[$i];
							}

							if($i == ($row_count - 1) && $key < ($fields_count - 1)) $tmp .= ", ";

							$create_fields = $tmp;
							if(!empty($create_fields)) $create_tbl .= $create_fields;
						}
					}

					if(!empty($pri_fields)) $create_tbl .= ", primary key (".$pri_fields.")";

					$create_tbl .= ") {$wpdb->get_charset_collate()}";
					$wpdb->query($create_tbl);
				}

				if(is_array($rows['data'])){
					$keys = array_keys($rows['data']);
					if(reset($keys) == "0") $data = $rows['data'];
					else $data = $rows;
				}else{
					$data = $rows;
				}

				if(!empty($data)){
					foreach($data as $key => $row){
						$keys = array_keys($row);
						$row_count = count($row);

						$fields_arr = array();
						for($i = 0; $i < $row_count; $i++){
							$fields_arr[] = "`".$keys[$i]."`";
						}
						$fields = implode(",", $fields_arr);

						$values_arr = array();
						for($i = 0; $i < $row_count; $i++){
							$values_arr[] = "'".addslashes($row[$keys[$i]]['@cdata'])."'";
						}
						$values = implode(",", $values_arr);

						$wpdb->query("INSERT INTO `".$create_tbls."` (".$fields.") VALUES (".$values.")");
					}
				}
			//}
		}
	}
}