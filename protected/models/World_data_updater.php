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
		
		echo "Initial checking... ";
		
		$player_info_data = $this->send_request_json($this->url_ajax_endpoint."GetPlayerInfo");
		
		$this->self_alliance_id = $player_info_data['AllianceId'];
		
		
		$server_info_data = $this->send_request_json($this->url_ajax_endpoint."GetServerInfo");
		
		$this->world_name = trim($server_info_data['n']);
		
		
		if(!query_scalar('select count(*) from alliance')){
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
			   
		
		$this->update_players_detail_data();
		
		query_execute("update global_data set dt_last_world_update=current_timestamp where id=1");
		
	}
	
	public function update_players_shallow_data(){
		echo "Shallow update alliance members... <br>";
		
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
	}
	
	
	public function update_players_detail_data(){
		echo "Update members detailed data... ";
		
		$sql_select_players = "SELECT p.id, p.alliance_id, p.rank, p.has_sat_code, p.points, p.points_main_base, p.fraction, p.has_badge, p.hub_name, p.hub_position".
			" FROM player p".
			" LEFT JOIN alliance a ON p.`alliance_id`=a.`id`".
			" WHERE a.`allways_full_update` OR p.`interested_in_ff_run` order by p.rank";
		
		$record_db_arr = query_arr($sql_select_players);
		
		if(!count($record_db_arr)){
			echo "failed: not exsts alliances with nonzero `allways_full_update` nor players with nonzero `interested_in_ff_run`<br>";
			return;
		}
		
		$hub_arr = $this->get_hub_arr();
		
		$player_updated_arr = array();
		
		foreach($record_db_arr as $record_id){
			$player_id = (int)$record_id['id'];
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
					$points = $base_info['p'];
					if($points > $data_parsed['points_main_base']) $data_parsed['points_main_base'] = $points;
					
					$coord = $base_info['x'].':'.$base_info['y'];
					
					foreach($hub_arr as $hub_name => $hub){
						if(isset($hub['position_arr'][$coord])){
							$data_parsed['hub_name'] = $hub_name;
							$data_parsed['hub_position'] = $hub['position_arr'][$coord];
							break;
						}
					}
				}
			}
			
			$player_updated_arr[] = $data_parsed;
			
		}
		
		if(count($player_updated_arr)){
			$fields = array_keys($player_updated_arr[0]);
			array_syncronize_with_table($player_updated_arr, 'player', 'id', $fields, $sql_select_players, true);
		}
		
		echo "ok<br>";
	}
	
	public function get_hub_arr(){
		$hub_arr = array(
			'Alpha' => array('x'=>550,'y'=>527),
			'Beta' => array('x'=>567,'y'=>537),
			'Gamma' => array('x'=>570,'y'=>553),
			'Delta' => array('x'=>556,'y'=>568),
			'Epsilon' => array('x'=>541,'y'=>570),
			'Zeta' => array('x'=>528,'y'=>553),
			'Eta' => array('x'=>538,'y'=>540),
		);
		foreach($hub_arr as &$hub){
			$x = $hub['x'];
			$y = $hub['y'];
			
			$hub['position_arr'] = array(
				($x-2).":".($y-2), ($x).":".($y-2), ($x+2).":".($y-2),
				($x-2).":".($y),                    ($x+2).":".($y),
				($x-2).":".($y+2), ($x).":".($y+2), ($x+2).":".($y+2),
			);
			$hub['position_arr'] = array_flip($hub['position_arr']);
			$x=$x;
		}
		unset($hub);
		return $hub_arr;
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
