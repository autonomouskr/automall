<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if(SITECOOKIEPATH != COOKIEPATH)
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

if(!function_exists("json_encode")){ 
    function json_encode($a=false){ 
        if(is_null($a)) return 'null'; 
        if($a === false) return 'false'; 
        if($a === true) return 'true'; 
        if(is_scalar($a)){ 
            if(is_float($a)) return floatval(str_replace(",", ".", strval($a))); 
            if(is_string($a)){ 
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')); 
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"'; 
            } else return $a; 
        } 
        $isList = true; 
        for($i=0, reset($a); $i<count($a); $i++, next($a)){ 
            if(key($a) !== $i){ 
                $isList = false; 
                break; 
            } 
        } 
        $result = array(); 
        if($isList){ 
            foreach($a as $v) $result[] = json_encode($v); 
            return '[' . join(',', $result) . ']'; 
        } else{ 
            foreach($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v); 
            return '{' . join(',', $result) . '}'; 
        } 
    } 
}

$V = $_GET;
$json_data = array();

if($V['order_name'] && $V['order_no']){
	$order_no = $V['order_no'];
	$sql = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE user_id='' AND order_no=%s AND order_name=%s", $V['order_no'], $V['order_name']);
	$order = $wpdb->get_row($sql);
	if(!$order->idx) {
		$json_data['result'] = "notExist";
		echo $V['callback']."([".json_encode($json_data)."])";
		exit;
	}
	else{
		$json_data['result'] = "success|||".str_replace("https","http",home_url())."/?bbseMy=order-detail";
		echo $V['callback']."([".json_encode($json_data)."])";
		exit;
	}
}
else{
	$json_data['result'] = "dataError";
	echo $V['callback']."([".json_encode($json_data)."])";
	exit;
}
?>