<?php
    define( 'NAVER_OAUTH_URL', 'https://nid.naver.com/oauth2.0/authorize' );
    define( 'NAVER_OAUTH_TOKEN_URL', 'https://nid.naver.com/oauth2.0/token' );
    define( 'NAVER_USERINFO_URL', 'https://apis.naver.com/nidlogin/nid/getUserProfile.xml');
    class NaverOAuthRequest{
        private $client_id;
        private $client_secret;
        private $redirect_url;
        private $state;
        private $authorize_url=NAVER_OAUTH_URL;
        private $accesstoken_url=NAVER_OAUTH_TOKEN_URL;
        private $tokenArr; 
        private $userInfo;
 
        function __construct( $client_id, $client_secret, $redirect_url) {
            $this->client_id=$client_id;
            $this->client_secret=$client_secret;
            $this->redirect_url=$redirect_url;
            if(!isset($_SESSION)) {
                session_start();
            }
        }

        function generate_state(){
            $mt=microtime();
            $rand=mt_rand();
            $this->state=md5( $mt . $rand );
        }

        function set_state(){
            $this->generate_state();
            $_SESSION['state']=$this->state;
        }

        public function request_auth(){ // 네이버 아이디로 로그인 요청 -> callback 페이지로 code 수신
			$reqUrl=$this->authorize_url.'?response_type=code&client_id='.$this->client_id.'&state='.$this->state.'&redirect_url='.urlencode($this->redirect_url); 
            header('Location: '.$reqUrl);
        }

        public function get_accesstoken($code,$state){ // Access Token을 받기 위한 요청 -> 수신
			$accUrl=$this->accesstoken_url.'?grant_type=authorization_code&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&code='.$code.'&state='.$state;
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $accUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);
            $data=json_decode($g, true);
            $this->tokenArr=array(
                 'Authorization: '.$data['token_type'].' '.$data['access_token']
            );
        }

        public function get_user_info(){ // 회원 정보 요청
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, NAVER_USERINFO_URL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->tokenArr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);
            $xml=simplexml_load_string($g);
            $this->userInfo=array(
                'sns_id'=>(string)$xml->response->email[0],
                'sns_type'=>'naver',
                'sns_name'=>(string)$xml->response->nickname,
                'sns_email'=>(string)$xml->response->email[0],
                'sns_gender'=>(string)$xml->response->gender,
                //'age'=>(string)$xml->response->age,
                //'birth'=>(string)$xml->response->birthday,
                //'profImg'=>(string)$xml->response->profile_image
            );

			return $this->userInfo;
        }
    }
?>