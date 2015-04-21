<?php


class World_data_updater {
	public $session_id;
	public $url_client;
	public $url_ajax_endpoint;
	public $self_alliance_id;
	public $world_name;
	
	/**
	 * @param string $session_id   ClientLib.Net.CommunicationManager.GetInstance().get_InstanceId();  example: 'bc433fa6-b8e8-41c0-bdfe-8092cdeb3abf'
	 * @param string $url_client  example: 'https://prodgame17.alliances.commandandconquer.com/259/index.aspx'
	 * @param string $url_ajax_endpoint_by_api  result of 'ClientLib.Net.CommunicationManager.GetInstance().get_ServerUrl()' on client; example: 'Presentation/Service.svc/ajaxEndpoint/'
	 * @throws Exception
	 */	
	public function __construct($session_id, $url_client, $url_ajax_endpoint_by_api){
		$this->session_id = $session_id;
		$this->url_client = $url_client;
		
		if( preg_match('/^https?\:.*/',$url_ajax_endpoint_by_api) ){
			$this->url_ajax_endpoint = $url_ajax_endpoint_by_api;
		}
		else{
			$slash = strrpos($url_client, '/');
			if($slash === false) throw new Exception('invalid $url_client: '.$url_client);
			$this->url_ajax_endpoint = rtrim(substr($url_client, 0, $slash),'/').'/'.rtrim($url_ajax_endpoint_by_api,'/').'/';
		}
		
	}

	
	
	public function make_update(){
		set_time_limit(600);
		
		header( 'Content-type: text/html; charset=utf-8' );
		?><htm><body><?
		echo "Initial checking... <br>";
		force_flush();
		
		$player_info_data = $this->send_request_json($this->url_ajax_endpoint."GetPlayerInfo");
		
		$this->self_alliance_id = $player_info_data['AllianceId'];
		
		
		$server_info_data = $this->send_request_json($this->url_ajax_endpoint."GetServerInfo");
		
		$this->world_name = trim($server_info_data['n']);
		
		/*
		//if(!query_scalar('select count(*) from alliance'))
		{
			// initialize alliance table
			
		
			$alliances_data = $this->send_request_json(
				$this->url_ajax_endpoint."RankingGetData", 
				array(
					'ascending' => true,
					'firstIndex' => 0,
					'lastIndex' => 99,
					'rankingType' => 0,
					'sortColumn' => 2,
					'view' => 1, // type 1: alliance, 0: players
				)
			);
			Packet_parser::update_alliances_list($alliances_data);
		}
		echo "ok<br>";
		
		$this->update_players_shallow_data();	
			   
		*/
		
		$this->update_players_detail_data();
		
		
		query_execute("update global_data set dt_last_world_update=current_timestamp where id=1");
		
	}
	
	public function update_players_shallow_data(){
		echo "Shallow update alliance members... <br>";
		force_flush();
		
		$alliance_record_arr = query_arr('select id, name from alliance where interested');
		foreach($alliance_record_arr as $alliance_record){
			$alliance_id = $alliance_record['id'];
			?>Updating alliance '<?=$alliance_record['name']?>' public info...<?

			$public_alliance_info = $this->send_request_json(
				 $this->url_ajax_endpoint."GetPublicAllianceInfo", 
				 array(
					 'id' => $alliance_id
				 )
			 );

			 Packet_parser::update_public_alliance_info($public_alliance_info);
			 ?> done.<br><?
		}
		echo "ok<br>";
		force_flush();
	}
	
	
	public function update_players_detail_data(){
		 
		
		echo "Update members detailed data...<br> ";
		force_flush();
		
		query_execute('UPDATE player p LEFT JOIN alliance a ON a.id=p.`alliance_id`'.
			' SET p.base_id_on_hub=0, hub_name=NULL, hub_position=NULL'.
			' WHERE (a.`id` IS NULL) OR (NOT a.`interested`)');
		
		
		$sql_select_players = "SELECT p.id, p.alliance_id, p.rank, p.has_sat_code, p.points, p.points_main_base, p.fraction, p.has_badge, p.hub_name, p.hub_position, ".
			" p.base_id_on_hub, p.distance_to_ff_main".
			" FROM player p".
			" LEFT JOIN alliance a ON p.`alliance_id`=a.`id`".
			" WHERE a.interested order by p.rank";
		
		$record_db_arr = query_arr($sql_select_players);
		
		if(!count($record_db_arr)){
			echo "failed: not exsts alliances with nonzero `allways_full_update` nor players with nonzero `interested_in_ff_run`<br>";
			return;
		}
		
		$hub_arr = World::get_hub_arr();
		
		$player_updated_arr = array();
		
		$count_loaded =0;
		foreach($record_db_arr as $record_db){
			
			$player_id = (int)$record_db['id'];
			$data_obfuscated = $this->send_request_json( $this->url_ajax_endpoint."GetPublicPlayerInfo", array('id' => $player_id) );


			
			$data_parsed = array(
				'id' => $player_id,
				'alliance_id' => $data_obfuscated['a'],
				'rank' => $data_obfuscated['r'],
				'has_sat_code' => $data_obfuscated['hchc'],
				'points' => $data_obfuscated['p'],
				'points_main_base' => 0,
				'fraction' => $data_obfuscated['f'],
				'has_badge' => 0,
				'hub_name' => null,
				'hub_position' => null,
				'distance_to_ff_main' => 999,
				'base_id_on_hub' => 0,
			);
			
			
			if(isset($data_obfuscated['ew'])){
				foreach($data_obfuscated['ew'] as $badge_info){
					if(trim($badge_info['n']) === $this->world_name){
						$data_parsed['has_badge'] = 1;
						break;
					}
				}
			}
			
			if(isset($data_obfuscated['c'])){
				foreach($data_obfuscated['c'] as $base_info){
					$base_id = $base_info['i'];
					$points = $base_info['p'];
					if($points > $data_parsed['points_main_base']){
						$data_parsed['points_main_base'] = $points;
						
						$dx = abs( $base_info['x'] - 550);
						$dy = abs( $base_info['y'] - 550);
						$data_parsed['distance_to_ff_main'] = (int)round(sqrt($dx*$dx + $dy*$dy));
					}
					
					$coord = $base_info['x'].':'.$base_info['y'];
					
					foreach($hub_arr as $hub_name => $hub){
						if(isset($hub['position_arr'][$coord])){
							
							$data_base = $this->send_request_json( $this->url_ajax_endpoint."GetPublicCityInfoById", array('id' => $base_id) );
							//echo json_encode(array('$player_id'=>$player_id, '$base_id'=> $base_id, '$data_base' => $data_base))."<br>";force_flush();
							if( array_default($data_base,'g',false) ) // base is destroyed
							{
								continue;
							}
							
							$data_parsed['hub_name'] = $hub_name;
							$data_parsed['hub_position'] = $hub['position_arr'][$coord];
							$data_parsed['base_id_on_hub'] = $base_id;
							break;
						}
					}
				}
			}
			
			$player_updated_arr[] = $data_parsed;
			
			
			
			$count_loaded++;
			if(!($count_loaded % 50)){
				echo "loaded ".$count_loaded." players<br>";
				force_flush();
			}
		}
		
		
		echo "saving changes...<br>";
		force_flush();
		
		if(count($player_updated_arr)){
			$fields = array_keys($player_updated_arr[0]);
			array_syncronize_with_table($player_updated_arr, 'player', 'id', $fields, $sql_select_players, true);
		}
		
		echo "ok<br>"; 
		force_flush();
		
	}
	
	
	
	

	/**
	 * @param string $url example: 'https://prodgame17.alliances.commandandconquer.com/259/Presentation/Service.svc/ajaxEndpoint/AllianceGetMemberData'
	 * @param mixed[string] $request_params params without 'session'
	 * @return mixed[] response json, parsed in assoc. array
	 */
	public function send_request_json($url, $request_params = array() ){
		$request_params_with_session = array_slice($request_params,0,count($request_params));
		$request_params_with_session['session'] = $this->session_id;
		
		$params_json = json_encode( $request_params_with_session );
		
		$params = array(
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $params_json,
			
			CURLOPT_HTTPHEADER => array(
				'X-Qooxdoo-Response-Type: application/json',
				'Content-Type: application/json',
				'Content-Length: '. strlen($params_json),
				
				
			),
		);
		
		if(array_default($GLOBALS,'CURL_DONT_VERIFY_SSL',0)){
			$params[CURLOPT_SSL_VERIFYHOST] = 0;
			$params[CURLOPT_SSL_VERIFYPEER] = 0;
		}
		
		$res = curl_get_contents($url, $params, true);
		if($res.""==="") throw new Exception ("empty server response on request '".$url."'");
		$res = json_decode($res, true);
		
		return $res;
	}
	
}
