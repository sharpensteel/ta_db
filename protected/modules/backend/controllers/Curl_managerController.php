<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Curl_manager
 *
 * @author Moss
 */
class Curl_managerController extends BackendBaseController{
	//put your code here
	public function actionIndex(){
		//if($_POST)
		$this->render('curl_manager_index');
	}
	
	
	
	public function actionTest_login_ta(){
		
		try{
			$ch = curl_init();

			$cookie_file = Yii::app()->basePath."/runtime/cookies_ta_db";
			$base_opt_arr = array(
				// CURLOPT_HTTPHEADER
				// CURLOPT_COOKIE
				CURLOPT_COOKIEFILE => $cookie_file,
				CURLOPT_COOKIEJAR => $cookie_file,
			);
			
			$GLOBALS['CURL_DONT_VERIFY_SSL']=1;

			if(array_default($GLOBALS,'CURL_DONT_VERIFY_SSL',0)){
				$base_opt_arr[CURLOPT_SSL_VERIFYHOST] = 0;
				$base_opt_arr[CURLOPT_SSL_VERIFYPEER] = 0;
			}
			else{
				if(isset($GLOBALS['SSL_CERTIFICATE_PATH'])){
					$base_opt_arr[CURLOPT_CAINFO] = $GLOBALS['SSL_CERTIFICATE_PATH'];
				}
			}
				




			$opt_arr = array_replace( $base_opt_arr, array(
				CURLOPT_URL => 'https://www.tiberiumalliances.com/j_security_check',
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => http_build_query(array(
					'spring-security-redirect'=>'', 'id'=>'', 'timezone'=>3,
					'j_username'=>'mastorize2121@gmail.com','j_password'=>'kakjatig2121'
				) ),
			) );

			//Curl_utils::curl_wrapper($ch,$opt_arr);




			$opt_arr = array_replace( $base_opt_arr, array(
				CURLOPT_URL => 'https://www.tiberiumalliances.com/game/launch',
				//CURLOPT_URL => 'https://www.tiberiumalliances.com/game/worldBrowser',
			) );

			$response = Curl_utils::curl_wrapper($ch,$opt_arr);
			
			if((string)$response['error_str']!=='')
				throw new Exception("error getting \"".$opt_arr[CURLOPT_URL]."\". Error message: ".$response['error_str']);
 
			
			if(!preg_match('/input type="hidden" name="sessionID" value="(.*)"/i', $response['response_body'], $matches)){
				throw new Exception('cant find input with name="sessionID"');
			}		
			 
			 



/*
			
			$opt_arr = array_replace( $base_opt_arr, array(
				CURLOPT_URL => 'https://gamecdnorigin.alliances.commandandconquer.com/WebWorldBrowser/index.aspx',
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => http_build_query(array(
					'sessionID'=>'6e3ac34c-f14c-4acf-85e5-8e00c68a9405',
					'locale'=>'en_US',
				) ),
			) );

			$response = Curl_utils::curl_wrapper($ch,$opt_arr);

			
			if(!preg_match('/SessionId = "(.*)";/i', $response['response_body'], $matches)){
				throw new Exception('cant find input with name="sessionID"');
			}		*/
			
			
			
			$session_id_2 = $matches[1];
			

			

			$opt_arr = array_replace( $base_opt_arr, array(
				CURLOPT_URL => 'https://prodgame17.alliances.commandandconquer.com/259/Presentation/Service.svc/ajaxEndpoint/OpenSession',
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => json_encode(array(
					'platformId' => 1, 'refId' => -1, 'reset' => true, 'session' => $session_id_2, 'version' => -1
				) ),
			) );
			if(!isset($opt_arr[CURLOPT_HTTPHEADER])) $opt_arr[CURLOPT_HTTPHEADER] = array();
			$opt_arr[CURLOPT_HTTPHEADER][] = "Content-Type: application/json";

			
			$response = Curl_utils::curl_wrapper($ch,$opt_arr);

			if((string)$response['error_str']!=='')
				throw new Exception("error getting \"".$opt_arr[CURLOPT_URL]."\". Error message: ".$response['error_str']);

			
			
			$response_obj = json_decode($response['response_body'],true); // {"i":"55302bf3-b793-4026-982d-ca5fc1292caf","r":0,"ri":1428973238525}
			
			$session_id_3 = $response_obj['i'];			
			
			{
				
				$params_json = json_encode( array('session'=>$session_id_3) );
				
				$opt_arr = array_replace( $base_opt_arr, array(
					CURLOPT_URL => 'https://prodgame17.alliances.commandandconquer.com/259/Presentation/Service.svc/ajaxEndpoint/AllianceGetMemberData',
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $params_json,
					
					CURLOPT_HTTPHEADER => array(
						'X-Qooxdoo-Response-Type: application/json',
						'Content-Type: application/json',
						'Content-Length: '. strlen($params_json),
					),
				) );
				


				$response = Curl_utils::curl_wrapper($ch,$opt_arr);
				
				
			

			}
			
			
			

			curl_close($ch);
		}
		catch(Exception $e){
			echo "<br>EXCEPTION: ".$e->getMessage()." at ".$e->getFile().":".$e->getLine();
		}
	}
}
