<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameClientBackend
 *
 * @author Moss
 */
class GameClientBackendController extends Controller {
	function actionStaring_script($_){
		header('Content-Type: application/javascript');
		?>
		console.log("<?=__METHOD__?>");
				
		jQ(".ta_stuff .fl_body").html("loaded!");
		
		
		
		
		/*
		jQ.ajax(ClientLib.Net.CommunicationManager.GetInstance().get_ServerUrl()+"AllianceGetMemberData",
	{ type:'POST', contentType:'application/json', 
	 headers: JSON.parse('{ "X-Qooxdoo-Response-Type":"application/json" }'),
	data: '{"session":"'+ClientLib.Net.CommunicationManager.GetInstance().get_InstanceId ()+'"' }
).done(function(data,textStatus){console.log('RESPONSE:',textStatus,JSON.stringify(data))});
		*/
		
		<?
	}
	
	function actionTest1($session_id){
		vd($session_id);
		$url = 'https://prodgame17.alliances.commandandconquer.com/259/Presentation/Service.svc/ajaxEndpoint/AllianceGetMemberData';
		
		$params_json = json_encode( array('session'=>$session_id) );
		
		$params = array(
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $params_json,
			
			CURLOPT_HTTPHEADER => array(
				'X-Qooxdoo-Response-Type: application/json',
				'Content-Type: application/json',
				'Content-Length: '. strlen($params_json),
				
				
			),
			//CURLOPT_HTTPHEADER => array('application/x-www-form-urlencoded')
		);
		
		if(array_default($GLOBALS,'CURL_DONT_VERIFY_SSL',0)){
			$params[CURLOPT_SSL_VERIFYHOST] = 0;
			$params[CURLOPT_SSL_VERIFYPEER] = 0;
		}
		
		$res = curl_get_contents($url, $params);
		
		vd($res);
		
	}
	
	// sample call: https://ta_local/ta_db/GameClientBackend/World_data_update?session_id=bc433fa6-b8e8-41c0-bdfe-8092cdeb3abf&url_ajax_endpoint_by_api=Presentation/Service.svc/ajaxEndpoint/&url_client=https://prodgame17.alliances.commandandconquer.com/259/index.aspx
	function actionWorld_data_update($session_id, $url_client, $url_ajax_endpoint_by_api)
	{
		$updater = new World_data_updater($session_id, $url_client, $url_ajax_endpoint_by_api);
		$updater->make_update();
		
		
		
	}
}
