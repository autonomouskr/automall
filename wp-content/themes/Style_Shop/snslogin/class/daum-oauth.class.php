<?php
    define( 'DAUM_OAUTH_URL', 'https://apis.daum.net/oauth2/authorize' );
    define( 'DAUM_OAUTH_TOKEN_URL', 'https://apis.daum.net/oauth2/token' );
    define( 'DAUM_USERINFO_URL', 'https://apis.daum.net/user/v1/show.json');
    class DaumOAuthRequest{
        private $client_id;
        private $client_secret;
        private $redirect_url;
        private $authorize_url=DAUM_OAUTH_URL;
        private $accesstoken_url=DAUM_OAUTH_TOKEN_URL;
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

		public function request_auth(){ // 다음 아이디로 로그인 요청 -> callback 페이지로 code 수신
			$reqUrl=$this->authorize_url.'?response_type=code&client_id='.$this->client_id.'&redirect_uri='.$this->redirect_url; 
            header('Location: '.$reqUrl);
        }

        public function get_accesstoken($code){ // Access Token을 받기 위한 요청 -> 수신
			$postParm = 'grant_type=authorization_code&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&redirect_uri='.$this->redirect_url.'&code='.$code;
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->accesstoken_url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParm); 

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);
            $data=json_decode($g, true);
			$this->tokenArr=$data['access_token'];
		}

        public function get_user_info(){ // 회원 정보 요청
			$accUrl = DAUM_USERINFO_URL.'?access_token='.$this->tokenArr;
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $accUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $g=curl_exec($ch);
            curl_close($ch);

            $userData=json_decode($g, true);

            $this->userInfo=array(
				'sns_id'=>(string)$userData['result']['id'],
				'sns_type'=>'daum',
				'sns_name'=>(string)$userData['result']['nickname'],
				'sns_email'=>"",
				'sns_gender'=>"",
				/*
                'userID'=>(string)$data['result']['id'],
                'nickname'=>(string)$data['result']['nickname'],
                'userid'=>(string)$data['result']['userid'],
                'imagePath'=>(string)$data['result']['imagePath'],
                'bigImagePath'=>(string)$data['result']['bigImagePath'],
                'openProfile'=>(string)$data['result']['openProfile']
				*/
            );

			return $this->userInfo;
        }
    }
?>