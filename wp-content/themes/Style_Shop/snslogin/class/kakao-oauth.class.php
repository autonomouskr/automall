<?php
    define( 'KAKAO_OAUTH_URL', 'https://kauth.kakao.com/oauth/authorize' );
    define( 'KAKAO_OAUTH_TOKEN_URL', 'https://kauth.kakao.com/oauth/token' );
    define( 'KAKAO_USERINFO_URL', 'https://kapi.kakao.com/v2/user/me');
    class KakaoOAuthRequest{
        private $client_id;
        private $redirect_url;
        private $state;
        private $authorize_url=KAKAO_OAUTH_URL;
        private $accesstoken_url=KAKAO_OAUTH_TOKEN_URL;
        private $tokenArr; 
        private $userInfo;
 
        function __construct( $client_id, $redirect_url) {
            $this->client_id=$client_id;
            $this->redirect_url=$redirect_url;
            if(!isset($_SESSION)) {
                session_start();
            }
        }

		public function request_auth(){ // 로그인 요청 -> callback 페이지로 code 수신
			$reqUrl=$this->authorize_url.'?client_id='.$this->client_id.'&redirect_uri='.urlencode($this->redirect_url).'&response_type=code'; 
            header('Location: '.$reqUrl);
        }

        public function get_accesstoken($code){ // Access Token을 받기 위한 요청 -> 수신
			$accUrl=$this->accesstoken_url.'?grant_type=authorization_code&client_id='.$this->client_id.'&code='.$code.'&redirect_uri='.urlencode($this->redirect_url);
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
			$propertyKeys=Array('nickname','thumbnail_image','profile_image');
			$sendFields=json_encode($propertyKeys);

            $ch=curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, KAKAO_USERINFO_URL);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "propertyKeys=".$sendFields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->tokenArr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, '');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            $response=curl_exec($ch);
            curl_close($ch);
	
			$userData = json_decode($response, true);
			if($userData['id']){
				$this->userInfo=array(
					'sns_id'=>(string)$userData['id'],
	                'sns_type'=>'kakao',
					'sns_name'=>(string)$userData['properties']['nickname'],
					'sns_email'=>'',
					'sns_gender'=>'',
				);
			}
			else{
				$this->userInfo=array(
					'code'=>(string)$userData['code'],
					'msg'=>(string)$userData['msg']
				);
			}

			return $this->userInfo;
        }
    }
?>