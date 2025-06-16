<?php
    define( 'GOOGLE_OAUTH_URL', 'https://accounts.google.com/o/oauth2/auth' );
    define( 'GOOGLE_OAUTH_TOKEN_URL', 'https://accounts.google.com/o/oauth2/token' );
    define( 'GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');
    class GoogleOAuthRequest{
        private $client_id;
        private $client_secret;
        private $redirect_url;
        private $state;
        private $authorize_url=GOOGLE_OAUTH_URL;
        private $accesstoken_url=GOOGLE_OAUTH_TOKEN_URL;
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

		public function request_auth(){ // 로그인 요청 -> callback 페이지로 code 수신
			$reqUrl=$this->authorize_url.'?scope=https://www.googleapis.com/auth/userinfo.email&redirect_uri='.urlencode($this->redirect_url).'&response_type=code&client_id='.$this->client_id;
            header('Location: '.$reqUrl);
        }

        public function get_accesstoken($code){ // Access Token을 받기 위한 요청 -> 수신
			$postParm = 'grant_type=authorization_code&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&code='.$code.'&redirect_uri='.urlencode($this->redirect_url);

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
            $this->tokenArr=array(
                 'Authorization : '.$data['token_type'].' '.$data['access_token']
            );
        }

        public function get_user_info(){ // 회원 정보 요청
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->tokenArr);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $response=curl_exec($ch);
            curl_close($ch);

			$userData = json_decode($response, true);

			$userData['gender']=($userData['gender']=='male')?'M':'F';	
			if($userData['id']){
				$this->userInfo=array(
					'sns_id'=>(string)$userData['id'],
					'sns_type'=>'google',
					'sns_name'=>(string)$userData['name'],
					'sns_email'=>(string)$userData['email'],
					'sns_gender'=>(string)$userData['gender'],
					//'verified_email'=>(string)$userData['verified_email'],
					//'given_name'=>(string)$userData['given_name'],
					//'family_name'=>(string)$userData['family_name'],
					//'link'=>(string)$userData['link'],
					//'picture'=>(string)$userData['picture'],
					//'error_code'=>"",
					//'error_message'=>"",
				);
			}
			else{
				$this->userInfo=array(
					'error_code'=>(string)$userData['error']['code'],
					'error_message'=>(string)$userData['error']['message'],
				);
			}

			return $this->userInfo;
        }
    }
?>