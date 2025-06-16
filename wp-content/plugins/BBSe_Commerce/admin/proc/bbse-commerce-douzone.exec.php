<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$cnt = 0;
for($i=0; $i<count($V['tIdx']); $i++){
    $sql = "DELETE FROM tbl_douzone_code WHERE goods_code='".$V['tIdx'][$i]."'";
    $result = $wpdb->query($sql);
    
    $sql = "INSERT INTO tbl_douzone_code (goods_douzone_code, goods_code, delete_yn ) VALUES ('".$V['dzArr'][$i]."','".$V['tIdx'][$i]."' , 'N')";
    $result = $wpdb->query($sql);
    if($result > 0){
        $cnt++;
    }
}
echo "success|||".$cnt."|||";
exit;

?>  