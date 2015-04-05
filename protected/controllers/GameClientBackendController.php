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
		
		jQ.ajax(ClientLib.Net.CommunicationManager.GetInstance().get_ServerUrl()+"AllianceGetMemberData",
	{ type:'POST', contentType:'application/json', 
	 headers: JSON.parse('{ "X-Qooxdoo-Response-Type":"application/json" }'),
	data: '{"session":"'+ClientLib.Net.CommunicationManager.GetInstance().get_InstanceId ()+'"' }
).done(function(data,textStatus){console.log('RESPONSE:',textStatus,JSON.stringify(data))});

		
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
		$res = curl_get_contents($url, $params);
		vd($res);
		
		
		
	}
}
