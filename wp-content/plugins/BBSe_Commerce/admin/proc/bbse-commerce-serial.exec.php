<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$cnt = 0;
for($i=0; $i<count($V['idxArr']); $i++){
    //$sql = "UPDATE tbl_rfid_serial SET delete_yn='Y' WHERE date='".$V['date'][$i]."' and serial_number = '".$V['serialNumber']."' and goods_code = '".$V['dzArr'][$i]."'";
    $sql = "UPDATE tbl_rfid_serial SET delete_yn='Y' , used = 1, quantity_remaining = '".$V['quantity'][$i]."' WHERE id = '".$V['idxArr'][$i]."'";
    $result = $wpdb->query($sql);
    if($result > 0){
        $cnt++;
    }
}
echo "success|||".$cnt."|||";
exit;

?>