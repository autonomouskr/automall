<?php
    define( 'FACEBOOK_OAUTH_URL', 'https://www.facebook.com/dialog/oauth' );
    define( 'FACEBOOK_OAUTH_TOKEN_URL', 'https://graph.facebook.com/oauth/access_token' );
    define( 'FACEBOOK_USERINFO_URL', 'https://graph.facebook.com/me');
    class FacebookOAuthRequest{
        private $client_id;
        private $client_secret;
        private $redirect_url;
        private $authorize_url=FACEBOOK_OAUTH_URL;
        private $accesstoken_url=FACEBOOK_OAUTH_TOKEN_URL;
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

        public function request_auth(){ // 페이스북 로그인 요청 -> callback 페이지로 code 수신
			$reqUrl=$this->authorize_url.'?client_id='.$this->client_id.'&redirect_uri='.urlencode($this->redirect_url); 
            header('Location: '.$reqUrl);
        }

        public function get_accesstoken($code){ // Access Token을 받기 위한 요청 -> 수신
			$accUrl=$this->accesstoken_url.'?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&code='.$code.'&redirect_uri='.urlencode($this->redirect_url); 
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $accUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);

			//parse_str($g);
			$output=json_decode($g, true);
			$access_token=$output['access_token'];

			$this->tokenArr=$access_token;
        }

        public function get_user_info(){ // 회원 정보 요청
			$infoUrl=FACEBOOK_USERINFO_URL.'?fields=name,email,gender&access_token='.$this->tokenArr;

            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $infoUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);
            
			$data=json_decode($g, true);

			$data['gender']=($data['gender']=='male')?'M':'F';
            $this->userInfo=array(
                'sns_id'=>(string)$data['id'],
                'sns_type'=>'facebook',
                'sns_name'=>(string)$data['name'],
                'sns_email'=>(string)$data['email'],
                'sns_gender'=>(string)$data['gender']
            );

			return $this->userInfo;
        }
    }
?>