<?php


class Packet_parser {
	
	
	static public function upload_form($packet_parse_form)
	{
	
		$packet_type_id = (int)$packet_parse_form->packet_type_id;
		if(!$packet_type_id){ $packet_parse_form->addError('packet_type_id','packet_type_id must be non empty'); return false; }
		
		
		$packets = json_decode($packet_parse_form->packets_json, true);
		if($packets === null){ $packet_parse_form->addError('packets_json','unable to parse JSON'); return false; }
		
		return $this->upload($packet_type_id, $packets);
	}
	
	/**
	 * 
	 * @param Packet_parse_from $packet_parse_form
	 * @returns string|boolean  on error returns false, if ok returns information about inserted records
	 */
	static public function upload($packet_type_id, $packets)
	{
		$count_uploaded = 0;
		$count_inserted = 0;
		$count_parsed = 0;
		$count_parse_errors = 0;
		

		switch($packet_type_id){
			case PacketType::PT_ATTACKS_LOG:
				$count_uploaded = count($packets);
				foreach($packets as $packet){
					$id = $packet['id'];
					$json = json_encode($packet,JSON_UNESCAPED_UNICODE);
					$count_inserted += query_execute('insert into attack (id, attack_log_json, dt) values (:id, :attack_log_json, from_unixtime(:dt)) ON DUPLICATE KEY UPDATE attack_log_json=:attack_log_json, dt=from_unixtime(:dt)',
						array('id'=>$id, 'attack_log_json' => $json, 'dt'=>(int)(((double)$packet['t'])/1000))  );
						
					try{
						$count_parsed += self::parse_and_update__attack($id, $json);
					}
					catch(Exception $e){
						error_log(__METHOD__.": exception in parse_and_update__attack(): ".$e->getMessage()."; json: ". $json);
						$count_parse_errors++;
					}
				}
				
				break;
			case PacketType::PT_ATTACK_REPORT:
				$packet_parse_form->addError('packets_json','unable to parse JSON'); return false;
				break;
			case PacketType::PT_PAYER_INFO:
				$packet_parse_form->addError('packets_json','unable to parse JSON'); return false;
				break;
			case PacketType::PT_ALLAINCE_MEMBER_DATA:
				$count_uploaded = count($packets);
				//query_execute('update player set is_member=0');
				
				foreach($packets as $packet){
					$id = $packet['i'];
					$name = $packet['n'];
					$json = json_encode($packet,JSON_UNESCAPED_UNICODE);
					
					$count_inserted += query_execute('insert into player (id, name) values (:id, :name, alliance_member_data_json) ON DUPLICATE KEY UPDATE name=:name',
						array('id'=>$id, 'name'=>$name));
						
					/*try{
						$count_parsed += self::parse_and_update__attack($id, $json);
					}
					catch(Exception $e){
						error_log(__METHOD__.": exception in parse_and_update__attack(): ".$e->getMessage()."; json: ". $json);
						$count_parse_errors++;
					}*/					
				}
				break;
			case PacketType::PT_PUBLIC_ALLIANCE_INFO:
				$count_inserted += self::update_public_alliance_info($packets);
				break;
			default:
				throw new Exception(__METHOD__.":".__LINE__." unknown packet_type_id=$packet_type_id");
		}
		
		
		return 'records uploaded: '.$count_uploaded.';  records inserted(updated): '.$count_inserted."; records parsed: ".$count_parsed."; record parse errors: ".$count_parse_errors;
		
	}
	
	
	/**
	 * @param mixed[] $data example(in json form): {"a":[{"a":3136,"an":"United People-rank 5","aw":false,"bc":435,"fac":1,"pc":50,"r":1,"s":1883697982,"sa":40694362,"sc":2034718103}....
	 */
	static public function update_alliances_list($data)
	{
		$count_inserted = 0;
		$record_arr = $data["a"];
		foreach($record_arr as $record){
			$id = $record['a'];
			$name = $record['an'];
			$rank = $record['r'];
			$count_inserted += query_execute('insert into alliance (id, name, rank) values (:id, :name, :rank) ON DUPLICATE KEY UPDATE name=:name, rank=:rank',
				array('id'=>$id, 'name'=>$name,'rank'=>$rank)
			);
		}
		return $count_inserted;
	}
	
	static public function update_public_alliance_info($data)
	{
		$count_inserted = 0;
		$alliance_id = (int)($data['i']);// 101 - vortex ares
		query_execute('update player set alliance_id=-alliance_id where alliance_id=:alliance_id',array('alliance_id'=>$alliance_id));
		foreach($data['m'] as $m){
			$id = $m['i'];
			$name = $m['n'];
			$rank = $m['r'];
			$points = $m['p'];
			$count_inserted += query_execute('insert into player (id, name, rank, points, alliance_id) values (:id, :name, :rank, :points, :alliance_id)'
					. ' ON DUPLICATE KEY UPDATE name=:name, rank=:rank, points=:points, alliance_id=:alliance_id',
				array('id'=>$id, 'name'=>$name, 'rank'=>$rank, 'points'=>$points, 'alliance_id'=>$alliance_id));
		}
		return $count_inserted;
	}
	
	/**
	 * 
	 * @param int $id
	 * @param string $json
	 * @return int count of changed records (0/1)
	 * @throws Exception
	 */
	static public function parse_and_update__attack($id, $json){
		$packet_object = json_decode($json,true);
		
		if($packet_object === false){
			throw new Exception('json parse error. json: '.$json);
		}
		
		$fields = array();
		
		self::packet_to_table_fields($packet_object, PacketType::PT_ATTACKS_LOG, $fields);
		
		if(!count($fields)){
			return 0;
		}

		$field_name_arr = array_keys($fields);
		//$sql_insert_fields = '`'.implode('`, `',$field_name_arr).'`';
		//$sql_insert_values = ':'.implode(', :',$field_name_arr);
		$sql_update_equate = '';


		foreach($field_name_arr as $field_name){
			if($field_name === 'id') continue;
			if(strlen($sql_update_equate)) $sql_update_equate .= ', ';
			if($field_name === 'dt'){
				$sql_update_equate .= "`{$field_name}`=from_unixtime(:{$field_name})";
			}
			else{
				$sql_update_equate .= "`{$field_name}`=:{$field_name}";
			}
		}


		foreach($fields as $field){
			if(is_array($field)){
				vd($field);
			}
		}
		return query_execute('UPDATE attack set '.$sql_update_equate.',attack_log_is_parsed=1 where id=:id', $fields);
	}


	static public function parse_all($packet_type_id, $forse_all = 0){
		$res = '';
		$count_parsed = 0;
		$count_changed = 0;
		$count_errors = 0;
		
		try{
			switch($packet_type_id){
				case PacketType::PT_ATTACKS_LOG:
					
					$record_arr = query_arr('select id, attack_log_json from attack where LENGTH(attack_log_json) '. ($forse_all ? ' and (not COALESCE(attack_log_is_parsed,0)) ':'') );
					
					$res .= 'records neeed to parse: '.count($record_arr).'<br>';
					foreach($record_arr as $record){
						$id = $record['id'];
						$json = $record['attack_log_json'];

						try{
							$count_changed += self::parse_and_update__attack($id,$json);
							$count_parsed++;
							
						}
						catch(Exception $e){
							error_log(__METHOD__.": exception in parse_and_update__attack(): ".$e->getMessage()."; id:".$id.", json: ". $json);
							$count_errors++;
							return;
						}
					}
					
					break;
				case PacketType::PT_ATTACK_REPORT:
					throw new UException(__METHOD__.":".__LINE__." not implemended");
					break;
				case PacketType::PT_PAYER_INFO:
					throw new Exception(__METHOD__.":".__LINE__." not implemended");
					break;
				case PacketType::PT_ALLAINCE_MEMBER_DATA:
					throw new Exception(__METHOD__.":".__LINE__." not implemended");
					break;
				case PacketType::PT_PUBLIC_ALLIANCE_INFO:
					throw new Exception(__METHOD__.":".__LINE__." not implemended");
					break;
				default:
					throw new Exception(__METHOD__.":".__LINE__." unknown packet_type_id=$packet_type_id");
			}
		}
		catch(Exception $e){
			my_log(__METHOD__.': exception on parsing of packet type #'.$packet_type_id.': '.$e->getMessage());
			throw $e;
		}
		$res .= 'records parsed: '.$count_parsed.'<br>';
		$res .= 'records with errors: '.$count_errors.'<br>';
		$res .= 'records changed: '.$count_changed.'<br>';
		
		return $res;
	}
	
	static public function packet_to_table_fields($packet_object, $packet_type_id, &$fields){
		
		switch($packet_type_id){
			
			case PacketType::PT_ATTACKS_LOG:	
				
				$fields_names = array('id','dt','def_login','def_is_forgotten','def_base_id','def_base_name','def_base_level','def_alliance_name','att_login',
					'att_base_id','att_base_name','report_id','outcome_id','outcome_text');
				foreach($fields_names as $field_name){
					$fields[$field_name] = null;
				}
				
				$fields['id'] = (int)$packet_object['id'];
				$fields['dt'] = (int)(((double)$packet_object['t'])/1000) ;
				
				
				
				$fields['outcome_id'] = (int)$packet_object['mdb'];
				
				if(in_array($fields['outcome_id'],array(43,34,42, 40,24,38))) // raid or other alliance attacked
				{
					$fields['att_alliance_name'] = 'self alliance'; 
					if(in_array($fields['outcome_id'],array(43,34,42))){
						$fields['def_alliance_name'] = 'Forgotten';
					}
					
					foreach($packet_object['p'] as $p){
						$k = (string)$p['k'];					
						switch($k){
							case '1':
								$fields['def_login'] = $p['v'];
								break;
							case '2':
								$fields['def_is_forgotten'] = $p['t']==="ncct" ? 1 : 0;
								$fields['def_base_id'] = $p['v'][0];
								$fields['def_base_name'] = $p['v'][1];
								break;
							case '3':
								$fields['def_base_level'] = $p['v'];
								break;
							case '4':
								$fields['def_alliance_name'] = $p['v'];
								break;
							case '5':
								$fields['att_login'] = $p['v'];
								break;
							case '6':
								$fields['att_base_id'] = $p['v'][0];
								$fields['att_base_name'] = $p['v'][1];
								break;
							case '7':
								$fields['att_base_level'] = $p['v'][0];
							case 'reportId':
								$fields['report_id'] = $p['v'][0];
								break;
						}
					}
				}
				else if( in_array($fields['outcome_id'],array(39,28,41))){ // self alliance attacked
					foreach($packet_object['p'] as $p){
						$k = (string)$p['k'];					
						switch($k){
							case '1':
								$fields['def_base_id'] = $p['v'][0];
								$fields['def_base_name'] = $p['v'][1];
								break;
							case '2':
								$fields['def_base_level'] = $p['v'];
								break;
							case '3':
								$fields['def_login'] = $p['v'];
								break;
							case '4':
								$fields['att_login'] = $p['v'];
								break;
							case '5':
								$fields['att_alliance_name'] = $p['v'];
								break;
							case '6':
								$fields['att_base_id'] = $p['v'][0];
								$fields['att_base_name'] = $p['v'][1];
								break;
							case '5':
								$fields['att_base_level'] = $p['v'];
								break;
							case 'reportId':
								$fields['report_id'] = $p['v'][0];
								break;
						}
					}
				}
				else{
					throw new Exception(__METHOD__.": unsupported combat log type (mdb=".$fields['outcome_id'].")");
				}
				
				$outcome_text_arr = array(
					43 => 'Alliance Raid: Total Defeat',
					34 => 'Alliance Raid: Victory',
					42 => 'Alliance Raid: Total Victory',					
					
					40 => 'Alliance Attack: Total Defeat',
					24 => 'Alliance Attack: Victory',
					38 => 'Alliance Attack: Total Victory',
					
					39 => 'Combat Battle Total Won Defense',
					28 => 'Combat Battle Won Attacker',
					41 => 'Combat Battle Total Won Attacker',
					
				);
				$fields['outcome_text'] = array_default($outcome_text_arr, $fields['outcome_id'], '');
				
				break;
			
			case PacketType::PT_ATTACK_REPORT:
				throw new Exception(__METHOD__.":".__LINE__." not implemended");
				break;
			
			case PacketType::PT_PAYER_INFO:
				throw new Exception(__METHOD__.":".__LINE__." not implemended");
				break;
			
			default:
					throw new Exception(__METHOD__.":".__LINE__." unknown packet_type_id=$packet_type_id");
		}
	}
	
}

/*SELECT DATE(dt), att_base_level, def_base_level, SUM(IF(outcome_id=42,1,0)) AS kills,  SUM(IF(outcome_id IN (43,34,42),1,0)) AS attacks FROM attack 
WHERE att_login='sharpensteel1'
GROUP BY 1,2
HAVING SUM(IF(outcome_id IN (43,34,42),1,0))>0*/