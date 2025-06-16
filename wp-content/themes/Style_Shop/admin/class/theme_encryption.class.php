<?php
Class BBSeThemeSecretCode{
	var $key	= "Khpk7Pywy250CMV7zKC09";  // default key값으로 keycreate() 또는 keyset()을 하지 않을 경우 이용됨
    var $pre_key = "BBSE_BOARD_";  // key값의 접두어 (보안을 위해 필요함)
	var $code = "";
    var $string = ""; 
    var $buffer = ""; 
    var $key2 = "";
	var $secret	= Array();
	var $keyset	= Array(Array(7, 48, 57), Array(7, 65, 90), Array(7, 97, 122));

	function bytexor($a, $b){
		$code = "";
		for($i = 0; $i < 16; $i++) $code .= $a{$i}^$b{$i}; 
		return $code; 
	}

	function dec($msg){
		$this->varclear();
        $msg = base64_decode($msg);
        while($msg){ 
			$this->key2 = pack("H*", md5($this->pre_key.$this->key.$this->key2.$this->buffer)); 
			$this->buffer = $this->bytexor(substr($msg, 0, 16), $this->key2); 
			$this->string .= $this->buffer; 
			$msg = substr($msg, 16); 
        } 
        return $this->string; 
	} 

	function enc($msg){
		$this->varclear();
        while($msg){ 
			$this->key2 = pack("H*", md5($this->pre_key.$this->key.$this->key2.$this->buffer)); 
			$this->buffer = substr($msg, 0, 16); 
			$this->string .= $this->bytexor($this->buffer, $this->key2); 
			$msg = substr($msg, 16); 
        } 
        return base64_encode($this->string); 
	}

	function keychk(){
		return $this->key;
    }

	function keyset($val){
		$this->key = $val;
	}

	function keycreate(){
		$this->key = "";
		for($s = 0; $s < sizeof($this->keyset); $s++) for($i = 0; $i < $this->keyset[$s][0]; $i++) 
			array_push($this->secret, rand($this->keyset[$s][1], $this->keyset[$s][2]));
		shuffle($this->secret);
		for($i = 0; $i < count($this->secret); $i++) $this->key .= chr($this->secret[$i]);
		return $this->key;
	}

	function varclear(){
		$this->code = "";
		$this->string = ""; 
		$this->buffer = ""; 
		$this->key2 = "";
	}
}

/*
$svalue = "test";
$cls = new BBSeSecretCode;
$scode = $cls->enc($svalue);

echo "<br />원본 메세지 : ".$svalue."<br />";
echo "<br />암호 메세지 : ".$scode."<br />";

$dcode = $cls->dec($scode);

echo "<br />복호 메세지 : ".$dcode."<br />";
*/
?>